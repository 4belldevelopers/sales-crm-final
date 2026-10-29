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

    /* Scoped Standalone Create Currency Styles */
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

<div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid cs-container">

        <!--begin::Tier 1: Header Toolbar Card-->
        <div class="cs-header-card">
            <div class="d-flex align-items-center gap-3">
                <div class="cs-brand-icon">
                    <i class="fa-solid fa-plus-circle"></i>
                </div>
                <div>
                    <h1 class="cs-title">Add New Currency</h1>
                    <p class="cs-subtitle">Configure multi-tenant ISO-3 currency definitions & baseline FX conversion rates</p>
                </div>
            </div>
            <div>
                <a href="{{ route('superadmin.currency-settings.index') }}" class="btn btn-sm btn-light-primary fw-bold d-inline-flex align-items-center">
                    <i class="fa-solid fa-arrow-left me-1 fs-8"></i> Back to Currencies
                </a>
            </div>
        </div>
        <!--end::Tier 1-->

        <!--begin::Tier 2: Form Card-->
        <div class="cs-form-card">
            <form id="createCurrencyForm" class="form">
                @csrf

                <!-- Sub-Card 1: Quick-Fill Preset from Master Catalog -->
                <div class="cs-subcard-section">
                    <div class="cs-section-header">
                        <i class="fa-solid fa-wand-magic-sparkles text-primary"></i> Quick-Fill from Master Catalog (132 Countries)
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <select id="masterCurrencyPreset" class="form-select form-select-solid form-select-sm" data-control="select2" data-placeholder="Choose country/currency preset to auto-fill name, symbol, code...">
                                <option value="">-- Choose preset or enter custom currency details below --</option>
                                @if (!empty($masterCurrencies))
                                    @foreach ($masterCurrencies as $m)
                                        <option value="{{ $m->code }}"
                                                data-name="{{ $m->currency }}"
                                                data-symbol="{{ $m->symbol }}"
                                                data-country="{{ $m->country }}">
                                            {{ $m->country }} — {{ $m->currency }} ({{ $m->code }} • {{ $m->symbol }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            <span class="fs-9 text-muted mt-1 d-block">Selecting a country preset will automatically populate the Currency Name, Symbol, and ISO-3 Code fields.</span>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 2: Primary Currency Identity -->
                <div class="cs-subcard-section">
                    <div class="cs-section-header">
                        <i class="fa-solid fa-id-card text-primary"></i> Currency Identity & Formatting
                    </div>
                    <div class="row g-4">
                        <div class="col-md-5">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">Currency Name</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" id="currency_name" name="currency_name" placeholder="e.g. Canadian Dollar" required>
                            <div class="invalid-feedback fs-8" id="err_currency_name"></div>
                        </div>
                        <div class="col-md-3">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">ISO-3 Currency Code</label>
                            <input type="text" class="form-control form-control-solid form-control-sm text-uppercase" id="currency_code" name="currency_code" placeholder="e.g. CAD" maxlength="3" required>
                            <div class="invalid-feedback fs-8" id="err_currency_code"></div>
                        </div>
                        <div class="col-md-2">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">Currency Symbol</label>
                            <input type="text" class="form-control form-control-solid form-control-sm" id="currency_symbol" name="currency_symbol" placeholder="e.g. C$" required>
                            <div class="invalid-feedback fs-8" id="err_currency_symbol"></div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fs-7 fw-bold text-dark mb-1">Display Preview</label>
                            <div class="form-control form-control-solid form-control-sm bg-light-primary text-primary fw-bolder d-flex align-items-center justify-content-between">
                                <span id="symbolPreviewText">$1,500.00</span>
                                <span class="badge badge-light-primary fs-9" id="codeBadgePreview">USD</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card 3: Dual Pivot Exchange Rates -->
                <div class="cs-subcard-section">
                    <div class="cs-section-header">
                        <i class="fa-solid fa-scale-balanced text-primary"></i> Dual Pivot Exchange Rates
                    </div>
                    <div class="row g-4 mb-2">
                        <div class="col-md-6">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">USD Based Rate (Value of 1 [Code] in USD)</label>
                            <div class="input-group input-group-sm input-group-solid">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.0001" min="0.000001" class="form-control form-control-solid" id="usd_based_rates" name="usd_based_rates" placeholder="1.0000" required>
                            </div>
                            <div class="invalid-feedback fs-8" id="err_usd_based_rates"></div>
                            <span class="fs-9 text-muted">Value of 1 unit in USD (e.g. 1 EUR = $1.11 USD).</span>
                        </div>
                        <div class="col-md-6">
                            <label class="required form-label fs-7 fw-bold text-dark mb-1">INR Based Rate (Value of 1 [Code] in INR)</label>
                            <div class="input-group input-group-sm input-group-solid">
                                <span class="input-group-text">₹</span>
                                <input type="number" step="0.0001" min="0.000001" class="form-control form-control-solid" id="inr_based_rates" name="inr_based_rates" placeholder="95.3000" required>
                            </div>
                            <div class="invalid-feedback fs-8" id="err_inr_based_rates"></div>
                            <span class="fs-9 text-muted">Value of 1 unit in INR (e.g. 1 EUR = ₹106.24 INR).</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-light-info d-flex align-items-center p-3 mb-4">
                    <i class="fa-solid fa-circle-info fs-5 text-info me-3"></i>
                    <div class="fs-8 text-dark">
                        <strong>Zero-SQL Architecture:</strong> Upon submission, this currency will be saved to MySQL and atomically compiled into <code>currencies.json</code> for 10,000+ concurrent tenant recruiters with zero read strain.
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 pt-2">
                    <a href="{{ route('superadmin.currency-settings.index') }}" class="btn btn-sm btn-light fw-bold">Cancel</a>
                    <button type="submit" class="btn btn-sm btn-primary fw-bold" id="btnSaveCurrency">
                        <span class="indicator-label"><i class="fa-solid fa-check me-1"></i> Save Currency</span>
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

    // Master Catalog Preset listener
    $('#masterCurrencyPreset').on('change', function() {
        var $opt = $(this).find('option:selected');
        var code = $(this).val();
        if (code) {
            var name = $opt.data('name') || '';
            var symbol = $opt.data('symbol') || '';
            $('#currency_name').val(name);
            $('#currency_code').val(code);
            $('#currency_symbol').val(symbol);
            updatePreview();
        }
    });

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
    $('#createCurrencyForm').on('submit', function(e) {
        e.preventDefault();
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').text('');

        var $btn = $('#btnSaveCurrency');
        $btn.attr('data-kt-indicator', 'on').prop('disabled', true);

        var data = {
            _token: $('meta[name="csrf-token"]').attr('content'),
            currency_name: $('#currency_name').val(),
            currency_code: $('#currency_code').val().toUpperCase(),
            currency_symbol: $('#currency_symbol').val(),
            usd_based_rates: $('#usd_based_rates').val(),
            inr_based_rates: $('#inr_based_rates').val()
        };

        $.ajax({
            url: "{{ route('superadmin.currency-settings.store') }}",
            type: "POST",
            data: data,
            dataType: "json",
            success: function(response) {
                $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                Swal.fire({
                    icon: 'success',
                    title: 'Created!',
                    text: response.message || 'Currency created successfully.',
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
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Error creating currency.';
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