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
                            <div class="col-md-2">
                                <label for="company_name">Event Type</label>
                                <select name="event" id="event" class="form-control form-control-solid">
                                    <option value=''>---Select Event Type---</option>
                                    <option value="operational"
                                        {{ $update && $coin_packages->event_type == 'operational' ? 'selected' : '' }}
                                        style="background-color:#FFFFFF; color:#000000">Operational
                                    </option>
                                    <option value="sales"
                                        {{ $update && $coin_packages->event_type == 'sales' ? 'selected' : '' }}
                                        style="background-color:#FFFFFF; color:#000000">Sales
                                    </option>
                                </select>
                                <span class="form-text text-muted"></span>
                                <div class="fv-plugins-message-container "></div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    @php
                                        if ($update) {
                                            $package_details = DB::table('coin_package_types')
                                                ->select('title')
                                                ->where('id', '=', $coin_packages->coin_package_type_id)
                                                ->first();
                                            $packagename = $package_details->title;
                                        } else {
                                            $packagename = '';
                                        }
                                    @endphp
                                    <label for="coin_package">@lang('modules.packages.packageName')</label>
                                    <input type="text" class="form-control form-control-solid" name="coin_package"
                                        id="coin_package" value="{{ $packagename }}" placeholder="Package Name...">

                                    {{-- <select name="coin_package" id="coin_package" class="form-control form-control-solid">
                                        <option value=''>---Select Coin Package---</option>
                                        @if ($coinPackageType)
                                        @foreach ($coinPackageType as $package)
                                        <option data-days="{{ $package->days }}" value="{{ $package->id }}"  {{ ($update && $coin_packages->coin_package_type_id == $package->id) ? 'selected' : '' }} style="background-color:#FFFFFF; color:#000000">{{ $package->title }}
                                        </option>
                                        @endforeach
                                        @endif
                                    </select> --}}
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="durations_label">Duration (In Days)</label>
                                    <input type="text" class="form-control" id="durations" name="durations"
                                        value="{{ $update && $coin_packages->days ? $coin_packages->days : '0' }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="durations_label">Total Coins</label>
                                    <input type="number" class="form-control form-control-solid" name="total_coins"
                                    id="total_coins"
                                    value="{{ $update && $coin_packages->coins ? $coin_packages->coins : '0' }}"
                                    placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="switchery-demo">
                                    <label for="packatype_label">Type</label>
                                    <select name="package_type" id="package_type"
                                        class="form-control form-control-solid">
                                        <option value="2"
                                            {{ $update && $coin_packages->custome_pck == 2 ? 'selected' : '' }}
                                            style="background-color:#FFFFFF; color:#000000">Free
                                        </option>
                                        <option value="1"
                                            {{ $update && $coin_packages->custome_pck == 1 ? 'selected' : '' }}
                                            style="background-color:#FFFFFF; color:#000000">Custom
                                        </option>
                                        <option value="0"
                                            {{ $update && $coin_packages->custome_pck == 0 ? 'selected' : '' }}
                                            style="background-color:#FFFFFF; color:#000000">Other
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <div class="col-md-1">
                                <div class="switchery-demo">
                                    <label class="">Status</label>
                                    <select name="status" id="status"
                                        class="form-control form-control-solid">
                                        <option value="1"
                                            {{ $update && $coin_packages->status == 1 ? 'selected' : '' }}
                                            style="background-color:#FFFFFF; color:#000000">Active
                                        </option>
                                        <option value="0"
                                            {{ $update && $coin_packages->status == 0 ? 'selected' : '' }}
                                            style="background-color:#FFFFFF; color:#000000">Inactive
                                        </option>
                                    </select>
                                </div>

                            </div>


                            <div class="col-md-1">
                                <div class="switchery-demo">
                                    <label for="packatype_label">Visibility</label>
                                    <select name="visibility_show" id="visibility_show"
                                        class="form-control form-control-solid">
                                        <option value="1"
                                            {{ $update && $coin_packages->visibility_show == 1 ? 'selected' : '' }}
                                            style="background-color:#FFFFFF; color:#000000">Yearly
                                        </option>
                                        <option value="2"
                                            {{ $update && $coin_packages->visibility_show == 2 ? 'selected' : '' }}
                                            style="background-color:#FFFFFF; color:#000000">Monthly
                                        </option>
                                        <option value="0"
                                            {{ $update && $coin_packages->visibility_show == 0 ? 'selected' : '' }}
                                            style="background-color:#FFFFFF; color:#000000">Daily
                                        </option>
                                    </select>
                                </div>

                            </div>







                        </div>

                        <div class="row">

                            <label class="form-label" style="padding-top:10px;">&nbsp;</label>
                            <input type="hidden" class="form-control"
                                value="{{ $update && $coin_packages->id ? $coin_packages->id : '' }}" id="token_id"
                                name="token_id">
                            <input type="hidden" class="form-control"
                                value="{{ $update && $coin_packages->coin_package_type_id ? $coin_packages->coin_package_type_id : '' }}"
                                id="coin_package_type_id" name="coin_package_type_id">
                        </div>



                        <div class="row">
                           
                            <div class="col-md-6">

                                
                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">

                                                
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Price(INR):</label>
                                                </div>
                                                @php
                                                    $actualPrice = 0;
                                                    if ($update) {
                                                        $actualPrice = $coin_packages->price / (1 - $coin_packages->discount / 100);
                                                    }

                                                @endphp

                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid" name="coin_price"
                                                        id="coin_price"
                                                        value="{{ $update && $coin_packages->price ? $actualPrice : '0' }}"
                                                        placeholder="0.00">
                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                   
                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                                
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Discount(%):</label>
                                                </div>

                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid discount" name="discount"
                                                        id="discount"
                                                        value="{{ $update && $coin_packages->discount ? $coin_packages->discount : '' }}"
                                                        placeholder="0.00">
                                                </div>


                                            </div>
                                        </div>
                                    </div>


                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">

                                            
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Total Price/Yearly(INR):</label>
                                                </div>

                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid" name="total_price"
                                                        id="total_price"
                                                        value="{{ $update && $coin_packages->price ? $coin_packages->price : '0' }}"
                                                        placeholder="0.00" readonly style="background-color:#FFFF99;">
                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">

                                            
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Total Price/Monthly(INR):</label>
                                                </div>

                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid" name="total_price_monthly"
                                                        id="total_price_monthly"
                                                        value="{{ $update && $coin_packages->total_price_monthly ? $coin_packages->total_price_monthly : '0' }}"
                                                        placeholder="0.00" readonly style="background-color:#FFFF99;">
                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">

                                            
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Total Price/Day(INR):</label>
                                                </div>

                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid" name="total_price_days"
                                                        id="total_price_days"
                                                        value="{{ $update && $coin_packages->total_price_days ? $coin_packages->total_price_days : '0' }}"
                                                        placeholder="0.00" readonly style="background-color:#FFFF99;">
                                                </div>


                                            </div>
                                        </div>
                                    </div>


                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">

                                            
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Price/Coins(INR):</label>
                                                </div>

                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid" name="total_price_rate"
                                                        id="total_price_rate"
                                                        value="{{ $update && $coin_packages->total_price_rate ? $coin_packages->total_price_rate : '0' }}"
                                                        placeholder="0.00" readonly style="background-color:#FFFF99;">
                                                </div>


                                            </div>
                                        </div>
                                    </div>



                            </div>


                            <div class="col-md-6">

                            
                                    

                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                                @php
                                                    $actualPriceusd = 0;
                                                    if ($update) {
                                                        $actualPriceusd =
                                                            $coin_packages->price_usd / (1 - $coin_packages->discount / 100);
                                                    }

                                                @endphp
                                                
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Price(USD):</label>
                                                </div>

                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="coin_price_usd" id="coin_price_usd"
                                                        value="{{ $update && $coin_packages->price_usd ? $actualPriceusd : '0' }}"
                                                        placeholder="0.00">
                                                </div>


                                            </div>
                                        </div>
                                    </div>


                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                                
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Discount(%):</label>
                                                </div>

                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid" name="discount_usd"
                                                        id="discount_usd"
                                                        value="{{ $update && $coin_packages->discount ? $coin_packages->discount : '' }}"
                                                        placeholder="0.00">
                                                </div>


                                            </div>
                                        </div>
                                    </div>


                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Total Price/Yearly(USD)</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="total_price_usd" id="total_price_usd"
                                                        value="{{ $update && $coin_packages->price_usd ? $coin_packages->price_usd : '0' }}"
                                                        placeholder="0.00" readonly style="background-color:#FFFF99;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Total Price/Monthly(USD)</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="total_price_usd_monthly" id="total_price_usd_monthly"
                                                        value="{{ $update && $coin_packages->total_price_usd_monthly ? $coin_packages->total_price_usd_monthly : '0' }}"
                                                        placeholder="0.00" readonly style="background-color:#FFFF99;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Total Price/Day(USD)</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="total_price_usd_days" id="total_price_usd_days"
                                                        value="{{ $update && $coin_packages->total_price_usd_days ? $coin_packages->total_price_usd_days : '0' }}"
                                                        placeholder="0.00" readonly style="background-color:#FFFF99;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-5">
                                        <div class="flex-column">
                                            <div class="form-group row fv-row mb-10 fv-plugins-icon-container row seven-cols">
                                                <div class="col-md-4">
                                                    <label class="form-label" style="padding-top:10px;">Price/Coins(USD)</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="number" class="form-control form-control-solid"
                                                        name="total_price_usd_rate" id="total_price_usd_rate"
                                                        value="{{ $update && $coin_packages->total_price_usd_rate ? $coin_packages->total_price_usd_rate : '0' }}"
                                                        placeholder="0.00" readonly style="background-color:#FFFF99;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                            </div>

                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table-row-dashed fs-6 gy-5 no-footer" id="bonusList" role="grid"
                                    aria-describedby="bonusList_info" style="width: 100%;">
                                
                                    <tbody>
                                    @php
                                    $coin_package_points = DB::table('coin_package_points')->where('coin_package_id','=',@$coin_packages->id)->get();
                                    
                                    @endphp
                                        @if(count($coin_package_points) > 0)
                                        @foreach ($coin_package_points as $key => $point)
                                        <tr class="other_section other_section_row row-template remove_row " role="row">
                                            <td class="" style="width: 67%;">
                                                <input &nbsp="" type="text"
                                                    class="form-control form-control-solid bonus_points" name="points[]"
                                                    id="points_0" data-id="0" data-offerid="0" value="{{ $point->title }}"
                                                    placeholder="Enter Point">
                                            </td>
                                            <td class="" style="width: 22%;">
                                                <select name="status_point[]" id="status_point"
                                                class="form-control form-control-solid">
                                                <option value="1" {{ $update && $point->status == 1 ? 'selected' : '' }}>Active
                                                </option>
                                                <option value="0" {{ $update && $point->status == 0 ? 'selected' : '' }}>Inactive
                                                </option>
                                            </select>
                                            </td>

                                            <td>
                                                <button type="button"
                                                    class="btn btn-light-danger font-weight-bolder {{ ($key == 0) ? 'remove_points' : 'remove_points_not_first' }} me-0">
                                                    <span class="svg-icon svg-icon svg-icon me-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <rect fill="#000000" x="4" y="11" width="16"
                                                                    height="2" rx="1">
                                                                </rect>
                                                            </g>
                                                        </svg>
                                                    </span></button>
                                                <button type="button" id="addRowBtnbonus"
                                                    class="btn btn-primary addRowBtnbonus me-0" style="">
                                                    <span class="svg-icon svg-icon svg-icon me-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <rect fill="#000000" x="4" y="11" width="16"
                                                                    height="2" rx="1"></rect>
                                                                <rect fill="#000000"
                                                                    transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                    x="4" y="11" width="16" height="2"
                                                                    rx="1">
                                                                </rect>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                </button>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr class="other_section other_section_row row-template remove_row " role="row">
                                            <td class="" style="width: 67%;">
                                                <input &nbsp="" type="text"
                                                    class="form-control form-control-solid bonus_points" name="points[]"
                                                    id="points_0" data-id="0" data-offerid="0" value=""
                                                    placeholder="Enter Point">
                                            </td>
                                            <td class="" style="width: 22%;">
                                                <select name="status_point[]" id="status_point"
                                                class="form-control form-control-solid">
                                                <option value="1">Active
                                                </option>
                                                <option value="0">Inactive
                                                </option>
                                            </select>
                                            </td>
                                            <td>
                                                <button type="button"
                                                    class="btn btn-light-danger font-weight-bolder     me-0">
                                                    <span class="svg-icon svg-icon svg-icon me-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <rect fill="#000000" x="4" y="11" width="16"
                                                                    height="2" rx="1">
                                                                </rect>
                                                            </g>
                                                        </svg>
                                                    </span></button>
                                                <button type="button" id="addRowBtnbonus"
                                                    class="btn btn-primary addRowBtnbonus me-0" style="">
                                                    <span class="svg-icon svg-icon svg-icon me-0">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            xmlns:xlink="http://www.w3.org/1999/xlink" width="24px"
                                                            height="24px" viewBox="0 0 24 24" version="1.1">
                                                            <g stroke="none" stroke-width="1" fill="none"
                                                                fill-rule="evenodd">
                                                                <rect fill="#000000" x="4" y="11" width="16"
                                                                    height="2" rx="1"></rect>
                                                                <rect fill="#000000"
                                                                    transform="translate(12.000000, 12.000000) rotate(-270.000000) translate(-12.000000, -12.000000) "
                                                                    x="4" y="11" width="16" height="2"
                                                                    rx="1">
                                                                </rect>
                                                            </g>
                                                        </svg>
                                                    </span>
                                                </button>
                                            </td>
                                        </tr>
                                        @endif

                                    </tbody>

                                </table>
                            
                            </div>

                        </div>




                        <div class="form-row row fv-row">
                            <div class="col-12 text-center py-10">

                                <button type="button" id="save-form"
                                    class="btn btn-success waves-effect waves-light m-r-10">
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



            $('#coin_package').change(function(e) {

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

            $('#coin_price').keyup(function(e) {
                updatePriceDetailInr();
            });
            $('#discount').keyup(function(e) {
                var discount = $(this).val();
                updatePriceDetailInr();
                updatePriceDetailUsd();
                $('#discount_usd').val(discount)
            });
            $('#total_coins').keyup(function(e) {
                updatePriceDetailInr();
                updatePriceDetailUsd();
            });
            $('#discount_usd').keyup(function(e) {
                var discount = $(this).val();
                updatePriceDetailInr();
                updatePriceDetailUsd();
                $('#discount').val(discount)
            });
            $('#coin_price_usd').keyup(function(e) {
                updatePriceDetailUsd();
            });

            function total_coins() {
                let value = parseFloat($('#coin_price').val());
                let value_usd = parseFloat($('#coin_price_usd').val());
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
                var total_usd = value_usd; // Add the percentage amount to the original value
                if (percentage != '') {
                    let additionalAmount = (percentage / 100) * value;
                    var total_coins = value - additionalAmount; // Add the percentage amount to the original value


                    let additionalAmountusd = (percentage / 100) * value_usd;
                    var total_usd = value_usd -
                    additionalAmountusd; // Add the percentage amount to the original value


                }


                $('#total_price').val(total_coins.toFixed(2)); // Set the value with two decimal points
                $('#total_price_usd').val(total_usd.toFixed(2)); // Set the value with two decimal points
            }



        });

        $(document).off('click', '.addRowBtnbonus').on('click', '.addRowBtnbonus', function() {
                        
                        var current_ctc_mode = $('#current_ctc_mode').val();
                            var newRow = $('tbody .other_section_row').first().clone(); // Clone the first template row{
                            newRow.find('input').val(''); // Clear input values                       


                            newRow.find('.remove_points').addClass('remove_points_not_first').removeClass('remove_points');
                           
                            newRow.find('.addRowBtn').addClass('addRowBtnbonus').removeClass('addRowBtn');
                            
                            newRow.find('.bonus_offer_hourly').removeClass('basic_salary_get_hourly');

                        
                        newRow.addClass('remove_row');
                        newRow.find('.remove_points').removeAttr('disabled'); // Reset selects to default values
                        $('#bonusList tbody').append(newRow); // Append the new row to the table body
                        finalRow();
                        // Attach event listeners for new row
                    });



        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        $('.js-switch').each(function() {
            new Switchery($(this)[0], $(this).data());

        });



        $(document).off('click', '.remove_points_not_first').on('click', '.remove_points_not_first', function() {
                        const removeRow = $(this).closest('.remove_row');
                        removeRow.remove();

                    });



        $('#save-form').click(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            var formData = new FormData($('#editSettings')[0]);
            $.ajax({
                url: '{{ route('superadmin.company.coinPackageStore') }}',
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
                        let errorMessages = Object.values(response.responseJSON.errors).flat().join(
                            '<br>');

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

        function updatePriceDetailInr() {

              let price = parseFloat($('#coin_price').val());
              let discount = parseFloat($('#discount').val());
              var coins = $('#total_coins').val();

                if (isNaN(price)) {
                    value = 0; // Default to 0 if invalid
                }
                if (isNaN(discount)) {
                    discount = 0; // Default to 0 if invalid
                }

                var finalPrice = price; // Add the percentage amount to the original value
                if (discount != '') {
                    let additionalAmount = (discount / 100) * price;
                    var finalPrice = price - additionalAmount; // Add the percentage amount to the original value
                }

                // Time-based calculations
                var yearly = finalPrice; // Yearly is the full price
                var monthly = (finalPrice / 12).toFixed(2); // Monthly rate
                var daily = (finalPrice / 365).toFixed(2); // Daily rate

                // Per-Coin Rate
                var perCoinRate = (finalPrice / coins).toFixed(2);

                // Display Results
                 $('#total_price').val(yearly);
                 $('#total_price_monthly').val(monthly);
                 $('#total_price_days').val(daily);
                 $('#total_price_rate').val(perCoinRate);
        }

        function updatePriceDetailUsd() {
              let price = parseFloat($('#coin_price_usd').val());
              let discount = parseFloat($('#discount').val());
              var coins = $('#total_coins').val();

                if (isNaN(price)) {
                    value = 0; // Default to 0 if invalid
                }
                if (isNaN(discount)) {
                    discount = 0; // Default to 0 if invalid
                }

                var finalPrice = price; // Add the percentage amount to the original value
                if (discount != '') {
                    let additionalAmount = (discount / 100) * price;
                    var finalPrice = price - additionalAmount; // Add the percentage amount to the original value
                }

            // Time-based calculations
            var yearly = finalPrice; // Yearly is the full price
            var monthly = (finalPrice / 12).toFixed(2); // Monthly rate
            var daily = (finalPrice / 365).toFixed(2); // Daily rate

            // Per-Coin Rate
            var perCoinRate = (finalPrice / coins).toFixed(2);

            // Display Results
            $('#total_price_usd').val(yearly);
            $('#total_price_usd_monthly').val(monthly);
            $('#total_price_usd_days').val(daily);
            $('#total_price_usd_rate').val(perCoinRate);

       }

    </script>
@endpush
