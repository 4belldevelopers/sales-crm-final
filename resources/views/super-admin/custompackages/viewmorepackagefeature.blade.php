@if ($coin_events)
{{-- <div class="row">
    <h4 class="card-title">Operations Features</h4>
</div> --}}
<div class="row">
    <div class="mb-5">
        <div class="flex-column">
            <div class="form-group row fv-row fv-plugins-icon-container row seven-cols">
                <div class="col-md-4">
                    <label class="form-label" style=""><b>Features</b></label>
                    <div class="fv-plugins-message-container "></div>
                </div>
                <div class="col-md-2">
                    <label class="form-label"><b>Deductions</b></label>
                    <div class="fv-plugins-message-container "></div>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><b>Deduction type</b></label>
                    <div class="fv-plugins-message-container "></div>
                </div>
                <div class="col-md-3">
                    <label class="form-label"><b>Minimum Coins Required</b></label>
                    <div class="fv-plugins-message-container "></div>
                </div>
            </div>
        </div>
    </div>
</div>
@foreach ($coin_events as $val)

    <div class="row rowsContainer ">
        <div class="mb-5">
            <div class="flex-column">
                <div class="form-group row fv-row mb-2 fv-plugins-icon-container row seven-cols">
                    <div class="col-md-4">
                        <span class="form-text text-muted">{{ $val->title }}</span>
                        <div class="fv-plugins-message-container "></div>
                        <input type="hidden" class="form-control form-control-solid"
                            name="id[]" placeholder="" value="{{ $val->id }}"
                            readonly>
                    </div>
                   
                    <div class="col-md-2">
                        <input type="number" class="form-control form-control-solid"
                            name="default_rate[]" id="{{ $val->id }}_default_rate"
                            placeholder="default_rate" value="{{ $val->default_rate }}"
                            oninput="calculateSubtotal('{{ $val->id }}')">
                        <span class="form-text text-muted"></span>
                        <div class="fv-plugins-message-container "></div>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control form-control-solid"
                            name="deduction_type[]" id="{{ $val->id }}_deduction_type"
                            placeholder="Deduction type" value="{{ $val->deduction_type }}"
                            oninput="calculateSubtotal('{{ $val->id }}')" readonly>
                        <span class="form-text text-muted"></span>
                        <div class="fv-plugins-message-container "></div>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control form-control-solid"
                            name="minimum_required[]" id="{{ $val->id }}_minimum_required"
                            placeholder="Minimum Coins" value="{{ $val->minimum_required }}"
                            oninput="calculateSubtotal('{{ $val->id }}')" readonly>
                        <span class="form-text text-muted"></span>
                        <div class="fv-plugins-message-container "></div>
                    </div>
                    
                    <input type="hidden" class="form-control form-control-solid" name="event_type"
                    id="event_type" value="{{ $event_type }}" placeholder="0.00" >
                  
                </div>
            </div>
        </div>
    </div>
@endforeach
@endif


                <div class="mb-5">
                    <div class="flex-column">
                        <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                            <div class="col-md-1">
                                <label class="form-label"
                                    style="padding-top:10px; font-weight:bold;">&nbsp;</label>
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>


                        </div>
                    </div>
                </div>


                   <div class="mb-5">
                            <div class="flex-column">
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">

                                    <div class="col-md-7">&nbsp;</div>
                                    <div class="col-md-1">
                                        <label class="form-label" style="padding-top:10px;">Total Coins:</label>
                                    </div>

                                    <div class="col-md-3">
                                        <input type="number" class="form-control form-control-solid" name="total_coins"
                                            id="total_coins" value="{{ $coins }}" placeholder="0.00" >
                                    </div>

                                   
                                </div>
                            </div>
                        </div>
                       
                        <div class="mb-5">
                            <div class="flex-column">
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                    
                                    <div class="col-md-7">&nbsp;</div>
                                    <div class="col-md-1">
                                        <label class="form-label" style="padding-top:10px;">Price:</label>
                                    </div>

                                    <div class="col-md-3">
                                        <input type="number" class="form-control form-control-solid" name="price"
                                            id="price" value="{{ $price }}" placeholder="0.00" style="background-color:#FFFF99;">
                                    </div>


                                </div>
                            </div>
                        </div>
                        <div class="mb-5">
                            <div class="flex-column">
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                    <div class="col-md-7">&nbsp;</div>
                                    <div class="col-md-1">
                                        <label class="form-label" style="padding-top:10px;">Discount(%):</label>
                                    </div>

                                    <div class="col-md-3">
                            
                                        <input type="number" class="form-control form-control-solid" name="discount"
                                            id="discount" value="{{ $discount }}" placeholder="0.00">
                                    </div>


                                </div>
                            </div>
                        </div>

                        <div class="mb-5">
                            <div class="flex-column">
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                    <div class="col-md-7">&nbsp;</div>
                                    <div class="col-md-1">
                                        <label class="form-label" style="padding-top:10px;">GST(%):</label>
                                    </div>

                                    <div class="col-md-3">
                                        <input type="number" class="form-control form-control-solid" name="gst"
                                            id="gst" value="{{ $gst }}" placeholder="0.00">
                                            <input type="hidden" class="form-control form-control-solid" name="gst_amount"
                                            id="gst_amount" value="{{ $gst_amount }}" placeholder="0.00">
                                    </div>


                                </div>
                            </div>
                        </div>
                        <div class="mb-5">
                            <div class="flex-column">
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                    
                                    <div class="col-md-7">&nbsp;</div>
                                    <div class="col-md-1">
                                        <label class="form-label" style="padding-top:10px;">Total Price:</label>
                                    </div>

                                    <div class="col-md-3">
                                        <input type="number" class="form-control form-control-solid" name="total_price"
                                            id="total_price" value="{{ $total_price }}" placeholder="0.00" style="background-color:#FFFF99;" readonly>
                                    </div>


                                </div>
                            </div>
                        </div>



