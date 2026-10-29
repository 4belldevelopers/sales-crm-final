@extends('layouts.superadmin_layout')

@php
    use App\Helper\GeneralMethod;
    $isUpdate = ($th_features->id ?? 0) > 0;
@endphp

@push('head-script')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/font-awesome/css/font-awesome.min.css') }}">
    <style>
        /* ==========================================================================
           Super Admin Feature Policy Editor — Modern Enterprise Layout
           Strictly Optimized for 15.6" Screens (1366x768 & 1920x1080) and Smaller
           GUARANTEED: Zero Horizontal Scrollbar (0% Overflow) Across All Viewports
           ========================================================================== */

        /* ===== Manage Top Gap: collapse Metronic's toolbar-fixed padding and content gap ===== */
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
                padding-top: calc(55px + 8px) !important;
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
        .content {
            padding-top: 0 !important;
        }

        .ft-edit-wrapper {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        .ft-edit-wrapper *,
        .ft-edit-wrapper *::before,
        .ft-edit-wrapper *::after {
            box-sizing: border-box !important;
        }

        /* Ensure FA 4.7 matches both normal and 900 weights if CDN fails */
        @font-face {
            font-family: 'FontAwesome';
            src: url('{{ asset("assets/plugins/font-awesome/fonts/fontawesome-webfont.woff2?v=4.7.0") }}') format('woff2'),
                 url('{{ asset("assets/plugins/font-awesome/fonts/fontawesome-webfont.woff?v=4.7.0") }}') format('woff'),
                 url('{{ asset("assets/plugins/font-awesome/fonts/fontawesome-webfont.ttf?v=4.7.0") }}') format('truetype');
            font-weight: 900;
            font-style: normal;
        }

        /* Protect FontAwesome icon fonts from general typography font-family inheritance */
        .ft-edit-wrapper i,
        .ft-edit-wrapper [class*="fa-"],
        .ft-edit-wrapper [class^="fa-"],
        .ft-edit-wrapper .fa,
        .ft-edit-wrapper .fas,
        .ft-edit-wrapper .fa-solid {
            font-family: "Font Awesome 6 Free", "FontAwesome", sans-serif !important;
            font-style: normal !important;
            font-weight: 900 !important;
            display: inline-block !important;
            line-height: 1 !important;
            text-rendering: auto !important;
            -webkit-font-smoothing: antialiased !important;
            -moz-osx-font-smoothing: grayscale !important;
        }

        /* Universal glyph content guarantees: maps every FA6 class to its Unicode glyph */
        .fa-magnifying-glass:before { content: "\f002" !important; }
        .fa-xmark:before            { content: "\f00d" !important; }
        .fa-pen-to-square:before    { content: "\f044" !important; }
        .fa-clock-rotate-left:before{ content: "\f1da" !important; }
        .fa-circle-check:before     { content: "\f058" !important; }
        .fa-circle-pause:before     { content: "\f28b" !important; }
        .fa-circle-info:before      { content: "\f05a" !important; }
        .fa-shield-halved:before    { content: "\f132" !important; }
        .fa-list-check:before       { content: "\f0ae" !important; }
        .fa-rotate-left:before      { content: "\f0e2" !important; }
        .fa-pen-nib:before          { content: "\f040" !important; }
        .fa-signature:before        { content: "\f044" !important; }
        .fa-calendar-day:before     { content: "\f073" !important; }
        .fa-power-off:before        { content: "\f011" !important; }
        .fa-wand-magic-sparkles:before { content: "\f0d0" !important; }
        .fa-triangle-exclamation:before { content: "\f071" !important; }
        .fa-arrow-left:before       { content: "\f060" !important; }
        .fa-sliders:before          { content: "\f1de" !important; }
        .fa-plus:before             { content: "\f067" !important; }
        .fa-check:before            { content: "\f00c" !important; }
        .fa-coins:before            { content: "\f0d6" !important; }
        .fa-database:before         { content: "\f1c0" !important; }
        .fa-globe:before            { content: "\f0ac" !important; }
        .fa-robot:before            { content: "\f085" !important; }
        .fa-layer-group:before      { content: "\f009" !important; }
        .fa-bolt:before             { content: "\f0e7" !important; }

        /* Top Toolbar Card */
        .ft-header-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 12px 18px !important;
            margin-bottom: 14px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 14px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            flex-wrap: wrap !important;
            width: 100% !important;
        }

        .ft-header-left {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
            min-width: 0 !important;
            flex-wrap: wrap !important;
        }

        .ft-back-btn {
            height: 36px !important;
            padding: 0 14px !important;
            font-size: 12px !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            color: #475569 !important;
            text-decoration: none !important;
            transition: all 0.15s ease !important;
        }

        .ft-back-btn:hover {
            background: #e2e8f0 !important;
            color: #0f172a !important;
        }

        .ft-header-icon {
            width: 38px !important;
            height: 38px !important;
            border-radius: 9px !important;
            background: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            color: #2563eb !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 16px !important;
            flex-shrink: 0 !important;
        }

        .ft-header-title {
            font-size: 15.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
        }

        .ft-header-sub {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin-top: 2px !important;
            line-height: 1.2 !important;
        }

        .ft-save-btn {
            height: 36px !important;
            padding: 0 18px !important;
            font-size: 12.5px !important;
            font-weight: 600 !important;
            border-radius: 8px !important;
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            color: #ffffff !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 6px !important;
            transition: all 0.15s ease !important;
        }

        .ft-save-btn:hover {
            background: #0086d6 !important;
            border-color: #0086d6 !important;
            color: #ffffff !important;
        }

        /* 2-Column Responsive Workspace */
        .ft-workspace-grid {
            display: grid !important;
            grid-template-columns: minmax(0, 7fr) minmax(0, 5fr) !important;
            gap: 16px !important;
            align-items: start !important;
            width: 100% !important;
        }

        @media (max-width: 991.98px) {
            .ft-workspace-grid {
                grid-template-columns: minmax(0, 1fr) !important;
            }
        }

        .ft-editor-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            padding: 20px !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 18px !important;
        }

        .ft-subcard-box {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 16px !important;
        }

        .ft-box-label {
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.4px !important;
            color: #475569 !important;
            margin-bottom: 8px !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .ft-stepper-btn {
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 4px 10px !important;
            border-radius: 6px !important;
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #334155 !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        .ft-stepper-btn:hover {
            background: #f1f5f9 !important;
            border-color: #94a3b8 !important;
            color: #0f172a !important;
        }

        /* Simulator Card */
        .ft-simulator-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            padding: 20px !important;
            position: sticky !important;
            top: 80px !important;
        }

        .ft-sim-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding-bottom: 12px !important;
            border-bottom: 1px solid #e2e8f0 !important;
            margin-bottom: 16px !important;
        }

        .ft-sim-title {
            font-size: 13px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            display: flex !important;
            align-items: center !important;
            gap: 6px !important;
        }

        .ft-preview-bubble {
            background: #eff6ff !important;
            border: 1px solid #bfdbfe !important;
            border-radius: 8px !important;
            padding: 14px 16px !important;
            margin-bottom: 14px !important;
        }

        .ft-sim-meta-item {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 8px 0 !important;
            border-bottom: 1px solid #f1f5f9 !important;
            font-size: 11.5px !important;
        }

        .ft-sim-meta-item:last-child {
            border-bottom: none !important;
        }

        .ft-sim-label {
            color: #64748b !important;
            font-weight: 500 !important;
        }

        .ft-sim-val {
            color: #0f172a !important;
            font-weight: 600 !important;
        }
    </style>
