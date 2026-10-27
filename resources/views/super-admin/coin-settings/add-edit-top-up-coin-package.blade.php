@extends('layouts.superadmin_layout')

@section('content')
<style>
.sa-topup-form-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    font-family: 'Poppins', sans-serif !important;
}

.sa-topup-form-header {
    padding: 18px 24px !important;
    border-bottom: 1px solid #e2e8f0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    background: #ffffff !important;
}

.sa-topup-form-body {
    padding: 24px !important;
}

.sa-topup-preview-box {
    background: #eff6ff !important;
    border: 1px dashed #bfdbfe !important;
    border-radius: 8px !important;
    padding: 16px 20px !important;
    margin-top: 14px !important;
}

.sa-topup-preview-grid {
    display: grid !important;
    grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    gap: 12px !important;
}
</style>

<div class="toolbar mb-5" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack p-0">
        <div class="page-title d-flex align-items-center flex-wrap me-3">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
                {{ $update ? 'Edit Top-Up Coin Package' : 'Create New Top-Up Coin Package' }}
            </h1>
        </div>
        <div class="d-flex align-items-center py-1">
            <a href="{{ route('superadmin.company.topUpCoinPackages') }}" class="btn btn-sm btn-light-primary fw-bold">
                <i class="fas fa-arrow-left me-1"></i> Back to Coin Packages
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card sa-topup-form-card">
            <div class="sa-topup-form-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="symbol symbol-40px bg-light-primary rounded d-flex align-items-center justify-content-center text-primary fs-4">
                        <i class="fa-solid fa-coins"></i>
                    </div>
                    <div>
                        <h4 class="card-title text-dark fw-bolder mb-1">
                            {{ $update ? 'Edit Top-Up Coin Package' : 'Create New Top-Up Coin Package' }}
                        </h4>
                        <div class="text-muted fs-7">
                            Configure bundle coins, pricing, discounts, and real-time per-coin rates.
                        </div>
                    </div>
                </div>
            </div>

            <div class="sa-topup-form-body">
                <form id="editSettings" class="ajax-form">
                    @csrf
                    <input type="hidden" name="token_id" id="token_id" value="{{ ($update && @$coin_packages->id) ? $coin_packages->id : '' }}">
                    <input type="hidden" name="per_coin_rate" id="per_coin_rate" value="{{ ($update && @$coin_packages->per_coin_rate) ? $coin_packages->per_coin_rate : '' }}">
                    <input type="hidden" name="per_coin_rate_usd" id="per_coin_rate_usd" value="{{ ($update && @$coin_packages->per_coin_rate_usd) ? $coin_packages->per_coin_rate_usd : '' }}">

                    @php
                        $discountVal = ($update && @$coin_packages->discount) ? (float)$coin_packages->discount : 0;
                        $finalInr = ($update && @$coin_packages->price) ? (float)$coin_packages->price : 0;
                        $finalUsd = ($update && @$coin_packages->price_usd) ? (float)$coin_packages->price_usd : 0;
                        $baseInr = ($discountVal > 0 && $discountVal < 100) ? ($finalInr / (1 - $discountVal / 100)) : $finalInr;
                        $baseUsd = ($discountVal > 0 && $discountVal < 100) ? ($finalUsd / (1 - $discountVal / 100)) : $finalUsd;
                    @endphp

                    <!-- Row 1: Scope & Package Mode -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7 required">Coin Package Scope</label>
                            <select class="form-select form-select-solid" name="event_type" id="event_type" required>
                                <option value="operational" {{ ($update && @$coin_packages->event_type == 'operational') ? 'selected' : '' }}>
                                    Operational Coins (ATS Pipeline)
                                </option>
                                <option value="sales" {{ ($update && @$coin_packages->event_type == 'sales') ? 'selected' : '' }}>
                                    Sales Coins (Marketplace Candidate Trading)
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7">Package Type</label>
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" name="custome_chk" id="custome_chk" value="1"
                                       {{ ($update && @$coin_packages->custome_chk == 1) ? 'checked' : '' }}>
                                <label class="form-check-label fs-7 fw-semibold" for="custome_chk">
                                    Custom Configurable Pack (Allows client custom coin quantity)
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Coins & Discount -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7 required">Total Coins</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fa-solid fa-coins text-warning"></i></span>
                                <input type="number" class="form-control form-control-solid" name="total_coins" id="total_coins"
                                       value="{{ ($update && @$coin_packages->coins) ? $coin_packages->coins : '' }}" placeholder="e.g. 1000" min="0" step="1" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7">Discount (%)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i class="fas fa-percent text-danger"></i></span>
                                <input type="number" class="form-control form-control-solid" name="discount" id="discount"
                                       value="{{ ($update && @$coin_packages->discount) ? $coin_packages->discount : '' }}" placeholder="0.00" min="0" max="100" step="0.01">
                            </div>
                        </div>
                    </div>

                    <!-- Row 3: INR Pricing -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7 required">Catalog Base Price (INR ₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">₹</span>
                                <input type="number" class="form-control form-control-solid" name="price" id="price"
                                       value="{{ $update ? number_format($baseInr, 2, '.', '') : '' }}" placeholder="0.00" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7 required">Final Total Price (INR ₹)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">₹</span>
                                <input type="number" class="form-control form-control-solid bg-light text-dark fw-bold" name="total_price" id="total_price"
                                       value="{{ ($update && @$coin_packages->price) ? $coin_packages->price : '' }}" placeholder="0.00" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Row 4: USD Pricing -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7">Catalog Base Price (USD $)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">$</span>
                                <input type="number" class="form-control form-control-solid" name="price_usd" id="price_usd"
                                       value="{{ $update ? number_format($baseUsd, 2, '.', '') : '' }}" placeholder="0.00" min="0" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-dark fs-7">Final Total Price (USD $)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0">$</span>
                                <input type="number" class="form-control form-control-solid bg-light text-dark fw-bold" name="total_price_usd" id="total_price_usd"
                                       value="{{ ($update && @$coin_packages->price_usd) ? $coin_packages->price_usd : '' }}" placeholder="0.00" readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Row 5: Status -->
                    <div class="row mb-6">
                        <div class="col-12">
                            <div class="p-4 bg-light rounded d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-bold text-dark fs-6">Package Active Status</div>
                                    <div class="text-muted fs-7">Enable this package for immediate purchasing in organization coin top-up drawers.</div>
                                </div>
                                <div class="form-check form-switch p-0 m-0">
                                    <input class="form-check-input" type="checkbox" role="switch" name="status" id="status" value="1"
                                           {{ (!$update || @$coin_packages->status == 1) ? 'checked' : '' }} style="width: 44px; height: 22px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Live Math Preview Card -->
                    <div class="sa-topup-preview-box mb-6">
                        <div class="fw-bold text-primary fs-7 text-uppercase mb-3">
                            <i class="fas fa-calculator me-1"></i> Real-Time Calculation & Unit Economics
                        </div>
                        <div class="sa-topup-preview-grid">
                            <div>
                                <div class="text-muted fs-8">Final INR Price</div>
                                <div class="fs-5 fw-bolder text-dark" id="txt_preview_inr">₹0.00</div>
                            </div>
                            <div>
                                <div class="text-muted fs-8">Final USD Price</div>
                                <div class="fs-5 fw-bolder text-dark" id="txt_preview_usd">$0.00</div>
                            </div>
                            <div>
                                <div class="text-muted fs-8">Rate / Coin (INR)</div>
                                <div class="fs-5 fw-bolder text-dark" id="txt_preview_rate_inr">₹0.0000</div>
                            </div>
                            <div>
                                <div class="text-muted fs-8">Rate / Coin (USD)</div>
                                <div class="fs-5 fw-bolder text-dark" id="txt_preview_rate_usd">$0.0000</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center justify-content-end gap-3 pt-4 border-top">
                        <a href="{{ route('superadmin.company.topUpCoinPackages') }}" class="btn btn-light">
                            Cancel
                        </a>
                        <button type="submit" id="save-form" class="btn btn-primary fw-bold px-6">
                            <i class="fas fa-check-circle me-1"></i> @lang('app.save')
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
$(document).ready(function() {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('#total_coins, #discount, #price, #price_usd').on('input keyup change', function() {
        calculatePricing();
    });

    function calculatePricing() {
        var coins = parseFloat($('#total_coins').val()) || 0;
        var inr = parseFloat($('#price').val()) || 0;
        var usd = parseFloat($('#price_usd').val()) || 0;
        var discount = parseFloat($('#discount').val()) || 0;

        if (discount < 0) discount = 0;
        if (discount > 100) discount = 100;

        var finalInr = inr;
        var finalUsd = usd;

        if (discount > 0) {
            finalInr = inr - (inr * (discount / 100));
            finalUsd = usd - (usd * (discount / 100));
        }

        $('#total_price').val(finalInr.toFixed(2));
        $('#total_price_usd').val(finalUsd.toFixed(2));

        var perCoinInr = (coins > 0) ? (finalInr / coins) : 0;
        var perCoinUsd = (coins > 0) ? (finalUsd / coins) : 0;

        $('#per_coin_rate').val(perCoinInr > 0 ? perCoinInr.toFixed(5) : '');
        $('#per_coin_rate_usd').val(perCoinUsd > 0 ? perCoinUsd.toFixed(5) : '');

        $('#txt_preview_inr').text('₹' + finalInr.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#txt_preview_usd').text('$' + finalUsd.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
        $('#txt_preview_rate_inr').text('₹' + perCoinInr.toFixed(4));
        $('#txt_preview_rate_usd').text('$' + perCoinUsd.toFixed(4));
    }

    calculatePricing();

    $('#editSettings').on('submit', function(e) {
        e.preventDefault();

        var $btn = $('#save-form');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');

        var formData = new FormData(this);

        $.ajax({
            url: '{{ route('superadmin.company.topUpCoinPackageStore') }}',
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $btn.prop('disabled', false).html(originalText);

                if (response.status === 'success') {
                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: { confirmButton: "btn btn-primary" }
                    }).then(function() {
                        window.location = response.url;
                    });
                } else {
                    Swal.fire({
                        text: response.message || "Failed to save settings.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalText);

                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var errorMessages = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    Swal.fire({
                        html: '<p class="fw-bold">Validation Errors:</p><p class="text-danger text-start">' + errorMessages + '</p>',
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                } else {
                    Swal.fire({
                        text: "An error occurred while saving. Please try again.",
                        icon: "error",
                        buttonsStyling: false,
                        confirmButtonText: "Ok",
                        customClass: { confirmButton: "btn btn-primary" }
                    });
                }
            }
        });
    });

});
</script>
@endpush
