@extends('layouts.superadmin_layout')

@push('head-script')
    <link rel="stylesheet" href="{{ asset('assets/node_modules/switchery/dist/switchery.min.css') }}">
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Create New Top Up</h4>

                    <form id="editSettings" class="ajax-form">
                        @csrf

                        <div class="row">
                            <div class="col-md-2">
                                <label for="company_name">Total Coins</label>
                                <input type="number" class="form-control form-control-solid" name="total_coins"
                                id="total_coins" value="{{ ($update && $coin_packages->coins) ? $coin_packages->coins : '0' }}" placeholder="0.00" >
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="coin_package">Discount(%):</label>
                                    <input type="number" class="form-control form-control-solid" name="discount"
                                    id="discount" value="{{ ($update && $coin_packages->discount) ? $coin_packages->discount : '' }}" placeholder="0.00">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    @php
                                        $actualPrice = 0;
                                        if ($update) {
                                            $actualPrice =
                                                $coin_packages->price / (1 - $coin_packages->discount / 100);
                                        }

                                    @endphp

                                    <label for="price_label">Price(INR)</label>
                                    <input type="number" class="form-control form-control-solid" name="price"
                                            id="price" value="{{ ($update && $coin_packages->price) ? $actualPrice : '0' }}" placeholder="0.00" style="background-color:#FFFF99;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="price_label">Total Price(INR)</label>
                                    <input type="number" class="form-control form-control-solid" name="total_price"
                                            id="total_price" value="{{ ($update && $coin_packages->price) ? $coin_packages->price : '0' }}" placeholder="0.00" readonly style="background-color:#FFFF99;">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    @php
                                        $actualPriceusd = 0;
                                        if ($update) {
                                            $actualPriceusd =
                                                $coin_packages->price_usd / (1 - $coin_packages->discount / 100);
                                        }

                                    @endphp
                                    <label for="price_usd_label">Price (USD)</label>
                                    <input type="number" class="form-control form-control-solid" name="price_usd"
                                            id="price_usd" value="{{ ($update && $coin_packages->price_usd) ? $actualPriceusd : '0' }}" placeholder="0.00" style="background-color:#FFFF99;">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="price_usd_label">Total Price (USD)</label>
                                    <input type="number" class="form-control form-control-solid" name="total_price_usd"
                                            id="total_price_usd" value="{{ ($update && $coin_packages->price_usd) ? $coin_packages->price_usd : '0' }}" placeholder="0.00" readonly style="background-color:#FFFF99;">
                                </div>
                            </div>

                           
                        </div>
                        
                        <div class="row">                        
                          
                            <label class="form-label" style="padding-top:10px;">&nbsp;</label>
                            <input type="hidden" class="form-control" value="{{ ($update && $coin_packages->id) ? $coin_packages->id : '' }}" id="token_id" name="token_id">
                        </div>

                    

                       

                        <div class="mb-5">
                            <div class="flex-column">
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                    <div class="col-md-7">&nbsp;</div>
                                    <div class="col-md-1">
                                        <label class="form-label" style="padding-top:10px;">@lang('app.status')</label>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="switchery-demo">
                                            <label class="form-label" style="padding-top:10px;">&nbsp;</label>
                                            <input type="checkbox" {{ ($update && $coin_packages->status==1) ? 'checked' : '' }}  class="js-switch" name="status"
                                                data-color="#99d683" data-size="small" />
                                        </div>
                                    </div>


                                    <div class="col-md-7">&nbsp;</div>
                                    <div class="col-md-1">
                                        <label class="form-label" style="padding-top:10px;">Per Coin Price: </label>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="switchery-demo">
                                            <label class="form-label per_coin_rate_text" style="padding-top:10px;">{{ ($update && $coin_packages->per_coin_rate) ? $coin_packages->per_coin_rate : '' }}/Coins</label>
                                            <input type="hidden"  class="js-switch per_coin_rate" name="per_coin_rate" value="{{ ($update && $coin_packages->per_coin_rate) ? $coin_packages->per_coin_rate : '' }}" />
                                        </div>
                                    </div>

                                    <div class="col-md-7">&nbsp;</div>
                                    <div class="col-md-1">
                                        <label class="form-label" style="padding-top:10px;">Per Coin Price(USD): </label>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="switchery-demo">
                                            <label class="form-label per_coin_rate_text_usd" style="padding-top:10px;">{{ ($update && $coin_packages->per_coin_rate_usd) ? $coin_packages->per_coin_rate_usd : '' }}/Coins</label>
                                            <input type="hidden"  class="js-switch per_coin_rate_usd" name="per_coin_rate_usd" value="{{ ($update && $coin_packages->per_coin_rate_usd) ? $coin_packages->per_coin_rate_usd : '' }}" />
                                        </div>
                                    </div>




                                </div>
                            </div>
                        </div>

                       

                        <div class="form-row row fv-row">
                            <div class="col-12 text-center py-10">

                            <button type="button" id="save-form" class="btn btn-success waves-effect waves-light m-r-10">
                                @lang('app.save')
                            </button>
                            <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>

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
        per_coin_rate();
        per_coin_rate_usd();
       });
       $('#discount').keyup(function(e){
        total_coins();
        per_coin_rate();
        per_coin_rate_usd();
      });

      $('#total_price').keyup(function(e){
         total_coins()
         per_coin_rate();
      });

      $('#total_price_usd').keyup(function(e){
         total_coins()
          per_coin_rate_usd();
      });
      $('#price_usd').keyup(function(e){
          total_coins()
          per_coin_rate_usd();
      });


        function total_coins(){
                let value = parseFloat($('#price').val());
                let value_usd = parseFloat($('#price_usd').val());
                let percentage = parseFloat($('#discount').val());

                if (isNaN(value)) {
                    value = 0; // Default to 0 if invalid
                }
                if (isNaN(value_usd)) {
                    value_usd = 0; // Default to 0 if invalid
                }
                if (isNaN(percentage)) {
                    percentage = 0; // Default to 0 if invalid
                }

                var total_coins = value; // Add the percentage amount to the original value
                var total_coins_usd = value_usd; // Add the percentage amount to the original value
                if(percentage !=''){
                    let additionalAmount = (percentage / 100) * value;
                    var total_coins = value - additionalAmount; // Add the percentage amount to the original value

                    let additionalAmountusd= (percentage / 100) * value_usd;
                    var total_coins_usd = value_usd - additionalAmountusd; // Add the percentage amount to the original value

                }
                
            
                $('#total_price').val(total_coins.toFixed(2)); // Set the value with two decimal points
                $('#total_price_usd').val(total_coins_usd.toFixed(2)); // Set the value with two decimal points
                per_coin_rate_usd();
                per_coin_rate();
        }



    });

    function per_coin_rate(){
        let price = parseFloat($('#total_coins').val());
        let coins = parseFloat($('#total_price').val());

        if (coins !== 0) {
            let perCoinPrice = (coins/price).toFixed(5);
            $('.per_coin_rate_text').text(perCoinPrice);
            $('.per_coin_rate').val(perCoinPrice);
            // console.log();
        } else {
            $('.per_coin_rate_text').text("Number of coins cannot be zero.");
            $('.per_coin_rate').val('');
        }
    }

    function per_coin_rate_usd(){
        let price = parseFloat($('#total_coins').val());
        let coins = parseFloat($('#total_price_usd').val());

        if (coins !== 0) {
            let perCoinPrice = (coins/price).toFixed(5);
            $('.per_coin_rate_text_usd').text(perCoinPrice);
            $('.per_coin_rate_usd').val(perCoinPrice);
            // console.log();
        } else {
            $('.per_coin_rate_text_usd').text("Number of coins cannot be zero.");
            $('.per_coin_rate_usd').val('');
        }
    }
    
   


    // total_coins total_price per_coin_rate

      
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
                url: '{{ route('superadmin.company.topUpCoinPackageStore') }}',
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
