@extends('layouts.superadmin_layout')

@section('content')
<style>
    /* ─── Global & Top-Gap Normalization (15.6" Screen Standard) ─── */
    body {
        overflow-x: hidden !important;
        background-color: #f8fafc !important;
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
    }
    .header-fixed.toolbar-fixed #kt_wrapper,
    .header-fixed #kt_wrapper,
    .toolbar-fixed #kt_wrapper,
    #kt_wrapper {
        padding-top: calc(65px + 8px) !important;
    }
    @media (max-width: 991.98px) {
        .header-fixed.toolbar-fixed #kt_wrapper,
        .header-fixed #kt_wrapper,
        .toolbar-fixed #kt_wrapper,
        #kt_wrapper {
            padding-top: calc(55px + 6px) !important;
        }
    }
    #kt_toolbar {
        display: none !important;
        height: 0 !important;
        min-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    #kt_content,
    .content,
    #kt_post,
    .post {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    /* Scoped Standalone Edit Currency Styles */
    .cs-container {
        width: 100%;
        max-width: 100%;
        padding: 0 16px 20px 16px !important;
        margin-top: 0 !important;
    }

    .cs-header-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 14px 20px !important;
        margin-bottom: 14px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
    }

    .cs-brand-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .cs-title {
        font-size: 15px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.25;
        margin-bottom: 2px;
    }

    .cs-subtitle {
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 0;
    }

    .cs-form-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 24px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
    }

    .cs-subcard-section {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .cs-section-header {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #475569;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
</style>

@php
    $code = strtoupper(trim($currency->currency_code));
    $isBase = in_array($code, ['USD', 'INR']);
    $countryMap = \App\Services\CurrencyService::getCountryMap();
    $country = $countryMap[$code] ?? 'Global / International';
@endphp

<div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid cs-container">

        <!--begin::Tier 1: Header Toolbar Card-->
        <div class="cs-header-card">
            <div class="d-flex align-items-center gap-3">
                <div class="cs-brand-icon">
                    <i class="fa-solid fa-pen-to-square"></i>
                </div>
                <div>
                    <h1 class="cs-title">Edit Currency — {{ $currency->currency_name }} ({{ $code }})</h1>
                    <p class="cs-subtitle"><i class="fa-solid fa-location-dot text-danger me-1 fs-9"></i>Country: <strong class="text-dark">{{ $country }}</strong> • Update exchange rates, symbols and system formatting</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                @if ($isBase)
                    <span class="badge badge-light-success fw-bolder py-2 px-3 fs-8">
                        <i class="fa-solid fa-shield-halved text-success me-1"></i> Core Benchmark Pivot
                    </span>
                @endif
                <a href="{{ route('superadmin.currency-settings.index') }}" class="btn btn-sm btn-light-primary fw-bold d-inline-flex align-items-center">
                    <i class="fa-solid fa-arrow-left me-1 fs-8"></i> Back to Currencies
                </a>
            </div>
        </div>
        <!--end::Tier 1-->

        <!--begin::Tier 2: Form Card-->
        <div class="cs-form-card">
            <form id="editCurrencyForm" class="form">
                @csrf
                @method('PUT')

                <!-- Sub-Card 1: Identity & Formatting -->
                <div class="cs-subcard-section">
                    <div class="cs-section-header">
                        <i class="fa-solid fa-id-card text-primary"></i> Currency Identity & Formatting
                    </div>
                    <div class="row g-4">
                        <div class="col-md-5">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">Currency Name</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" id="currency_name" name="currency_name" value="{{ $currency->currency_name }}" required>
                            <div class="invalid-feedback fs-8" id="err_currency_name"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">ISO-3 Currency Code</label>
                            <input type="text" class="form-control form-control-solid form-control-sm text-uppercase"
                                   id="currency_code" name="currency_code" value="{{ $code }}" maxlength="3"
                                   {{ $isBase ? 'readonly' : '' }} required>
                            <div class="invalid-feedback fs-8" id="err_currency_code"></div>
                            @if ($isBase)
                                <span class="fs-9 text-muted d-block mt-1">
                                    <i class="fa-solid fa-lock text-warning fs-10 me-1"></i> Base anchor currency code is protected.
                                </span>
                            @endif
                        </div>
                        <div class="col-md-2">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">Currency Symbol</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" id="currency_symbol" name="currency_symbol" value="{{ $currency->currency_symbol }}" required>
                            <div class="invalid-feedback fs-8" id="err_currency_symbol"></div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fs-7 fw-bold text-dark mb-1">Display Preview</label>
                            <div class="form-control form-control-solid form-control-sm bg-light-primary text-primary fw-bolder d-flex align-items-center justify-content-between">
                                <span id="symbolPreviewText">{{ $currency->currency_symbol }}1,500.00</span>
                                <span class="badge badge-light-primary fs-9" id="codeBadgePreview">{{ $code }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 2: Dual Pivot Exchange Rates -->
                <div class="cs-subcard-section">
                    <div class="cs-section-header">
                        <i class="fa-solid fa-scale-balanced text-primary"></i> Dual Pivot Exchange Rates
                    </div>
                    <div class="row g-4 mb-2">
                        <div class="col-md-6">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">USD Based Rate (Value of 1 [Code] in USD)</label>
                            <div class="input-group input-group-sm input-group-solid">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.0001" min="0.000001" class="form-control form-control-solid"
                                       id="usd_based_rates" name="usd_based_rates" value="{{ $currency->usd_based_rates }}" required>
                            </div>
                            <div class="invalid-feedback fs-8" id="err_usd_based_rates"></div>
                            <span class="fs-9 text-muted">Value of 1 unit in USD (e.g. 1 EUR = $1.11 USD).</span>
                        </div>
                        <div class="col-md-6">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">INR Based Rate (Value of 1 [Code] in INR)</label>
                            <div class="input-group input-group-sm input-group-solid">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.0001" min="0.000001" class="form-control form-control-solid"
                                       id="inr_based_rates" name="inr_based_rates" value="{{ $currency->inr_based_rates }}" required>
                            </div>
                            <div class="invalid-feedback fs-8" id="err_inr_based_rates"></div>
                            <span class="fs-9 text-muted">Value of 1 unit in INR (e.g. 1 EUR = ₹106.24 INR).</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-light-info d-flex align-items-center p-3 mb-4">
                    <i class="fa-solid fa-circle-info fs-5 text-info me-3"></i>
                    <div class="fs-8 text-dark">
                        <strong>Zero-SQL Architecture:</strong> Updating this currency immediately updates MySQL and synchronizes <code>currencies.json</code> and all multi-tenant Redis caches.
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2">
                    <a href="{{ route('superadmin.currency-settings.index') }}" class="btn btn-sm btn-light fw-bold">Cancel</a>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold" id="btnSaveCurrency">
                        <span class="indicator-label"><i class="fa-solid fa-check me-1"></i> Save Changes</span>
                        <span class="indicator-progress">Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                    </button>
                </div>
            </form>
        </div>
        <!--end::Tier 2-->

    </div>
</div>
@endsection

@push('footer-script')
<script>
$(document).ready(function() {

    $('#currency_symbol, #currency_code').on('input', function() {
        updatePreview();
    });

    function updatePreview() {
        var symbol = $('#currency_symbol').val() || '$';
        var code = ($('#currency_code').val() || 'USD').toUpperCase();
        $('#symbolPreviewText').text(symbol + '1,500.00');
        $('#codeBadgePreview').text(code);
    }

    // Auto-estimate INR rate based on USD rate
    var baselineUsdToInr = 95.30;
    $('#usd_based_rates').on('input', function() {
        var u = parseFloat($(this).val());
        if (!isNaN(u) && u > 0) {
            $('#inr_based_rates').val((u * baselineUsdToInr).toFixed(4));
        }
    });

    // Form submit AJAX
    $('#editCurrencyForm').on('submit', function(e) {
        e.preventDefault();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        var $btn = $('#btnSaveCurrency');
        $btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        var data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            _method: 'PUT',
            currency_name: $('#currency_name').val(),
            currency_code: $('#currency_code').val().toUpperCase(),
            currency_symbol: $('#currency_symbol').val(),
            usd_based_rates: $('#usd_based_rates').val(),
            inr_based_rates: $('#inr_based_rates').val()
        };

        $.ajax({
            url: "{{ route('superadmin.currency-settings.update', $currency->id) }}",
            type: "POST",
            data: data,
            dataType: "json",
            success: function(response) {
                $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                Swal.fire({
                    icon: 'success',
                    title: 'Updated!',
                    text: response.message || 'Currency updated successfully.',
                    confirmButtonColor: '#009ef7'
                }).then(() => {
                    window.location.href = "{{ route('superadmin.currency-settings.index') }}";
                });
            },
            error: function(xhr) {
                $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, msgs) {
                        $('#' + field).addClass('is-invalid');
                        $('#err_' + field).text(msgs[0]);
                    });
                } else {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error updating currency.';
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: msg,
                        confirmButtonColor: '#009ef7'
                    });
                }
            }
        });
    });

});
</script>
@endpush