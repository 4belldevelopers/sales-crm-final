@extends('layouts.service_company_layout')

@push('head-script')
    <link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/internal-job-create-design.css') }}?v=20261015_salvis_godlevel_v2">
    <style>
        /* Product Form Scoped Layout Helpers (matching Job Create 1:1) */
        #jrwrap.internal-job-create .content { padding: 0 20px 32px !important; }
        #jrwrap .step-content { display: none; }
        #jrwrap .step-content.active { display: block; }
        #jrwrap .card {
            background: #ffffff;
            border: 1px solid #ebd99f;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(16, 20, 59, 0.04);
            margin-bottom: 16px;
            overflow: hidden;
        }
        #jrwrap .card-body { padding: 0; }
        #jrwrap .ij-card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 20px;
            background: linear-gradient(180deg, #fffdf7 0%, #fff9df 100%);
            border-bottom: 1px solid #ebd99f;
        }
        #jrwrap .ij-card-head-left { display: flex; align-items: center; gap: 12px; }
        #jrwrap .ij-card-head-icon {
            width: 32px; height: 32px; border-radius: 8px; background: #ffe394; color: #9a6500;
            display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;
        }
        #jrwrap .ij-card-head-title { font-size: 13px; font-weight: 600; color: #10143b; line-height: 1.3; }
        #jrwrap .ij-card-head-sub { font-size: 11px; color: #687795; line-height: 1.3; }
        #jrwrap .ij-card-inner { padding: 18px 20px; }

        #jrwrap .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 22px; }
        #jrwrap .form-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px 22px; }
        #jrwrap .form-group-cus { display: flex; flex-direction: column; gap: 5px; }
        #jrwrap .form-group-cus.full-width { grid-column: 1 / -1; }
        #jrwrap .form-label { font-size: 12px; font-weight: 500; color: #10143b; margin: 0; display: flex; align-items: center; gap: 4px; }
        #jrwrap .required::after { content: '*'; color: #ef4444; margin-left: 2px; }
        #jrwrap .form-hint { font-size: 11px; color: #687795; margin-top: 2px; }
        #jrwrap .form-error { font-size: 11px; color: #dc2626; display: none; }
        #jrwrap .form-group-cus.has-error .form-error { display: block; }
        #jrwrap .form-group-cus.has-error .form-input,
        #jrwrap .form-group-cus.has-error .form-select { border-color: #dc2626; background: #fef2f2; }

        #jrwrap .form-input, #jrwrap .form-select, #jrwrap .form-textarea {
            width: 100%; height: 38px; padding: 8px 12px; border: 1px solid #dce2ed; border-radius: 8px;
            font-family: 'Poppins', sans-serif; font-size: 13px; color: #10143b; background: #ffffff; outline: none;
        }
        #jrwrap .form-textarea { height: auto; min-height: 82px; resize: vertical; }
        #jrwrap .form-input:focus, #jrwrap .form-select:focus, #jrwrap .form-textarea:focus {
            border-color: #e6af00; box-shadow: 0 0 0 3px rgba(230, 175, 0, 0.14);
        }

        /* Inline Currency + Price Input (Screenshot 2) */
        #jrwrap .ij-price-group {
            display: flex; align-items: stretch; border: 1px solid #dce2ed; border-radius: 8px; overflow: hidden; background: #fff; height: 38px;
        }
        #jrwrap .ij-price-group:focus-within { border-color: #e6af00; box-shadow: 0 0 0 3px rgba(230, 175, 0, 0.14); }
        #jrwrap .ij-curr-select {
            border: none; border-right: 1px solid #dce2ed; background: #f8fafc; padding: 0 10px; font-size: 12px; font-weight: 600; color: #10143b; outline: none; cursor: pointer;
        }
        #jrwrap .ij-price-input {
            border: none; flex: 1; padding: 0 12px; font-size: 13px; color: #10143b; outline: none; min-width: 0;
        }

        /* Choice Cards with Radio Dot + Icon + Title + Subtitle (Screenshot 3) */
        #jrwrap .ij-choice-grid-4 { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; }
        #jrwrap .ij-choice-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 12px; }
        #jrwrap .ij-choice-grid-5 { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 12px; }
        #jrwrap .ij-choice-card {
            display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1px solid #dce2ed; border-radius: 10px;
            background: #ffffff; cursor: pointer; text-align: left; transition: border-color 0.15s, background-color 0.15s;
        }
        #jrwrap .ij-choice-card:hover { border-color: #e6af00; background: #fffdf7; }
        #jrwrap .ij-choice-card.is-selected { border: 1.5px solid #e6af00; background: #fffdf5; }
        #jrwrap .ij-radio-dot {
            width: 16px; height: 16px; border-radius: 50%; border: 2px solid #cbd5e1; flex-shrink: 0; position: relative;
        }
        #jrwrap .ij-choice-card.is-selected .ij-radio-dot { border-color: #d97706; }
        #jrwrap .ij-choice-card.is-selected .ij-radio-dot::after {
            content: ''; position: absolute; inset: 3px; border-radius: 50%; background: #d97706;
        }
        #jrwrap .ij-choice-ico {
            width: 30px; height: 30px; border-radius: 7px; background: #f8fafc; border: 1px solid #e2e8f0; color: #475569;
            display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0;
        }
        #jrwrap .ij-choice-card.is-selected .ij-choice-ico { background: #fff9df; border-color: #ebd99f; color: #9a6500; }
        #jrwrap .ij-choice-title { font-size: 12.5px; font-weight: 600; color: #10143b; line-height: 1.25; }
        #jrwrap .ij-choice-sub { font-size: 11px; color: #687795; line-height: 1.25; margin-top: 2px; }

        /* Popular Hubs Pill Row (Screenshot 2) */
        #jrwrap .ij-hubs-bar {
            display: flex; flex-wrap: wrap; align-items: center; gap: 8px; padding: 10px 14px;
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 12px;
        }
        #jrwrap .ij-hub-pill {
            padding: 4px 11px; border-radius: 999px; border: 1px solid #dce2ed; background: #fff;
            font-size: 11.5px; font-weight: 500; color: #10143b; cursor: pointer;
        }
        #jrwrap .ij-hub-pill:hover, #jrwrap .ij-hub-pill.is-selected {
            border-color: #2563eb; background: #eff6ff; color: #2563eb;
        }
        #jrwrap .chips-box {
            border: 1px solid #dce2ed; border-radius: 8px; padding: 7px 12px; display: flex; flex-wrap: wrap; gap: 6px; align-items: center; min-height: 40px; background: #fff;
        }
        #jrwrap .chip-item {
            display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 999px;
            background: #fff9df; border: 1px solid #ebd99f; color: #9a6500; font-size: 12px; font-weight: 500;
        }
        #jrwrap .chip-item button { background: none; border: none; color: #9a6500; cursor: pointer; padding: 0; font-size: 13px; }
        #jrwrap .chip-input-bare { border: none; outline: none; flex: 1; min-width: 160px; font-family: 'Poppins', sans-serif; font-size: 13px; }

        /* Rich Editor & Footer Buttons */
        #jrwrap .editor-toolbar { display: flex; gap: 4px; padding: 8px 12px; background: #fffdf7; border: 1px solid #dce2ed; border-bottom: none; border-radius: 8px 8px 0 0; }
        #jrwrap .toolbar-btn { width: 28px; height: 28px; border: none; background: transparent; border-radius: 5px; color: #475569; cursor: pointer; }
        #jrwrap .toolbar-btn:hover { background: #ffe394; color: #9a6500; }
        #jrwrap .rich-editor { min-height: 150px; padding: 12px; border: 1px solid #dce2ed; border-radius: 0 0 8px 8px; background: #fff; outline: none; }
        #jrwrap .rich-editor:empty::before { content: attr(data-placeholder); color: #94a3b8; }

        #jrwrap .form-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 16px; }
        #jrwrap .btn { padding: 9px 18px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; border: 1px solid transparent; }
        #jrwrap .btn-secondary { background: #fff; border-color: #dce2ed; color: #10143b; }
        #jrwrap .btn-primary { background: #9a6500; border-color: #9a6500; color: #fff; }
        #jrwrap .btn-primary:hover { background: #7a5000; }

        #jrwrap .r4-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
        #jrwrap .rv-row { display: flex; justify-content: space-between; gap: 10px; padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
        #jrwrap .rv-label { color: #687795; }
        #jrwrap .rv-val { font-weight: 500; color: #10143b; text-align: right; }

        @media (max-width: 1199px) {
            #jrwrap .ij-choice-grid-5 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            #jrwrap .ij-choice-grid-4 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            #jrwrap .r4-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 768px) {
            #jrwrap .form-grid, #jrwrap .form-grid-3, #jrwrap .ij-choice-grid-3, #jrwrap .ij-choice-grid-4, #jrwrap .ij-choice-grid-5, #jrwrap .r4-grid { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
    @php
        $isEdit = ($page_event ?? 'ADD') === 'EDIT' && isset($offering);
        $currencyList = $currency ?? [];
        $defaultCurrency = $offering->currency_code ?? ($currency_code ?? 'INR');
        $tz = $tenantTimezone ?? 'UTC';
    @endphp

    <div id="jrwrap" class="internal-job-create">
        <div class="content">
            {{-- Edge-to-Edge Sticky Header + 4-Step Stepper --}}
            <div class="ij-header">
                <div class="ij-heading">
                    <div>
                        <h1>{{ $isEdit ? 'Edit Service' : 'Add Service' }}</h1>
                        <p>Enter service details and publish</p>
                    </div>
                </div>

                <div class="stepper-v2" id="stepper">
                    <div class="step-v2 current" data-step="1" onclick="crmGoToStep(1)">
                        <div class="step-v2-icon">1</div>
                        <div class="step-v2-info"><div class="step-v2-label">Basic Details</div></div>
                    </div>
                    <div class="step-v2 pending" data-step="2" onclick="crmGoToStep(2)">
                        <div class="step-v2-icon">2</div>
                        <div class="step-v2-info"><div class="step-v2-label">Service Description</div></div>
                    </div>
                    <div class="step-v2 pending" data-step="3" onclick="crmGoToStep(3)">
                        <div class="step-v2-icon">3</div>
                        <div class="step-v2-info"><div class="step-v2-label">Qualification</div></div>
                    </div>
                    <div class="step-v2 pending" data-step="4" onclick="crmGoToStep(4)">
                        <div class="step-v2-icon">4</div>
                        <div class="step-v2-info"><div class="step-v2-label">Review &amp; Publish</div></div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-secondary" id="btnTopSaveDraft" style="padding:6px 12px; font-size:12px;">
                        <i class="fas fa-save"></i> Save Draft
                    </button>
                    <button type="button" class="btn btn-primary" id="btnTopPublish" style="padding:6px 14px; font-size:12px;">
                        <span>Next Page</span> <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>

            <form id="crmOfferingForm" method="POST" action="javascript:void(0);" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="operation_id" id="operation_id" value="{{ $offering->operation_id ?? \Illuminate\Support\Str::uuid()->toString() }}">

                {{-- ================= STEP 1: BASIC DETAILS ================= --}}
                <div class="step-content active" id="step1">
                    {{-- Card 1: Basic Information (Matches Screenshot 1) --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-box-open"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Basic Information</div>
                                    <div class="ij-card-head-sub">Enter the basic details for this service.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="form-grid">
                                <div class="form-group-cus" data-field="offering_class">
                                    <label class="form-label required" for="offering_class">Select Offering (Service / Product)</label>
                                    <select class="form-select" id="offering_class" name="offering_class" required>
                                        <option value="service" {{ ($offering->offering_class ?? 'service') === 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="product" {{ ($offering->offering_class ?? 'service') === 'product' ? 'selected' : '' }}>Product</option>
                                    </select>
                                    <span class="form-hint">*Choose whether you are adding a Service or a Product.</span>
                                </div>

                                <div class="form-group-cus" data-field="product_url">
                                    <label class="form-label" for="product_url">Service URL</label>
                                    <input type="url" class="form-input" id="product_url" name="product_url" maxlength="500" value="{{ $offering->product_url ?? '' }}" placeholder="https://example.com/service">
                                    <span class="form-hint">Optional public landing page or service brochure URL.</span>
                                    <span class="form-error">Please enter a valid URL (e.g. https://...).</span>
                                </div>

                                <div class="form-group-cus" data-field="name">
                                    <label class="form-label required" for="offering_name">Service Name</label>
                                    <input type="text" class="form-input" id="offering_name" name="name" maxlength="255" value="{{ $offering->name ?? '' }}" placeholder="Enter service name" required>
                                    <span class="form-hint">*Enter the full name of this service.</span>
                                    <span class="form-error">Service Name is required.</span>
                                </div>

                                <div class="form-group-cus" data-field="sku">
                                    <label class="form-label required" for="sku">Service Code / SKU</label>
                                    <input type="text" class="form-input" id="sku" name="sku" value="{{ $offering->sku ?? '' }}" placeholder="Enter service code (e.g. SRV-001)" style="text-transform: uppercase;">
                                    <span class="form-hint">*Unique service code within your company catalog.</span>
                                    <span class="form-error" id="skuError">Service code already exists in your company catalog.</span>
                                </div>

                                <div class="form-group-cus" data-field="crm_category">
                                    <label class="form-label required" for="crm_category">Service Category</label>
                                    <select class="form-select" id="crm_category" name="crm_category" required>
                                        <option value="">Select Category</option>
                                        <optgroup label="Service Categories">
                                            <option value="IT & Cloud">IT &amp; Cloud</option>
                                            <option value="Software / SaaS">Software / SaaS</option>
                                            <option value="AI & Automation">AI &amp; Automation</option>
                                            <option value="Consulting & Advisory">Consulting &amp; Advisory</option>
                                            <option value="Managed Services">Managed Services</option>
                                        </optgroup>
                                        @if(!empty($industries))
                                            <optgroup label="Industries">
                                                @foreach($industries as $ind)
                                                    <option value="{{ $ind['name'] ?? ($ind['title'] ?? '') }}" data-industry-id="{{ $ind['id'] ?? '' }}">
                                                        {{ $ind['name'] ?? ($ind['title'] ?? '') }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                                    <input type="hidden" name="industry_id" id="industry_id" value="">
                                    <span class="form-hint">*Please select the category for this service.</span>
                                    <span class="form-error">Service Category is required.</span>
                                </div>

                                <div class="form-group-cus" data-field="offering_type">
                                    <label class="form-label required" for="offering_type">Service Type</label>
                                    <select class="form-select" id="offering_type" name="offering_type">
                                        <option value="Professional Service" selected>Professional Service</option>
                                        <option value="Consulting">Consulting</option>
                                        <option value="Managed Service">Managed Service</option>
                                        <option value="Staffing / Recruitment Service">Staffing / Recruitment Service</option>
                                        <option value="Software / SaaS">Software / SaaS</option>
                                        <option value="Subscription">Subscription</option>
                                        <option value="Digital Product">Digital Product</option>
                                        <option value="Physical Product">Physical Product</option>
                                    </select>
                                    <span class="form-hint">Select the service or product delivery type.</span>
                                </div>

                                <div class="form-group-cus" data-field="owner_client_id">
                                    <label class="form-label" for="owner_client_id">Organization / Brand Owner</label>
                                    <select class="form-select" id="owner_client_id" name="owner_client_id">
                                        @foreach(($clients ?? collect()) as $cl)
                                            <option value="{{ $cl->id }}" {{ $cl->self_company ? 'selected' : '' }}>
                                                {{ $cl->name }} {{ $cl->self_company ? '(My Organization)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="form-hint">Select your organization or vendor/partner owner.</span>
                                </div>

                                <div class="form-group-cus" data-field="address_id">
                                    <label class="form-label" for="address_id">Warehouse / Origin Address</label>
                                    <select class="form-select" id="address_id" name="address_id">
                                        <option value="">Select Address (Optional for Digital)</option>
                                        @foreach(($clientAddresses ?? collect()) as $addr)
                                            <option value="{{ $addr->id }}"
                                                    data-client-id="{{ $addr->client_id }}"
                                                    data-city="{{ $addr->city }}"
                                                    {{ (int) ($offering->address_id ?? 0) === (int) $addr->id ? 'selected' : '' }}>
                                                {{ trim(($addr->address ?? '') . ($addr->city ? ', ' . $addr->city : '') . ($addr->postcode ? ' - ' . $addr->postcode : ''), ', ') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="form-hint">Filtered automatically by the selected Organization / Brand Owner.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Product Inventory & Status (Matches Screenshot 1 Card 2) --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-cubes"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Inventory &amp; Status</div>
                                    <div class="ij-card-head-sub">Specify stock capacity, unit, and catalog status for this product.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="form-grid-3">
                                <div class="form-group-cus" data-field="unit">
                                    <label class="form-label" for="unit">Unit of Measurement</label>
                                    <select class="form-select" id="unit" name="unit">
                                        <option value="piece">Piece</option>
                                        <option value="box">Box</option>
                                        <option value="pack">Pack</option>
                                        <option value="kg">Kg</option>
                                        <option value="litre">Litre</option>
                                        <option value="license">License / Seat</option>
                                    </select>
                                    <span class="form-hint">Unit used on quotations &amp; orders.</span>
                                </div>

                                <div class="form-group-cus" data-field="available_capacity">
                                    <label class="form-label required" for="available_capacity">Available Quantity / Stock</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="number" min="0" class="form-input" id="available_capacity" name="available_capacity" value="100">
                                        <label class="d-flex align-items-center gap-1 mb-0" style="white-space:nowrap; font-size:12px; cursor:pointer;">
                                            <input type="checkbox" id="is_unlimited_capacity" name="is_unlimited_capacity" value="1"> Unlimited
                                        </label>
                                    </div>
                                    <span class="form-hint">Available stock units (or Unlimited for Digital).</span>
                                </div>

                                <div class="form-group-cus" data-field="status">
                                    <label class="form-label required" for="offering_status">Service Status</label>
                                    <select class="form-select" id="offering_status" name="status">
                                        <option value="active" selected>Active</option>
                                        <option value="draft">Draft</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="temporarily_unavailable">Temporarily Unavailable</option>
                                        <option value="discontinued">Discontinued</option>
                                    </select>
                                    <span class="form-hint">Inactive products cannot be used in new sales.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Pricing (Matches Screenshot 2 Compensation Card) --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-rupee-sign"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Pricing</div>
                                    <div class="ij-card-head-sub">Define the pricing details for this product.</div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:12px; font-weight:500; color:#475569;">Pricing Visibility</span>
                                <select class="form-select" id="pricing_visibility" name="pricing_visibility" style="width:auto; height:32px; padding:4px 10px; font-size:12px;">
                                    <option value="public_catalog">Visible Everywhere</option>
                                    <option value="client_portal">Client Portal &amp; Quotes</option>
                                    <option value="internal_only">Internal Sales Only</option>
                                    <option value="confidential">Price on Request</option>
                                </select>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="form-grid-3">
                                <div class="form-group-cus pricing-input-row" data-field="min_price">
                                    <label class="form-label required" for="min_price">Selling / Base Price</label>
                                    <div class="ij-price-group">
                                        <select class="ij-curr-select" id="currency_code" name="currency_code">
                                            @foreach($currencyList as $cCode)
                                                <option value="{{ $cCode }}" {{ strtoupper((string)$cCode) === strtoupper((string)$defaultCurrency) ? 'selected' : '' }}>{{ $cCode }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" step="0.01" min="0" class="ij-price-input" id="min_price" name="min_price" placeholder="Enter selling price">
                                    </div>
                                    <span class="form-hint">*Selling price offered for this product.</span>
                                    <span class="form-error">Selling Price is required.</span>
                                </div>

                                <div class="form-group-cus pricing-input-row" data-field="max_price">
                                    <label class="form-label" for="max_price">MRP / Maximum Price</label>
                                    <div class="ij-price-group">
                                        <span class="ij-curr-select" id="maxCurrMirror" style="display:flex;align-items:center;">{{ $defaultCurrency }}</span>
                                        <input type="number" step="0.01" min="0" class="ij-price-input" id="max_price" name="max_price" placeholder="Enter MRP / max price">
                                    </div>
                                    <span class="form-hint">Optional MRP or upper price range.</span>
                                </div>

                                <div class="form-group-cus pricing-input-row">
                                    <label class="form-label required" for="pricing_frequency">Billing Cycle</label>
                                    <select class="form-select" id="pricing_frequency" name="pricing_frequency">
                                        <option value="One-Time">One-Time</option>
                                        <option value="Monthly">Monthly</option>
                                        <option value="Annual">Annual</option>
                                        <option value="Hourly">Hourly</option>
                                        <option value="Per Unit">Per Unit</option>
                                    </select>
                                    <span class="form-hint">Billing frequency for this product.</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4: Delivery Location or Coverage (Matches Screenshot 2 Card 2) --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Delivery Location or Coverage</div>
                                    <div class="ij-card-head-sub">Select up to 15 regions or cities where this product is available.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="ij-hubs-bar" id="popularHubsPills">
                                <span style="font-size:12px; font-weight:500; color:#475569;">Popular Hubs:</span>
                                @foreach(['Global / Worldwide', 'Pan-India', 'Digital / Remote', 'Bengaluru', 'Mumbai', 'Delhi NCR', 'Hyderabad', 'Pune', 'London', 'New York', 'Singapore'] as $hub)
                                    <button type="button" class="ij-hub-pill" data-val="{{ $hub }}">+ {{ $hub }}</button>
                                @endforeach
                            </div>
                            <div class="chips-box" id="locationsBox">
                                <input type="text" class="chip-input-bare" id="locationInput" placeholder="Search or add delivery cities/regions (e.g. Bengaluru, Mumbai, London, Global)...">
                            </div>
                            <div class="d-flex justify-content-between mt-2" style="font-size:12px;">
                                <span id="selectedLocationsCount" style="color:#475569;">Selected Locations (0/15)</span>
                                <button type="button" id="btnClearLocations" style="background:none;border:none;color:#9a6500;font-weight:500;cursor:pointer;">Clear All</button>
                            </div>
                            <input type="hidden" name="locations" id="locations_json" value="[]">
                        </div>
                    </div>

                    {{-- Card 5: Pricing Model (Matches Screenshot 3 Position Type) --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-tags"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Pricing Model <span style="color:#ef4444;">*</span></div>
                                    <div class="ij-card-head-sub">Select the commercial pricing structure for this product.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <input type="hidden" name="pricing_model" id="pricing_model" value="Fixed Price">
                            <div class="ij-choice-grid-4" id="pricingModelSelector">
                                <button type="button" class="ij-choice-card is-selected" data-val="Fixed Price">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-tag"></i></span>
                                    <div><div class="ij-choice-title">Fixed Price</div><div class="ij-choice-sub">One-time product purchase</div></div>
                                </button>
                                <button type="button" class="ij-choice-card" data-val="Subscription">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-sync-alt"></i></span>
                                    <div><div class="ij-choice-title">Subscription</div><div class="ij-choice-sub">Recurring billing plan</div></div>
                                </button>
                                <button type="button" class="ij-choice-card" data-val="Per Unit">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-cubes"></i></span>
                                    <div><div class="ij-choice-title">Per Unit</div><div class="ij-choice-sub">Quantity / seat-based pricing</div></div>
                                </button>
                                <button type="button" class="ij-choice-card" data-val="Custom / Negotiable">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-handshake"></i></span>
                                    <div><div class="ij-choice-title">Custom / Negotiable</div><div class="ij-choice-sub">Quote-based enterprise pricing</div></div>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Card 6: Delivery Mode (Matches Screenshot 3 Work Mode) --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-globe"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Delivery Mode</div>
                                    <div class="ij-card-head-sub">Choose whether this product is Digital/Cloud, Hybrid, or Physical Shipping.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <input type="hidden" name="delivery_model" id="delivery_model" value="Remote">
                            <div class="ij-choice-grid-3" id="deliveryModelSelector">
                                <button type="button" class="ij-choice-card is-selected" data-val="Remote">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-cloud"></i></span>
                                    <div><div class="ij-choice-title">Digital / Remote</div><div class="ij-choice-sub">Delivered online or cloud-based</div></div>
                                </button>
                                <button type="button" class="ij-choice-card" data-val="Hybrid">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-layer-group"></i></span>
                                    <div><div class="ij-choice-title">Hybrid</div><div class="ij-choice-sub">Physical + Digital fulfillment</div></div>
                                </button>
                                <button type="button" class="ij-choice-card" data-val="On-site">
                                    <span class="ij-radio-dot"></span>
                                    <span class="ij-choice-ico"><i class="fas fa-truck"></i></span>
                                    <div><div class="ij-choice-title">Physical / On-site</div><div class="ij-choice-sub">Dispatched from warehouse</div></div>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Card 7: Service Availability (Matches Screenshot 3 Work Shift) --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-clock"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Support &amp; Availability</div>
                                    <div class="ij-card-head-sub">Specify the operational support window for this product.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <input type="hidden" name="service_availability" id="service_availability" value="Business Hours">
                            <div class="ij-choice-grid-5" id="availabilitySelector">
                                @foreach([
                                    ['val' => '24 x 7', 'sub' => 'Round-the-clock support', 'ico' => 'fa-headset'],
                                    ['val' => 'Business Hours', 'sub' => 'Standard daytime hours', 'ico' => 'fa-sun'],
                                    ['val' => 'Flexible Hours', 'sub' => 'Custom SLA schedule', 'ico' => 'fa-clock'],
                                    ['val' => 'On Demand', 'sub' => 'Scheduled per request', 'ico' => 'fa-bolt'],
                                    ['val' => 'Not Applicable', 'sub' => 'No ongoing support window', 'ico' => 'fa-minus-circle'],
                                ] as $sa)
                                    <button type="button" class="ij-choice-card {{ $sa['val'] === 'Business Hours' ? 'is-selected' : '' }}" data-val="{{ $sa['val'] }}">
                                        <span class="ij-radio-dot"></span>
                                        <div><div class="ij-choice-title">{{ $sa['val'] }}</div><div class="ij-choice-sub">{{ $sa['sub'] }}</div></div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Card 8: Warranty, Brochure & Validity --}}
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-shield-alt"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Specifications &amp; Media</div>
                                    <div class="ij-card-head-sub">Optional warranty, product image/brochure, and validity period.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="form-grid-3">
                                <div class="form-group-cus">
                                    <label class="form-label" for="warranty">Warranty Period</label>
                                    <input type="text" class="form-input" id="warranty" name="warranty" placeholder="e.g. 12 Months Standard">
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="brochure">Product Image / Brochure</label>
                                    <input type="file" class="form-input" id="brochure" name="brochure" accept=".png,.jpg,.jpeg,.webp,.pdf" style="padding-top:6px;">
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="validity_days">Catalog Validity</label>
                                    <select class="form-select" id="validity_days" name="validity_days">
                                        <option value="ongoing" selected>Ongoing / No Expiry</option>
                                        <option value="30">30 Days</option>
                                        <option value="60">60 Days</option>
                                        <option value="90">90 Days</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <span></span>
                        <button type="button" class="btn btn-primary" onclick="crmGoToStep(2)">Continue <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                {{-- ================= STEP 2: PRODUCT DESCRIPTION ================= --}}
                <div class="step-content" id="step2">
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-align-left"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Service Description <span style="color:#ef4444;">*</span></div>
                                    <div class="ij-card-head-sub">Describe service scope, deliverables, SLA, and customer benefits.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="editor-toolbar">
                                <button type="button" class="toolbar-btn" data-cmd="bold"><i class="fas fa-bold"></i></button>
                                <button type="button" class="toolbar-btn" data-cmd="italic"><i class="fas fa-italic"></i></button>
                                <button type="button" class="toolbar-btn" data-cmd="underline"><i class="fas fa-underline"></i></button>
                                <button type="button" class="toolbar-btn" data-cmd="insertUnorderedList"><i class="fas fa-list-ul"></i></button>
                                <button type="button" class="toolbar-btn" data-cmd="insertOrderedList"><i class="fas fa-list-ol"></i></button>
                            </div>
                            <div class="rich-editor" id="crmDescriptionEditor" contenteditable="true" data-placeholder="Enter detailed product description, specifications, and value proposition..."></div>
                            <textarea name="description" id="crm_description" class="d-none"></textarea>
                        </div>
                    </div>

                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-tags"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Service Features &amp; Tags</div>
                                    <div class="ij-card-head-sub">Add tags to help sales reps filter services in Quotations &amp; Deals.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div class="chips-box" id="featureTagsBox">
                                <input type="text" class="chip-input-bare" id="featureTagInput" placeholder="Type feature/tag and press Enter...">
                            </div>
                            <input type="hidden" name="feature_tags" id="feature_tags_json" value="[]">
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" onclick="crmGoToStep(1)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn btn-primary" onclick="crmGoToStep(3)">Continue <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                {{-- ================= STEP 3: QUALIFICATION QUESTIONS ================= --}}
                <div class="step-content" id="step3">
                    <div class="card">
                        <div class="ij-card-head">
                            <div class="ij-card-head-left">
                                <div class="ij-card-head-icon"><i class="fas fa-question-circle"></i></div>
                                <div>
                                    <div class="ij-card-head-title">Customer Qualification Questions</div>
                                    <div class="ij-card-head-sub">Add pre-sales discovery questions for quotes and proposals.</div>
                                </div>
                            </div>
                        </div>
                        <div class="ij-card-inner">
                            <div id="questionsContainer" class="mb-3"></div>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-input" id="customQuestionInput" placeholder="Add a customer qualification question...">
                                <select class="form-select" id="customQuestionType" style="max-width: 180px;">
                                    <option value="short">Short Answer</option>
                                    <option value="paragraph">Paragraph</option>
                                    <option value="yesno">Yes / No</option>
                                    <option value="number">Number</option>
                                </select>
                                <button type="button" class="btn btn-secondary" id="btnAddQuestion"><i class="fas fa-plus"></i> Add</button>
                            </div>
                            <input type="hidden" name="qualification_questions" id="qualification_questions_json" value="[]">
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" onclick="crmGoToStep(2)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn btn-primary" onclick="crmGoToStep(4)">Continue <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                {{-- ================= STEP 4: REVIEW & PUBLISH ================= --}}
                <div class="step-content" id="step4">
                    <div class="r4-grid mb-4">
                        <div class="card mb-0">
                            <div class="ij-card-head"><div class="ij-card-head-title">1. Basic Information</div></div>
                            <div class="ij-card-inner">
                                <div class="rv-row"><span class="rv-label">Product Name</span><span class="rv-val" id="rvName">-</span></div>
                                <div class="rv-row"><span class="rv-label">SKU / Code</span><span class="rv-val" id="rvSku">-</span></div>
                                <div class="rv-row"><span class="rv-label">Category</span><span class="rv-val" id="rvCategory">-</span></div>
                                <div class="rv-row"><span class="rv-label">Status</span><span class="rv-val" id="rvStatus">-</span></div>
                            </div>
                        </div>
                        <div class="card mb-0">
                            <div class="ij-card-head"><div class="ij-card-head-title">2. Pricing &amp; Delivery</div></div>
                            <div class="ij-card-inner">
                                <div class="rv-row"><span class="rv-label">Selling Price</span><span class="rv-val" id="rvPricing">-</span></div>
                                <div class="rv-row"><span class="rv-label">Pricing Model</span><span class="rv-val" id="rvModel">-</span></div>
                                <div class="rv-row"><span class="rv-label">Delivery Mode</span><span class="rv-val" id="rvDelivery">-</span></div>
                                <div class="rv-row"><span class="rv-label">Availability</span><span class="rv-val" id="rvAvailability">-</span></div>
                            </div>
                        </div>
                        <div class="card mb-0">
                            <div class="ij-card-head"><div class="ij-card-head-title">3. Catalog Visibility</div></div>
                            <div class="ij-card-inner">
                                <label class="d-flex align-items-center gap-2 mb-2" style="font-size:12px; cursor:pointer;">
                                    <input type="checkbox" name="visibility_internal_crm" value="1" checked> Internal Sales Team (CRM)
                                </label>
                                <label class="d-flex align-items-center gap-2 mb-2" style="font-size:12px; cursor:pointer;">
                                    <input type="checkbox" name="visibility_client_portal" value="1" checked> Client Proposals &amp; Quotes
                                </label>
                                <label class="d-flex align-items-center gap-2 mb-0" style="font-size:12px; cursor:pointer;">
                                    <input type="checkbox" name="visibility_public_catalog" value="1"> Public Product Catalog
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" onclick="crmGoToStep(3)"><i class="fas fa-arrow-left"></i> Back</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-secondary" id="btnBottomSaveDraft"><i class="fas fa-save"></i> Save Draft</button>
                            <button type="button" class="btn btn-primary" id="btnBottomPublish"><i class="fas fa-paper-plane"></i> {{ $isEdit ? 'Update Service' : 'Publish Service' }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('footer-script')
    <script>
        (function () {
            'use strict';

            const SUBMIT_URL = @json($isEdit ? route('service_company.crm.offerings.postUpdate', $offering->id) : route('service_company.crm.offerings.store'));
            const CHECK_SKU_URL = @json(route('service_company.crm.offerings.checkSku'));
            const EDIT_ID = @json($isEdit ? (int) $offering->id : 0);
            const CSRF_TOKEN = @json(csrf_token());

            let currentStep = 1;
            const topActionBtn = document.getElementById('btnTopPublish');
            const isEditMode = @json($isEdit);

            function updateTopActionBtn(stepNum) {
                if (stepNum < 4) {
                    topActionBtn.innerHTML = '<span>Next Page</span> <i class="fas fa-arrow-right"></i>';
                } else {
                    topActionBtn.innerHTML = '<i class="fas fa-paper-plane"></i> <span>' + (isEditMode ? 'Update Service' : 'Publish Service') + '</span>';
                }
            }

            function validateCrmStep(stepNum) {
                let ok = true;
                document.querySelectorAll('#step' + stepNum + ' .form-group-cus').forEach(function (g) { g.classList.remove('has-error'); });

                if (stepNum === 1) {
                    const nameEl = document.getElementById('offering_name');
                    const catEl = document.getElementById('crm_category');
                    const skuEl = document.getElementById('sku');
                    const minPriceEl = document.getElementById('min_price');
                    const isConf = document.getElementById('pricing_visibility').value === 'confidential';

                    if (!nameEl.value.trim()) { nameEl.closest('.form-group-cus').classList.add('has-error'); ok = false; }
                    if (!skuEl.value.trim()) { skuEl.closest('.form-group-cus').classList.add('has-error'); document.getElementById('skuError').textContent = 'Product / Service SKU / Code is required.'; ok = false; }
                    if (!catEl.value) { catEl.closest('.form-group-cus').classList.add('has-error'); ok = false; }
                    if (!isConf && (minPriceEl.value === '' || parseFloat(minPriceEl.value) < 0)) {
                        minPriceEl.closest('.form-group-cus').classList.add('has-error');
                        ok = false;
                    }
                } else if (stepNum === 2) {
                    const descTxt = (document.getElementById('crmDescriptionEditor').textContent || '').trim();
                    if (!descTxt) {
                        if (typeof toastr !== 'undefined') toastr.error('Please enter Product / Service Description.');
                        else alert('Please enter Product / Service Description.');
                        ok = false;
                    }
                }

                if (!ok) {
                    const firstErr = document.querySelector('#step' + stepNum + ' .has-error');
                    if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return ok;
            }

            window.crmGoToStep = function (stepNum) {
                if (stepNum > currentStep && !validateCrmStep(currentStep)) {
                    return;
                }
                currentStep = stepNum;
                document.querySelectorAll('#jrwrap .step-content').forEach(function (el) { el.classList.remove('active'); });
                document.getElementById('step' + stepNum).classList.add('active');
                document.querySelectorAll('#stepper .step-v2').forEach(function (el) {
                    const s = parseInt(el.getAttribute('data-step'), 10);
                    el.classList.remove('current', 'completed', 'pending');
                    if (s === stepNum) el.classList.add('current');
                    else if (s < stepNum) el.classList.add('completed');
                    else el.classList.add('pending');
                });
                updateTopActionBtn(stepNum);
                if (stepNum === 4) populateReview();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            document.getElementById('offering_type').addEventListener('change', function () {
                document.getElementById('offering_class').value = (this.value === 'Professional Service') ? 'service' : 'product';
            });

            // Organization -> Dependent Warehouse / Origin Address Filter
            const ownerClientSelect = document.getElementById('owner_client_id');
            const addressSelect = document.getElementById('address_id');
            const allAddressOptions = Array.from(addressSelect.querySelectorAll('option[data-client-id]')).map(function (opt) {
                return opt.cloneNode(true);
            });

            function syncAddressesForOwner(clientId, preserveCurrent) {
                const currentVal = preserveCurrent ? addressSelect.value : '';
                addressSelect.innerHTML = '<option value="">Select Address (Optional for Digital)</option>';
                const matching = allAddressOptions.filter(function (opt) {
                    return String(opt.getAttribute('data-client-id')) === String(clientId);
                });
                matching.forEach(function (opt) {
                    const cloned = opt.cloneNode(true);
                    if (currentVal && String(cloned.value) === String(currentVal)) {
                        cloned.selected = true;
                    }
                    addressSelect.appendChild(cloned);
                });
                if (matching.length > 0 && (!preserveCurrent || !currentVal)) {
                    addressSelect.value = matching[0].value;
                }
            }

            if (ownerClientSelect) {
                ownerClientSelect.addEventListener('change', function () {
                    syncAddressesForOwner(this.value, false);
                });
                syncAddressesForOwner(ownerClientSelect.value, true);
            }

            function bindChoiceCards(containerId, hiddenId, onChange) {
                const container = document.getElementById(containerId);
                const hidden = document.getElementById(hiddenId);
                container.querySelectorAll('.ij-choice-card').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        container.querySelectorAll('.ij-choice-card').forEach(function (b) { b.classList.remove('is-selected'); });
                        btn.classList.add('is-selected');
                        hidden.value = btn.getAttribute('data-val');
                        if (onChange) onChange(hidden.value);
                    });
                });
            }

            const freqSelect = document.getElementById('pricing_frequency');
            bindChoiceCards('deliveryModelSelector', 'delivery_model');
            bindChoiceCards('availabilitySelector', 'service_availability');
            bindChoiceCards('pricingModelSelector', 'pricing_model', function (val) {
                if (val === 'Fixed Price') { freqSelect.value = 'One-Time'; freqSelect.disabled = true; }
                else { freqSelect.disabled = false; }
            });
            freqSelect.disabled = true;

            document.getElementById('currency_code').addEventListener('change', function () {
                document.getElementById('maxCurrMirror').textContent = this.value;
            });

            document.getElementById('is_unlimited_capacity').addEventListener('change', function () {
                document.getElementById('available_capacity').disabled = this.checked;
            });

            document.getElementById('pricing_visibility').addEventListener('change', function () {
                const isConf = this.value === 'confidential';
                document.querySelectorAll('.pricing-input-row').forEach(function (el) { el.style.display = isConf ? 'none' : 'flex'; });
            });

            // Locations / Hubs
            const locations = [];
            const locBox = document.getElementById('locationsBox');
            const locInput = document.getElementById('locationInput');
            const locHidden = document.getElementById('locations_json');
            function renderLocations() {
                locBox.querySelectorAll('.chip-item').forEach(function (c) { c.remove(); });
                locations.forEach(function (v, i) {
                    const chip = document.createElement('span');
                    chip.className = 'chip-item';
                    chip.innerHTML = '<span></span><button type="button">&times;</button>';
                    chip.querySelector('span').textContent = v;
                    chip.querySelector('button').addEventListener('click', function () { locations.splice(i, 1); renderLocations(); });
                    locBox.insertBefore(chip, locInput);
                });
                locHidden.value = JSON.stringify(locations);
                document.getElementById('selectedLocationsCount').textContent = 'Selected Locations (' + locations.length + '/15)';
            }
            function addLocation(val) {
                const v = val.trim();
                if (v && locations.length < 15 && locations.indexOf(v) === -1) { locations.push(v); renderLocations(); }
            }
            locInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ',') { e.preventDefault(); addLocation(locInput.value.replace(/,/g, '')); locInput.value = ''; }
            });
            document.querySelectorAll('#popularHubsPills .ij-hub-pill').forEach(function (btn) {
                btn.addEventListener('click', function () { addLocation(btn.getAttribute('data-val')); });
            });
            document.getElementById('btnClearLocations').addEventListener('click', function () { locations.length = 0; renderLocations(); });

            // Tags
            const tags = [];
            const tagBox = document.getElementById('featureTagsBox');
            const tagInput = document.getElementById('featureTagInput');
            const tagHidden = document.getElementById('feature_tags_json');
            function renderTags() {
                tagBox.querySelectorAll('.chip-item').forEach(function (c) { c.remove(); });
                tags.forEach(function (v, i) {
                    const chip = document.createElement('span');
                    chip.className = 'chip-item';
                    chip.innerHTML = '<span></span><button type="button">&times;</button>';
                    chip.querySelector('span').textContent = v;
                    chip.querySelector('button').addEventListener('click', function () { tags.splice(i, 1); renderTags(); });
                    tagBox.insertBefore(chip, tagInput);
                });
                tagHidden.value = JSON.stringify(tags);
            }
            tagInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ',') {
                    e.preventDefault();
                    const v = tagInput.value.replace(/,/g, '').trim();
                    if (v && tags.indexOf(v) === -1) { tags.push(v); renderTags(); }
                    tagInput.value = '';
                }
            });

            // Rich Text Editor
            const descEditor = document.getElementById('crmDescriptionEditor');
            const descHidden = document.getElementById('crm_description');
            document.querySelectorAll('#step2 .toolbar-btn').forEach(function (btn) {
                btn.addEventListener('click', function () { document.execCommand(btn.getAttribute('data-cmd'), false, null); descEditor.focus(); });
            });

            // Questions
            const questions = [];
            const qContainer = document.getElementById('questionsContainer');
            const qHidden = document.getElementById('qualification_questions_json');
            function renderQuestions() {
                qContainer.innerHTML = '';
                questions.forEach(function (q, idx) {
                    const row = document.createElement('div');
                    row.className = 'd-flex align-items-center justify-content-between p-2 mb-2 border rounded';
                    row.innerHTML = '<div><strong>' + (idx + 1) + '.</strong> <span class="q-txt"></span> <small class="text-muted">(' + q.answer_type + ')</small></div>' +
                        '<button type="button" class="btn btn-secondary" style="padding:4px 10px;">&times;</button>';
                    row.querySelector('.q-txt').textContent = q.question;
                    row.querySelector('button').addEventListener('click', function () { questions.splice(idx, 1); renderQuestions(); });
                    qContainer.appendChild(row);
                });
                qHidden.value = JSON.stringify(questions);
            }
            document.getElementById('btnAddQuestion').addEventListener('click', function () {
                const inp = document.getElementById('customQuestionInput');
                if (inp.value.trim()) {
                    questions.push({ question: inp.value.trim(), answer_type: document.getElementById('customQuestionType').value });
                    inp.value = '';
                    renderQuestions();
                }
            });

            // SKU Uniqueness Check
            const skuInput = document.getElementById('sku');
            skuInput.addEventListener('blur', function () {
                const v = skuInput.value.trim().toUpperCase();
                if (!v) return;
                fetch(CHECK_SKU_URL + '?sku=' + encodeURIComponent(v) + '&exclude_id=' + EDIT_ID, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        const grp = document.querySelector('[data-field="sku"]');
                        if (res.available === false) grp.classList.add('has-error');
                        else grp.classList.remove('has-error');
                    });
            });

            function populateReview() {
                document.getElementById('rvName').textContent = document.getElementById('offering_name').value || '-';
                document.getElementById('rvSku').textContent = skuInput.value || '-';
                document.getElementById('rvCategory').textContent = document.getElementById('crm_category').value || '-';
                document.getElementById('rvStatus').textContent = document.getElementById('offering_status').value;
                const isConf = document.getElementById('pricing_visibility').value === 'confidential';
                document.getElementById('rvPricing').textContent = isConf ? 'Price on Request' : (document.getElementById('currency_code').value + ' ' + (document.getElementById('min_price').value || '0'));
                document.getElementById('rvModel').textContent = document.getElementById('pricing_model').value + ' (' + freqSelect.value + ')';
                document.getElementById('rvDelivery').textContent = document.getElementById('delivery_model').value;
                document.getElementById('rvAvailability').textContent = document.getElementById('service_availability').value;
            }

            function submitOffering(isDraft) {
                descHidden.value = descEditor.innerHTML;
                if (isDraft) document.getElementById('offering_status').value = 'draft';
                freqSelect.disabled = false;
                const formData = new FormData(document.getElementById('crmOfferingForm'));
                if (document.getElementById('pricing_model').value === 'Fixed Price') freqSelect.disabled = true;

                fetch(SUBMIT_URL, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(async function (res) {
                    const data = await res.json().catch(function () { return {}; });
                    if (res.ok && data.status === 'success') {
                        if (typeof toastr !== 'undefined') toastr.success(data.message);
                        else alert(data.message);
                        if (!EDIT_ID && data.offering && data.offering.id) {
                            window.location.href = @json(url('service_company/crm/products-services')) + '/' + data.offering.id + '/edit';
                        }
                    } else {
                        if (data.errors) {
                            let firstFieldEl = null;
                            Object.keys(data.errors).forEach(function (k) {
                                const grp = document.querySelector('.form-group-cus[data-field="' + k + '"]');
                                if (grp) {
                                    grp.classList.add('has-error');
                                    if (!firstFieldEl) firstFieldEl = grp;
                                }
                            });
                            if (firstFieldEl) {
                                const stepEl = firstFieldEl.closest('.step-content');
                                if (stepEl) {
                                    const sNum = parseInt(stepEl.id.replace('step', ''), 10);
                                    if (sNum && sNum !== currentStep) {
                                        currentStep = sNum;
                                        document.querySelectorAll('#jrwrap .step-content').forEach(function (el) { el.classList.remove('active'); });
                                        stepEl.classList.add('active');
                                        updateTopActionBtn(sNum);
                                    }
                                }
                                firstFieldEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            }
                        }
                        const msg = data.message || 'Validation failed. Please check required fields.';
                        if (typeof toastr !== 'undefined') toastr.error(msg);
                        else alert(msg);
                    }
                })
                .catch(function () { alert('Network error while saving product.'); });
            }

            document.getElementById('btnTopSaveDraft').addEventListener('click', function () { submitOffering(true); });
            document.getElementById('btnBottomSaveDraft').addEventListener('click', function () { submitOffering(true); });
            document.getElementById('btnTopPublish').addEventListener('click', function () {
                if (currentStep < 4) {
                    crmGoToStep(currentStep + 1);
                } else {
                    submitOffering(false);
                }
            });
            document.getElementById('btnBottomPublish').addEventListener('click', function () { submitOffering(false); });
        })();
    </script>
@endpush
