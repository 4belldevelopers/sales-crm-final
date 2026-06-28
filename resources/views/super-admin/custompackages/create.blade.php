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
        .payment-method {
            display: none; /* Hide all sections initially */
            margin-bottom: 20px;
        }
        .active {
            display: block; /* Show active section */
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
    <div class="row" id="createPackage">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    {{-- <form action="{{ route('phonepe.payment') }}" method="POST">
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                        <input type="number" name="amount" placeholder="Amount" required>
                        <button type="submit">Create Order</button>
                    </form> --}}





                    <div class="card-header cursor-pointer px-0">
                        <!--begin::Card title-->
                        <div class="card-title m-0">
                            <h3 class="fw-bolder m-0">Create New Package</h3>
                        </div>
                        <div class="align-self-center">

                        </div>

                        <!--end::Card title-->
                        <!--begin::Action-->
                        <!--<a href="javascript:void(0);" class="btn btn-primary align-self-center">Add Contact</a>-->
                        <!--end::Action-->
                    </div>



                    <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
                        <input type="hidden" name="company_id" id="company_id" value="{{ @$company_id }}" />
                        <input type="hidden" name="paymentLinkPaymentstatus" id="paymentLinkPaymentstatus" value="" />
                        <input type="hidden" name="paymentLinkpayment_orderId" id="paymentLinkpayment_orderId" value="" />
                        <input type="hidden" name="paymentLinkshort_url" id="paymentLinkshort_url" value="" />
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="required fs-6 fw-semibold form-label mb-2">Currency</label>
                                <select name="currency" id="currency_get" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required>
                                    <option value="INR"><i class="fa fa-inr" aria-hidden="true"></i> INR</option>
                                    <option value="USD"><i class="fa fa-usd" aria-hidden="true"></i> USD</option>
                                </select>
                              </div>
                              <div class="col-md-2">
                                <label class="required fs-6 fw-semibold form-label mb-2">GST</label>
                                <select name="gst_on" id="gst_on_get" class="form-select form-select-solid" data-control="select2" data-hide-search="true" required>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                              </div>
                            <div class="col-md-4">
                                <label class="form-label">Packages</label>
                                <select name="packages_id" id="packages_id" class="form-control form-control-solid">
                                    <option value=""style="background-color:#FFFFFF; color:#000000">Select Package
                                    </option>
                                    @if (@$packages)
                                        @foreach (@$packages as $pack)
                                            <option value="{{ $pack->id }}"
                                                style="background-color:#FFFFFF; color:#000000">{{ $pack->title }} -{{ ucfirst($pack->event_type) }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control form-control-solid" name="cvaccess_startdate"
                                    id="cvaccess_startdate" placeholder="Start Date" value=""
                                    onfocus="this.showPicker()">
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control form-control-solid" name="cvaccess_enddate"
                                    id="cvaccess_enddate" placeholder="End Date" value="" onfocus="this.showPicker()" readonly>
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Packages Status</label>
                                <select name="packages_status" id="packages_status" class="form-control form-control-solid">
                                    <option value="1"style="background-color:#FFFFFF; color:#000000">Active</option>
                                    <option value="0"style="background-color:#FFFFFF; color:#000000">Inactive</option>
                                  
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-control form-control-solid">
                                    <option value="1"style="background-color:#FFFFFF; color:#000000">Paid</option>
                                    <option value="0"style="background-color:#FFFFFF; color:#000000">Pending</option>
                                  
                                </select>
                            </div>
                        </div>

                        @csrf


                        <br>

                        <div id="packagefeatures">

                        </div>



                         {{-- ////////////////////////////////// --}}
                                          
                         <div class="row">
                            <div class="col-xxl-6 mb-5 mb-xl-10">
                              <select id="payment-mode-selector" name="paymentmodeselector" class="form-select form-select-solid">
                                  <option value="">Select Payment Mode</option>
                                  <option value="Cash">Cash</option>
                                  <option value="Cheque">Cheque</option>
                                  <option value="Bank Transfer">Bank Transfer</option>
                                  <option value="Payment Link">Payment Link</option>
                              </select>
                            </div>
                            <div class="col-xxl-6 mb-5 mb-xl-10">
                              <select id="payment-method-selector" name="paymentmethodselector" class="form-select form-select-solid">
                                  <option value="">Select Payment Method</option>
                              </select>
                            </div>
                          </div>
                        
                          <div id="cash" class="payment-method">
                            <p>Please provide the cash amount directly to our representative.</p>
                          </div>
                    
                          <!-- Cheque Payment -->
                          <div id="cheque" class="payment-method">
                              <p>
                                  Please issue a cheque in favor of <strong>Company Name</strong> and provide the following details:
                              </p>
                              <div class="row">
                                <div class="col-xxl-4 mb-5 mb-xl-10">
                                  <label for="cheque-number">Cheque Number:</label>
                                  <input type="text" class="form-control form-control-solid" id="cheque-number" name="chequenumber">
                                </div>
                                <div class="col-xxl-4 mb-5 mb-xl-10">
                                  <label for="cheque-date">Cheque Date:</label>
                                  <input type="date" class="form-control form-control-solid" id="cheque-date" name="chequedate">
                                </div>
                                <div class="col-xxl-4 mb-5 mb-xl-10">
                                  <label for="bank-name">Bank Name:</label>
                                  <input type="text" class="form-control form-control-solid" id="bank-name" name="bankname" >
                                </div>
                              </div>
                          </div>
                      
                          <!-- Bank Transfer -->
                          <div id="bank_transfer" class="payment-method">
                              <p>
                                  After completing the transfer, please provide the transaction ID and the bank reference number below.
                              </p>
                              <div class="row">
                                <div class="col-xxl-6 mb-5 mb-xl-10">
                                  <label for="transaction-id">Transaction ID:</label>
                                  <input type="text" class="form-control form-control-solid" id="transaction-id" name="transactionid">
                                </div>
                                <div class="col-xxl-6 mb-5 mb-xl-10">
                                  <label for="bank-reference">Bank Reference Number:</label>
                                  <input type="text" class="form-control form-control-solid" id="bank-reference" name="bankreference">
                                </div>
                              </div>
                          </div>
                      
                          <!-- Online Payment -->
                          <div id="online" class="payment-method">
                              <p>Please click the button below to proceed to our secure online payment gateway. <button type="button" class="paymentLinkGenerate" onclick="paymentLinkGenerate()">Payment Link Generate</button></p>
                              <div class="paymentLinkshow" style="display:none">
                                <label class="form-label">Payment Link</label>
                                <input type="text" class="form-control form-control-solid" id="paymentLinkshow" value="" readonly/>
                              </div>
                             



                          </div>

                      {{-- //////////////////////////////////////////// --}}



                      <div class="row">
                        <div class="col-sm-9">
                            <div class="fw-bold fs-6 text-gray-800">Billing Details</div>
                            <p><span id="usernamebill">{{ @$user_name }}</span><p>
                            <p>{{ @$company_name }}
                            <p>
                            <p><span id="billing_address">{{ @$locArr }}</span> <p>
                            <p><span id="billing_gst">{{ @$gstin }}</span> <p>
                            <p><span id="billing_pan">{{ @$pan_no }}</span> <p>
                        </div>
                        <div class="col-sm-3">
                            <a href="javascript:;" class="btn btn-primary show-billing-address"
                                id="" style="float: right;">Edit Address</a>
                        </div>
                    </div>





                        <div class="form-row row fv-row">
                            <div class="col-12 text-center py-10">
                                <button type="button" name="save" id="save-form" value="save"
                                    class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                        <!--<button type="button" id="save-form" class="btn btn-success waves-effect waves-light m-r-10"> Submit </button>
                            <button type="reset" class="btn btn-inverse waves-effect waves-light">@lang('app.reset')</button>-->
                    </form>
                    @if($link_payments)
                    <p><strong>1.</strong>Pending Payment<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id="paymentLinktextToCopy">{{ $link_payments->payment_link }}</span> <button class="copy-button" onclick="paymentLinkcopyText()">Click to Copy Link</button></p>
                    @endif

                </div>
                
            </div>
        </div>
    </div>


    <div id="kt_billing_address_toggle"></div>
    <div id="kt_activitiesApplication" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activitiesApplication"
        data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="" data-kt-drawer-direction="end"
        data-kt-drawer-toggle="#kt_billing_address_toggle" data-kt-drawer-close="#kt_activitiesApplication_close">
        <div class="card shadow-none rounded-0 w-100" id="application_show_right_bar">


            <div class="card-header px-5 py-0" id="kt_activities_header">
                <div class="d-flex align-items-center py-1">
                    <a href="javascript:void(0);" id=""
                        class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder me-3"> <span
                            class="svg-icon svg-icon-primary svg-icon-1x">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                    <path
                                        d="M12,11 C9.790861,11 8,9.209139 8,7 C8,4.790861 9.790861,3 12,3 C14.209139,3 16,4.790861 16,7 C16,9.209139 14.209139,11 12,11 Z"
                                        fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                    <path
                                        d="M3.00065168,20.1992055 C3.38825852,15.4265159 7.26191235,13 11.9833413,13 C16.7712164,13 20.7048837,15.2931929 20.9979143,20.2 C21.0095879,20.3954741 20.9979143,21 20.2466999,21 C16.541124,21 11.0347247,21 3.72750223,21 C3.47671215,21 2.97953825,20.45918 3.00065168,20.1992055 Z"
                                        fill="#000000" fill-rule="nonzero"></path>
                                </g>
                            </svg>
                        </span> Billing Details
                    </a>

                </div>

                <div class="card-toolbar">
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5"
                        id="kt_activitiesApplication_close"> <span class="svg-icon svg-icon-1">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)"
                                    fill="#000000">
                                    <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                                    <rect fill="#000000" opacity="0.5"
                                        transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)"
                                        x="0" y="7" width="16" height="2" rx="1" />
                                </g>
                            </svg>
                        </span> </button>
                </div>
            </div>
            <div class="card card-custom mx-2">
                <div class="card-body py-0">
                    <div class="tab-content pt-5">
                        <form id="show-billing-form">
                            @csrf
                            <input class="form-control form-control-solid" value="{{ $company_id }}" type="hidden"
                                        id="company_id" name="company_id">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="country">Country / Region</label>
                                            <select class="form-control form-control-solid" id="country" name="country">

                                                @if ($countries)
                                                    @foreach ($countries as $country)
                                                        <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="first_name">First Name</label>
                                            <input class="form-control form-control-solid" value="{{ $firstName }}"
                                                type="text" id="first_name" name="first_name" placeholder="First Name">
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="last_name">Last Name</label>
                                            <input class="form-control form-control-solid" value="{{ $lastName }}" type="text"
                                                id="last_name" name="last_name" placeholder="Last Name">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input class="form-control form-control-solid" value="{{ $phone }}" type="tel" id="phone"
                                                name="phone" placeholder="Phone Number">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="building">Building / Society</label>
                                            <input class="form-control form-control-solid" value="{{ $building }}" type="text" id="building"
                                                name="building" placeholder="Building / Society">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label for="street">Street Name / Landmark</label>
                                            <input class="form-control form-control-solid" value="{{ @$locArr }}"
                                                type="text" id="street" name="street"
                                                placeholder="Street Name / Landmark">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="state">State</label>
                                            <select class="form-control form-control-solid" id="state" name="state_id">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="city">City</label>
                                            <select class="form-control form-control-solid" id="city" name="city">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="postal_code">Postal Code</label>
                                    <input class="form-control form-control-solid" value="{{ $postal_code }}" type="text" id="postal_code"
                                        name="postal_code" placeholder="Postal Code">
                                </div>
                                <div class="form-group">
                                    <label for="organization">Organization</label>
                                    <input class="form-control form-control-solid" type="text" value="{{ @$company_name }}"
                                        id="organization" name="organization" placeholder="Organization" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="gstin" id="gstin_text">GSTIN</label>
                                    <input class="form-control form-control-solid" value="{{ $gstin }}" type="text" id="gstin" name="gstin"
                                        placeholder="">
                                </div>
                                <div class="form-group">
                                    <label for="pan_no" id="pan_no_text">PAN No.</label>
                                    <input class="form-control form-control-solid" value="{{ $pan_no }}" type="text" id="pan_no" name="pan_no"
                                        placeholder="">
                                </div>
                        <!-- Buttons -->
                            <div class="text-center mt-4">
                                <button type="submit" id="submitbtn" class="btn btn-light-primary">
                                    <span class="indicator-label">Save</span>
                                    <span class="indicator-progress">
                                        Please wait...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                    </form>


                    </div>
                </div>
            </div>


        </div>
    </div>


@endsection
@push('footer-script')
    <script src="{{ asset('assets/node_modules/switchery/dist/switchery.min.js') }}"></script>


    <script>


        var country_id = "{{ $countrie_id }}";
        var state_id = "{{ $state_id }}";
        var city_id = "{{ $city_id }}";
        setTimeout(() => {
            $('#country').val(country_id).change();
        }, 200);


        $('#country').change(function() {
            const country = $(this).val();
            const stateDropdown = $('#state');
            const cityDropdown = $('#city');

            var countrie_id = $('#country').val();
            if(countrie_id==101){
                
                $('#gstin_text').text('GSTIN')
            }else{
                $('#gstin_text').text('TAXIN')
            }


            // Reset State and City Dropdowns
            stateDropdown.empty().append('<option value="">Select State</option>').prop('disabled', true);
            cityDropdown.empty().append('<option value="">Select City</option>').prop('disabled', true);

            if (country) {
                // Fetch States via AJAX
                $.ajax({
                    url: '{{ route('get.states') }}',
                    method: 'GET',
                    data: {
                        country: country
                    },
                    success: function(response) {
                        if (response.length) {
                            response.forEach(function(state) {
                                stateDropdown.append(
                                    `<option value="${state.id}">${state.name}</option>`);
                            });
                            stateDropdown.prop('disabled', false);
                        }
                        $('#state').val(state_id).change();
                    },
                    error: function() {
                        alert('Failed to fetch states. Please try again.');
                    }
                });
            }
        });

        // Handle State Change
        $('#state').change(function() {
            const state = $(this).val();
            const cityDropdown = $('#city');

            // Reset City Dropdown
            cityDropdown.empty().append('<option value="">Select City</option>').prop('disabled', true);

            if (state) {
                // Fetch Cities via AJAX
                $.ajax({
                    url: '{{ route('get.cities') }}',
                    method: 'GET',
                    data: {
                        state: state
                    },
                    success: function(response) {
                        if (response.length) {
                            response.forEach(function(city) {
                                cityDropdown.append(
                                    `<option value="${city.id}">${city.name}</option>`);
                            });
                            cityDropdown.prop('disabled', false);
                        }
                        $('#city').val(city_id).change();
                    },
                    error: function() {
                        alert('Failed to fetch cities. Please try again.');
                    }
                });
            }
        });


        $(document).on('click', '.show-billing-address', function() {
           
            //alert("call");	
            $('#kt_billing_address_toggle').trigger('click');
            var countrie_id = $('#country').val();
            if(countrie_id==101){
                
                $('#gstin_text').text('GSTIN')
            }else{
                $('#gstin_text').text('TAXIN')
            }




        });

        $(document).on('submit', '#show-billing-form', function(e) {
            e.preventDefault(); // Prevent default form submission

            var formData = $(this).serialize(); // Serialize form data

            $.ajax({
                url: '{{ route('form.submit') }}', // Route to handle form submission
                method: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF Token header
                },
                success: function (response) {
                    // Handle success
                    Swal.fire("Success", "Billing Details updated successfully ", "success").then(
                                        function() {
                                            $('#usernamebill').text(response.usernamebill);
                                            $('#billing_address').text(response.street);
                                            $('#billing_gst').text(response.billing_gst);
                                            $('#billing_pan').text(response.billing_pan);
                                            $('#kt_activitiesApplication_close').click();
                                        });
                },
                error: function (response) {
                    // Handle error
                    console.log(response);
                    alert('Form submission failed');
                }
            });
        });


   </script>

    <?php if($company->company_type !=3){	?>
    <script>
         document.getElementById('cvaccess_startdate').value = new Date().toISOString().split('T')[0];
        var input = document.querySelector('input[name=boostkeywords]');
        new Tagify(input)
    </script>
    <?php  } ?>

    <script>

    function paymentLinkGenerate(){
        var total_price  = $('#total_price').val();
        var currency_get  = $('#currency_get').val();
        var company_id =  $('#company_id').val();
        var packages_id =  $('#packages_id').val();

        $.ajax({
                url: '{{ route('superadmin.custompackages.generatecustompackages', @$company_id) }}',
                type: "GET",
                data: {'total_price':total_price,'currency_get':currency_get,'company_id':company_id,'packages_id':packages_id,'mainType':1},
                success: function(response) {
                    //
                    if(response.status=='success'){
                        // alert(response.short_url);
                        $('#paymentLinktextToCopy').text(response.short_url);
                        $('#paymentLinkPaymentstatus').val(response.Paymentstatus);
                        $('#paymentLinkpayment_orderId').val(response.payment_orderId);
                        $('#paymentLinkshort_url').val(response.short_url);
                        $('.paymentLinkshow').show();
                        $('#paymentLinkshow').val(response.short_url);
                    }else{
                        $('.paymentLinkshow').hide();
                        alert('Failed to create payment link');
                    }
                    
                },
                error: function(response) {
                    handleFails(response);
                    alert('Failed to create payment link');
                    $('.paymentLinkshow').hide();
                }
            });

    }


        
    function paymentLinkcopyText() {
        // Get the text to be copied
        var textToCopy = document.getElementById("paymentLinktextToCopy").innerText;
        
        // Create a temporary input element
        var tempInput = document.createElement("input");
        tempInput.value = textToCopy;
        document.body.appendChild(tempInput);
        
        // Select the text
        tempInput.select();
        tempInput.setSelectionRange(0, 99999); // For mobile devices
        
        // Copy the text
        document.execCommand("copy");
        
        // Remove the temporary input element
        document.body.removeChild(tempInput);
        
        // Provide feedback to the user
        alert("Link copied to clipboard: " + textToCopy);
    }

        const paymentSelector = document.getElementById('payment-mode-selector');
        const paymentMethods = document.querySelectorAll('.payment-method');

        // Function to hide all payment sections
        function hideAllMethods() {
            paymentMethods.forEach(method => method.classList.remove('active'));
        }

        // Event listener for payment method selection
        paymentSelector.addEventListener('change', function () {
          
            hideAllMethods(); // Hide all sections
            const selectedMethod = this.value;
            if (selectedMethod) {
                if(selectedMethod=='Cash'){
                   var activeSection = 'cash';
                }else if(selectedMethod=='Cheque'){
                   var activeSection = 'cheque';
                }else if(selectedMethod=='Bank Transfer'){
                   var activeSection = 'bank_transfer';
                }else if(selectedMethod=='Payment Link'){
                   var activeSection = 'online';
                }
                document.getElementById(activeSection).classList.add('active');
            }
        });


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

            afterdiscount_amount = Number(afterdiscount_amount).toFixed(2);

            //$('#amount_total_paid').val(0);	
            $('#amount_total_paid').val(afterdiscount_amount);

        }


        function lastdate(){
            var cvaccess_startdate = $('#cvaccess_startdate').val();
            var package_id = $('#packages_id').val();
            var currency_get =  $('#currency_get').val();
            $.ajax({
                url: '{{ route('superadmin.custompackages.lastdate') }}',
                type: "GET",
                data: {
                    "package_id": package_id,"cvaccess_startdate":cvaccess_startdate,"currency_get":currency_get
                },
                success: function(response) {
                    //window.location = response.url;
                    if(cvaccess_startdate !=''){
                        $('#cvaccess_enddate').val(response.newDate);
                    }
                },
                error: function(response) {
                    handleFails(response);
                }
            })
        }

        $(document).on('change','#gst_on_get', function(){
            gst_calculate();
        });

        $(document).on('change','#payment-mode-selector',function(e){
                var paymentMode = $(this).val();
                var paymenLink = $('#payment-method-selector').val();
                $('#paymentLinkPaymentstatus').val('');
                $('#paymentLinkpayment_orderId').val('');
                $('#paymentLinkshort_url').val('');
                $('.paymentLinkshow').hide();
                   if (paymenLink === 'razorpay') {
                            $('.paymentLinkGenerate').show();
                    } else { //paymentLinkGenerate
                        $('.paymentLinkGenerate').hide();
                    }
                    var paymentMethodSelector = $('#payment-method-selector');

                    // Clear current payment method options
                    paymentMethodSelector.html('<option value="">Select Payment Method</option>');

                    if (paymentMode === 'Payment Link') {
                        // Show only Razorpay and PhonePe options
                        paymentMethodSelector.append('<option value="razorpay">Razorpay</option>');
                        // paymentMethodSelector.append('<option value="phonepe">PhonePe</option>');
                    } else {
                        // Reset to default options
                        paymentMethodSelector.append('<option value="offline">Offline</option>');
                        paymentMethodSelector.append('<option value="online">Online</option>');
                    }
            });

            $(document).on('change','#payment-method-selector',function(e){
                var paymentMode = $(this).val();
                    if (paymentMode === 'razorpay') {
                            $('.paymentLinkGenerate').show();
                    } else { //paymentLinkGenerate
                        $('.paymentLinkGenerate').hide();
                    }
            });



        $('#packages_id,#currency_get').change(function() {
            var package_id = $('#packages_id').val();
            var currency_get =  $('#currency_get').val();
            var gst_on_get = $('#gst_on_get').val();
            // alert(package_id);
            $.ajax({
                url: '{{ route('superadmin.custompackages.packagefeature') }}',
                type: "GET",
                data: {
                    "package_id": package_id,"currency_get":currency_get,"gst_on_get":gst_on_get
                },
                success: function(response) {
                    //window.location = response.url;
                    if(response.check){
                        $('#cvaccess_enddate').removeAttr('readonly');
                    }else{
                        $('#cvaccess_enddate').attr('readonly','readonly');
                    }

                    $('#packagefeatures').html(response.view);
                    lastdate();
                    setTimeout(() => {
                        calculateSum();
                        total_amount();
                    }, 500);
                },
                error: function(response) {
                    handleFails(response);
                }
            })
        });
        

        $('#cvaccess_startdate').change(function() {
            lastdate();
        });
           
        $(document).on('keyup','#price,#gst,#total_coins', function(){
            gst_calculate();
        });
        function gst_calculate(){
            var currency_get = $('#currency_get').val(); // Get the selected currency
            var price = parseFloat($('#price').val()); // Get and parse the price value
            var discount = parseFloat($('#discount').val()); // Get and parse the price value
            var discount_amount = price * discount / 100; // 
            discount_amount_final = price;
            if(discount>0){
                var discount_amount_final = price-discount_amount;
            }
           
            var gst = parseFloat($('#gst').val()); // Get and parse the GST percentage (if needed)
            var gst_on_get = $('#gst_on_get').val();
            if (currency_get === "INR" && gst_on_get==="Yes") {
                var gst_amount = discount_amount_final * gst / 100; // Calculate GST amount at 18%
                var total_amount = discount_amount_final + gst_amount; // Calculate total amount with GST
                
                // Displaying the calculated amounts (you can update this part as needed)
                $('#gst_amount').val(gst_amount.toFixed(2)); // Show GST amount (2 decimal places)
                // $('#gst').val(18); // Show GST amount (2 decimal places)
                $('#total_price').val(total_amount.toFixed(2)); // Show total amount (2 decimal places)
            } else if(currency_get === "INR" && gst_on_get==="No") {
                 var gst_amount = discount_amount_final * 0 / 100; // Calculate GST amount at 18%
                 var total_amount = discount_amount_final + gst_amount; // Calculate total amount with GST
                
                // Displaying the calculated amounts (you can update this part as needed)
                $('#gst_amount').val(gst_amount.toFixed(2)); // Show GST amount (2 decimal places)
                $('#gst').val(0); // Show GST amount (2 decimal places)
                $('#total_price').val(total_amount.toFixed(2)); // Show total amount (2 decimal places)
            }else{
                $('#gst').val(0);
                $('#gst_amount').val(0);  
                $('#total_price').val(discount_amount_final.toFixed(2)); // Show total amount (2 decimal places)
            }
        }


        $('#save-form').click(function() {

            const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        })


            var formData = new FormData($('#createForm')[0]);
            $.ajax({
                url: '{{ route('superadmin.custompackages.createInvoice', @$company_id) }}' + '?_token=' +
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
                    //
                    if(response.status=='success'){
                        swalWithBootstrapButtons.fire(
                                'Required',
                                response.message,
                                'success'
                            )
                            window.location = response.url;
                    }else if(response.status=='offline'){
                        Swal.fire("Success", "Payment has been successfully ", "success").then(function(){
                            window.location = response.url;
                        });	
                        return false;
                    }else if(response.status=='razorpay'){
                        Swal.fire("Success", "Payment link has been send successfully ", "success").then(function(){
                            window.location = response.url;
                        });	
                        return false;
                    }else{
                        swalWithBootstrapButtons.fire(
                                'Required',
                                response.message,
                                'error'
                            )
                    }
                    
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
                gst_calculate();
                // calculateSum();
                // total_amount();
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
                per_val = Number(per_val).toFixed(2);
            }
            $('#labelval_' + labelper_rel).val(per_val);

            total_amount();
        });
    </script>
@endpush