<script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>

  
    <script>
        var input = document.querySelector('input[name=boostkeywords]');
        new Tagify(input)
    </script>
 

    <script>


        function calculateSubtotal(ele) {
            var quantity = parseFloat(document.getElementById(ele + "_quantity").value);
            var price = parseFloat(document.getElementById(ele + "_price").value);
            var subtotal = quantity * price;
            document.getElementById(ele + "_finalprice").value = subtotal.toFixed(2);
            calculateSum();
            total_amount();
        }

		function calculateSum() {
            var total = 0;
            var inputFields = document.getElementsByClassName("sub_total_amount");
            for (var i = 0; i < inputFields.length; i++) {
                total += parseFloat(inputFields[i].value || 0);
            }
            document.getElementById("sub_total").value = total;
        }

        function total_amount() {

            var sub_total = document.getElementById("sub_total").value;
            // DISCOUNT 
            var discount = $('#discount').val();

            var discount_amount = (sub_total * discount) / 100;
            var afterdiscount_amount = sub_total - discount_amount;

            var cvalues = 0;
            var inps = document.getElementsByName('per_val[]');
            for (var i = 0; i < inps.length; i++) {
                var inp = inps[i];
                var loopVal = parseFloat(inp.value);
                if (!isNaN(loopVal)) {
                    cvalues += loopVal;
                }

            }


            console.log(cvalues);

            afterdiscount_amount = afterdiscount_amount + cvalues;

            afterdiscount_amount = (afterdiscount_amount).toFixed(2);

            //$('#amount_total_paid').val(0);	
            $('#amount_total_paid').val(afterdiscount_amount);

        }

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
                
            
                // $('#price').val(total_coins.toFixed(2)); // Set the value with two decimal points
                gst_calculate();

        }




	// 	$('#packages_id').change(function() {
	// 		var package_id = $(this).val();
	// 		// alert(package_id);
	// 		$.ajax({
	// 			url: '{{ route('superadmin.custompackages.packagefeature')}}',
	// 			type: "GET",
	// 			data: {"package_id":package_id},
	// 			success: function(response) {
	// 				//window.location = response.url;
	// 				$('#packagefeatures').html(response.view);
	// 			},
	// 			error: function(response) {
	// 				handleFails(response);
	// 			}
	// 		})
    //    });

    </script>



    <script>
        // cvaccess_quantity cvaccess_price 
    </script>

    <script type="text/javascript">
        $(document).ready(function() {

            $('body').on('keyup', '#discount', function(e) {
				calculateSum();
                total_amount();
            });


        });
    </script>

    <script>
        $(document).ready(function() {
            var max_fields = 10; //maximum input boxes allowed
            var wrapper = $(".input_fields_wrap"); //Fields wrapper
            var add_button = $(".add_field_button"); //Add button ID

            var x = 1; //initlal text box count
            $(add_button).click(function(e) { //on add input button click
                e.preventDefault();
                if (x < max_fields) { //max input box allowed
                    x++; //text box increment
                    $(wrapper).append(
                        '<div class="row" style=" margin:10px;"><input style="width:100px; border-radius: 0.475rem; background-color: #f5f8fa; border-color: #f5f8fa;" type="text" name="label[]" id="label_' +
                        x +
                        '" placeholder="label"/>&nbsp;&nbsp;<input class="labelper" style="width:100px; border-radius: 0.475rem; background-color: #f5f8fa; border-color: #f5f8fa;" type="number" name="per_text[]" id="labelper_' +
                        x + '" rel="' + x +
                        '"/>% &nbsp;&nbsp;<input style="width:100px; border-radius: 0.475rem; background-color:#FFFF99;" type="text" name="per_val[]" id="labelval_' +
                        x + '"/><a href="#" class="remove_field">Remove</a></div>'); //add input box 
                }
            });

            $(wrapper).on("click", ".remove_field", function(e) { //user click on remove text 
                e.preventDefault();
                $(this).parent('div').remove();
                x--;
            })
        });



        $('body').on('keyup', '.labelper', function(e) {

            var labelper_id = $(this).attr('id');
            var labelper_rel = $(this).attr('rel');

            var labelper_value = $('#' + labelper_id).val();
            var sub_total = $('#sub_total').val();

            var discount = $('#discount').val();
            var discount_amount = (sub_total * discount) / 100;

            sub_total = sub_total - discount_amount;

            var per_val = 0;
            if (sub_total > 0) {
                per_val = (sub_total * labelper_value) / 100;
                per_val = (per_val).toFixed(2);
            }
            $('#labelval_' + labelper_rel).val(per_val);

            total_amount();
        });

    </script>
