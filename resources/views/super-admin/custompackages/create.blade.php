@extends('layouts.superadmin_layout')

@push('head-script')
<style>
    /* =========================================================================
       TOP GAP COLLAPSE & VIEWPORT SPACING MANAGEMENT (Eliminate Metronic 55px whitespace)
       ========================================================================= */
    #kt_body {
        --kt-toolbar-height: 0px !important;
        --kt-toolbar-height-tablet-and-mobile: 0px !important;
    }

    .header-fixed.toolbar-fixed #kt_wrapper,
    .header-fixed #kt_wrapper,
    .toolbar-fixed #kt_wrapper,
    #kt_wrapper {
        padding-top: calc(65px + 8px) !important;
    }

    @media (max-width: 991.98px) {
        .header-tablet-and-mobile-fixed.toolbar-tablet-and-mobile-fixed #kt_wrapper,
        .header-tablet-and-mobile-fixed #kt_wrapper,
        #kt_wrapper {
            padding-top: calc(55px + 6px) !important;
        }
    }

    #kt_toolbar {
        display: none !important;
        height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    #kt_content,
    .content {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }

    /* Modern Enterprise Custom Package Page Styling */
    .custom-package-page {
        font-family: 'Poppins', sans-serif;
        padding-top: 2px !important;
    }

    /* Tier 1 Header Toolbar Card */
    .cp-header-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 12px 18px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
        margin-bottom: 14px !important;
    }

    .cp-icon-box {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        background: #eff6ff;
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    /* Card & Sub-Cards */
    .cp-card {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
        margin-bottom: 20px;
    }

    .cp-card-header {
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 14px 20px !important;
        background: #ffffff;
        border-radius: 10px 10px 0 0 !important;
    }

    .cp-card-body {
        padding: 20px !important;
    }

    /* Form Labels & Controls */
    .cp-label {
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #334155 !important;
        margin-bottom: 6px !important;
        display: block;
    }

    .cp-label.required::after {
        content: " *";
        color: #f1416c;
    }

    .cp-input {
        height: 40px !important;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 13px !important;
        color: #1e293b !important;
        background-color: #f8fafc !important;
        transition: all 0.2s ease !important;
    }

    .cp-input:focus {
        background-color: #ffffff !important;
        border-color: #009ef7 !important;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
    }

    .cp-select {
        height: 40px !important;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0 !important;
        font-size: 13px !important;
        color: #1e293b !important;
        background-color: #f8fafc !important;
    }

    .cp-select:focus {
        background-color: #ffffff !important;
        border-color: #009ef7 !important;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
    }

    /* Payment Methods */
    .payment-method-box {
        display: none;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        margin-top: 15px;
    }

    .payment-method-box.active {
        display: block;
        animation: fadeIn 0.25s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Summary Card Sticky */
    .cp-sticky-summary {
        position: sticky;
        top: 20px;
        z-index: 10;
    }

    .cp-summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        font-size: 12.5px;
        color: #475569;
    }

    .cp-summary-total {
        border-top: 2px dashed #e2e8f0;
        padding-top: 14px;
        margin-top: 10px;
    }

    /* Billing Drawer Styling */
    #kt_activitiesApplication {
        width: 100% !important;
        max-width: 620px !important;
        box-shadow: -5px 0 25px rgba(0, 0, 0, 0.08) !important;
    }

    .cp-drawer-header {
        border-bottom: 1px solid #e2e8f0;
        padding: 16px 20px;
        background: #ffffff;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .cp-drawer-footer {
        border-top: 1px solid #e2e8f0;
        padding: 16px 20px;
        background: #ffffff;
        position: sticky;
        bottom: 0;
        z-index: 10;
    }

    .cp-drawer-body {
        padding: 20px;
        overflow-y: auto;
        max-height: calc(100vh - 145px);
    }
</style>
@endpush

