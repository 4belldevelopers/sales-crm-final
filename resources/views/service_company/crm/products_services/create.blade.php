@extends('layouts.service_company_layout')

@push('head-script')
    <link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --crm-primary: #2563eb;
            --crm-primary-hover: #1d4ed8;
            --crm-primary-light: #eff6ff;
            --crm-primary-border: #bfdbfe;
            --crm-bg: #f8fafc;
            --crm-header-bg: #f8fafc;
            --crm-card-bg: #ffffff;
            --crm-border: #e2e8f0;
            --crm-text: #0f172a;
            --crm-text-sec: #475569;
            --crm-text-muted: #64748b;
            --crm-danger: #dc2626;
            --crm-danger-bg: #fef2f2;
            --crm-success: #059669;
            --crm-success-bg: #ecfdf5;
            --crm-ease: cubic-bezier(0.22, 1, 0.36, 1);
        }

        #crmwrap *, #crmwrap *::before, #crmwrap *::after { box-sizing: border-box; }
        #crmwrap { font-family: 'Poppins', sans-serif; color: var(--crm-text); font-size: 13px; line-height: 1.5; max-width: 100%; overflow-x: hidden; }
        #crmwrap p, #crmwrap h1, #crmwrap h2, #crmwrap h3, #crmwrap h4 { margin: 0; }
        #crmwrap .crm-container { padding: 8px 20px 36px; max-width: 1440px; margin: 0 auto; }

        /* Sticky Header Toolbar (Rule 20) */
        #crmwrap .crm-topbar {
            background: var(--crm-header-bg); border: 1px solid var(--crm-border); border-radius: 10px;
            padding: 14px 20px; margin-bottom: 16px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;
        }
        #crmwrap .crm-topbar-left { display: flex; align-items: center; gap: 12px; }
        #crmwrap .crm-topbar-icon {
            width: 38px; height: 38px; border-radius: 8px; background: var(--crm-primary-light); border: 1px solid var(--crm-primary-border);
            color: var(--crm-primary); display: flex; align-items: center; justify-content: center; font-size: 16px;
        }
        #crmwrap .crm-title { font-size: 15px; font-weight: 600; color: var(--crm-text); }
        #crmwrap .crm-subtitle { font-size: 12px; color: var(--crm-text-muted); }
        #crmwrap .crm-topbar-actions { display: flex; align-items: center; gap: 10px; }

        /* 4-Step Wizard Stepper */
        #crmwrap .stepper-v2 {
            display: flex; align-items: center; gap: 12px; margin-bottom: 16px; background: #fff;
            border: 1px solid var(--crm-border); border-radius: 10px; padding: 14px 20px;
        }
        #crmwrap .step-v2 { display: flex; align-items: center; gap: 10px; flex: 1; cursor: pointer; position: relative; }
        #crmwrap .step-v2-icon {
            width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 600; background: var(--crm-bg); border: 1px solid var(--crm-border); color: var(--crm-text-muted);
        }
        #crmwrap .step-v2.current .step-v2-icon { background: var(--crm-primary); border-color: var(--crm-primary); color: #fff; }
        #crmwrap .step-v2.completed .step-v2-icon { background: var(--crm-success); border-color: var(--crm-success); color: #fff; }
        #crmwrap .step-v2-label { font-size: 13px; font-weight: 600; color: var(--crm-text); }
        #crmwrap .step-v2.current .step-v2-label { color: var(--crm-primary); }
        #crmwrap .step-v2-desc { font-size: 11px; color: var(--crm-text-muted); }

        #crmwrap .step-content { display: none; }
        #crmwrap .step-content.active { display: block; }

        /* Cards with Mandatory Header Background (Rule 20) */
        #crmwrap .crm-card { background: #fff; border: 1px solid var(--crm-border); border-radius: 12px; margin-bottom: 16px; overflow: hidden; }
        #crmwrap .crm-card-header {
            background: var(--crm-header-bg); border-bottom: 1px solid var(--crm-border); padding: 12px 20px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        #crmwrap .crm-card-title { display: flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 600; color: var(--crm-text); }
        #crmwrap .crm-card-title i { color: var(--crm-primary); }
        #crmwrap .crm-card-body { padding: 20px; }

        /* Form Grids & Controls */
        #crmwrap .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px 20px; }
        #crmwrap .form-grid-3 { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px 20px; }
        #crmwrap .form-group-cus { display: flex; flex-direction: column; gap: 6px; }
        #crmwrap .form-group-cus.full-width { grid-column: 1 / -1; }
        #crmwrap .form-label { font-size: 12px; font-weight: 500; color: var(--crm-text); margin: 0; }
        #crmwrap .required::after { content: '*'; color: var(--crm-danger); margin-left: 3px; }
        #crmwrap .form-hint { font-size: 11px; color: var(--crm-text-muted); }
        #crmwrap .form-error { font-size: 11px; color: var(--crm-danger); display: none; }
        #crmwrap .form-group-cus.has-error .form-error { display: block; }
        #crmwrap .form-group-cus.has-error .form-input,
        #crmwrap .form-group-cus.has-error .form-select { border-color: var(--crm-danger); background: var(--crm-danger-bg); }

        #crmwrap .form-input, #crmwrap .form-select, #crmwrap .form-textarea {
            width: 100%; padding: 9px 12px; border: 1px solid var(--crm-border); border-radius: 6px;
            font-family: 'Poppins', sans-serif; font-size: 13px; color: var(--crm-text); background: #fff; outline: none;
            transition: border-color 160ms var(--crm-ease), box-shadow 160ms var(--crm-ease);
        }
        #crmwrap .form-textarea { min-height: 82px; resize: vertical; }
        #crmwrap .form-input:focus, #crmwrap .form-select:focus, #crmwrap .form-textarea:focus {
            border-color: var(--crm-primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        /* Choice Cards & Pills */
        #crmwrap .choice-grid { display: flex; flex-wrap: wrap; gap: 8px; }
        #crmwrap .choice-pill {
            padding: 8px 14px; border: 1px solid var(--crm-border); border-radius: 7px; background: #fff;
            font-size: 12px; font-weight: 500; color: var(--crm-text-sec); cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            transition: background-color 150ms var(--crm-ease), border-color 150ms var(--crm-ease), color 150ms var(--crm-ease);
        }
        #crmwrap .choice-pill:hover { background: var(--crm-bg); }
        #crmwrap .choice-pill.is-selected { background: var(--crm-primary-light); border-color: var(--crm-primary); color: var(--crm-primary); font-weight: 600; }

        /* Chips Container */
        #crmwrap .chips-box {
            border: 1px solid var(--crm-border); border-radius: 6px; padding: 7px 10px; display: flex; flex-wrap: wrap; gap: 6px; align-items: center; min-height: 40px;
        }
        #crmwrap .chip-item {
            display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 999px;
            background: var(--crm-primary-light); border: 1px solid var(--crm-primary-border); color: var(--crm-primary); font-size: 12px; font-weight: 500;
        }
        #crmwrap .chip-item button { background: none; border: none; color: var(--crm-primary); cursor: pointer; padding: 0; font-size: 13px; }
        #crmwrap .chip-input-bare { border: none; outline: none; flex: 1; min-width: 140px; font-family: 'Poppins', sans-serif; font-size: 13px; }

        /* Rich Editor */
        #crmwrap .editor-toolbar {
            display: flex; gap: 4px; padding: 7px 10px; background: var(--crm-header-bg);
            border: 1px solid var(--crm-border); border-bottom: none; border-radius: 6px 6px 0 0;
        }
        #crmwrap .toolbar-btn { width: 28px; height: 28px; border: none; background: transparent; border-radius: 4px; color: var(--crm-text-sec); cursor: pointer; }
        #crmwrap .toolbar-btn:hover { background: #e2e8f0; }
        #crmwrap .rich-editor {
            min-height: 130px; padding: 12px; border: 1px solid var(--crm-border); border-radius: 0 0 6px 6px; background: #fff; outline: none;
        }
        #crmwrap .rich-editor:empty::before { content: attr(data-placeholder); color: var(--crm-text-muted); }

        /* Buttons & Footer */
        #crmwrap .btn-crm-sec {
            padding: 8px 16px; border-radius: 6px; border: 1px solid var(--crm-border); background: #fff; color: var(--crm-text);
            font-size: 13px; font-weight: 500; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        }
        #crmwrap .btn-crm-sec:hover { background: var(--crm-bg); }
        #crmwrap .btn-crm-pri {
            padding: 8px 18px; border-radius: 6px; border: 1px solid var(--crm-primary); background: var(--crm-primary); color: #fff;
            font-size: 13px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
        }
        #crmwrap .btn-crm-pri:hover { background: var(--crm-primary-hover); }
        #crmwrap .step-footer { display: flex; justify-content: space-between; align-items: center; margin-top: 16px; }

        /* Review 3-Column Grid */
        #crmwrap .review-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 16px; }
        #crmwrap .rv-row { display: flex; justify-content: space-between; gap: 10px; padding: 7px 0; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
        #crmwrap .rv-label { color: var(--crm-text-muted); }
        #crmwrap .rv-val { font-weight: 500; color: var(--crm-text); text-align: right; }

        @media (max-width: 1199px) { #crmwrap .review-grid { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 768px) { #crmwrap .form-grid, #crmwrap .form-grid-3, #crmwrap .review-grid, #crmwrap .stepper-v2 { grid-template-columns: 1fr; flex-direction: column; align-items: stretch; } }
        @media (prefers-reduced-motion: reduce) { #crmwrap * { transition: none !important; } }
    </style>
@endpush

@section('content')
    @php
        $isEdit = ($page_event ?? 'ADD') === 'EDIT' && isset($offering);
        $currencyList = $currency ?? [];
        $defaultCurrency = $offering->currency_code ?? ($currency_code ?? 'INR');
        $tz = $tenantTimezone ?? 'UTC';
    @endphp

    <div id="crmwrap" class="crm-offering-create">
        <div class="crm-container">
            {{-- Header Toolbar --}}
            <div class="crm-topbar">
                <div class="crm-topbar-left">
                    <div class="crm-topbar-icon"><i class="fas fa-cubes"></i></div>
                    <div>
                        <h1 class="crm-title">{{ $isEdit ? 'Edit Product / Service' : 'Add Product / Service' }}</h1>
                        <p class="crm-subtitle">Enter offering details and publish to JeroCRM Catalog (Timezone: {{ $tz }})</p>
                    </div>
                </div>
                <div class="crm-topbar-actions">
                    <button type="button" class="btn-crm-sec" id="btnTopSaveDraft"><i class="fas fa-save"></i> Save Draft</button>
                    <button type="button" class="btn-crm-pri" id="btnTopPublish"><i class="fas fa-paper-plane"></i> {{ $isEdit ? 'Update Offering' : 'Publish Offering' }}</button>
                </div>
            </div>

            {{-- 4-Step Wizard Stepper --}}
            <div class="stepper-v2" id="stepper">
                <div class="step-v2 current" data-step="1" onclick="crmGoToStep(1)">
                    <div class="step-v2-icon">1</div>
                    <div>
                        <div class="step-v2-label">Basic &amp; Pricing</div>
                        <div class="step-v2-desc">Identity, pricing &amp; delivery</div>
                    </div>
                </div>
                <div class="step-v2" data-step="2" onclick="crmGoToStep(2)">
                    <div class="step-v2-icon">2</div>
                    <div>
                        <div class="step-v2-label">Details &amp; Features</div>
                        <div class="step-v2-desc">Description, scope &amp; tags</div>
                    </div>
                </div>
                <div class="step-v2" data-step="3" onclick="crmGoToStep(3)">
                    <div class="step-v2-icon">3</div>
                    <div>
                        <div class="step-v2-label">Customer Qualification</div>
                        <div class="step-v2-desc">Pre-sales discovery questions</div>
                    </div>
                </div>
                <div class="step-v2" data-step="4" onclick="crmGoToStep(4)">
                    <div class="step-v2-icon">4</div>
                    <div>
                        <div class="step-v2-label">Review &amp; Publish</div>
                        <div class="step-v2-desc">Verify &amp; set visibility</div>
                    </div>
                </div>
            </div>

            <form id="crmOfferingForm" method="POST" action="javascript:void(0);" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="operation_id" id="operation_id" value="{{ $offering->operation_id ?? \Illuminate\Support\Str::uuid()->toString() }}">

                {{-- ================= STEP 1: BASIC & COMMERCIAL DETAILS ================= --}}
                <div class="step-content active" id="step1">
                    {{-- Card 1: Basic Information --}}
                    <div class="crm-card">
                        <div class="crm-card-header">
                            <div class="crm-card-title"><i class="fas fa-tag"></i> Basic Information &amp; Classification</div>
                        </div>
                        <div class="crm-card-body">
                            <div class="form-grid">
                                <div class="form-group-cus">
                                    <label class="form-label required">Offering Class</label>
                                    <input type="hidden" name="offering_class" id="offering_class" value="product">
                                    <div class="choice-grid" id="offeringClassSelector">
                                        <button type="button" class="choice-pill is-selected" data-val="product"><i class="fas fa-box"></i> Product</button>
                                        <button type="button" class="choice-pill" data-val="service"><i class="fas fa-concierge-bell"></i> Service</button>
                                    </div>
                                </div>

                                <div class="form-group-cus" data-field="offering_type">
                                    <label class="form-label required" for="offering_type">Product / Service Type</label>
                                    <select class="form-select" id="offering_type" name="offering_type">
                                        <option value="Physical Product">Physical Product</option>
                                        <option value="Digital Product">Digital Product</option>
                                        <option value="Software / SaaS">Software / SaaS</option>
                                        <option value="Subscription">Subscription</option>
                                        <option value="Professional Service">Professional Service</option>
                                        <option value="Consulting">Consulting</option>
                                        <option value="Managed Service">Managed Service</option>
                                        <option value="Staffing / Recruitment Service">Staffing / Recruitment Service</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>

                                <div class="form-group-cus" data-field="owner_client_id">
                                    <label class="form-label" for="owner_client_id">Organization / Offering Owner</label>
                                    <select class="form-select" id="owner_client_id" name="owner_client_id">
                                        @foreach(($clients ?? collect()) as $cl)
                                            <option value="{{ $cl->id }}" {{ $cl->self_company ? 'selected' : '' }}>
                                                {{ $cl->name }} {{ $cl->self_company ? '(My Organization)' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group-cus" data-field="name">
                                    <label class="form-label required" for="offering_name">Product / Service Name</label>
                                    <input type="text" class="form-input" id="offering_name" name="name" maxlength="255" placeholder="e.g. JeroCRM Enterprise Subscription" required>
                                    <span class="form-error">Product / Service Name is required.</span>
                                </div>

                                <div class="form-group-cus" data-field="crm_category">
                                    <label class="form-label required" for="crm_category">Product Category / Industry</label>
                                    <select class="form-select" id="crm_category" name="crm_category" required>
                                        <option value="">Select Category / Industry...</option>
                                        <optgroup label="CRM Categories">
                                            <option value="IT & Cloud">IT &amp; Cloud</option>
                                            <option value="Software / SaaS">Software / SaaS</option>
                                            <option value="AI & Automation">AI &amp; Automation</option>
                                            <option value="Consulting & Advisory">Consulting &amp; Advisory</option>
                                            <option value="Staffing & Managed Services">Staffing &amp; Managed Services</option>
                                        </optgroup>
                                        @if(!empty($industries))
                                            <optgroup label="Global Industries">
                                                @foreach($industries as $ind)
                                                    <option value="{{ $ind['name'] ?? ($ind['title'] ?? '') }}" data-industry-id="{{ $ind['id'] ?? '' }}">
                                                        {{ $ind['name'] ?? ($ind['title'] ?? '') }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endif
                                    </select>
                                    <input type="hidden" name="industry_id" id="industry_id" value="">
                                    <span class="form-error">Product Category / Industry is required.</span>
                                </div>

                                <div class="form-group-cus" data-field="address_id">
                                    <label class="form-label" for="address_id">Origin / Primary Office Address</label>
                                    <input type="number" class="form-input" id="address_id" name="address_id" placeholder="Optional for Digital Delivery / Cloud-based">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2: Targeting, Capacity & Status --}}
                    <div class="crm-card">
                        <div class="crm-card-header">
                            <div class="crm-card-title"><i class="fas fa-bullseye"></i> Targeting, Capacity &amp; Status</div>
                        </div>
                        <div class="crm-card-body">
                            <div class="form-grid-3">
                                <div class="form-group-cus full-width">
                                    <label class="form-label">Target Customer Segment</label>
                                    <input type="hidden" name="target_segments" id="target_segments" value="[]">
                                    <div class="choice-grid" id="segmentPills">
                                        @foreach(['Individual', 'Startup', 'Small Business', 'Mid-sized Business', 'Enterprise', 'Government', 'Global Organizations', 'All Segments'] as $seg)
                                            <button type="button" class="choice-pill" data-seg="{{ $seg }}">{{ $seg }}</button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group-cus" data-field="available_capacity">
                                    <label class="form-label" for="available_capacity">Available Units / Service Capacity</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="number" min="1" class="form-input" id="available_capacity" name="available_capacity" value="100">
                                        <label class="d-flex align-items-center gap-1 mb-0" style="white-space:nowrap; font-size:12px; cursor:pointer;">
                                            <input type="checkbox" id="is_unlimited_capacity" name="is_unlimited_capacity" value="1"> Unlimited
                                        </label>
                                    </div>
                                    <span class="form-error">Capacity must be at least 1 unless Unlimited is checked.</span>
                                </div>

                                <div class="form-group-cus" data-field="status">
                                    <label class="form-label required" for="offering_status">Product / Service Status</label>
                                    <select class="form-select" id="offering_status" name="status">
                                        <option value="draft">Draft</option>
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="temporarily_unavailable">Temporarily Unavailable</option>
                                        <option value="discontinued">Discontinued</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3: Commercial Pricing --}}
                    <div class="crm-card">
                        <div class="crm-card-header">
                            <div class="crm-card-title"><i class="fas fa-rupee-sign"></i> Commercial Pricing</div>
                        </div>
                        <div class="crm-card-body">
                            <div class="form-grid-3">
                                <div class="form-group-cus">
                                    <label class="form-label required" for="pricing_visibility">Pricing Visibility</label>
                                    <select class="form-select" id="pricing_visibility" name="pricing_visibility">
                                        <option value="public_catalog">Public Catalog (Visible Everywhere)</option>
                                        <option value="client_portal">Client Portal &amp; Quotes</option>
                                        <option value="internal_only">Internal Sales Only</option>
                                        <option value="confidential">Price on Request (Confidential)</option>
                                    </select>
                                </div>

                                <div class="form-group-cus">
                                    <label class="form-label required" for="currency_code">Currency</label>
                                    <select class="form-select" id="currency_code" name="currency_code">
                                        @foreach($currencyList as $cCode)
                                            @php $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($cCode); @endphp
                                            <option value="{{ $cCode }}" {{ strtoupper((string)$cCode) === strtoupper((string)$defaultCurrency) ? 'selected' : '' }}>
                                                {{ $cMeta['label'] ?? $cCode }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group-cus">
                                    <label class="form-label">Price Negotiable</label>
                                    <input type="hidden" name="is_negotiable" id="is_negotiable" value="0">
                                    <div class="choice-grid" id="negotiableSelector">
                                        <button type="button" class="choice-pill" data-val="1">Yes</button>
                                        <button type="button" class="choice-pill is-selected" data-val="0">No</button>
                                    </div>
                                </div>

                                <div class="form-group-cus full-width">
                                    <label class="form-label required">Pricing Model</label>
                                    <input type="hidden" name="pricing_model" id="pricing_model" value="Fixed Price">
                                    <div class="choice-grid" id="pricingModelSelector">
                                        @foreach(['Fixed Price', 'Hourly', 'Daily', 'Weekly', 'Monthly', 'Annual', 'Per Unit', 'Subscription', 'Milestone Based', 'Custom / Negotiable'] as $pm)
                                            <button type="button" class="choice-pill {{ $pm === 'Fixed Price' ? 'is-selected' : '' }}" data-val="{{ $pm }}">{{ $pm }}</button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group-cus pricing-input-row" data-field="min_price">
                                    <label class="form-label required" for="min_price" id="minPriceLabel">Unit / Base Price</label>
                                    <input type="number" step="0.01" min="0" class="form-input" id="min_price" name="min_price" placeholder="0.00">
                                    <span class="form-error">Base / Minimum Price is required.</span>
                                </div>

                                <div class="form-group-cus pricing-input-row" data-field="max_price">
                                    <label class="form-label" for="max_price">Maximum Price (Optional Range)</label>
                                    <input type="number" step="0.01" min="0" class="form-input" id="max_price" name="max_price" placeholder="0.00">
                                    <span class="form-error">Maximum Price must be &ge; Minimum Price.</span>
                                </div>

                                <div class="form-group-cus pricing-input-row" id="pricingFrequencyGroup">
                                    <label class="form-label" for="pricing_frequency">Pricing Frequency / Billing Cycle</label>
                                    <select class="form-select" id="pricing_frequency" name="pricing_frequency">
                                        @foreach(['One-Time', 'Hourly', 'Daily', 'Weekly', 'Monthly', 'Quarterly', 'Annual', 'Per Unit', 'Per Milestone', 'Custom'] as $pf)
                                            <option value="{{ $pf }}">{{ $pf }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4: Delivery & Availability --}}
                    <div class="crm-card">
                        <div class="crm-card-header">
                            <div class="crm-card-title"><i class="fas fa-truck"></i> Delivery, Availability &amp; Validity</div>
                        </div>
                        <div class="crm-card-body">
                            <div class="form-grid">
                                <div class="form-group-cus full-width">
                                    <label class="form-label required">Delivery Model</label>
                                    <input type="hidden" name="delivery_model" id="delivery_model" value="Remote">
                                    <div class="choice-grid" id="deliveryModelSelector">
                                        @foreach(['On-site', 'Remote', 'Hybrid', 'Client Location', 'Vendor Location', 'Cloud-based', 'Digital Delivery', 'Not Applicable'] as $dm)
                                            <button type="button" class="choice-pill {{ $dm === 'Remote' ? 'is-selected' : '' }}" data-val="{{ $dm }}">{{ $dm }}</button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group-cus full-width">
                                    <label class="form-label required">Service Availability / Operating Hours</label>
                                    <input type="hidden" name="service_availability" id="service_availability" value="Business Hours">
                                    <div class="choice-grid" id="availabilitySelector">
                                        @foreach(['24 x 7', 'Business Hours', 'Flexible Hours', 'On Demand', 'Scheduled', 'Not Applicable'] as $sa)
                                            <button type="button" class="choice-pill {{ $sa === 'Business Hours' ? 'is-selected' : '' }}" data-val="{{ $sa }}">{{ $sa }}</button>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="form-group-cus">
                                    <label class="form-label">Delivery Coverage / Locations</label>
                                    <input type="hidden" name="coverage_scope" id="coverage_scope" value="Digital / Remote">
                                    <div class="choice-grid mb-2" id="coverageScopePills">
                                        @foreach(['Global / Worldwide', 'Pan-India', 'Digital / Remote'] as $cs)
                                            <button type="button" class="choice-pill {{ $cs === 'Digital / Remote' ? 'is-selected' : '' }}" data-val="{{ $cs }}">{{ $cs }}</button>
                                        @endforeach
                                    </div>
                                    <div class="chips-box" id="locationsBox">
                                        <input type="text" class="chip-input-bare" id="locationInput" placeholder="Add city/region and press Enter...">
                                    </div>
                                    <input type="hidden" name="locations" id="locations_json" value="[]">
                                </div>

                                <div class="form-group-cus">
                                    <label class="form-label">Availability / Validity Period</label>
                                    <input type="hidden" name="validity_days" id="validity_days" value="ongoing">
                                    <div class="choice-grid mb-2" id="validitySelector">
                                        <button type="button" class="choice-pill is-selected" data-val="ongoing">Ongoing / No Expiry</button>
                                        <button type="button" class="choice-pill" data-val="30">30 Days</button>
                                        <button type="button" class="choice-pill" data-val="60">60 Days</button>
                                        <button type="button" class="choice-pill" data-val="90">90 Days</button>
                                        <button type="button" class="choice-pill" data-val="custom">Custom Date</button>
                                    </div>
                                    <input type="date" class="form-input d-none" id="valid_until" name="valid_until">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 5: Conditional Offering Specs (Product vs Service) --}}
                    <div class="crm-card">
                        <div class="crm-card-header">
                            <div class="crm-card-title"><i class="fas fa-sliders-h"></i> <span id="conditionalSpecsTitle">Product Specifications</span></div>
                        </div>
                        <div class="crm-card-body">
                            <div class="form-grid-3" id="productOnlySpecs">
                                <div class="form-group-cus" data-field="sku">
                                    <label class="form-label" for="sku">Product Code / SKU</label>
                                    <input type="text" class="form-input" id="sku" name="sku" placeholder="e.g. JERO-CRM-001" style="text-transform: uppercase;">
                                    <span class="form-hint" id="skuHint">Unique within your company catalog.</span>
                                    <span class="form-error" id="skuError">SKU already exists in your company catalog.</span>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="unit">Unit of Measurement</label>
                                    <select class="form-select" id="unit" name="unit">
                                        <option value="piece">Piece</option>
                                        <option value="license">License / Seat</option>
                                        <option value="box">Box</option>
                                        <option value="pack">Pack</option>
                                        <option value="kg">Kilogram (kg)</option>
                                    </select>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="warranty">Warranty</label>
                                    <input type="text" class="form-input" id="warranty" name="warranty" placeholder="e.g. 12 Months Standard">
                                </div>
                                <div class="form-group-cus full-width">
                                    <label class="form-label" for="brochure">Product Images / Brochure (PNG, JPG, PDF)</label>
                                    <input type="file" class="form-input" id="brochure" name="brochure" accept=".png,.jpg,.jpeg,.webp,.pdf">
                                </div>
                            </div>

                            <div class="form-grid-3 d-none" id="serviceOnlySpecs">
                                <div class="form-group-cus">
                                    <label class="form-label" for="service_duration">Service Duration</label>
                                    <input type="text" class="form-input" id="service_duration" name="service_duration" placeholder="e.g. 6 Months / Ongoing">
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="implementation_timeline">Implementation Timeline</label>
                                    <input type="text" class="form-input" id="implementation_timeline" name="implementation_timeline" placeholder="e.g. 2-4 Weeks">
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="sla_details">Support &amp; Maintenance / SLA</label>
                                    <input type="text" class="form-input" id="sla_details" name="sla_details" placeholder="e.g. 99.9% Uptime / 4h Response">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 6: Eligibility & Sales Team --}}
                    <div class="crm-card">
                        <div class="crm-card-header">
                            <div class="crm-card-title"><i class="fas fa-users"></i> Technical Prerequisites &amp; Sales Team</div>
                        </div>
                        <div class="crm-card-body">
                            <div class="form-grid">
                                <div class="form-group-cus">
                                    <label class="form-label">Technical Requirements / Eligibility Criteria</label>
                                    <div class="chips-box" id="eligibilityBox">
                                        <input type="text" class="chip-input-bare" id="eligibilityInput" placeholder="Add prerequisite and press Enter...">
                                    </div>
                                    <div class="choice-grid mt-1" id="eligibilityQuickPills">
                                        @foreach(['No Prerequisites', 'Active Cloud Account', 'Enterprise License', 'NDA Required', 'API Access'] as $eq)
                                            <button type="button" class="choice-pill" data-val="{{ $eq }}">+ {{ $eq }}</button>
                                        @endforeach
                                    </div>
                                    <input type="hidden" name="eligibility_criteria" id="eligibility_criteria_json" value="[]">
                                </div>

                                <div class="form-group-cus">
                                    <label class="form-label" for="teamMemberSelect">Sales &amp; Delivery Team (First selected = Lead Owner)</label>
                                    <select class="form-select" id="teamMemberSelect" multiple style="min-height: 92px;">
                                        @foreach(($teamRoster ?? []) as $tm)
                                            <option value="{{ $tm['id'] ?? '' }}">{{ $tm['name'] ?? ($tm['email'] ?? 'Team Member') }}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="team_members" id="team_members_json" value="[]">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer">
                        <span></span>
                        <button type="button" class="btn-crm-pri" onclick="crmGoToStep(2)">Continue to Details &amp; Features <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                {{-- ================= STEP 2: DESCRIPTION, SCOPE & FEATURES ================= --}}
                <div class="step-content" id="step2">
                    <div class="crm-card">
                        <div class="crm-card-header">
                            <div class="crm-card-title"><i class="fas fa-align-left"></i> Product / Service Description &amp; Value Proposition</div>
                        </div>
                        <div class="crm-card-body">
                            <div class="form-group-cus full-width mb-4" data-field="description">
                                <label class="form-label required">Product / Service Description</label>
                                <div class="editor-toolbar">
                                    <button type="button" class="toolbar-btn" data-cmd="bold"><i class="fas fa-bold"></i></button>
                                    <button type="button" class="toolbar-btn" data-cmd="italic"><i class="fas fa-italic"></i></button>
                                    <button type="button" class="toolbar-btn" data-cmd="underline"><i class="fas fa-underline"></i></button>
                                    <button type="button" class="toolbar-btn" data-cmd="insertUnorderedList"><i class="fas fa-list-ul"></i></button>
                                    <button type="button" class="toolbar-btn" data-cmd="insertOrderedList"><i class="fas fa-list-ol"></i></button>
                                </div>
                                <div class="rich-editor" id="crmDescriptionEditor" contenteditable="true" data-placeholder="Enter detailed product or service description..."></div>
                                <textarea name="description" id="crm_description" class="d-none"></textarea>
                                <span class="form-error">Product / Service Description is required.</span>
                            </div>

                            <div class="form-grid">
                                <div class="form-group-cus">
                                    <label class="form-label" for="features_scope">Features / Scope of Services</label>
                                    <textarea class="form-textarea" id="features_scope" name="features_scope" placeholder="Key capabilities or scope items..."></textarea>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="customer_benefits">Customer Benefits / Value Proposition</label>
                                    <textarea class="form-textarea" id="customer_benefits" name="customer_benefits" placeholder="ROI, time savings, compliance outcomes..."></textarea>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="specifications_eligibility">Specifications / Eligibility</label>
                                    <textarea class="form-textarea" id="specifications_eligibility" name="specifications_eligibility"></textarea>
                                </div>
                                <div class="form-group-cus">
                                    <label class="form-label" for="additional_info">Additional Information</label>
                                    <textarea class="form-textarea" id="additional_info" name="additional_info"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="crm-card">
                        <div class="crm-card-header">
                            <div class="crm-card-title"><i class="fas fa-tags"></i> Key Features / Technologies / Expertise</div>
                        </div>
                        <div class="crm-card-body">
                            <div class="chips-box" id="featureTagsBox">
                                <input type="text" class="chip-input-bare" id="featureTagInput" placeholder="Type feature/technology and press Enter...">
                            </div>
                            <div class="choice-grid mt-2" id="featureQuickPills">
                                @foreach(['API Integration', '24/7 Support', 'ISO 27001', 'Multi-Tenant', 'Custom SLA', 'Cloud Native', 'SSO / SAML'] as $fq)
                                    <button type="button" class="choice-pill" data-val="{{ $fq }}">+ {{ $fq }}</button>
                                @endforeach
                            </div>
                            <input type="hidden" name="feature_tags" id="feature_tags_json" value="[]">
                        </div>
                    </div>

                    <div class="step-footer">
                        <button type="button" class="btn-crm-sec" onclick="crmGoToStep(1)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn-crm-pri" onclick="crmGoToStep(3)">Continue to Qualification <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                {{-- ================= STEP 3: CUSTOMER QUALIFICATION QUESTIONS ================= --}}
                <div class="step-content" id="step3">
                    <div class="crm-card">
                        <div class="crm-card-header">
                            <div class="crm-card-title"><i class="fas fa-question-circle"></i> Customer Discovery / Pre-Sales Qualification Questions</div>
                        </div>
                        <div class="crm-card-body">
                            <div class="choice-grid mb-3" id="discoveryTemplates">
                                @foreach([
                                    'What is your target budget range?',
                                    'What is your expected go-live date?',
                                    'How many user seats / units do you require?',
                                    'What is your existing tech stack?',
                                    'Do you require a custom NDA or compliance review?'
                                ] as $tpl)
                                    <button type="button" class="choice-pill" data-q="{{ $tpl }}">+ {{ $tpl }}</button>
                                @endforeach
                            </div>
                            <div id="questionsContainer" class="mb-3"></div>
                            <div class="d-flex gap-2">
                                <input type="text" class="form-input" id="customQuestionInput" placeholder="Add a custom discovery question...">
                                <select class="form-select" id="customQuestionType" style="max-width: 180px;">
                                    <option value="short">Short Answer</option>
                                    <option value="paragraph">Paragraph</option>
                                    <option value="yesno">Yes / No</option>
                                    <option value="number">Number</option>
                                    <option value="date">Date</option>
                                </select>
                                <button type="button" class="btn-crm-sec" id="btnAddQuestion"><i class="fas fa-plus"></i> Add</button>
                            </div>
                            <input type="hidden" name="qualification_questions" id="qualification_questions_json" value="[]">
                        </div>
                    </div>

                    <div class="step-footer">
                        <button type="button" class="btn-crm-sec" onclick="crmGoToStep(2)"><i class="fas fa-arrow-left"></i> Back</button>
                        <button type="button" class="btn-crm-pri" onclick="crmGoToStep(4)">Review &amp; Publish <i class="fas fa-arrow-right"></i></button>
                    </div>
                </div>

                {{-- ================= STEP 4: REVIEW & PUBLISH ================= --}}
                <div class="step-content" id="step4">
                    <div class="review-grid mb-4">
                        <div class="crm-card mb-0">
                            <div class="crm-card-header"><div class="crm-card-title">1. Offering Summary</div></div>
                            <div class="crm-card-body">
                                <div class="rv-row"><span class="rv-label">Class / Type</span><span class="rv-val" id="rvClassType">-</span></div>
                                <div class="rv-row"><span class="rv-label">Name</span><span class="rv-val" id="rvName">-</span></div>
                                <div class="rv-row"><span class="rv-label">Category</span><span class="rv-val" id="rvCategory">-</span></div>
                                <div class="rv-row"><span class="rv-label">Status</span><span class="rv-val" id="rvStatus">-</span></div>
                            </div>
                        </div>
                        <div class="crm-card mb-0">
                            <div class="crm-card-header"><div class="crm-card-title">2. Commercials &amp; Delivery</div></div>
                            <div class="crm-card-body">
                                <div class="rv-row"><span class="rv-label">Pricing</span><span class="rv-val" id="rvPricing">-</span></div>
                                <div class="rv-row"><span class="rv-label">Model / Cycle</span><span class="rv-val" id="rvModel">-</span></div>
                                <div class="rv-row"><span class="rv-label">Delivery</span><span class="rv-val" id="rvDelivery">-</span></div>
                                <div class="rv-row"><span class="rv-label">Availability</span><span class="rv-val" id="rvAvailability">-</span></div>
                            </div>
                        </div>
                        <div class="crm-card mb-0">
                            <div class="crm-card-header"><div class="crm-card-title">3. Catalog Visibility &amp; Notes</div></div>
                            <div class="crm-card-body">
                                <label class="d-flex align-items-center gap-2 mb-2" style="font-size:12px; cursor:pointer;">
                                    <input type="checkbox" name="visibility_internal_crm" value="1" checked> Internal Sales Team (CRM Only)
                                </label>
                                <label class="d-flex align-items-center gap-2 mb-2" style="font-size:12px; cursor:pointer;">
                                    <input type="checkbox" name="visibility_client_portal" value="1" checked> Shareable in Client Proposals / Portal
                                </label>
                                <label class="d-flex align-items-center gap-2 mb-3" style="font-size:12px; cursor:pointer;">
                                    <input type="checkbox" name="visibility_public_catalog" value="1"> Public Service / Product Catalog
                                </label>
                                <ul style="padding-left:16px; font-size:11px; color:var(--crm-text-muted); margin:0;">
                                    <li>No coin deduction for internal CRM catalog</li>
                                    <li>Status can be toggled anytime</li>
                                    <li>Linked deals &amp; quotes are preserved</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="step-footer">
                        <button type="button" class="btn-crm-sec" onclick="crmGoToStep(3)"><i class="fas fa-arrow-left"></i> Back</button>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn-crm-sec" id="btnBottomSaveDraft"><i class="fas fa-save"></i> Save Draft</button>
                            <button type="button" class="btn-crm-pri" id="btnBottomPublish"><i class="fas fa-paper-plane"></i> {{ $isEdit ? 'Update Offering' : 'Publish Offering' }}</button>
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

            // Stepper Navigation
            window.crmGoToStep = function (stepNum) {
                document.querySelectorAll('#crmwrap .step-content').forEach(function (el) { el.classList.remove('active'); });
                document.getElementById('step' + stepNum).classList.add('active');

                document.querySelectorAll('#stepper .step-v2').forEach(function (el) {
                    const s = parseInt(el.getAttribute('data-step'), 10);
                    el.classList.remove('current', 'completed');
                    if (s === stepNum) el.classList.add('current');
                    else if (s < stepNum) el.classList.add('completed');
                });

                if (stepNum === 4) populateReview();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            };

            // Offering Class Toggle (Product vs Service)
            const classInput = document.getElementById('offering_class');
            const productSpecs = document.getElementById('productOnlySpecs');
            const serviceSpecs = document.getElementById('serviceOnlySpecs');
            const specsTitle = document.getElementById('conditionalSpecsTitle');

            function applyOfferingClass(cls) {
                classInput.value = cls;
                if (cls === 'product') {
                    productSpecs.classList.remove('d-none');
                    serviceSpecs.classList.add('d-none');
                    specsTitle.textContent = 'Product Specifications (SKU, Unit, Warranty & Media)';
                } else {
                    productSpecs.classList.add('d-none');
                    serviceSpecs.classList.remove('d-none');
                    specsTitle.textContent = 'Service Specifications (Duration, Implementation & SLA)';
                }
            }

            document.querySelectorAll('#offeringClassSelector .choice-pill').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.querySelectorAll('#offeringClassSelector .choice-pill').forEach(function (b) { b.classList.remove('is-selected'); });
                    btn.classList.add('is-selected');
                    applyOfferingClass(btn.getAttribute('data-val'));
                });
            });

            // Single-select choice grids
            function bindSingleChoice(containerId, hiddenId, onChange) {
                const container = document.getElementById(containerId);
                const hidden = document.getElementById(hiddenId);
                container.querySelectorAll('.choice-pill').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        container.querySelectorAll('.choice-pill').forEach(function (b) { b.classList.remove('is-selected'); });
                        btn.classList.add('is-selected');
                        hidden.value = btn.getAttribute('data-val');
                        if (onChange) onChange(hidden.value);
                    });
                });
            }

            const freqSelect = document.getElementById('pricing_frequency');
            bindSingleChoice('negotiableSelector', 'is_negotiable');
            bindSingleChoice('deliveryModelSelector', 'delivery_model');
            bindSingleChoice('availabilitySelector', 'service_availability');
            bindSingleChoice('coverageScopePills', 'coverage_scope');
            bindSingleChoice('pricingModelSelector', 'pricing_model', function (val) {
                if (val === 'Fixed Price') {
                    freqSelect.value = 'One-Time';
                    freqSelect.disabled = true;
                } else {
                    freqSelect.disabled = false;
                }
            });
            freqSelect.disabled = true; // default Fixed Price

            bindSingleChoice('validitySelector', 'validity_days', function (val) {
                const dateEl = document.getElementById('valid_until');
                dateEl.classList.toggle('d-none', val !== 'custom');
            });

            // Multi-select Target Segments
            const segments = [];
            document.querySelectorAll('#segmentPills .choice-pill').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const val = btn.getAttribute('data-seg');
                    const idx = segments.indexOf(val);
                    if (idx === -1) { segments.push(val); btn.classList.add('is-selected'); }
                    else { segments.splice(idx, 1); btn.classList.remove('is-selected'); }
                    document.getElementById('target_segments').value = JSON.stringify(segments);
                });
            });

            // Unlimited Capacity Toggle
            const capInput = document.getElementById('available_capacity');
            const unlimChk = document.getElementById('is_unlimited_capacity');
            unlimChk.addEventListener('change', function () {
                capInput.disabled = unlimChk.checked;
            });

            // Confidential Pricing Bypass
            const visSelect = document.getElementById('pricing_visibility');
            visSelect.addEventListener('change', function () {
                const isConf = visSelect.value === 'confidential';
                document.querySelectorAll('.pricing-input-row').forEach(function (el) {
                    el.style.display = isConf ? 'none' : 'flex';
                });
            });

            // Industry ID sync
            document.getElementById('crm_category').addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                document.getElementById('industry_id').value = (opt && opt.getAttribute('data-industry-id')) || '';
            });

            // Reusable Chip Manager
            function initChipManager(boxId, inputId, hiddenId, quickPillsId) {
                const items = [];
                const box = document.getElementById(boxId);
                const input = document.getElementById(inputId);
                const hidden = document.getElementById(hiddenId);

                function render() {
                    box.querySelectorAll('.chip-item').forEach(function (c) { c.remove(); });
                    items.forEach(function (val, i) {
                        const chip = document.createElement('span');
                        chip.className = 'chip-item';
                        chip.innerHTML = '<span></span><button type="button" data-i="' + i + '">&times;</button>';
                        chip.querySelector('span').textContent = val;
                        box.insertBefore(chip, input);
                    });
                    hidden.value = JSON.stringify(items);
                }
                function add(val) {
                    const v = val.trim();
                    if (v && items.indexOf(v) === -1) { items.push(v); render(); }
                }

                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter' || e.key === ',') {
                        e.preventDefault();
                        add(input.value.replace(/,/g, ''));
                        input.value = '';
                    }
                });
                box.addEventListener('click', function (e) {
                    if (e.target.tagName === 'BUTTON') {
                        items.splice(parseInt(e.target.getAttribute('data-i'), 10), 1);
                        render();
                    } else input.focus();
                });
                if (quickPillsId) {
                    document.querySelectorAll('#' + quickPillsId + ' .choice-pill').forEach(function (btn) {
                        btn.addEventListener('click', function () { add(btn.getAttribute('data-val')); });
                    });
                }
                return { add: add, getItems: function () { return items; } };
            }

            initChipManager('locationsBox', 'locationInput', 'locations_json');
            initChipManager('eligibilityBox', 'eligibilityInput', 'eligibility_criteria_json', 'eligibilityQuickPills');
            initChipManager('featureTagsBox', 'featureTagInput', 'feature_tags_json', 'featureQuickPills');

            // Rich Text Editor
            const descEditor = document.getElementById('crmDescriptionEditor');
            const descHidden = document.getElementById('crm_description');
            document.querySelectorAll('#step2 .toolbar-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.execCommand(btn.getAttribute('data-cmd'), false, null);
                    descEditor.focus();
                });
            });

            // Qualification Questions
            const questions = [];
            const qContainer = document.getElementById('questionsContainer');
            const qHidden = document.getElementById('qualification_questions_json');

            function renderQuestions() {
                qContainer.innerHTML = '';
                questions.forEach(function (q, idx) {
                    const row = document.createElement('div');
                    row.className = 'd-flex align-items-center justify-content-between p-2 mb-2 border rounded';
                    row.innerHTML = '<div><strong>' + (idx + 1) + '.</strong> <span class="q-txt"></span> <small class="text-muted">(' + q.answer_type + ')</small></div>' +
                        '<button type="button" class="btn-crm-sec" style="padding:4px 10px;" data-idx="' + idx + '">&times;</button>';
                    row.querySelector('.q-txt').textContent = q.question;
                    row.querySelector('button').addEventListener('click', function () {
                        questions.splice(idx, 1);
                        renderQuestions();
                    });
                    qContainer.appendChild(row);
                });
                qHidden.value = JSON.stringify(questions);
            }

            document.getElementById('btnAddQuestion').addEventListener('click', function () {
                const inp = document.getElementById('customQuestionInput');
                const typ = document.getElementById('customQuestionType').value;
                if (inp.value.trim()) {
                    questions.push({ question: inp.value.trim(), answer_type: typ, is_required: false });
                    inp.value = '';
                    renderQuestions();
                }
            });
            document.querySelectorAll('#discoveryTemplates .choice-pill').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    questions.push({ question: btn.getAttribute('data-q'), answer_type: 'short', is_required: false });
                    renderQuestions();
                });
            });

            // Team Roster JSON sync
            document.getElementById('teamMemberSelect').addEventListener('change', function () {
                const arr = [];
                Array.from(this.selectedOptions).forEach(function (opt, i) {
                    arr.push({ user_id: opt.value, name: opt.textContent.trim(), role_type: i === 0 ? 'owner' : 'collaborator', is_lead: i === 0 });
                });
                document.getElementById('team_members_json').value = JSON.stringify(arr);
            });

            // Live SKU check
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
                document.getElementById('rvClassType').textContent = classInput.value.toUpperCase() + ' / ' + document.getElementById('offering_type').value;
                document.getElementById('rvName').textContent = document.getElementById('offering_name').value || '-';
                document.getElementById('rvCategory').textContent = document.getElementById('crm_category').value || '-';
                document.getElementById('rvStatus').textContent = document.getElementById('offering_status').value;
                const isConf = visSelect.value === 'confidential';
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
                        const msg = data.message || 'Validation failed. Please check required fields.';
                        if (typeof toastr !== 'undefined') toastr.error(msg);
                        else alert(msg);
                    }
                })
                .catch(function () { alert('Network error while saving offering.'); });
            }

            document.getElementById('btnTopSaveDraft').addEventListener('click', function () { submitOffering(true); });
            document.getElementById('btnBottomSaveDraft').addEventListener('click', function () { submitOffering(true); });
            document.getElementById('btnTopPublish').addEventListener('click', function () { submitOffering(false); });
            document.getElementById('btnBottomPublish').addEventListener('click', function () { submitOffering(false); });
        })();
    </script>
@endpush
