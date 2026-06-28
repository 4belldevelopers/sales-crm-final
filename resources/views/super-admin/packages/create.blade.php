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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name">@lang('modules.packages.packageName')</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_phone">Duration (In Days)</label>
                                    <input type="text" class="form-control" id="trial_duration" name="trial_duration"
                                        value="">
                                </div>
                            </div>


                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="company_email">@lang('modules.packages.description')</label>
                                <textarea name="description" id="description" cols="30" rows="3" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="row">
                            <input type="hidden" class="form-control" value="0" id="monthly_price" name="monthly_price">
                            <input type="hidden" class="form-control" value="0" id="annual_price" name="annual_price">
                            {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_phone">@lang('modules.packages.monthlyPrice')</label>
                                <input type="text" class="form-control" id="monthly_price" name="monthly_price">
                            </div>
                        </div> --}}
                            {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_phone">@lang('modules.packages.annualPrice')</label>
                                <input type="text" class="form-control" id="annual_price" name="annual_price">
                            </div>
                        </div> --}}
                            @if ($paymentSetting->stripe_status == 'active')
                                {{-- <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stripe_monthly_plan_id">@lang('modules.packages.stripeMonthlyPlanId')</label>
                                    <input type="text" class="form-control" id="stripe_monthly_plan_id" name="stripe_monthly_plan_id">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stripe_annual_plan_id">@lang('modules.packages.stripeAnnualPlanId')</label>
                                    <input type="text" class="form-control" id="stripe_annual_plan_id" name="stripe_annual_plan_id">
                                </div>
                            </div> --}}
                            @endif
                            @if ($paymentSetting->razorpay_status == 'active')
                                {{-- <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">@lang('modules.packages.razorpayAnnualPlanId')</label>
                                        <input type="text" id="razorpay_annual_plan_id" name="razorpay_annual_plan_id" value="" class="form-control" >
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>@lang('modules.packages.razorpayMonthlyPlanId')</label>
                                        <input type="text" name="razorpay_monthly_plan_id" id="razorpay_monthly_plan_id" value=""  class="form-control">
                                    </div>
                                </div> --}}
                            @endif
                            {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_phone">@lang('modules.packages.noOfJobOpenings') @lang('modules.packages.leaveBlank')</label>
                                <input type="text" class="form-control" id="no_of_job_openings" name="no_of_job_openings">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_phone">@lang('modules.packages.noOfCandidateAccess') @lang('modules.packages.leaveBlank')</label>
                                <input type="text" class="form-control" id="no_of_candidate_access" name="no_of_candidate_access">
                            </div>
                        </div> --}}
                        </div>

                        <div class="row">
                            {{-- <div class="col-sm-6 col-md-4">
                                <div class="form-group">
                                    <label class="control-label col-sm-8">@lang('modules.packages.careerWebsite')</label>

                                    <div class="col-sm-4">
                                        <div class="switchery-demo">
                                            <input type="checkbox" class="js-switch" name="career_website"
                                                data-color="#99d683" data-size="small" />
                                        </div>
                                    </div>
                                </div>
                            </div>--}}

                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-sm-8">Custom</label>

                                    <div class="col-sm-4">
                                        <div class="switchery-demo">
                                            <input type="checkbox" class="js-switch" name="custome_pck"
                                                data-color="#99d683" data-size="small" />
                                        </div>
                                    </div>
                                </div>
                            </div> 
                            <div class="col-sm-6 col-md-3">
                            <div class="form-group">
                                <label class="control-label col-sm-8">@lang('modules.packages.recommended')</label>

                                <div class="col-sm-4">
                                    <div class="switchery-demo">
                                        <input type="checkbox"
                                                class="js-switch"
                                                name="recommended" 
                                                data-color="#99d683" data-size="small"
                                                />
                                    </div>
                                 </div>
                              </div>
                          </div>

                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-sm-8">Trial</label>

                                    <div class="col-sm-4">
                                        <div class="switchery-demo">
                                            <input type="checkbox" class="js-switch" name="is_trail" data-color="#99d683"
                                                data-size="small" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6 col-md-3">
                                <div class="form-group">
                                    <label class="control-label col-sm-8">@lang('app.status')</label>

                                    <div class="col-sm-4">
                                        <div class="switchery-demo">
                                            <input type="checkbox" checked class="js-switch" name="status"
                                                data-color="#99d683" data-size="small" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <hr>
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label" style="">Features</label>
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantity</label>
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Price</label>
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Final Price</label>
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>
                        </div>
                        @if ($package_features_operations)
                            <div class="row">
                                <h4 class="card-title">Operations Features</h4>
                            </div>
                            @foreach ($package_features_operations as $val)
                                <div class="row rowsContainer ">
                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div
                                                class="form-group row fv-row mb-2 fv-plugins-icon-container row seven-cols">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control form-control-solid"
                                                        value="{{ $val->title }}" readonly="">
                                                    <input type="hidden" class="form-control form-control-solid"
                                                        name="id[]" placeholder="" value="{{ $val->id }}"
                                                        readonly="">
                                                    <span class="form-text text-muted"></span>
                                                    <div class="fv-plugins-message-container "></div>
                                                </div>

                                                <div class="col-md-2">
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="quantity[]" id="{{ $val->field_name }}_quantity"
                                                        placeholder="Quantity" value=""
                                                        oninput="calculateSubtotal('{{ $val->field_name }}')">
                                                    <span class="form-text text-muted"></span>
                                                    <div class="fv-plugins-message-container "></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="price[]" id="{{ $val->field_name }}_price"
                                                        placeholder="&#x20b9;0.00" value=""
                                                        oninput="calculateSubtotal('{{ $val->field_name }}')">
                                                    <span class="form-text text-muted"></span>
                                                    <div class="fv-plugins-message-container "></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number"
                                                        class="form-control form-control-solid sub_total_amount"
                                                        name="finalprice[]" id="{{ $val->field_name }}_finalprice"
                                                        placeholder="&#x20b9;0.00" value="" readonly=""
                                                        style="background-color: #CCCCCC">
                                                    <span class="form-text text-muted"></span>
                                                    <div class="fv-plugins-message-container "></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="status[]" id="{{ $val->field_name }}_status"
                                                        class="form-control form-control-solid">
                                                        <option value="1" selected="selected"
                                                            style="background-color:#FFFFFF; color:#000000">Active
                                                        </option>
                                                        <option value="0"
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
                                            <div
                                                class="form-group row fv-row mb-2 fv-plugins-icon-container row seven-cols">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control form-control-solid"
                                                        value="{{ $val->title }}" readonly="">
                                                    <input type="hidden" class="form-control form-control-solid"
                                                        name="id[]" placeholder="" value="{{ $val->id }}"
                                                        readonly="">
                                                    <span class="form-text text-muted"></span>
                                                    <div class="fv-plugins-message-container "></div>
                                                </div>

                                                <div class="col-md-2">
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="quantity[]" id="{{ $val->field_name }}_quantity"
                                                        placeholder="Quantity" value=""
                                                        oninput="calculateSubtotal('{{ $val->field_name }}')">
                                                    <span class="form-text text-muted"></span>
                                                    <div class="fv-plugins-message-container "></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="price[]" id="{{ $val->field_name }}_price"
                                                        placeholder="&#x20b9;0.00" value=""
                                                        oninput="calculateSubtotal('{{ $val->field_name }}')">
                                                    <span class="form-text text-muted"></span>
                                                    <div class="fv-plugins-message-container "></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number"
                                                        class="form-control form-control-solid sub_total_amount"
                                                        name="finalprice[]" id="{{ $val->field_name }}_finalprice"
                                                        placeholder="&#x20b9;0.00" value="" readonly=""
                                                        style="background-color: #CCCCCC">
                                                    <span class="form-text text-muted"></span>
                                                    <div class="fv-plugins-message-container "></div>
                                                </div>
                                                <div class="col-md-2">
                                                    <select name="status[]" id="{{ $val->field_name }}_status"
                                                        class="form-control form-control-solid">
                                                        <option value="1" selected="selected"
                                                            style="background-color:#FFFFFF; color:#000000">Active
                                                        </option>
                                                        <option value="0"
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
                                    <div class="col-md-1">&nbsp;</div>
                                    <div class="col-md-1">&nbsp;</div>
                                    <div class="col-md-2">&nbsp;</div>
                                    <div class="col-md-3">&nbsp;</div>
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
                                    <div class="col-md-2">&nbsp;</div>
                                    <div class="col-md-3">&nbsp;</div>
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

                        <div class="mb-5">
                            <div class="flex-column">
                                <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                    <div class="col-md-1">&nbsp;</div>
                                    <div class="col-md-1">&nbsp;</div>
                                    <div class="col-md-2">&nbsp;</div>
                                    <div class="col-md-3">&nbsp;</div>
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




            $('body').on('keyup', '#discount', function(e) {
                total_amount();
            });

    });

        function calculateSubtotal(ele) {
            var quantity = parseFloat(document.getElementById(ele + "_quantity").value);
            var price = parseFloat(document.getElementById(ele + "_price").value);
            var subtotal = quantity * price;
            document.getElementById(ele + "_finalprice").value = subtotal.toFixed(2);
            calculateSum();
            total_amount();
        }

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
                per_val = Number(per_val).toFixed(2);
            }
            $('#labelval_' + labelper_rel).val(per_val);
            calculateSum();
            total_amount();
        });

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

            afterdiscount_amount = Number(afterdiscount_amount).toFixed(2);

            //$('#amount_total_paid').val(0);	
            $('#amount_total_paid').val(afterdiscount_amount);

        }



        // Switchery
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
    url: '{{ route('superadmin.packages.store') }}',
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
