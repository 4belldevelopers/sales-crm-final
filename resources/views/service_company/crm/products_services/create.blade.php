@extends('layouts.service_company_layout')

@push('head-script')
    <link href="{{ asset('demo/v7/css/plugins.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="{{ asset('demo/v7/css/style.bundle.css') }}<?php echo TM_VERSION; ?>" rel="stylesheet" type="text/css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --crm-primary: #9a6500;
            --crm-primary-hover: #7a5000;
            --crm-primary-light: #fff9df;
            --crm-primary-border: #e6af00;
            --crm-bg: #f8fafc;
            --crm-header-bg: linear-gradient(180deg, #fffdf7 0%, #fff9df 100%);
            --crm-header-border: #ebd99f;
            --crm-card-bg: #ffffff;
            --crm-border: #dce2ed;
            --crm-text: #10143b;
            --crm-text-sec: #475569;
            --crm-text-muted: #687795;
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

        /* Cards with Warm Amber Header Background matching Job Create Design */
        #crmwrap .crm-card { background: #fff; border: 1px solid var(--crm-header-border); border-radius: 12px; margin-bottom: 16px; overflow: hidden; box-shadow: 0 1px 3px rgba(16, 20, 59, 0.04); }
        #crmwrap .crm-card-header {
            background: var(--crm-header-bg); border-bottom: 1px solid var(--crm-header-border); padding: 12px 20px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        #crmwrap .crm-card-title { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: var(--crm-text); }
        #crmwrap .crm-card-title i {
            width: 28px; height: 28px; border-radius: 7px; background: #ffe394; color: #9a6500;
            display: inline-flex; align-items: center; justify-content: center; font-size: 13px;
        }
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

        /* Custom Popovers & Comboboxes (Name Templates, Status Icon Dropdown, Currency Popover, Team Picker) */
        #crmwrap .crm-rel { position: relative; }
        #crmwrap .crm-popover {
            position: absolute; top: calc(100% + 4px); left: 0; right: 0; z-index: 60;
            background: #fff; border: 1px solid var(--crm-border); border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12); display: none; max-height: 280px; overflow-y: auto;
        }
        #crmwrap .crm-popover.open { display: block; }
        #crmwrap .crm-pop-head {
            padding: 8px 12px; background: var(--crm-header-bg); border-bottom: 1px solid var(--crm-border);
            font-size: 11px; font-weight: 600; color: var(--crm-text-muted); text-transform: uppercase; letter-spacing: 0.03em;
        }
        #crmwrap .crm-pop-item {
            padding: 9px 12px; display: flex; align-items: center; justify-content: space-between; gap: 10px;
            cursor: pointer; font-size: 12.5px; border-bottom: 1px solid #f1f5f9;
            transition: background-color 140ms var(--crm-ease);
        }
        #crmwrap .crm-pop-item:hover { background: var(--crm-bg); }
        #crmwrap .crm-pop-item.is-active { background: var(--crm-primary-light); color: var(--crm-primary); font-weight: 600; }
        #crmwrap .crm-trigger-btn {
            width: 100%; padding: 9px 12px; border: 1px solid var(--crm-border); border-radius: 6px; background: #fff;
            display: flex; align-items: center; justify-content: space-between; gap: 8px; cursor: pointer;
            font-family: 'Poppins', sans-serif; font-size: 13px; color: var(--crm-text); text-align: left;
        }
        #crmwrap .crm-trigger-btn:focus { border-color: var(--crm-primary); box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12); outline: none; }
        #crmwrap .crm-status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        #crmwrap .crm-team-list { border: 1px solid var(--crm-border); border-radius: 6px; max-height: 150px; overflow-y: auto; background: #fff; }
        #crmwrap .crm-team-row {
            display: flex; align-items: center; justify-content: space-between; gap: 8px; padding: 7px 10px;
            border-bottom: 1px solid #f1f5f9; cursor: pointer; font-size: 12.5px;
        }
        #crmwrap .crm-team-row:hover { background: var(--crm-bg); }
        #crmwrap .crm-lead-badge {
            font-size: 10px; font-weight: 600; padding: 2px 7px; border-radius: 999px;
            background: #fef3c7; color: #92400e; border: 1px solid #fcd34d;
        }

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
        $tzAbbr = $tenantTimezoneAbbr ?? \Carbon\Carbon::now($tz)->format('T');
        $offeringPayload = null;
        if ($isEdit) {
            $offeringPayload = [
                'id' => $offering->id,
                'offering_class' => $offering->offering_class,
                'offering_type' => $offering->offering_type,
                'owner_client_id' => $offering->owner_client_id,
                'address_id' => $offering->address_id,
                'name' => $offering->name,
                'crm_category' => $offering->crm_category,
                'industry_id' => $offering->industry_id,
                'target_segments' => $offering->target_segments ?? [],
                'available_capacity' => $offering->available_capacity,
                'is_unlimited_capacity' => (bool) $offering->is_unlimited_capacity,
                'status' => $offering->status,
                'pricing_visibility' => $offering->pricing_visibility,
                'min_price' => $offering->min_price,
                'max_price' => $offering->max_price,
                'currency_code' => $offering->currency_code,
                'pricing_model' => $offering->pricing_model,
                'pricing_frequency' => $offering->pricing_frequency,
                'is_negotiable' => (bool) $offering->is_negotiable,
                'delivery_model' => $offering->delivery_model,
                'service_availability' => $offering->service_availability,
                'coverage_scope' => $offering->coverage_scope,
                'validity_days' => $offering->validity_days,
                'valid_until' => $offering->valid_until ? $offering->valid_until->format('Y-m-d') : null,
                'sku' => $offering->sku,
                'unit' => $offering->unit,
                'warranty' => $offering->warranty,
                'brochure_path' => $offering->brochure_path,
                'service_duration' => $offering->service_duration,
                'implementation_timeline' => $offering->implementation_timeline,
                'sla_details' => $offering->sla_details,
                'scope_of_work' => $offering->scope_of_work,
                'deliverables' => $offering->deliverables,
                'eligibility_criteria' => $offering->eligibility_criteria ?? [],
                'description' => $offering->description,
                'features_scope' => $offering->features_scope,
                'specifications_eligibility' => $offering->specifications_eligibility,
                'customer_benefits' => $offering->customer_benefits,
                'additional_info' => $offering->additional_info,
                'visibility_internal_crm' => (bool) $offering->visibility_internal_crm,
                'visibility_client_portal' => (bool) $offering->visibility_client_portal,
                'visibility_public_catalog' => (bool) $offering->visibility_public_catalog,
                'locations' => $offering->locations->pluck('location_name')->values()->all(),
                'feature_tags' => $offering->tags->pluck('tag_name')->values()->all(),
                'qualification_questions' => $offering->questions->map(fn ($q) => [
                    'question' => $q->question,
                    'answer_type' => $q->answer_type,
                    'is_required' => (bool) $q->is_required,
                ])->values()->all(),
                'team_members' => $offering->team->map(fn ($t) => [
                    'user_id' => (string) $t->user_id,
                    'name' => $t->member_name,
                    'role_type' => $t->role_type,
                    'is_lead' => (bool) $t->is_lead,
                ])->values()->all(),
            ];
        }
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

                                <div class="form-group-cus crm-rel" data-field="name">
                                    <label class="form-label required" for="offering_name">Product Name</label>
                                    <input type="text" class="form-input" id="offering_name" name="name" maxlength="255" value="{{ $offering->name ?? '' }}" placeholder="Search standard CRM templates or type custom name..." autocomplete="off" required>
                                    <div class="crm-popover" id="crmTemplateDropdown">
                                        <div class="crm-pop-head">Standard CRM Templates (Click to Auto-Fill)</div>
                                        <div id="crmTemplateList"></div>
                                    </div>
                                    <span class="form-error">Product Name is required.</span>
                                </div>

                                <div class="form-group-cus" data-field="sku">
                                    <label class="form-label" for="sku">Product SKU / Code</label>
                                    <input type="text" class="form-input" id="sku" name="sku" value="{{ $offering->sku ?? '' }}" placeholder="e.g. JERO-CRM-001" style="text-transform: uppercase;">
                                    <span class="form-hint" id="skuHint">Unique within your company catalog.</span>
                                    <span class="form-error" id="skuError">SKU already exists in your company catalog.</span>
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
                                    <select class="form-select" id="address_id" name="address_id">
                                        <option value="">Optional for Digital Delivery / Cloud-based</option>
                                        @foreach(($clientAddresses ?? collect()) as $addr)
                                            <option value="{{ $addr->id }}" data-client-id="{{ $addr->client_id }}" data-city="{{ $addr->city }}">
                                                {{ trim(($addr->address ?? '') . ($addr->city ? ', ' . $addr->city : '') . ($addr->postcode ? ' - ' . $addr->postcode : ''), ', ') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="form-hint">Selecting a physical address auto-syncs its city for On-site / Physical delivery.</span>
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

                                <div class="form-group-cus crm-rel" data-field="status">
                                    <label class="form-label required" for="offering_status">Product / Service Status</label>
                                    <input type="hidden" id="offering_status" name="status" value="active">
                                    <button type="button" class="crm-trigger-btn" id="statusTriggerBtn">
                                        <span class="d-flex align-items-center gap-2" id="statusTriggerLabel">
                                            <i class="fas fa-check-circle text-success"></i> <span>Active</span>
                                        </span>
                                        <i class="fas fa-chevron-down text-muted" style="font-size:11px;"></i>
                                    </button>
                                    <div class="crm-popover" id="statusPopover">
                                        <div class="crm-pop-head">Select Offering Status</div>
                                        @foreach([
                                            ['val' => 'draft', 'label' => 'Draft', 'icon' => 'fa-file-alt', 'color' => '#64748b'],
                                            ['val' => 'active', 'label' => 'Active', 'icon' => 'fa-check-circle', 'color' => '#059669'],
                                            ['val' => 'inactive', 'label' => 'Inactive', 'icon' => 'fa-pause-circle', 'color' => '#d97706'],
                                            ['val' => 'temporarily_unavailable', 'label' => 'Temporarily Unavailable', 'icon' => 'fa-clock', 'color' => '#ea580c'],
                                            ['val' => 'discontinued', 'label' => 'Discontinued', 'icon' => 'fa-ban', 'color' => '#dc2626'],
                                        ] as $st)
                                            <div class="crm-pop-item {{ $st['val'] === 'active' ? 'is-active' : '' }}" data-status="{{ $st['val'] }}" data-label="{{ $st['label'] }}" data-icon="{{ $st['icon'] }}" data-color="{{ $st['color'] }}">
                                                <span class="d-flex align-items-center gap-2"><i class="fas {{ $st['icon'] }}" style="color:{{ $st['color'] }};"></i> {{ $st['label'] }}</span>
                                            </div>
                                        @endforeach
                                    </div>
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

                                <div class="form-group-cus crm-rel">
                                    <label class="form-label required" for="currency_code">Currency</label>
                                    <select class="d-none" id="currency_code" name="currency_code">
                                        @foreach($currencyList as $cCode)
                                            @php $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($cCode); @endphp
                                            <option value="{{ $cCode }}" {{ strtoupper((string)$cCode) === strtoupper((string)$defaultCurrency) ? 'selected' : '' }}>
                                                {{ $cMeta['label'] ?? $cCode }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="button" class="crm-trigger-btn" id="currencyTriggerBtn">
                                        <span id="currencyTriggerLabel">{{ $defaultCurrency }}</span>
                                        <i class="fas fa-chevron-down text-muted" style="font-size:11px;"></i>
                                    </button>
                                    <div class="crm-popover" id="currencyPopover" style="padding:8px;">
                                        <input type="text" class="form-input mb-2" id="currencySearchInput" placeholder="Search currency (INR, USD, EUR)..." style="padding:6px 10px; font-size:12px;">
                                        <div class="choice-grid mb-2" id="currencyQuickChips">
                                            @foreach(['INR', 'USD', 'EUR', 'GBP', 'AED', 'SGD'] as $qc)
                                                <button type="button" class="choice-pill" style="padding:4px 9px; font-size:11px;" data-cur="{{ $qc }}">{{ $qc }}</button>
                                            @endforeach
                                        </div>
                                        <div id="currencyListItems">
                                            @foreach($currencyList as $cCode)
                                                @php $cMeta = \App\Helper\GeneralMethod::currencyEnterpriseMeta($cCode); @endphp
                                                <div class="crm-pop-item" data-cur="{{ $cCode }}" data-label="{{ $cMeta['label'] ?? $cCode }}">
                                                    <span>{{ $cMeta['label'] ?? $cCode }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
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
                                    <span class="form-hint" id="minPriceWord"></span>
                                    <span class="form-error">Base / Minimum Price is required.</span>
                                </div>

                                <div class="form-group-cus pricing-input-row" data-field="max_price">
                                    <label class="form-label" for="max_price">Maximum Price (Optional Range)</label>
                                    <input type="number" step="0.01" min="0" class="form-input" id="max_price" name="max_price" placeholder="0.00">
                                    <span class="form-hint" id="maxPriceWord"></span>
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
                                        <input type="text" class="chip-input-bare" id="locationInput" placeholder="Add city/region and press Enter (max 15)...">
                                    </div>
                                    <div class="choice-grid mt-1" id="popularHubsPills">
                                        @foreach(['Mumbai', 'Bengaluru', 'Delhi NCR', 'Hyderabad', 'Pune', 'London', 'New York', 'Dubai', 'Singapore'] as $hub)
                                            <button type="button" class="choice-pill" style="padding:4px 9px; font-size:11px;" data-val="{{ $hub }}">+ {{ $hub }}</button>
                                        @endforeach
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
                                    <div class="form-hint mt-1" id="validityPreview">Ongoing / No Expiry ({{ $tzAbbr }})</div>
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
                            <div class="form-grid" id="productOnlySpecs">
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
                                    @if($isEdit && !empty($offering->brochure_path))
                                        <span class="form-hint">Current file: <a href="{{ asset($offering->brochure_path) }}" target="_blank" rel="noopener">View uploaded brochure</a></span>
                                    @endif
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
                                <div class="form-group-cus full-width">
                                    <label class="form-label" for="scope_of_work">Scope of Work</label>
                                    <textarea class="form-textarea" id="scope_of_work" name="scope_of_work" placeholder="Define boundaries, phases, and responsibilities included in this service..."></textarea>
                                </div>
                                <div class="form-group-cus full-width">
                                    <label class="form-label" for="deliverables">Deliverables</label>
                                    <textarea class="form-textarea" id="deliverables" name="deliverables" placeholder="Concrete outputs, reports, or milestones delivered to the client..."></textarea>
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
                                    <label class="form-label" for="teamSearchInput">Sales &amp; Delivery Team (First selected = Lead Owner)</label>
                                    <select class="d-none" id="teamMemberSelect" multiple>
                                        @foreach(($teamRoster ?? []) as $tm)
                                            <option value="{{ $tm['id'] ?? '' }}">{{ $tm['name'] ?? ($tm['email'] ?? 'Team Member') }}</option>
                                        @endforeach
                                    </select>
                                    <input type="text" class="form-input mb-1" id="teamSearchInput" placeholder="Search team members..." style="padding:6px 10px; font-size:12px;">
                                    <div class="crm-team-list" id="crmTeamList">
                                        @forelse(($teamRoster ?? []) as $tm)
                                            <label class="crm-team-row" data-name="{{ strtolower($tm['name'] ?? ($tm['email'] ?? '')) }}">
                                                <span class="d-flex align-items-center gap-2">
                                                    <input type="checkbox" class="crm-team-chk" value="{{ $tm['id'] ?? '' }}" data-label="{{ $tm['name'] ?? ($tm['email'] ?? 'Team Member') }}">
                                                    <span>{{ $tm['name'] ?? ($tm['email'] ?? 'Team Member') }}</span>
                                                </span>
                                                <span class="crm-lead-badge d-none">Lead Owner</span>
                                            </label>
                                        @empty
                                            <div class="p-2 text-muted" style="font-size:12px;">No team members found.</div>
                                        @endforelse
                                    </div>
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
                                <div class="d-flex justify-content-between align-items-center mt-1">
                                    <span class="form-error">Product / Service Description is required.</span>
                                    <span class="form-hint" id="charCount">0 characters</span>
                                </div>
                                <textarea name="description" id="crm_description" class="d-none"></textarea>
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
                                    <option value="multiple">Multiple Choice</option>
                                    <option value="number">Number</option>
                                    <option value="date">Date</option>
                                    <option value="phone">Phone</option>
                                    <option value="email">Email</option>
                                </select>
                                <label class="d-flex align-items-center gap-1 mb-0 px-2" style="white-space:nowrap; font-size:12px; cursor:pointer;">
                                    <input type="checkbox" id="customQuestionRequired"> Required
                                </label>
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
                            <div class="crm-card-header">
                                <div class="crm-card-title">1. Offering Summary</div>
                                <button type="button" class="btn-crm-sec" style="padding:3px 10px; font-size:11px;" onclick="crmGoToStep(1)">Edit</button>
                            </div>
                            <div class="crm-card-body">
                                <div class="rv-row"><span class="rv-label">Class / Type</span><span class="rv-val" id="rvClassType">-</span></div>
                                <div class="rv-row"><span class="rv-label">Owner</span><span class="rv-val" id="rvOwner">-</span></div>
                                <div class="rv-row"><span class="rv-label">Name</span><span class="rv-val" id="rvName">-</span></div>
                                <div class="rv-row"><span class="rv-label">Category</span><span class="rv-val" id="rvCategory">-</span></div>
                                <div class="rv-row"><span class="rv-label">Segments</span><span class="rv-val" id="rvSegments">-</span></div>
                                <div class="rv-row"><span class="rv-label">Capacity</span><span class="rv-val" id="rvCapacity">-</span></div>
                                <div class="rv-row"><span class="rv-label">Status</span><span class="rv-val" id="rvStatus">-</span></div>
                            </div>
                        </div>
                        <div class="crm-card mb-0">
                            <div class="crm-card-header">
                                <div class="crm-card-title">2. Commercials &amp; Delivery</div>
                                <button type="button" class="btn-crm-sec" style="padding:3px 10px; font-size:11px;" onclick="crmGoToStep(1)">Edit</button>
                            </div>
                            <div class="crm-card-body">
                                <div class="rv-row"><span class="rv-label">Pricing</span><span class="rv-val" id="rvPricing">-</span></div>
                                <div class="rv-row"><span class="rv-label">Model / Cycle</span><span class="rv-val" id="rvModel">-</span></div>
                                <div class="rv-row"><span class="rv-label">Negotiable</span><span class="rv-val" id="rvNegotiable">-</span></div>
                                <div class="rv-row"><span class="rv-label">Delivery</span><span class="rv-val" id="rvDelivery">-</span></div>
                                <div class="rv-row"><span class="rv-label">Availability</span><span class="rv-val" id="rvAvailability">-</span></div>
                                <div class="rv-row"><span class="rv-label">Validity</span><span class="rv-val" id="rvValidity">-</span></div>
                                <div class="rv-row"><span class="rv-label">Specs / SLA</span><span class="rv-val" id="rvSpecs">-</span></div>
                            </div>
                        </div>
                        <div class="crm-card mb-0">
                            <div class="crm-card-header">
                                <div class="crm-card-title">3. Scope, Visibility &amp; Notes</div>
                                <button type="button" class="btn-crm-sec" style="padding:3px 10px; font-size:11px;" onclick="crmGoToStep(2)">Edit</button>
                            </div>
                            <div class="crm-card-body">
                                <div class="rv-row"><span class="rv-label">Feature Tags</span><span class="rv-val" id="rvTagsCount">0</span></div>
                                <div class="rv-row mb-2"><span class="rv-label">Discovery Questions</span><span class="rv-val" id="rvQuestionsCount">0</span></div>
                                <label class="d-flex align-items-center gap-2 mb-2" style="font-size:12px; cursor:pointer;">
                                    <input type="checkbox" id="visibility_internal_crm" name="visibility_internal_crm" value="1" checked> Internal Sales Team (CRM Only)
                                </label>
                                <label class="d-flex align-items-center gap-2 mb-2" style="font-size:12px; cursor:pointer;">
                                    <input type="checkbox" id="visibility_client_portal" name="visibility_client_portal" value="1" checked> Shareable in Client Proposals / Portal
                                </label>
                                <label class="d-flex align-items-center gap-2 mb-3" style="font-size:12px; cursor:pointer;">
                                    <input type="checkbox" id="visibility_public_catalog" name="visibility_public_catalog" value="1"> Public Service / Product Catalog
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
            const EDIT_OFFERING = @json($offeringPayload);
            const TZ_ABBR = @json($tzAbbr);
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
                document.querySelectorAll('#offeringClassSelector .choice-pill').forEach(function (b) {
                    b.classList.toggle('is-selected', b.getAttribute('data-val') === cls);
                });
                if (cls === 'product') {
                    productSpecs.classList.remove('d-none');
                    serviceSpecs.classList.add('d-none');
                    specsTitle.textContent = 'Product Specifications (SKU, Unit, Warranty & Media)';
                } else {
                    productSpecs.classList.add('d-none');
                    serviceSpecs.classList.remove('d-none');
                    specsTitle.textContent = 'Service Specifications (Duration, Implementation, SLA, Scope & Deliverables)';
                }
            }

            document.querySelectorAll('#offeringClassSelector .choice-pill').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    applyOfferingClass(btn.getAttribute('data-val'));
                });
            });

            // Single-select choice grids
            function setSingleChoice(containerId, hiddenId, val, onChange) {
                const container = document.getElementById(containerId);
                const hidden = document.getElementById(hiddenId);
                hidden.value = val;
                container.querySelectorAll('.choice-pill').forEach(function (b) {
                    b.classList.toggle('is-selected', b.getAttribute('data-val') === String(val));
                });
                if (onChange) onChange(String(val));
            }

            function bindSingleChoice(containerId, hiddenId, onChange) {
                const container = document.getElementById(containerId);
                container.querySelectorAll('.choice-pill').forEach(function (btn) {
                    btn.addEventListener('click', function () {
                        setSingleChoice(containerId, hiddenId, btn.getAttribute('data-val'), onChange);
                    });
                });
            }

            const freqSelect = document.getElementById('pricing_frequency');
            function handlePricingModelChange(val) {
                if (val === 'Fixed Price') {
                    freqSelect.value = 'One-Time';
                    freqSelect.disabled = true;
                } else {
                    freqSelect.disabled = false;
                }
            }

            function updateValidityPreview() {
                const val = document.getElementById('validity_days').value;
                const preview = document.getElementById('validityPreview');
                const customDate = document.getElementById('valid_until').value;
                if (val === 'ongoing') {
                    preview.textContent = 'Ongoing / No Expiry (' + TZ_ABBR + ')';
                    return;
                }
                let targetDate = null;
                if (val === 'custom' && customDate) {
                    targetDate = new Date(customDate + 'T23:59:00');
                } else if (['30', '60', '90'].includes(val)) {
                    targetDate = new Date();
                    targetDate.setDate(targetDate.getDate() + parseInt(val, 10));
                }
                if (targetDate && !isNaN(targetDate.getTime())) {
                    const fmt = targetDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: '2-digit' });
                    preview.textContent = 'Valid until: ' + fmt + ', 11:59 PM ' + TZ_ABBR;
                } else {
                    preview.textContent = 'Select a custom expiry date (' + TZ_ABBR + ')';
                }
            }

            bindSingleChoice('negotiableSelector', 'is_negotiable');
            bindSingleChoice('deliveryModelSelector', 'delivery_model');
            bindSingleChoice('availabilitySelector', 'service_availability');
            bindSingleChoice('coverageScopePills', 'coverage_scope');
            bindSingleChoice('pricingModelSelector', 'pricing_model', handlePricingModelChange);
            freqSelect.disabled = true;

            bindSingleChoice('validitySelector', 'validity_days', function (val) {
                document.getElementById('valid_until').classList.toggle('d-none', val !== 'custom');
                updateValidityPreview();
            });
            document.getElementById('valid_until').addEventListener('change', updateValidityPreview);

            // Multi-select Target Segments
            const segments = [];
            function syncSegmentPills() {
                document.querySelectorAll('#segmentPills .choice-pill').forEach(function (btn) {
                    btn.classList.toggle('is-selected', segments.indexOf(btn.getAttribute('data-seg')) !== -1);
                });
                document.getElementById('target_segments').value = JSON.stringify(segments);
            }
            document.querySelectorAll('#segmentPills .choice-pill').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    const val = btn.getAttribute('data-seg');
                    const idx = segments.indexOf(val);
                    if (idx === -1) segments.push(val);
                    else segments.splice(idx, 1);
                    syncSegmentPills();
                });
            });

            // Unlimited Capacity Toggle
            const capInput = document.getElementById('available_capacity');
            const unlimChk = document.getElementById('is_unlimited_capacity');
            unlimChk.addEventListener('change', function () {
                capInput.disabled = unlimChk.checked;
                if (unlimChk.checked) clearFieldError('available_capacity');
            });

            // Confidential Pricing Bypass
            const visSelect = document.getElementById('pricing_visibility');
            function syncPricingVisibility() {
                const isConf = visSelect.value === 'confidential';
                document.querySelectorAll('.pricing-input-row').forEach(function (el) {
                    el.style.display = isConf ? 'none' : 'flex';
                });
                if (isConf) {
                    clearFieldError('min_price');
                    clearFieldError('max_price');
                }
            }
            visSelect.addEventListener('change', syncPricingVisibility);

            // Industry ID sync
            document.getElementById('crm_category').addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                document.getElementById('industry_id').value = (opt && opt.getAttribute('data-industry-id')) || '';
                clearFieldError('crm_category');
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
                    const v = String(val || '').trim();
                    if (v && items.indexOf(v) === -1) { items.push(v); render(); }
                }
                function setAll(arr) {
                    items.length = 0;
                    (arr || []).forEach(function (v) {
                        const t = String(v || '').trim();
                        if (t && items.indexOf(t) === -1) items.push(t);
                    });
                    render();
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
                return { add: add, setAll: setAll, getItems: function () { return items; } };
            }

            const locManager = initChipManager('locationsBox', 'locationInput', 'locations_json', 'popularHubsPills');
            const eligManager = initChipManager('eligibilityBox', 'eligibilityInput', 'eligibility_criteria_json', 'eligibilityQuickPills');
            const tagManager = initChipManager('featureTagsBox', 'featureTagInput', 'feature_tags_json', 'featureQuickPills');

            // 1. Product / Service Name Template Combobox
            const CRM_TEMPLATES = [
                { name: 'Enterprise SaaS Subscription', cls: 'product', type: 'Software / SaaS', cat: 'Software / SaaS', desc: '<p>Multi-tenant cloud software subscription with role-based access, API integrations, and automated backups.</p>', tags: ['Multi-Tenant', 'API Integration', 'SSO / SAML', 'Cloud Native'] },
                { name: 'Managed Cloud Operations (24x7)', cls: 'service', type: 'Managed Service', cat: 'IT & Cloud', desc: '<p>Round-the-clock infrastructure monitoring, incident response, patching, and SLA-backed reliability engineering.</p>', tags: ['24/7 Support', 'Custom SLA', 'ISO 27001'] },
                { name: 'IT Staffing & Augmentation Retainer', cls: 'service', type: 'Staffing / Recruitment Service', cat: 'Staffing & Managed Services', desc: '<p>Dedicated vetted engineering and product specialists embedded into your delivery pods on monthly retainers.</p>', tags: ['Custom SLA', '24/7 Support'] },
                { name: 'AI Workflow Automation Suite', cls: 'product', type: 'Digital Product', cat: 'AI & Automation', desc: '<p>Pre-built intelligent automation pipelines for CRM lead scoring, document extraction, and omnichannel routing.</p>', tags: ['API Integration', 'Cloud Native', 'Multi-Tenant'] },
                { name: 'Enterprise Advisory & Implementation', cls: 'service', type: 'Consulting', cat: 'Consulting & Advisory', desc: '<p>End-to-end architecture discovery, data migration, team onboarding, and go-live governance.</p>', tags: ['Custom SLA', 'SSO / SAML'] }
            ];
            const nameInput = document.getElementById('offering_name');
            const tplDropdown = document.getElementById('crmTemplateDropdown');
            const tplList = document.getElementById('crmTemplateList');

            function renderTemplates(filterText) {
                const q = String(filterText || '').trim().toLowerCase();
                tplList.innerHTML = '';
                const matches = CRM_TEMPLATES.filter(function (t) { return !q || t.name.toLowerCase().includes(q) || t.cat.toLowerCase().includes(q); });
                if (!matches.length) {
                    tplList.innerHTML = '<div class="p-2 text-muted" style="font-size:12px;">Custom offering name — continue typing.</div>';
                    return;
                }
                matches.forEach(function (t) {
                    const item = document.createElement('div');
                    item.className = 'crm-pop-item';
                    item.innerHTML = '<span></span><small class="text-muted"></small>';
                    item.querySelector('span').textContent = t.name;
                    item.querySelector('small').textContent = t.cls.toUpperCase() + ' • ' + t.cat;
                    item.addEventListener('click', function () {
                        nameInput.value = t.name;
                        applyOfferingClass(t.cls);
                        document.getElementById('offering_type').value = t.type;
                        document.getElementById('crm_category').value = t.cat;
                        if (!descEditor.textContent.trim()) {
                            descEditor.innerHTML = t.desc;
                            updateCharCount();
                        }
                        t.tags.forEach(function (tg) { tagManager.add(tg); });
                        tplDropdown.classList.remove('open');
                        clearFieldError('name');
                        clearFieldError('crm_category');
                    });
                    tplList.appendChild(item);
                });
            }
            nameInput.addEventListener('focus', function () { renderTemplates(nameInput.value); tplDropdown.classList.add('open'); });
            nameInput.addEventListener('input', function () { renderTemplates(nameInput.value); tplDropdown.classList.add('open'); });

            // 2. Custom Icon Status Dropdown
            const statusHidden = document.getElementById('offering_status');
            const statusTrigger = document.getElementById('statusTriggerBtn');
            const statusLabel = document.getElementById('statusTriggerLabel');
            const statusPopover = document.getElementById('statusPopover');

            function setStatusValue(val) {
                const item = statusPopover.querySelector('[data-status="' + val + '"]') || statusPopover.querySelector('[data-status="active"]');
                if (!item) return;
                statusHidden.value = item.getAttribute('data-status');
                statusLabel.innerHTML = '<i class="fas ' + item.getAttribute('data-icon') + '" style="color:' + item.getAttribute('data-color') + ';"></i> <span>' + item.getAttribute('data-label') + '</span>';
                statusPopover.querySelectorAll('.crm-pop-item').forEach(function (el) { el.classList.toggle('is-active', el === item); });
            }
            statusTrigger.addEventListener('click', function (e) { e.stopPropagation(); statusPopover.classList.toggle('open'); });
            statusPopover.querySelectorAll('.crm-pop-item').forEach(function (item) {
                item.addEventListener('click', function () {
                    setStatusValue(item.getAttribute('data-status'));
                    statusPopover.classList.remove('open');
                });
            });

            // 3. Currency Popover & Live Price-in-Words
            const curSelect = document.getElementById('currency_code');
            const curTrigger = document.getElementById('currencyTriggerBtn');
            const curLabel = document.getElementById('currencyTriggerLabel');
            const curPopover = document.getElementById('currencyPopover');
            const curSearch = document.getElementById('currencySearchInput');

            function setCurrencyValue(code) {
                const upper = String(code || 'INR').toUpperCase();
                curSelect.value = upper;
                const match = curPopover.querySelector('#currencyListItems [data-cur="' + upper + '"]');
                curLabel.textContent = match ? match.getAttribute('data-label') : upper;
                curPopover.querySelectorAll('#currencyListItems .crm-pop-item').forEach(function (el) {
                    el.classList.toggle('is-active', el.getAttribute('data-cur') === upper);
                });
                updatePriceWords();
            }
            curTrigger.addEventListener('click', function (e) {
                e.stopPropagation();
                curPopover.classList.toggle('open');
                if (curPopover.classList.contains('open')) curSearch.focus();
            });
            curSearch.addEventListener('input', function () {
                const q = curSearch.value.trim().toLowerCase();
                curPopover.querySelectorAll('#currencyListItems .crm-pop-item').forEach(function (el) {
                    el.style.display = !q || el.getAttribute('data-label').toLowerCase().includes(q) ? 'flex' : 'none';
                });
            });
            curPopover.querySelectorAll('[data-cur]').forEach(function (el) {
                el.addEventListener('click', function () {
                    setCurrencyValue(el.getAttribute('data-cur'));
                    curPopover.classList.remove('open');
                });
            });
            setCurrencyValue(curSelect.value || 'INR');

            function formatAmountWords(val, cur) {
                const n = parseFloat(val);
                if (isNaN(n) || n <= 0) return '';
                if (cur === 'INR') {
                    if (n >= 10000000) return (n / 10000000).toFixed(2) + ' Crore INR';
                    if (n >= 100000) return (n / 100000).toFixed(2) + ' Lakh INR';
                    if (n >= 1000) return (n / 1000).toFixed(1) + ' Thousand INR';
                } else {
                    if (n >= 1000000000) return (n / 1000000000).toFixed(2) + ' Billion ' + cur;
                    if (n >= 1000000) return (n / 1000000).toFixed(2) + ' Million ' + cur;
                    if (n >= 1000) return (n / 1000).toFixed(1) + ' Thousand ' + cur;
                }
                return n.toLocaleString() + ' ' + cur;
            }
            function updatePriceWords() {
                const cur = curSelect.value || 'INR';
                document.getElementById('minPriceWord').textContent = formatAmountWords(document.getElementById('min_price').value, cur);
                document.getElementById('maxPriceWord').textContent = formatAmountWords(document.getElementById('max_price').value, cur);
            }
            document.getElementById('min_price').addEventListener('input', updatePriceWords);
            document.getElementById('max_price').addEventListener('input', updatePriceWords);

            // Close popovers on outside click
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.crm-rel')) {
                    tplDropdown.classList.remove('open');
                    statusPopover.classList.remove('open');
                    curPopover.classList.remove('open');
                }
            });

            // Address filter by Owner + city auto-sync for physical/on-site delivery
            const ownerSelect = document.getElementById('owner_client_id');
            const addressSelect = document.getElementById('address_id');
            function filterAddressesByOwner() {
                const ownerId = ownerSelect.value;
                Array.from(addressSelect.options).forEach(function (opt) {
                    if (!opt.value) return;
                    const match = !ownerId || opt.getAttribute('data-client-id') === ownerId;
                    opt.hidden = !match;
                });
                const curOpt = addressSelect.options[addressSelect.selectedIndex];
                if (curOpt && curOpt.hidden) addressSelect.value = '';
            }
            ownerSelect.addEventListener('change', filterAddressesByOwner);
            addressSelect.addEventListener('change', function () {
                const opt = this.options[this.selectedIndex];
                const city = opt ? opt.getAttribute('data-city') : '';
                const dm = document.getElementById('delivery_model').value;
                if (city && !['Cloud-based', 'Digital Delivery', 'Not Applicable'].includes(dm)) {
                    locManager.add(city);
                }
            });
            filterAddressesByOwner();

            // Rich Text Editor + Live Char Count
            const descEditor = document.getElementById('crmDescriptionEditor');
            const descHidden = document.getElementById('crm_description');
            const charCountEl = document.getElementById('charCount');
            function updateCharCount() {
                const txt = (descEditor.textContent || '').trim();
                charCountEl.textContent = txt.length + ' characters';
                if (txt.length > 0) clearFieldError('description');
            }
            descEditor.addEventListener('input', updateCharCount);
            document.querySelectorAll('#step2 .toolbar-btn').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    document.execCommand(btn.getAttribute('data-cmd'), false, null);
                    descEditor.focus();
                    updateCharCount();
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
                        '<div class="d-flex align-items-center gap-2">' +
                        '<label class="d-flex align-items-center gap-1 mb-0" style="font-size:11px;cursor:pointer;"><input type="checkbox" class="q-req"> Required</label>' +
                        '<button type="button" class="btn-crm-sec q-del" style="padding:4px 10px;" data-idx="' + idx + '">&times;</button></div>';
                    row.querySelector('.q-txt').textContent = q.question;
                    const reqChk = row.querySelector('.q-req');
                    reqChk.checked = Boolean(q.is_required);
                    reqChk.addEventListener('change', function () {
                        questions[idx].is_required = reqChk.checked;
                        qHidden.value = JSON.stringify(questions);
                    });
                    row.querySelector('.q-del').addEventListener('click', function () {
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
                const req = document.getElementById('customQuestionRequired').checked;
                if (inp.value.trim()) {
                    questions.push({ question: inp.value.trim(), answer_type: typ, is_required: req });
                    inp.value = '';
                    document.getElementById('customQuestionRequired').checked = false;
                    renderQuestions();
                }
            });
            document.querySelectorAll('#discoveryTemplates .choice-pill').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    questions.push({ question: btn.getAttribute('data-q'), answer_type: 'short', is_required: false });
                    renderQuestions();
                });
            });

            // 5. Searchable Sales & Delivery Team Picker with Lead Owner badge
            const teamSelect = document.getElementById('teamMemberSelect');
            const teamSearch = document.getElementById('teamSearchInput');
            function syncTeamJson() {
                const arr = [];
                let firstFound = false;
                document.querySelectorAll('#crmTeamList .crm-team-row').forEach(function (row) {
                    const chk = row.querySelector('.crm-team-chk');
                    const badge = row.querySelector('.crm-lead-badge');
                    const opt = teamSelect.querySelector('option[value="' + chk.value + '"]');
                    if (opt) opt.selected = chk.checked;
                    if (chk.checked) {
                        const isLead = !firstFound;
                        firstFound = true;
                        badge.classList.toggle('d-none', !isLead);
                        arr.push({ user_id: chk.value, name: chk.getAttribute('data-label'), role_type: isLead ? 'owner' : 'collaborator', is_lead: isLead });
                    } else {
                        badge.classList.add('d-none');
                    }
                });
                document.getElementById('team_members_json').value = JSON.stringify(arr);
            }
            document.querySelectorAll('#crmTeamList .crm-team-chk').forEach(function (chk) {
                chk.addEventListener('change', syncTeamJson);
            });
            teamSearch.addEventListener('input', function () {
                const q = teamSearch.value.trim().toLowerCase();
                document.querySelectorAll('#crmTeamList .crm-team-row').forEach(function (row) {
                    row.style.display = !q || row.getAttribute('data-name').includes(q) ? 'flex' : 'none';
                });
            });

            // Live SKU check
            const skuInput = document.getElementById('sku');
            skuInput.addEventListener('blur', function () {
                const v = skuInput.value.trim().toUpperCase();
                if (!v) { clearFieldError('sku'); return; }
                fetch(CHECK_SKU_URL + '?sku=' + encodeURIComponent(v) + '&exclude_id=' + EDIT_ID, { headers: { 'Accept': 'application/json' } })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (res.available === false) setFieldError('sku', res.message || 'SKU already exists in your company catalog.');
                        else clearFieldError('sku');
                    });
            });

            // Validation helpers & 422 mapping
            function setFieldError(field, msg) {
                const grp = document.querySelector('[data-field="' + field + '"]');
                if (!grp) return;
                grp.classList.add('has-error');
                const err = grp.querySelector('.form-error');
                if (err && msg) err.textContent = msg;
            }
            function clearFieldError(field) {
                const grp = document.querySelector('[data-field="' + field + '"]');
                if (grp) grp.classList.remove('has-error');
            }
            function clearAllErrors() {
                document.querySelectorAll('#crmwrap .form-group-cus.has-error').forEach(function (g) { g.classList.remove('has-error'); });
            }

            ['offering_name', 'min_price', 'max_price', 'available_capacity'].forEach(function (id) {
                const el = document.getElementById(id);
                if (el) el.addEventListener('input', function () { clearFieldError(el.name); });
            });

            function validateBeforeSubmit(isDraft) {
                clearAllErrors();
                let firstErrorStep = null;
                let firstErrorEl = null;

                function fail(field, msg, step) {
                    setFieldError(field, msg);
                    if (!firstErrorStep) {
                        firstErrorStep = step;
                        firstErrorEl = document.querySelector('[data-field="' + field + '"]');
                    }
                }

                if (!document.getElementById('offering_name').value.trim()) {
                    fail('name', 'Product / Service Name is required.', 1);
                }

                if (!isDraft) {
                    if (!document.getElementById('crm_category').value) {
                        fail('crm_category', 'Product Category / Industry is required.', 1);
                    }
                    if (!unlimChk.checked) {
                        const cap = parseInt(capInput.value, 10);
                        if (!cap || cap < 1) fail('available_capacity', 'Capacity must be at least 1 unless Unlimited is checked.', 1);
                    }
                    if (visSelect.value !== 'confidential') {
                        const minP = document.getElementById('min_price').value;
                        const maxP = document.getElementById('max_price').value;
                        if (minP === '' || parseFloat(minP) < 0) {
                            fail('min_price', 'Base / Minimum Price is required.', 1);
                        } else if (maxP !== '' && parseFloat(maxP) < parseFloat(minP)) {
                            fail('max_price', 'Maximum Price must be greater than or equal to Minimum Price.', 1);
                        }
                    }
                    const descPlain = (descEditor.textContent || '').trim();
                    if (!descPlain) {
                        fail('description', 'Product / Service Description is required.', 2);
                    }
                }

                if (firstErrorStep) {
                    window.crmGoToStep(firstErrorStep);
                    if (firstErrorEl) firstErrorEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return false;
                }
                return true;
            }

            function populateReview() {
                const ownerOpt = ownerSelect.options[ownerSelect.selectedIndex];
                document.getElementById('rvClassType').textContent = classInput.value.toUpperCase() + ' / ' + document.getElementById('offering_type').value;
                document.getElementById('rvOwner').textContent = ownerOpt ? ownerOpt.textContent.trim() : 'My Organization';
                document.getElementById('rvName').textContent = document.getElementById('offering_name').value || '-';
                document.getElementById('rvCategory').textContent = document.getElementById('crm_category').value || '-';
                document.getElementById('rvSegments').textContent = segments.length ? segments.join(', ') : 'All / Unspecified';
                document.getElementById('rvCapacity').textContent = unlimChk.checked ? 'Unlimited' : (capInput.value || '-');
                document.getElementById('rvStatus').textContent = document.getElementById('offering_status').value;

                const isConf = visSelect.value === 'confidential';
                const minP = document.getElementById('min_price').value || '0';
                const maxP = document.getElementById('max_price').value;
                const cur = document.getElementById('currency_code').value;
                document.getElementById('rvPricing').textContent = isConf ? 'Price on Request' : (cur + ' ' + minP + (maxP ? ' - ' + maxP : ''));
                document.getElementById('rvModel').textContent = document.getElementById('pricing_model').value + ' (' + freqSelect.value + ')';
                document.getElementById('rvNegotiable').textContent = document.getElementById('is_negotiable').value === '1' ? 'Yes' : 'No';
                document.getElementById('rvDelivery').textContent = document.getElementById('delivery_model').value;
                document.getElementById('rvAvailability').textContent = document.getElementById('service_availability').value;
                document.getElementById('rvValidity').textContent = document.getElementById('validityPreview').textContent;
                document.getElementById('rvSpecs').textContent = classInput.value === 'product'
                    ? (skuInput.value ? 'SKU: ' + skuInput.value.toUpperCase() : 'No SKU')
                    : (document.getElementById('service_duration').value || document.getElementById('sla_details').value || 'Standard');
                document.getElementById('rvTagsCount').textContent = tagManager.getItems().length + ' tags';
                document.getElementById('rvQuestionsCount').textContent = questions.length + ' questions';
            }

            // Hydrate Edit Mode
            if (EDIT_OFFERING) {
                applyOfferingClass(EDIT_OFFERING.offering_class || 'product');
                if (EDIT_OFFERING.offering_type) document.getElementById('offering_type').value = EDIT_OFFERING.offering_type;
                if (EDIT_OFFERING.owner_client_id) {
                    ownerSelect.value = String(EDIT_OFFERING.owner_client_id);
                    filterAddressesByOwner();
                }
                if (EDIT_OFFERING.address_id) addressSelect.value = String(EDIT_OFFERING.address_id);
                document.getElementById('offering_name').value = EDIT_OFFERING.name || '';
                if (EDIT_OFFERING.crm_category) document.getElementById('crm_category').value = EDIT_OFFERING.crm_category;
                if (EDIT_OFFERING.industry_id) document.getElementById('industry_id').value = String(EDIT_OFFERING.industry_id);

                (EDIT_OFFERING.target_segments || []).forEach(function (s) {
                    if (segments.indexOf(s) === -1) segments.push(s);
                });
                syncSegmentPills();

                unlimChk.checked = Boolean(EDIT_OFFERING.is_unlimited_capacity);
                capInput.disabled = unlimChk.checked;
                if (EDIT_OFFERING.available_capacity) capInput.value = EDIT_OFFERING.available_capacity;
                if (EDIT_OFFERING.status) setStatusValue(EDIT_OFFERING.status);

                if (EDIT_OFFERING.pricing_visibility) visSelect.value = EDIT_OFFERING.pricing_visibility;
                syncPricingVisibility();
                if (EDIT_OFFERING.min_price !== null && EDIT_OFFERING.min_price !== undefined) document.getElementById('min_price').value = EDIT_OFFERING.min_price;
                if (EDIT_OFFERING.max_price !== null && EDIT_OFFERING.max_price !== undefined) document.getElementById('max_price').value = EDIT_OFFERING.max_price;
                if (EDIT_OFFERING.currency_code) setCurrencyValue(EDIT_OFFERING.currency_code);
                updatePriceWords();

                setSingleChoice('negotiableSelector', 'is_negotiable', EDIT_OFFERING.is_negotiable ? '1' : '0');
                setSingleChoice('pricingModelSelector', 'pricing_model', EDIT_OFFERING.pricing_model || 'Fixed Price', handlePricingModelChange);
                if (EDIT_OFFERING.pricing_frequency) freqSelect.value = EDIT_OFFERING.pricing_frequency;

                setSingleChoice('deliveryModelSelector', 'delivery_model', EDIT_OFFERING.delivery_model || 'Remote');
                setSingleChoice('availabilitySelector', 'service_availability', EDIT_OFFERING.service_availability || 'Business Hours');
                if (EDIT_OFFERING.coverage_scope) setSingleChoice('coverageScopePills', 'coverage_scope', EDIT_OFFERING.coverage_scope);
                setSingleChoice('validitySelector', 'validity_days', EDIT_OFFERING.validity_days || 'ongoing', function (val) {
                    document.getElementById('valid_until').classList.toggle('d-none', val !== 'custom');
                });
                if (EDIT_OFFERING.valid_until) document.getElementById('valid_until').value = EDIT_OFFERING.valid_until;
                updateValidityPreview();

                if (EDIT_OFFERING.sku) skuInput.value = EDIT_OFFERING.sku;
                if (EDIT_OFFERING.unit) document.getElementById('unit').value = EDIT_OFFERING.unit;
                if (EDIT_OFFERING.warranty) document.getElementById('warranty').value = EDIT_OFFERING.warranty;
                if (EDIT_OFFERING.service_duration) document.getElementById('service_duration').value = EDIT_OFFERING.service_duration;
                if (EDIT_OFFERING.implementation_timeline) document.getElementById('implementation_timeline').value = EDIT_OFFERING.implementation_timeline;
                if (EDIT_OFFERING.sla_details) document.getElementById('sla_details').value = EDIT_OFFERING.sla_details;
                if (EDIT_OFFERING.scope_of_work) document.getElementById('scope_of_work').value = EDIT_OFFERING.scope_of_work;
                if (EDIT_OFFERING.deliverables) document.getElementById('deliverables').value = EDIT_OFFERING.deliverables;

                locManager.setAll(EDIT_OFFERING.locations || []);
                eligManager.setAll(EDIT_OFFERING.eligibility_criteria || []);
                tagManager.setAll(EDIT_OFFERING.feature_tags || []);

                descEditor.innerHTML = EDIT_OFFERING.description || '';
                updateCharCount();
                if (EDIT_OFFERING.features_scope) document.getElementById('features_scope').value = EDIT_OFFERING.features_scope;
                if (EDIT_OFFERING.customer_benefits) document.getElementById('customer_benefits').value = EDIT_OFFERING.customer_benefits;
                if (EDIT_OFFERING.specifications_eligibility) document.getElementById('specifications_eligibility').value = EDIT_OFFERING.specifications_eligibility;
                if (EDIT_OFFERING.additional_info) document.getElementById('additional_info').value = EDIT_OFFERING.additional_info;

                (EDIT_OFFERING.qualification_questions || []).forEach(function (q) { questions.push(q); });
                renderQuestions();

                const teamIds = (EDIT_OFFERING.team_members || []).map(function (t) { return String(t.user_id); });
                document.querySelectorAll('#crmTeamList .crm-team-chk').forEach(function (chk) { chk.checked = teamIds.indexOf(chk.value) !== -1; });
                syncTeamJson();

                document.getElementById('visibility_internal_crm').checked = Boolean(EDIT_OFFERING.visibility_internal_crm);
                document.getElementById('visibility_client_portal').checked = Boolean(EDIT_OFFERING.visibility_client_portal);
                document.getElementById('visibility_public_catalog').checked = Boolean(EDIT_OFFERING.visibility_public_catalog);
            }

            function submitOffering(isDraft) {
                descHidden.value = descEditor.innerHTML;
                if (isDraft) setStatusValue('draft');

                if (!validateBeforeSubmit(isDraft)) return;

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
                            Object.keys(data.errors).forEach(function (field) {
                                setFieldError(field, data.errors[field][0]);
                            });
                        }
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