@endpush

@section('content')
<div class="ft-edit-wrapper px-3 py-0">

    <!-- Top Toolbar Card -->
    <div class="ft-header-card">
        <div class="ft-header-left">
            <a href="{{ route('superadmin.company.featuresList') }}" class="ft-back-btn">
                <i class="fa fa-arrow-left fa-solid fa-arrow-left"></i> Back to Policies
            </a>
            <div class="ft-header-icon">
                <i class="fa fa-sliders fa-solid fa-sliders"></i>
            </div>
            <div class="d-flex flex-column">
                <span class="ft-header-title">
                    {{ $isUpdate ? 'Edit Feature Policy #' . $th_features->id : 'Create New Feature Policy' }}
                </span>
                <span class="ft-header-sub">Configure platform lifecycle rules, validity duration, and automated cron execution.</span>
            </div>
        </div>

        <div>
            <button type="button" id="save-form" class="btn ft-save-btn">
                <span class="indicator-label"><i class="fa fa-check fa-solid fa-check me-1"></i> Save Policy</span>
                <span class="indicator-progress d-none">
                    Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </div>

    <!-- 2-Column Responsive Form Workspace -->
    <form id="editSettings" class="ajax-form">
        @csrf
        <input type="hidden" name="tokenId" id="tokenId" value="{{ $th_features->id ?? 0 }}">

        <div class="ft-workspace-grid">
            
            <!-- Left Column: Form Controls (7 cols) -->
            <div class="ft-editor-card">
                
                <!-- Sub-Card 1: Policy Name -->
                <div class="ft-subcard-box">
                    <div class="ft-box-label">
                        <i class="fa fa-edit fa-solid fa-signature text-primary"></i> Policy Name / Feature Title <span class="text-danger">*</span>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-gray-300 text-muted">
                            <i class="fa fa-pencil fa-solid fa-pen-nib"></i>
                        </span>
                        <input type="text"
                               name="title"
                               id="title"
                               class="form-control form-control-solid border-gray-300 fw-semibold"
                               placeholder="e.g. Job Request expiration auto close"
                               value="{{ $th_features->title ?? '' }}"
                               required />
                    </div>
                    <div class="form-text text-muted fs-8 mt-2">
                        Enter a distinct, descriptive name identifying the system rule or ATS workflow.
                    </div>
                </div>

                <!-- Sub-Card 2: Duration / Validity Window -->
                <div class="ft-subcard-box">
                    <div class="ft-box-label">
                        <i class="fa fa-history fa-solid fa-clock-rotate-left text-primary"></i> Expiration / Validity Window (Days) <span class="text-danger">*</span>
                    </div>
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-white border-gray-300 text-muted">
                            <i class="fa fa-calendar fa-solid fa-calendar-day"></i>
                        </span>
                        <input type="number"
                               name="days"
                               id="days"
                               class="form-control form-control-solid border-gray-300 fw-bold fs-5"
                               placeholder="0"
                               min="0"
                               max="3650"
                               value="{{ $th_features->days ?? 0 }}"
                               required />
                        <span class="input-group-text bg-white border-gray-300 text-muted fw-semibold">Days</span>
                    </div>

                    <!-- Quick Stepper Buttons -->
                    <div class="d-flex align-items-center gap-1 flex-wrap mt-2">
                        <span class="text-muted fs-8 me-1">Quick Presets:</span>
                        <button type="button" class="ft-stepper-btn" data-step="-1">-1 Day</button>
                        <button type="button" class="ft-stepper-btn" data-step="1">+1 Day</button>
                        <button type="button" class="ft-stepper-btn" data-step="7">+7 Days</button>
                        <button type="button" class="ft-stepper-btn" data-step="14">+14 Days</button>
                        <button type="button" class="ft-stepper-btn" data-step="30">+30 Days</button>
                        <button type="button" class="ft-stepper-btn" data-set="0" style="background: #fffbeb !important; border-color: #fef08a !important; color: #b45309 !important;">Immediate (0d)</button>
                    </div>
                    <div class="form-text text-muted fs-8 mt-2">
                        Configure the number of inactive days before the system automatically expires or auto-closes requests. Set to <code>0</code> for real-time event gating without delays.
                    </div>
                </div>

                <!-- Sub-Card 3: Operational Status -->
                <div class="ft-subcard-box">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="ft-box-label mb-1">
                                <i class="fa fa-power-off fa-solid fa-power-off text-primary"></i> Operational Enforcement Status
                            </div>
                            <div class="text-muted fs-8">Enable or disable platform enforcement of this policy across all organizations.</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold fs-7 {{ ($th_features->status ?? 1) == 1 ? 'text-success' : 'text-muted' }}" id="statusTextDisplay">
                                {{ ($th_features->status ?? 1) == 1 ? 'Active (ON)' : 'Inactive (OFF)' }}
                            </span>
                            <div class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input cursor-pointer"
                                       type="checkbox"
                                       role="switch"
                                       id="status_switch"
                                       {{ ($th_features->status ?? 1) == 1 ? 'checked' : '' }} />
                            </div>
                            <input type="hidden" name="status_is" id="status_is" value="{{ $th_features->status ?? 1 }}" />
                        </div>
                    </div>
                </div>

            </div>

            <!-- Right Column: Live Policy Simulator (5 cols) -->
            <div class="ft-simulator-card">
                <div class="ft-sim-header">
                    <span class="ft-sim-title">
                        <i class="fa fa-magic fa-solid fa-wand-magic-sparkles text-primary"></i> Live Policy Simulator
                    </span>
                    <span class="badge badge-light-primary fw-bold" id="simRuleIdBadge">
                        {{ $isUpdate ? '#FTR-00' . $th_features->id : 'NEW RULE' }}
                    </span>
                </div>

                <div class="ft-preview-bubble">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="fa fa-shield fa-solid fa-shield-halved text-primary fs-6"></i>
                        <strong class="fs-7 text-dark" id="simTitlePreview">
                            {{ $th_features->title ?: 'Feature Policy Title Preview' }}
                        </strong>
                    </div>
                    <div class="fs-8 text-muted" id="simBehaviorPreview">
                        {{ ($th_features->status ?? 1) == 1
                            ? 'Active policy: Automatically closes or triggers actions after ' . ($th_features->days ?? 0) . ' days.'
                            : 'Inactive policy: Expiration checks are paused; pending items remain open.' }}
                    </div>
                </div>

                <div class="p-3 bg-light rounded-3 mb-3">
                    <div class="ft-sim-meta-item">
                        <span class="ft-sim-label">Policy ID</span>
                        <span class="ft-sim-val">#{{ $th_features->id ?? 'Auto' }}</span>
                    </div>
                    <div class="ft-sim-meta-item">
                        <span class="ft-sim-label">Duration Window</span>
                        <span class="ft-sim-val text-primary" id="simDaysVal">{{ $th_features->days ?? 0 }} Days</span>
                    </div>
                    <div class="ft-sim-meta-item">
                        <span class="ft-sim-label">Enforcement Mode</span>
                        <span class="ft-sim-val" id="simModeVal">
                            {{ ($th_features->days ?? 0) > 0 ? 'Cron-Automated Timeout' : 'Real-time Event Gated' }}
                        </span>
                    </div>
                    <div class="ft-sim-meta-item">
                        <span class="ft-sim-label">Tenancy Scope</span>
                        <span class="ft-sim-val text-success">Universal Multi-Tenant</span>
                    </div>
                    <div class="ft-sim-meta-item">
                        <span class="ft-sim-label">Status</span>
                        <span class="ft-sim-val" id="simStatusVal">
                            {{ ($th_features->status ?? 1) == 1 ? '✅ Active (Enforced)' : '⏸️ Inactive (Paused)' }}
                        </span>
                    </div>
                </div>

                <div class="p-3 rounded-2 text-muted fs-8" style="background: #f8fafc; border: 1px dashed #cbd5e1;">
                    <i class="fa fa-warning fa-solid fa-triangle-exclamation text-warning me-1"></i>
                    <strong>Senior Architecture Note:</strong> Modifying policy validity windows directly impacts core ATS timeout calculations without affecting candidate stage history.
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('footer-script')
<script>
    (function() {
        'use strict';

        const titleInput = document.getElementById('title');
        const daysInput = document.getElementById('days');
        const statusSwitch = document.getElementById('status_switch');
        const statusHidden = document.getElementById('status_is');
        const statusText = document.getElementById('statusTextDisplay');
        const saveBtn = document.getElementById('save-form');

        // Simulator Elements
        const simTitle = document.getElementById('simTitlePreview');
        const simBehavior = document.getElementById('simBehaviorPreview');
        const simDays = document.getElementById('simDaysVal');
        const simMode = document.getElementById('simModeVal');
        const simStatus = document.getElementById('simStatusVal');

        function updateSimulator() {
            const title = titleInput.value.trim() || 'Feature Policy Title Preview';
            const days = parseInt(daysInput.value, 10) || 0;
            const isActive = statusSwitch.checked;

            if (simTitle) simTitle.textContent = title;
            if (simDays) simDays.textContent = days + ' Days';
            if (simMode) simMode.textContent = days > 0 ? 'Cron-Automated Timeout' : 'Real-time Event Gated';

            if (simStatus) {
                simStatus.textContent = isActive ? '✅ Active (Enforced)' : '⏸️ Inactive (Paused)';
            }

            if (simBehavior) {
                if (isActive) {
                    simBehavior.textContent = days > 0
                        ? 'Active policy: Automatically closes or triggers actions after ' + days + ' days.'
                        : 'Active policy: Immediately evaluates event trigger rules without delay.';
                } else {
                    simBehavior.textContent = 'Inactive policy: Expiration checks are paused; pending items remain open indefinitely.';
                }
            }
        }

        // Live input listeners
        if (titleInput) titleInput.addEventListener('input', updateSimulator);
        if (daysInput) daysInput.addEventListener('input', updateSimulator);

        // Status switch toggle
        if (statusSwitch) {
            statusSwitch.addEventListener('change', function() {
                const isActive = this.checked;
                statusHidden.value = isActive ? '1' : '0';
                if (statusText) {
                    statusText.textContent = isActive ? 'Active (ON)' : 'Inactive (OFF)';
                    statusText.className = 'fw-bold fs-7 ' + (isActive ? 'text-success' : 'text-muted');
                }
                updateSimulator();
            });
        }

        // Quick Stepper Buttons
        document.querySelectorAll('.ft-stepper-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                let current = parseInt(daysInput.value, 10) || 0;
                if (this.hasAttribute('data-set')) {
                    current = parseInt(this.getAttribute('data-set'), 10);
                } else if (this.hasAttribute('data-step')) {
                    current += parseInt(this.getAttribute('data-step'), 10);
                }
                if (current < 0) current = 0;
                if (current > 3650) current = 3650;
                daysInput.value = current;
                updateSimulator();
            });
        });

        // AJAX Save Submission
        if (saveBtn) {
            saveBtn.addEventListener('click', function(e) {
                e.preventDefault();

                const title = titleInput.value.trim();
                const days = daysInput.value.trim();
                const statusIs = statusHidden.value;
                const tokenId = document.getElementById('tokenId').value;

                if (!title) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Missing Required Field',
                        text: 'Please enter a policy name.',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    titleInput.focus();
                    return;
                }

                if (days === '' || isNaN(days) || parseInt(days, 10) < 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Duration',
                        text: 'Please enter a valid non-negative number of days.',
                        customClass: { confirmButton: 'btn btn-primary' }
                    });
                    daysInput.focus();
                    return;
                }

                saveBtn.querySelector('.indicator-label').classList.add('d-none');
                saveBtn.querySelector('.indicator-progress').classList.remove('d-none');
                saveBtn.disabled = true;

                $.ajax({
                    type: 'POST',
                    url: "{{ route('superadmin.company.addEditfeaturesListupdate') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        tokenId: tokenId,
                        title: title,
                        days: days,
                        status_is: statusIs
                    },
                    success: function(response) {
                        saveBtn.querySelector('.indicator-label').classList.remove('d-none');
                        saveBtn.querySelector('.indicator-progress').classList.add('d-none');
                        saveBtn.disabled = false;

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Policy Saved!',
                                text: response.message || 'Feature policy saved successfully.',
                                timer: 1200,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.href = "{{ route('superadmin.company.featuresList') }}";
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Save Failed',
                                text: response.message || 'Could not save policy.',
                                customClass: { confirmButton: 'btn btn-primary' }
                            });
                        }
                    },
                    error: function(xhr) {
                        saveBtn.querySelector('.indicator-label').classList.remove('d-none');
                        saveBtn.querySelector('.indicator-progress').classList.add('d-none');
                        saveBtn.disabled = false;

                        let errorMsg = 'An unexpected server error occurred.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMsg,
                            customClass: { confirmButton: 'btn btn-primary' }
                        });
                    }
                });
            });
        }
    })();
</script>
@endpush