@section('content')
<div class="custom-package-page container-fluid px-0">

    {{-- TIER 1: TOP HEADER TOOLBAR CARD --}}
    <div class="cp-header-card d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="cp-icon-box">
                <i class="fa-solid fa-box-open"></i>
            </div>
            <div>
                <h3 class="fw-bold text-gray-900 m-0" style="font-size: 18px; line-height: 1.25;">
                    Assign Custom Package &amp; Create Invoice
                </h3>
                <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                    <span class="badge badge-light-primary fw-semibold px-2 py-1" style="font-size: 11px;">
                        <i class="fa-solid fa-building me-1 text-primary"></i> {{ @$company_name }}
                    </span>
                    <span class="badge badge-light-info fw-semibold px-2 py-1 text-uppercase" style="font-size: 11px;">
                        <i class="fa-solid fa-layer-group me-1 text-info"></i> {{ ucfirst(@$packageType ?? 'operational') }} Plan
                    </span>
                    <span class="badge badge-light-secondary text-muted fw-normal px-2 py-1" style="font-size: 11px;">
                        Company ID: #{{ @$company_id }}
                    </span>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ url('super-admin/company') }}" class="btn btn-sm btn-light btn-active-light-primary fw-semibold" style="height: 36px;">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Companies
            </a>
        </div>
    </div>

    {{-- Pending Payment Notice if active link exists --}}
    @if($link_payments)
    <div class="notice d-flex bg-light-warning rounded border-warning border border-dashed p-4 mb-5">
        <span class="svg-icon svg-icon-2tx svg-icon-warning me-4">
            <i class="fa-solid fa-clock text-warning fs-2 mt-1"></i>
        </span>
        <div class="d-flex flex-stack flex-grow-1 flex-wrap gap-2">
            <div>
                <h5 class="text-gray-900 fw-bold fs-6 mb-1">Active Pending Payment Link Found</h5>
                <div class="fs-7 text-gray-700">
                    A payment link was previously generated for this company:
                    <span class="fw-bold text-primary font-monospace ms-1" id="paymentLinktextToCopy">{{ $link_payments->payment_link }}</span>
                </div>
            </div>
            <div>
                <button type="button" class="btn btn-sm btn-warning fw-semibold" onclick="paymentLinkcopyText()">
                    <i class="fa-solid fa-copy me-1"></i> Copy Link
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Form Container --}}
    <form class="ajax-form" method="POST" id="createForm" name="createForm" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="company_id" id="company_id" value="{{ @$company_id }}" />
        <input type="hidden" name="paymentLinkPaymentstatus" id="paymentLinkPaymentstatus" value="" />
        <input type="hidden" name="paymentLinkpayment_orderId" id="paymentLinkpayment_orderId" value="" />
        <input type="hidden" name="paymentLinkshort_url" id="paymentLinkshort_url" value="" />

        {{-- Hidden pricing fields for form serialization --}}
        <input type="hidden" name="price" id="price" value="0.00" />
        <input type="hidden" name="total_coins" id="total_coins" value="0" />
        <input type="hidden" name="discount" id="discount" value="0" />
        <input type="hidden" name="gst" id="gst" value="0" />
        <input type="hidden" name="gst_amount" id="gst_amount" value="0.00" />
        <input type="hidden" name="total_price" id="total_price" value="0.00" />

        <div class="row g-6">
            {{-- LEFT COLUMN: Configuration & Matrix (8 Cols) --}}
            <div class="col-lg-8 col-xl-8">

                {{-- SUB-CARD 1: Package & Validity Configuration --}}
                <div class="cp-card">
                    <div class="cp-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-primary fs-5"><i class="fa-solid fa-sliders text-primary"></i></span>
                            <h4 class="fw-bold text-gray-900 m-0" style="font-size: 14.5px;">1. Package &amp; Validity Configuration</h4>
                        </div>
                        <span class="badge badge-light-primary fw-semibold px-2 py-1" style="font-size: 11px;">Step 1 of 3</span>
                    </div>

                    <div class="cp-card-body">
                        <div class="row g-4">
                            {{-- Currency --}}
                            <div class="col-md-3">
                                <label class="cp-label required" for="currency_get">Currency</label>
                                <select name="currency" id="currency_get" class="form-select cp-select" required>
                                    <option value="INR" selected>INR (&#8377;)</option>
                                    <option value="USD">USD ($)</option>
                                </select>
                            </div>

                            {{-- GST Applicable --}}
                            <div class="col-md-3">
                                <label class="cp-label required" for="gst_on_get">Apply GST (18%)</label>
                                <select name="gst_on" id="gst_on_get" class="form-select cp-select" required>
                                    <option value="Yes" selected>Yes (18% GST)</option>
                                    <option value="No">No (0% Tax)</option>
                                </select>
                            </div>

                            {{-- Packages Dropdown --}}
                            <div class="col-md-6">
                                <label class="cp-label required" for="packages_id">Select Package</label>
                                <select name="packages_id" id="packages_id" class="form-select cp-select" required>
                                    <option value="">-- Choose Package --</option>
                                    @if (@$packages)
                                        @foreach (@$packages as $pack)
                                            <option value="{{ $pack->id }}" data-days="{{ $pack->days }}">
                                                {{ $pack->title }} &bull; {{ $pack->days }} Days ({{ ucfirst($pack->event_type) }})
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            {{-- Start Date --}}
                            <div class="col-md-3">
                                <label class="cp-label required" for="cvaccess_startdate">Start Date</label>
                                <div class="input-group">
                                    <input type="date" 
                                           class="form-control cp-input" 
                                           name="cvaccess_startdate" 
                                           id="cvaccess_startdate" 
                                           value="{{ date('Y-m-d') }}" 
                                           min="{{ date('Y-m-d') }}"
                                           required />
                                </div>
                            </div>

                            {{-- End Date --}}
                            <div class="col-md-3">
                                <label class="cp-label required" for="cvaccess_enddate">End Date</label>
                                <div class="input-group">
                                    <input type="date" 
                                           class="form-control cp-input bg-light" 
                                           name="cvaccess_enddate" 
                                           id="cvaccess_enddate" 
                                           placeholder="Auto Calculated" 
                                           readonly 
                                           required />
                                </div>
                                <span class="text-muted fs-8 mt-1 d-block" id="duration_hint">Calculated automatically</span>
                            </div>

                            {{-- Package Status --}}
                            <div class="col-md-3">
                                <label class="cp-label" for="packages_status">Package Status</label>
                                <select name="packages_status" id="packages_status" class="form-select cp-select">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            {{-- Payment Status --}}
                            <div class="col-md-3">
                                <label class="cp-label" for="payment_status">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-select cp-select">
                                    <option value="1" selected>Paid</option>
                                    <option value="0">Pending</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUB-CARD 2: Dynamic Feature & Coin Deduction Matrix --}}
                <div class="cp-card">
                    <div class="cp-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-warning fs-5"><i class="fa-solid fa-coins text-warning"></i></span>
                            <h4 class="fw-bold text-gray-900 m-0" style="font-size: 14.5px;">2. Feature &amp; Coin Deduction Rules</h4>
                        </div>
                        <span class="badge badge-light-warning fw-semibold px-2 py-1" style="font-size: 11px;">Step 2 of 3</span>
                    </div>

                    <div class="cp-card-body p-4">
                        <div id="packagefeatures">
                            {{-- Placeholder before package is chosen --}}
                            <div class="text-center py-8 px-4 border border-dashed border-gray-300 rounded-2 bg-light">
                                <div class="symbol symbol-45px symbol-circle bg-light-primary mb-3">
                                    <span class="symbol-label">
                                        <i class="fa-solid fa-cube text-primary fs-3"></i>
                                    </span>
                                </div>
                                <h5 class="fw-bold text-gray-800 fs-6 mb-1">No Package Selected</h5>
                                <p class="text-muted fs-7 mb-0">Please select a package in Step 1 to load feature deduction rules and pricing.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SUB-CARD 3: Payment Method & Execution --}}
                <div class="cp-card">
                    <div class="cp-card-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-success fs-5"><i class="fa-solid fa-credit-card text-success"></i></span>
                            <h4 class="fw-bold text-gray-900 m-0" style="font-size: 14.5px;">3. Payment Method &amp; Transaction Details</h4>
                        </div>
                        <span class="badge badge-light-success fw-semibold px-2 py-1" style="font-size: 11px;">Step 3 of 3</span>
                    </div>

                    <div class="cp-card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="cp-label required" for="payment-mode-selector">Payment Mode</label>
                                <select id="payment-mode-selector" name="paymentmodeselector" class="form-select cp-select" required>
                                    <option value="">-- Select Payment Mode --</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="Payment Link">Payment Link (Razorpay)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="cp-label" for="payment-method-selector">Payment Gateway / Channel</label>
                                <select id="payment-method-selector" name="paymentmethodselector" class="form-select cp-select">
                                    <option value="offline">Offline Processing</option>
                                    <option value="online">Online Processing</option>
                                </select>
                            </div>
                        </div>

                        {{-- Cash Section --}}
                        <div id="cash" class="payment-method-box">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-money-bill-wave text-success fs-2 me-3"></i>
                                <div>
                                    <div class="fw-bold text-gray-800 fs-6">Cash Payment</div>
                                    <div class="text-muted fs-7">Please collect the verified cash amount directly from the client representative. An offline receipt will be generated.</div>
                                </div>
                            </div>
                        </div>

                        {{-- Cheque Section --}}
                        <div id="cheque" class="payment-method-box">
                            <div class="mb-3">
                                <div class="fw-bold text-gray-800 fs-6 mb-1">Cheque Details</div>
                                <div class="text-muted fs-7">Please enter the cheque instruments received for this transaction.</div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="cp-label required" for="cheque-number">Cheque Number</label>
                                    <input type="text" class="form-control cp-input" id="cheque-number" name="chequenumber" placeholder="e.g. 000124">
                                </div>
                                <div class="col-md-4">
                                    <label class="cp-label required" for="cheque-date">Cheque Date</label>
                                    <input type="date" class="form-control cp-input" id="cheque-date" name="chequedate">
                                </div>
                                <div class="col-md-4">
                                    <label class="cp-label required" for="bank-name">Drawee Bank Name</label>
                                    <input type="text" class="form-control cp-input" id="bank-name" name="bankname" placeholder="e.g. HDFC Bank">
                                </div>
                            </div>
                        </div>

                        {{-- Bank Transfer Section --}}
                        <div id="bank_transfer" class="payment-method-box">
                            <div class="mb-3">
                                <div class="fw-bold text-gray-800 fs-6 mb-1">NEFT / RTGS / IMPS Bank Transfer Details</div>
                                <div class="text-muted fs-7">Please provide the confirmed bank reference and transaction identifier.</div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="cp-label required" for="transaction-id">Transaction ID / UTR</label>
                                    <input type="text" class="form-control cp-input" id="transaction-id" name="transactionid" placeholder="e.g. CMS2948291039">
                                </div>
                                <div class="col-md-6">
                                    <label class="cp-label required" for="bank-reference">Bank Reference Number</label>
                                    <input type="text" class="form-control cp-input" id="bank-reference" name="bankreference" placeholder="e.g. REF-83921">
                                </div>
                            </div>
                        </div>

                        {{-- Payment Link Section --}}
                        <div id="online" class="payment-method-box">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                                <div>
                                    <div class="fw-bold text-gray-800 fs-6">Generate Instant Razorpay Payment Link</div>
                                    <div class="text-muted fs-7">Generate a secure payment gateway link to share with the client via SMS / Email.</div>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-sm btn-primary fw-semibold paymentLinkGenerate" onclick="paymentLinkGenerate()">
                                        <i class="fa-solid fa-link me-1"></i> Generate Payment Link
                                    </button>
                                </div>
                            </div>

                            <div class="paymentLinkshow" style="display: none;">
                                <label class="cp-label" for="paymentLinkshow">Generated Payment Link</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-gray-300">
                                        <i class="fa-solid fa-lock text-success fs-7"></i>
                                    </span>
                                    <input type="text" class="form-control cp-input font-monospace bg-light" id="paymentLinkshow" readonly />
                                    <button type="button" class="btn btn-light-primary fw-semibold" onclick="copyGeneratedLink()">
                                        <i class="fa-solid fa-copy me-1"></i> Copy
                                    </button>
                                </div>
                                <span class="text-success fs-8 mt-1 d-block"><i class="fa-solid fa-check-circle me-1"></i> Payment link is active. An automatic notification will be emailed upon invoice submission.</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: Pricing Summary & Billing Address (4 Cols) --}}
            <div class="col-lg-4 col-xl-4">
                <div class="cp-sticky-summary">

                    {{-- SUB-CARD 4: Pricing & Invoice Summary --}}
                    <div class="cp-card mb-5">
                        <div class="cp-card-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-primary fs-5"><i class="fa-solid fa-receipt text-primary"></i></span>
                                <h4 class="fw-bold text-gray-900 m-0" style="font-size: 14.5px;">Order Summary</h4>
                            </div>
                            <span class="badge badge-light-primary fw-bold" id="badge_currency">INR</span>
                        </div>

                        <div class="cp-card-body">
                            {{-- Total Coins --}}
                            <div class="cp-summary-row pb-3 border-bottom border-gray-200">
                                <span class="fw-semibold text-gray-700">Total Coins Included</span>
                                <span class="badge badge-primary fw-bold px-3 py-1" style="font-size: 12px;" id="summary_total_coins">
                                    <i class="fa-solid fa-coins me-1 text-white"></i> 0 Coins
                                </span>
                            </div>

                            {{-- Base Price --}}
                            <div class="cp-summary-row mt-2">
                                <span class="text-gray-600">Base Price</span>
                                <span class="fw-semibold text-gray-900" id="summary_base_price">&#8377; 0.00</span>
                            </div>

                            {{-- Discount Input --}}
                            <div class="cp-summary-row">
                                <span class="text-gray-600">Discount (%)</span>
                                <div class="input-group input-group-sm w-90px">
                                    <input type="number" 
                                           step="0.01" 
                                           min="0" 
                                           max="100" 
                                           class="form-control form-control-solid form-control-sm text-end fw-semibold" 
                                           id="discount_display" 
                                           value="0" 
                                           style="height: 30px;">
                                    <span class="input-group-text bg-light py-0 px-2">%</span>
                                </div>
                            </div>

                            {{-- Discount Amount --}}
                            <div class="cp-summary-row">
                                <span class="text-gray-600">Discount Amount</span>
                                <span class="fw-semibold text-danger" id="summary_discount_amount">- &#8377; 0.00</span>
                            </div>

                            {{-- GST Tax --}}
                            <div class="cp-summary-row">
                                <span class="text-gray-600" id="summary_gst_label">GST (18%)</span>
                                <span class="fw-semibold text-gray-900" id="summary_gst_amount">+ &#8377; 0.00</span>
                            </div>

                            {{-- Grand Total --}}
                            <div class="cp-summary-total">
                                <div class="d-flex justify-content-between align-items-baseline mb-4">
                                    <div>
                                        <div class="fw-bold text-gray-900" style="font-size: 14px;">Total Payable</div>
                                        <div class="text-muted fs-8">Inclusive of applicable taxes</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bolder text-primary" style="font-size: 22px;" id="summary_total_price">
                                            &#8377; 0.00
                                        </div>
                                    </div>
                                </div>

                                {{-- Submit Button --}}
                                <button type="button" name="save" id="save-form" class="btn btn-primary w-100 py-3 fw-bold fs-6 shadow-sm">
                                    <span class="indicator-label">
                                        <i class="fa-solid fa-file-invoice-dollar me-2"></i> Create Invoice &amp; Assign
                                    </span>
                                    <span class="indicator-progress">
                                        Processing Order...
                                        <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- SUB-CARD 5: Client & Billing Information --}}
                    <div class="cp-card">
                        <div class="cp-card-header d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-info fs-5"><i class="fa-solid fa-address-card text-info"></i></span>
                                <h4 class="fw-bold text-gray-900 m-0" style="font-size: 14.5px;">Billing Details</h4>
                            </div>
                            <button type="button" class="btn btn-sm btn-light-primary fw-semibold py-1 px-3 show-billing-address">
                                <i class="fa-solid fa-pen-to-square me-1"></i> Edit
                            </button>
                        </div>

                        <div class="cp-card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="symbol symbol-35px symbol-circle bg-light-primary me-3">
                                    <span class="symbol-label text-primary fw-bold">
                                        {{ strtoupper(substr(@$company_name ?? 'C', 0, 1)) }}
                                    </span>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-gray-900 fs-6" id="preview_company_name">{{ @$company_name }}</span>
                                    <span class="text-muted fs-8" id="usernamebill">{{ @$user_name }}</span>
                                </div>
                            </div>

                            <div class="border-top border-gray-100 pt-3">
                                <div class="d-flex align-items-start mb-2">
                                    <i class="fa-solid fa-location-dot text-gray-400 fs-7 mt-1 me-2"></i>
                                    <span class="text-gray-700 fs-7" id="billing_address">
                                        {{ @$locArr ?: 'No address specified' }}
                                    </span>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mt-3 pt-2 border-top border-gray-100 flex-wrap gap-2">
                                    <div>
                                        <span class="text-muted fs-8 d-block">GSTIN / TAX</span>
                                        <span class="fw-semibold text-gray-800 fs-7" id="billing_gst">
                                            {{ @$gstin ?: '—' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-muted fs-8 d-block">PAN No.</span>
                                        <span class="fw-semibold text-gray-800 fs-7" id="billing_pan">
                                            {{ @$pan_no ?: '—' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>

{{-- Offcanvas Drawer: Edit Billing Address --}}
<div id="kt_billing_address_toggle" class="d-none"></div>
<div id="kt_activitiesApplication" 
     class="bg-body" 
     data-kt-drawer="true" 
     data-kt-drawer-name="activitiesApplication"
     data-kt-drawer-activate="true" 
     data-kt-drawer-overlay="true" 
     data-kt-drawer-width="{default:'300px', 'md': '550px', 'lg': '620px'}" 
     data-kt-drawer-direction="end"
     data-kt-drawer-toggle="#kt_billing_address_toggle" 
     data-kt-drawer-close="#kt_activitiesApplication_close">
    
    <div class="d-flex flex-column h-100">
        {{-- Drawer Header --}}
        <div class="cp-drawer-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <span class="symbol symbol-30px symbol-circle bg-light-primary me-2">
                    <span class="symbol-label"><i class="fa-solid fa-address-book text-primary fs-6"></i></span>
                </span>
                <h4 class="fw-bold text-gray-900 m-0" style="font-size: 15px;">Update Billing Information</h4>
            </div>
            <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" id="kt_activitiesApplication_close">
                <i class="fa-solid fa-xmark fs-5 text-gray-500"></i>
            </button>
        </div>

        {{-- Drawer Form Body --}}
        <div class="cp-drawer-body flex-grow-1">
            <form id="show-billing-form">
                @csrf
                <input type="hidden" id="drawer_company_id" name="company_id" value="{{ @$company_id }}">

                <div class="row g-4">
                    {{-- Country --}}
                    <div class="col-12">
                        <label class="cp-label required" for="country">Country / Region</label>
                        <select class="form-select cp-select" id="country" name="country" required>
                            @if ($countries)
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" {{ $countrie_id == $country->id ? 'selected' : '' }}>
                                        {{ $country->country_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- First & Last Name --}}
                    <div class="col-md-6">
                        <label class="cp-label required" for="first_name">First Name</label>
                        <input class="form-control cp-input" type="text" id="first_name" name="first_name" value="{{ $firstName }}" placeholder="First Name" required>
                    </div>
                    <div class="col-md-6">
                        <label class="cp-label" for="last_name">Last Name</label>
                        <input class="form-control cp-input" type="text" id="last_name" name="last_name" value="{{ $lastName }}" placeholder="Last Name">
                    </div>

                    {{-- Phone Number --}}
                    <div class="col-12">
                        <label class="cp-label required" for="phone">Phone Number</label>
                        <input class="form-control cp-input" type="tel" id="phone" name="phone" value="{{ $phone }}" placeholder="Contact Phone" required>
                    </div>

                    {{-- Building & Street --}}
                    <div class="col-12">
                        <label class="cp-label" for="building">Building / Suite / Office</label>
                        <input class="form-control cp-input" type="text" id="building" name="building" value="{{ $building }}" placeholder="e.g. Tower B, 4th Floor">
                    </div>
                    <div class="col-12">
                        <label class="cp-label required" for="street">Street Address / Landmark</label>
                        <input class="form-control cp-input" type="text" id="street" name="street" value="{{ @$locArr }}" placeholder="Street Address" required>
                    </div>

                    {{-- State & City --}}
                    <div class="col-md-6">
                        <label class="cp-label required" for="state">State / Province</label>
                        <select class="form-select cp-select" id="state" name="state_id" required>
                            <option value="">Select State</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="cp-label required" for="city">City</label>
                        <select class="form-select cp-select" id="city" name="city" required>
                            <option value="">Select City</option>
                        </select>
                    </div>

                    {{-- Postal Code & Organization --}}
                    <div class="col-md-6">
                        <label class="cp-label required" for="postal_code">Postal / PIN Code</label>
                        <input class="form-control cp-input" type="text" id="postal_code" name="postal_code" value="{{ $postal_code }}" placeholder="Postal Code" required>
                    </div>
                    <div class="col-md-6">
                        <label class="cp-label" for="organization">Organization Name</label>
                        <input class="form-control cp-input bg-light" type="text" id="organization" name="organization" value="{{ @$company_name }}" readonly>
                    </div>

                    {{-- GSTIN & PAN --}}
                    <div class="col-md-6">
                        <label class="cp-label" for="gstin" id="gstin_text">GSTIN</label>
                        <input class="form-control cp-input" type="text" id="gstin" name="gstin" value="{{ $gstin }}" placeholder="GSTIN (if applicable)">
                    </div>
                    <div class="col-md-6">
                        <label class="cp-label" for="pan_no" id="pan_no_text">PAN Number</label>
                        <input class="form-control cp-input" type="text" id="pan_no" name="pan_no" value="{{ $pan_no }}" placeholder="PAN (if applicable)">
                    </div>
                </div>

                {{-- Hidden button for form submit trigger --}}
                <button type="submit" id="drawer_submit_hidden" class="d-none"></button>
            </form>
        </div>

        {{-- Drawer Footer --}}
        <div class="cp-drawer-footer d-flex align-items-center justify-content-end gap-3">
            <button type="button" class="btn btn-sm btn-light fw-semibold" id="drawer_cancel_btn" onclick="$('#kt_activitiesApplication_close').click();">
                Cancel
            </button>
            <button type="button" id="submitbtn" class="btn btn-sm btn-primary fw-semibold px-5" onclick="$('#drawer_submit_hidden').click();">
                <span class="indicator-label"><i class="fa-solid fa-save me-1"></i> Save Changes</span>
                <span class="indicator-progress">
                    Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
    // Initial server-side values for location chaining
    var initialCountryId = "{{ $countrie_id }}";
    var initialStateId   = "{{ $state_id }}";
    var initialCityId    = "{{ $city_id }}";

    $(document).ready(function() {
        // Initialize State & City cascading dropdowns
        if (initialCountryId) {
            $('#country').val(initialCountryId).trigger('change');
        }

        // Initialize End date calculation if start date or package is present
        calculateEndDate();

        // Check for pre-selected package
        if ($('#packages_id').val()) {
            loadPackageFeatures();
        }
    });

    // Cascading Country -> State
    $('#country').on('change', function() {
        const country = $(this).val();
        const stateDropdown = $('#state');
        const cityDropdown = $('#city');

        if (country == 101) {
            $('#gstin_text').text('GSTIN');
        } else {
            $('#gstin_text').text('TAXIN / VAT ID');
        }

        stateDropdown.empty().append('<option value="">Loading States...</option>').prop('disabled', true);
        cityDropdown.empty().append('<option value="">Select City</option>').prop('disabled', true);

        if (country) {
            $.ajax({
                url: '{{ route("get.states") }}',
                method: 'GET',
                data: { country: country },
                success: function(response) {
                    stateDropdown.empty().append('<option value="">Select State</option>');
                    if (response && response.length) {
                        response.forEach(function(state) {
                            stateDropdown.append(`<option value="${state.id}">${state.name}</option>`);
                        });
                        stateDropdown.prop('disabled', false);
                        if (initialStateId) {
                            stateDropdown.val(initialStateId).trigger('change');
                            initialStateId = null; // Clear after initial load
                        }
                    }
                },
                error: function() {
                    stateDropdown.empty().append('<option value="">Failed to load states</option>');
                }
            });
        }
    });

    // Cascading State -> City
    $('#state').on('change', function() {
        const state = $(this).val();
        const cityDropdown = $('#city');

        cityDropdown.empty().append('<option value="">Loading Cities...</option>').prop('disabled', true);

        if (state) {
            $.ajax({
                url: '{{ route("get.cities") }}',
                method: 'GET',
                data: { state: state },
                success: function(response) {
                    cityDropdown.empty().append('<option value="">Select City</option>');
                    if (response && response.length) {
                        response.forEach(function(city) {
                            cityDropdown.append(`<option value="${city.id}">${city.name}</option>`);
                        });
                        cityDropdown.prop('disabled', false);
                        if (initialCityId) {
                            cityDropdown.val(initialCityId);
                            initialCityId = null; // Clear after initial load
                        }
                    }
                },
                error: function() {
                    cityDropdown.empty().append('<option value="">Failed to load cities</option>');
                }
            });
        }
    });

    // Drawer Open Trigger
    $(document).on('click', '.show-billing-address', function() {
        $('#kt_billing_address_toggle').trigger('click');
    });

    // AJAX Billing Form Submission with SweetAlert2 & Immediate UI Update
    $(document).on('submit', '#show-billing-form', function(e) {
        e.preventDefault();

        var submitBtn = $('#submitbtn');
        submitBtn.attr('data-kt-indicator', 'on').prop('disabled', true);

        var formData = $(this).serialize();

        $.ajax({
            url: '{{ route("form.submit") }}',
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);

                Swal.fire({
                    title: 'Billing Details Updated',
                    text: 'The billing profile has been saved successfully.',
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'Great!',
                    customClass: { confirmButton: 'btn btn-primary' }
                }).then(function() {
                    if (response.usernamebill) {
                        $('#usernamebill').text(response.usernamebill);
                    }
                    if (response.street) {
                        $('#billing_address').text(response.street);
                    }
                    if (response.billing_gst) {
                        $('#billing_gst').text(response.billing_gst);
                    }
                    if (response.billing_pan) {
                        $('#billing_pan').text(response.billing_pan);
                    }
                    $('#kt_activitiesApplication_close').click();
                });
            },
            error: function(xhr) {
                submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);
                Swal.fire({
                    title: 'Update Failed',
                    text: 'Unable to save billing details. Please check the required fields.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-danger' }
                });
            }
        });
    });

    // Package Change & Currency / GST Change Handler
    $('#packages_id, #currency_get, #gst_on_get').on('change', function() {
        loadPackageFeatures();
        calculateEndDate();
    });

    $('#cvaccess_startdate').on('change', function() {
        calculateEndDate();
    });

    // Calculate End Date via Backend API
    function calculateEndDate() {
        var cvaccess_startdate = $('#cvaccess_startdate').val();
        var package_id = $('#packages_id').val();
        var currency_get = $('#currency_get').val();

        if (!package_id || !cvaccess_startdate) return;

        $.ajax({
            url: '{{ route("superadmin.custompackages.lastdate") }}',
            type: "GET",
            data: {
                package_id: package_id,
                cvaccess_startdate: cvaccess_startdate,
                currency_get: currency_get
            },
            success: function(response) {
                if (response.newDate) {
                    $('#cvaccess_enddate').val(response.newDate);
                    var days = Math.round((new Date(response.newDate) - new Date(cvaccess_startdate)) / (1000 * 60 * 60 * 24));
                    $('#duration_hint').text('Validity: ' + days + ' Days (Expires ' + response.newDate + ')');
                }
                if (response.check) {
                    $('#cvaccess_enddate').removeAttr('readonly').removeClass('bg-light');
                } else {
                    $('#cvaccess_enddate').attr('readonly', 'readonly').addClass('bg-light');
                }
            }
        });
    }

    // Load Package Features via AJAX
    function loadPackageFeatures() {
        var package_id = $('#packages_id').val();
        var currency_get = $('#currency_get').val();
        var gst_on_get = $('#gst_on_get').val();

        // Update Currency Badge in Order Summary
        $('#badge_currency').text(currency_get);

        if (!package_id) {
            $('#packagefeatures').html(`
                <div class="text-center py-8 px-4 border border-dashed border-gray-300 rounded-2 bg-light">
                    <div class="symbol symbol-45px symbol-circle bg-light-primary mb-3">
                        <span class="symbol-label"><i class="fa-solid fa-cube text-primary fs-3"></i></span>
                    </div>
                    <h5 class="fw-bold text-gray-800 fs-6 mb-1">No Package Selected</h5>
                    <p class="text-muted fs-7 mb-0">Please select a package in Step 1 to load feature deduction rules and pricing.</p>
                </div>
            `);
            resetSummary();
            return;
        }

        $('#packagefeatures').html(`
            <div class="text-center py-8">
                <span class="spinner-border text-primary"></span>
                <div class="text-muted fs-7 mt-2">Loading package features and calculation rules...</div>
            </div>
        `);

        $.ajax({
            url: '{{ route("superadmin.custompackages.packagefeature") }}',
            type: "GET",
            data: {
                package_id: package_id,
                currency_get: currency_get,
                gst_on_get: gst_on_get
            },
            success: function(response) {
                if (response.status === 'success') {
                    $('#packagefeatures').html(response.view);

                    // Update values
                    var basePrice = parseFloat(response.price || 0);
                    var discountPct = parseFloat(response.discount || 0);
                    var coins = parseInt(response.coins || 0);

                    $('#price').val(basePrice.toFixed(2));
                    $('#total_coins').val(coins);
                    $('#discount').val(discountPct);
                    $('#discount_display').val(discountPct);

                    // Recalculate totals
                    recalculateTotals();
                } else {
                    $('#packagefeatures').html(`
                        <div class="alert alert-danger d-flex align-items-center p-4">
                            <i class="fa-solid fa-triangle-exclamation fs-2 text-danger me-3"></i>
                            <div>${response.message || 'Error loading package details.'}</div>
                        </div>
                    `);
                }
            },
            error: function() {
                $('#packagefeatures').html(`
                    <div class="alert alert-danger d-flex align-items-center p-4">
                        <i class="fa-solid fa-triangle-exclamation fs-2 text-danger me-3"></i>
                        <div>Failed to communicate with package service. Please try again.</div>
                    </div>
                `);
            }
        });
    }

    // Live Discount Input Listener
    $(document).on('keyup change', '#discount_display', function() {
        var discountVal = parseFloat($(this).val()) || 0;
        if (discountVal < 0) discountVal = 0;
        if (discountVal > 100) discountVal = 100;
        $('#discount').val(discountVal);
        recalculateTotals();
    });

    // Master Client-side Calculation Engine
    function recalculateTotals() {
        var currency = $('#currency_get').val() || 'INR';
        var gstApplicable = $('#gst_on_get').val() || 'Yes';
        var currencySymbol = (currency === 'INR') ? '&#8377; ' : '$ ';

        var basePrice = parseFloat($('#price').val()) || 0;
        var discountPct = parseFloat($('#discount').val()) || 0;
        var totalCoins = parseInt($('#total_coins').val()) || 0;

        // Calculate Discount
        var discountAmount = (basePrice * discountPct) / 100;
        var discountedPrice = basePrice - discountAmount;
        if (discountedPrice < 0) discountedPrice = 0;

        // Calculate GST (18% for INR if applicable)
        var gstRate = (currency === 'INR' && gstApplicable === 'Yes') ? 18 : 0;
        var gstAmount = (discountedPrice * gstRate) / 100;
        var grandTotal = discountedPrice + gstAmount;

        // Update hidden inputs for backend submission
        $('#gst').val(gstRate);
        $('#gst_amount').val(gstAmount.toFixed(2));
        $('#total_price').val(grandTotal.toFixed(2));

        // Update UI Summary Displays
        $('#summary_total_coins').html('<i class="fa-solid fa-coins me-1 text-white"></i> ' + totalCoins.toLocaleString() + ' Coins');
        $('#summary_base_price').html(currencySymbol + basePrice.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#summary_discount_amount').html('- ' + currencySymbol + discountAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        
        if (currency === 'INR') {
            $('#summary_gst_label').text('GST (' + gstRate + '%)');
        } else {
            $('#summary_gst_label').text('Tax (0%)');
        }
        $('#summary_gst_amount').html('+ ' + currencySymbol + gstAmount.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
        $('#summary_total_price').html(currencySymbol + grandTotal.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2}));
    }

    function resetSummary() {
        var currency = $('#currency_get').val() || 'INR';
        var currencySymbol = (currency === 'INR') ? '&#8377; ' : '$ ';
        $('#price').val('0.00');
        $('#total_coins').val('0');
        $('#discount').val('0');
        $('#gst').val('0');
        $('#gst_amount').val('0.00');
        $('#total_price').val('0.00');

        $('#summary_total_coins').html('<i class="fa-solid fa-coins me-1 text-white"></i> 0 Coins');
        $('#summary_base_price').html(currencySymbol + '0.00');
        $('#summary_discount_amount').html('- ' + currencySymbol + '0.00');
        $('#summary_gst_amount').html('+ ' + currencySymbol + '0.00');
        $('#summary_total_price').html(currencySymbol + '0.00');
    }

    // Payment Mode Selector Handling
    $('#payment-mode-selector').on('change', function() {
        var mode = $(this).val();
        $('.payment-method-box').removeClass('active');

        // Reset payment link hidden fields
        $('#paymentLinkPaymentstatus').val('');
        $('#paymentLinkpayment_orderId').val('');
        $('#paymentLinkshort_url').val('');
        $('.paymentLinkshow').hide();

        var methodSelector = $('#payment-method-selector');
        methodSelector.empty();

        if (mode === 'Cash') {
            $('#cash').addClass('active');
            methodSelector.append('<option value="offline" selected>Offline Handover</option>');
        } else if (mode === 'Cheque') {
            $('#cheque').addClass('active');
            methodSelector.append('<option value="offline" selected>Cheque Clearing</option>');
        } else if (mode === 'Bank Transfer') {
            $('#bank_transfer').addClass('active');
            methodSelector.append('<option value="offline" selected>NEFT / RTGS Transfer</option>');
        } else if (mode === 'Payment Link') {
            $('#online').addClass('active');
            methodSelector.append('<option value="razorpay" selected>Razorpay Gateway</option>');
        } else {
            methodSelector.append('<option value="offline">Offline Processing</option>');
        }
    });

    // Generate Razorpay Payment Link
    function paymentLinkGenerate() {
        var totalPrice = $('#total_price').val();
        var currency = $('#currency_get').val();
        var companyId = $('#company_id').val();
        var packageId = $('#packages_id').val();

        if (!packageId) {
            Swal.fire({
                title: 'Package Required',
                text: 'Please select a package before generating a payment link.',
                icon: 'warning',
                buttonsStyling: false,
                confirmButtonText: 'Got It',
                customClass: { confirmButton: 'btn btn-primary' }
            });
            return;
        }

        if (parseFloat(totalPrice) <= 0) {
            Swal.fire({
                title: 'Invalid Amount',
                text: 'Total amount must be greater than zero to generate a payment link.',
                icon: 'warning',
                buttonsStyling: false,
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' }
            });
            return;
        }

        var btn = $('.paymentLinkGenerate');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Generating...');

        $.ajax({
            url: '{{ route("superadmin.custompackages.generatecustompackages", @$company_id) }}',
            type: "GET",
            data: {
                total_price: totalPrice,
                currency_get: currency,
                company_id: companyId,
                packages_id: packageId,
                mainType: 1
            },
            success: function(response) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-link me-1"></i> Generate Payment Link');

                if (response.status === 'success' && response.short_url) {
                    $('#paymentLinkshow').val(response.short_url);
                    $('#paymentLinkshort_url').val(response.short_url);
                    $('#paymentLinkPaymentstatus').val(response.Paymentstatus || 'created');
                    $('#paymentLinkpayment_orderId').val(response.payment_orderId || '');
                    $('.paymentLinkshow').slideDown(200);

                    Swal.fire({
                        title: 'Payment Link Generated!',
                        text: 'Link: ' + response.short_url,
                        icon: 'success',
                        buttonsStyling: false,
                        confirmButtonText: 'Copy Link',
                        showCancelButton: true,
                        cancelButtonText: 'Done',
                        customClass: {
                            confirmButton: 'btn btn-primary',
                            cancelButton: 'btn btn-light'
                        }
                    }).then(function(result) {
                        if (result.isConfirmed) {
                            copyGeneratedLink();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Gateway Error',
                        text: response.message || 'Unable to generate payment link via Razorpay.',
                        icon: 'error',
                        buttonsStyling: false,
                        confirmButtonText: 'Close',
                        customClass: { confirmButton: 'btn btn-danger' }
                    });
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i class="fa-solid fa-link me-1"></i> Generate Payment Link');
                var err = xhr.responseJSON ? xhr.responseJSON.message : 'Failed to communicate with payment gateway.';
                Swal.fire({
                    title: 'Gateway Error',
                    text: err,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Close',
                    customClass: { confirmButton: 'btn btn-danger' }
                });
            }
        });
    }

    function copyGeneratedLink() {
        var copyText = document.getElementById("paymentLinkshow");
        if (!copyText || !copyText.value) return;
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value).then(function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Payment link copied to clipboard',
                showConfirmButton: false,
                timer: 2000
            });
        });
    }

    function paymentLinkcopyText() {
        var copyText = document.getElementById("paymentLinktextToCopy");
        if (!copyText) return;
        navigator.clipboard.writeText(copyText.innerText).then(function() {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Existing link copied to clipboard',
                showConfirmButton: false,
                timer: 2000
            });
        });
    }

    // Comprehensive Client-side Form Validation & Submission
    $('#save-form').on('click', function(e) {
        e.preventDefault();

        // 1. Validate Package Selected
        var packageId = $('#packages_id').val();
        if (!packageId) {
            Swal.fire({
                title: 'Package Required',
                text: 'Please select a package from Step 1 before creating the invoice.',
                icon: 'warning',
                buttonsStyling: false,
                confirmButtonText: 'Select Package',
                customClass: { confirmButton: 'btn btn-primary' }
            }).then(function() {
                $('html, body').animate({ scrollTop: $('#packages_id').offset().top - 100 }, 300);
                $('#packages_id').focus();
            });
            return;
        }

        // 2. Validate Start & End Dates
        var startDate = $('#cvaccess_startdate').val();
        var endDate = $('#cvaccess_enddate').val();
        if (!startDate) {
            Swal.fire({
                title: 'Start Date Required',
                text: 'Please provide a valid package start date.',
                icon: 'warning',
                buttonsStyling: false,
                confirmButtonText: 'OK',
                customClass: { confirmButton: 'btn btn-primary' }
            });
            return;
        }

        if (endDate && new Date(endDate) < new Date(startDate)) {
            Swal.fire({
                title: 'Invalid Date Range',
                text: 'Package end date cannot be earlier than the start date.',
                icon: 'error',
                buttonsStyling: false,
                confirmButtonText: 'Correct Date',
                customClass: { confirmButton: 'btn btn-danger' }
            });
            return;
        }

        // 3. Validate Payment Mode
        var paymentMode = $('#payment-mode-selector').val();
        if (!paymentMode) {
            Swal.fire({
                title: 'Payment Mode Required',
                text: 'Please select a payment mode in Step 3.',
                icon: 'warning',
                buttonsStyling: false,
                confirmButtonText: 'Select Mode',
                customClass: { confirmButton: 'btn btn-primary' }
            }).then(function() {
                $('html, body').animate({ scrollTop: $('#payment-mode-selector').offset().top - 100 }, 300);
                $('#payment-mode-selector').focus();
            });
            return;
        }

        // Mode-specific validation
        if (paymentMode === 'Cheque') {
            if (!$('#cheque-number').val() || !$('#cheque-date').val() || !$('#bank-name').val()) {
                Swal.fire({
                    title: 'Cheque Details Incomplete',
                    text: 'Please provide Cheque Number, Cheque Date, and Drawee Bank Name.',
                    icon: 'warning',
                    buttonsStyling: false,
                    confirmButtonText: 'Fill In Details',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }
        } else if (paymentMode === 'Bank Transfer') {
            if (!$('#transaction-id').val() || !$('#bank-reference').val()) {
                Swal.fire({
                    title: 'Transfer Details Incomplete',
                    text: 'Please provide the Transaction ID (UTR) and Bank Reference Number.',
                    icon: 'warning',
                    buttonsStyling: false,
                    confirmButtonText: 'Fill In Details',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }
        } else if (paymentMode === 'Payment Link') {
            if (!$('#paymentLinkshort_url').val()) {
                Swal.fire({
                    title: 'Payment Link Required',
                    text: 'You selected Payment Link. Please click "Generate Payment Link" or select another payment mode.',
                    icon: 'warning',
                    buttonsStyling: false,
                    confirmButtonText: 'Got It',
                    customClass: { confirmButton: 'btn btn-primary' }
                });
                return;
            }
        }

        // Confirmation Dialog
        Swal.fire({
            title: 'Confirm Invoice Creation',
            text: 'Are you sure you want to create this custom package invoice for ' + $('#preview_company_name').text() + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Create Invoice',
            cancelButtonText: 'Review Details',
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-primary me-3',
                cancelButton: 'btn btn-light'
            }
        }).then(function(result) {
            if (result.isConfirmed) {
                executeFormSubmit();
            }
        });
    });

    function executeFormSubmit() {
        var submitBtn = $('#save-form');
        submitBtn.attr('data-kt-indicator', 'on').prop('disabled', true);

        var formData = new FormData($('#createForm')[0]);

        $.ajax({
            url: '{{ route("superadmin.custompackages.createInvoice", @$company_id) }}',
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);

                if (response.status === 'offline' || response.status === 'success') {
                    Swal.fire({
                        title: 'Invoice Created Successfully!',
                        text: response.message || 'Custom package has been assigned and the invoice is created.',
                        icon: 'success',
                        buttonsStyling: false,
                        confirmButtonText: 'Go to Companies',
                        customClass: { confirmButton: 'btn btn-primary' }
                    }).then(function() {
                        window.location.href = response.url || '{{ url("super-admin/company") }}';
                    });
                } else if (response.status === 'razorpay') {
                    Swal.fire({
                        title: 'Invoice Created & Link Sent!',
                        text: 'Custom package order was recorded and payment link notification has been sent.',
                        icon: 'success',
                        buttonsStyling: false,
                        confirmButtonText: 'Go to Companies',
                        customClass: { confirmButton: 'btn btn-primary' }
                    }).then(function() {
                        window.location.href = response.url || '{{ url("super-admin/company") }}';
                    });
                } else {
                    Swal.fire({
                        title: 'Notice',
                        text: response.message || 'The invoice was created.',
                        icon: 'info',
                        buttonsStyling: false,
                        confirmButtonText: 'OK',
                        customClass: { confirmButton: 'btn btn-primary' }
                    }).then(function() {
                        if (response.url) {
                            window.location.href = response.url;
                        }
                    });
                }
            },
            error: function(xhr) {
                submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);
                var err = xhr.responseJSON ? xhr.responseJSON.message : 'Error processing invoice creation. Please review the form.';
                Swal.fire({
                    title: 'Creation Failed',
                    text: err,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'btn btn-danger' }
                });
            }
        });
    }
</script>
@endpush
