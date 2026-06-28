@if ($package_features_operations)
<div class="row">
    <h4 class="card-title">Operations Features</h4>
</div>
@foreach ($package_features_operations as $val)

    <div class="row rowsContainer ">
        <div class="mb-5">
            <div class="flex-column">
                <div class="form-group row fv-row mb-2 fv-plugins-icon-container row seven-cols">
                    <div class="col-md-3">
                        <label class="form-label" style="">{{ $val->title }} </label>
                        <span class="form-text text-muted"></span>
                        <div class="fv-plugins-message-container "></div>
                        <input type="hidden" class="form-control form-control-solid"
                            name="id[]" placeholder="" value="{{ $val->feature_id }}"
                            readonly>
                    </div>
                   
                    <div class="col-md-2">
                        <input type="number" class="form-control form-control-solid"
                            name="quantity[]" id="{{ $val->field_name }}_quantity"
                            placeholder="Quantity" value="{{ $val->quantity }}"
                            oninput="calculateSubtotal('{{ $val->field_name }}')" {{ (@$val->custome_pck) ? '' : 'readonly' }}>
                        <span class="form-text text-muted"></span>
                        <div class="fv-plugins-message-container "></div>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control form-control-solid"
                            name="price[]" id="{{ $val->field_name }}_price"
                            placeholder="&#x20b9;0.00" value="{{ $val->price }}"
                            oninput="calculateSubtotal('{{ $val->field_name }}')" {{ (@$val->custome_pck) ? '' : 'readonly' }}>
                        <span class="form-text text-muted"></span>
                        <div class="fv-plugins-message-container "></div>
                    </div>
                    <div class="col-md-3">
                        <input type="number"
                            class="form-control form-control-solid sub_total_amount"
                            name="finalprice[]" id="{{ $val->field_name }}_finalprice"
                            placeholder="&#x20b9;0.00" value="{{ $val->sub_total }}" {{ (@$val->custome_pck) ? '' : 'readonly' }}
                            style="background-color: #CCCCCC">
                        <span class="form-text text-muted"></span>
                        <div class="fv-plugins-message-container "></div>
                    </div>
                    <div class="col-md-2">
                        <select name="status[]" id="{{ $val->field_name }}_status"
                            class="form-control form-control-solid">
                            <option value="1" {{ ( $val->status=='1') ? 'selected' : '' }}
                                style="background-color:#FFFFFF; color:#000000">Active
                            </option>
                            <option value="0" {{ ( $val->status=='0') ? 'selected' : '' }}
                                style="background-color:#FFFFFF; color:#000000">Inactive
                            </option>
                        </select>
                        <span class="form-text text-muted"></span>
                        <div class="fv-plugins-message-container "></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endif

@if ($package_features_salse)
<div class="row">
    <h4 class="card-title">Sales Features</h4>
</div>
@foreach ($package_features_salse as $val)
<div class="row rowsContainer ">
    <div class="mb-5">
        <div class="flex-column">
            <div class="form-group row fv-row mb-2 fv-plugins-icon-container row seven-cols">
                <div class="col-md-3">
                    <label class="form-label" style="">{{ $val->title }}</label>
                    <span class="form-text text-muted"></span>
                    <div class="fv-plugins-message-container "></div>
                    <input type="hidden" class="form-control form-control-solid"
                        name="id[]" placeholder="" value="{{ $val->feature_id }}"
                        readonly="">
                </div>

                <div class="col-md-2">
                    <input type="number" class="form-control form-control-solid"
                        name="quantity[]" id="{{ $val->field_name }}_quantity"
                        placeholder="Quantity" value="{{ $val->quantity }}"
                        oninput="calculateSubtotal('{{ $val->field_name }}')" {{ (@$val->custome_pck) ? '' : 'readonly' }}>
                    <span class="form-text text-muted"></span>
                    <div class="fv-plugins-message-container "></div>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control form-control-solid"
                        name="price[]" id="{{ $val->field_name }}_price"
                        placeholder="&#x20b9;0.00" value="{{ $val->price }}"
                        oninput="calculateSubtotal('{{ $val->field_name }}')" {{ (@$val->custome_pck) ? '' : 'readonly' }}>
                    <span class="form-text text-muted"></span>
                    <div class="fv-plugins-message-container "></div>
                </div>
                <div class="col-md-3">
                    <input type="number"
                        class="form-control form-control-solid sub_total_amount"
                        name="finalprice[]" id="{{ $val->field_name }}_finalprice"
                        placeholder="&#x20b9;0.00" value="{{ $val->sub_total }}" {{ (@$val->custome_pck) ? '' : 'readonly' }}
                        style="background-color: #CCCCCC">
                    <span class="form-text text-muted"></span>
                    <div class="fv-plugins-message-container "></div>
                </div>
                <div class="col-md-2">
                    <select name="status[]" id="{{ $val->field_name }}_status"
                        class="form-control form-control-solid" readonly>
                        <option value="1" {{ ( $val->status=='1') ? 'selected' : '' }}
                            style="background-color:#FFFFFF; color:#000000">Active
                        </option>
                        <option value="0" {{ ( $val->status=='0') ? 'selected' : '' }}
                            style="background-color:#FFFFFF; color:#000000">Inactive
                        </option>
                    </select>
                    <span class="form-text text-muted"></span>
                    <div class="fv-plugins-message-container "></div>
                </div>
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


            <div class="col-md-8">
                <input type="text" class="form-control form-control-solid"
                    name="boostkeywords" id="boostkeywords" placeholder="Boost Keywords"
                    value="">
                <span class="form-text text-muted"></span>
                <div class="fv-plugins-message-container "></div>
            </div>

        </div>
    </div>
</div>


<div class="mb-5">
    <div class="flex-column">
        <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">
                <label class="form-label" style="padding-top:10px;">Sub Total:</label>
            </div>

            <div class="col-md-1">
                <input type="number" class="form-control form-control-solid" name="sub_total"
                    id="sub_total" value="0.00" style="background-color:#FFFF99;"
                    readonly="">
            </div>

            <div class="col-md-1">&nbsp;</div>

        </div>
    </div>
</div>
<div class="mb-5">
    <div class="flex-column">
        <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">
                <label class="form-label" style="padding-top:10px;">Discount(%):</label>
            </div>

            <div class="col-md-1">
                <input type="number" class="form-control form-control-solid" name="discount"
                    id="discount" value="" placeholder="0.00">
            </div>

            <div class="col-md-1">&nbsp;</div>

        </div>
    </div>
</div>

<div class="form-group row fv-row mb-10 fv-plugins-icon-container">
    <div class="col-lg-6">&nbsp;</div>
    <div class="col-lg-6">
        <div class="input_fields_wrap">
            <button
                class="add_field_button search__btn btn btn-sm btn-primary btn-active-light-primary">Add</button>
        </div>
    </div>
</div>
<div class="mb-5">
    <div class="flex-column">
        <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">
                <label class="form-label" style="padding-top:10px;">Amount Total Paid:</label>
            </div>
            <div class="col-md-1">
                <input type="number" class="form-control form-control-solid"
                    name="amount_total_paid" id="amount_total_paid" value="0.00"
                    style="background-color: #009900; color:#FFFFFF" readonly="">
            </div>
            <div class="col-md-1">&nbsp;</div>
        </div>
    </div>
</div>

<div class="mb-5">
    <div class="flex-column">
        <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
            <div class="col-md-1">&nbsp;</div>
            <div class="col-md-1">&nbsp;</div>


            <div class="col-md-1">
                <label class="form-label" style="padding-top:10px; font-weight:bold;">Payment
                    Mode:</label>
            </div>

            <div class="col-md-1">
                <input name="payment_mode" type="radio" value="online" class=""
                    checked="checked" /> Online
            </div>
            <div class="col-md-1">
                <input name="payment_mode" type="radio" value="cash" class="" /> Cash
            </div>


            <div class="col-md-1">&nbsp;</div>

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


		$('#packages_id').change(function() {
			var package_id = $(this).val();
			// alert(package_id);
			$.ajax({
				url: '{{ route('superadmin.custompackages.packagefeature')}}',
				type: "GET",
				data: {"package_id":package_id},
				success: function(response) {
					//window.location = response.url;
					$('#packagefeatures').html(response.view);
				},
				error: function(response) {
					handleFails(response);
				}
			})
       });

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
