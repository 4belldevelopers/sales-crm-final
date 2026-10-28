@extends('layouts.superadmin_layout')

@push('head-script')
    <style>
        /* ==========================================================================
           JERO ENTERPRISE DESIGN SYSTEM: NOTIFICATION AUTHORING STUDIO
           Reference: docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md
           Target Viewport: 15.6" Laptops (1366x768 & 1920x1080) with Zero Horizontal Overflow
           ========================================================================== */
        body {
            overflow-x: hidden !important;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            background-color: #f5f8fa;
        }

        /* ── Top Gap Collapse: eliminate excessive Metronic toolbar & wrapper spacing on standard 15.6" viewports ── */
        #kt_toolbar {
            display: none !important;
            height: 0 !important;
            min-height: 0 !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .header-fixed.toolbar-fixed #kt_wrapper,
        .header-fixed #kt_wrapper,
        .toolbar-fixed #kt_wrapper,
        #kt_wrapper {
            padding-top: calc(65px + 12px) !important;
        }

        @media (max-width: 991.98px) {
            .header-fixed.toolbar-fixed #kt_wrapper,
            .header-fixed #kt_wrapper,
            .toolbar-fixed #kt_wrapper,
            #kt_wrapper {
                padding-top: calc(55px + 8px) !important;
            }
        }

        #kt_content,
        .content {
            padding-top: 0 !important;
            margin-top: 0 !important;
        }

        .authoring-header-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 14px 20px !important;
            margin-top: 0 !important;
            margin-bottom: 14px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 12px !important;
        }

        .authoring-panel-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            margin-bottom: 16px !important;
            overflow: hidden !important;
        }

        .authoring-card-header {
            padding: 12px 18px !important;
            background: #ffffff !important;
            border-bottom: 1px solid #f1f5f9 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        .authoring-card-title {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            margin: 0 !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .authoring-card-body {
            padding: 18px 20px !important;
        }

        /* 1-Click Dynamic Token Palette */
        .token-palette-box {
            background: #f8fafc !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 12px !important;
            margin-bottom: 14px !important;
        }

        .token-palette-header {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
            gap: 8px !important;
            margin-bottom: 10px !important;
            padding-bottom: 8px !important;
            border-bottom: 1px solid #e2e8f0 !important;
        }

        .token-palette-tabs {
            display: flex !important;
            align-items: center !important;
            flex-wrap: wrap !important;
            gap: 4px !important;
        }

        .token-tab-pill {
            background: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            color: #475569 !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 3px 10px !important;
            border-radius: 6px !important;
            cursor: pointer !important;
            transition: all 0.15s ease !important;
        }

        .token-tab-pill.active {
            background: #009ef7 !important;
            border-color: #009ef7 !important;
            color: #ffffff !important;
        }

        .token-grid-container {
            display: flex !important;
            flex-wrap: wrap !important;
            gap: 6px !important;
            max-height: 160px !important;
            overflow-y: auto !important;
            padding-right: 4px !important;
        }

        .token-insert-btn {
            background: #ffffff !important;
            border: 1px solid #bae6fd !important;
            color: #0284c7 !important;
            font-family: monospace !important;
            font-size: 11px !important;
            font-weight: 600 !important;
            padding: 3px 8px !important;
            border-radius: 5px !important;
            cursor: pointer !important;
            display: inline-flex !important;
            align-items: center !important;
            gap: 4px !important;
            transition: all 0.15s ease !important;
            user-select: none !important;
        }

        .token-insert-btn:hover {
            background: #0284c7 !important;
            border-color: #0284c7 !important;
            color: #ffffff !important;
            transform: translateY(-1px) !important;
            box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2) !important;
        }

        .token-insert-btn i {
            font-size: 9px !important;
            opacity: 0.7 !important;
        }

        /* Role Selection Checkbox List */
        .role-checklist-box {
            max-height: 280px !important;
            overflow-y: auto !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 10px 14px !important;
            background: #f8fafc !important;
        }

        .role-check-item {
            display: flex !important;
            align-items: center !important;
            padding: 6px 0 !important;
            border-bottom: 1px dashed #e2e8f0 !important;
        }

        .role-check-item:last-child {
            border-bottom: none !important;
        }

        /* Form Controls */
        .form-label-custom {
            font-size: 11.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
            color: #475569 !important;
            margin-bottom: 6px !important;
        }
    </style>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid pt-0 pb-6" id="kt_content">
    <div class="container-fluid px-4" id="kt_content_container">

        @php
            $isEditing = !empty($th_email_template && $th_email_template->id > 0);
            $templateId = $isEditing ? (int) $th_email_template->id : 0;
            $mailPermissionsArray = $mail_permissions ?? [];
        @endphp

        <!-- Top Header Card -->
        <div class="authoring-header-card">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('superadmin.company.notification') }}" class="btn btn-sm btn-icon btn-light-primary" title="Return to Notifications Catalog">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="fs-4 fw-bolder text-gray-900 mb-0">
                        {{ $isEditing ? 'Edit Email Template: ' . ($th_email_template->purpose ?? '') : 'Create New Email Notification Template' }}
                    </h1>
                    <p class="fs-7 text-muted mb-0">
                        {{ $isEditing ? "Template ID: #NOTIF-" . str_pad($templateId, 3, '0', STR_PAD_LEFT) . " • Strictly CQRS & Outbox Audited" : "Provision a new automated platform notification template" }}
                    </p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @if ($isEditing)
                    <button type="button" class="btn btn-sm btn-light-info fw-bold" id="btnQuickTestPreview">
                        <i class="fas fa-eye me-1"></i> Preview Template
                    </button>
                @endif
                <button type="button" id="btnSaveTemplate" class="btn btn-sm btn-primary fw-bold px-4">
                    <i class="fas fa-save me-1"></i> Save Changes
                </button>
            </div>
        </div>

        <!-- Main Form Container -->
        <form id="notificationForm" class="ajax-form">
            @csrf
            <input type="hidden" name="tokenId" id="tokenId" value="{{ $templateId }}">

            <div class="row g-4">
                <!-- LEFT COLUMN: Content & Template Authoring (8 Columns) -->
                <div class="col-lg-8">
                    <!-- Template Identity & Subject Panel -->
                    <div class="authoring-panel-card">
                        <div class="authoring-card-header">
                            <h3 class="authoring-card-title">
                                <i class="fas fa-envelope-open-text text-primary"></i>
                                Notification Identity & Header
                            </h3>
                            @if ($isEditing)
                                <span class="badge badge-light-primary fw-bolder font-mono">
                                    #NOTIF-{{ str_pad($templateId, 3, '0', STR_PAD_LEFT) }}
                                </span>
                            @endif
                        </div>
                        <div class="authoring-card-body">
                            <div class="mb-4">
                                <label class="form-label-custom" for="purpose">
                                    Template Purpose / Action Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-solid" 
                                       name="purpose" 
                                       id="purpose" 
                                       placeholder="e.g. Job Closed(Team Notification)" 
                                       value="{{ old('purpose', $th_email_template->purpose ?? '') }}" 
                                       maxlength="255" 
                                       required>
                                <div class="form-text text-muted fs-8">Internal administrative description of what event triggers this notification.</div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label-custom" for="subject">
                                    Email Subject Line <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <input type="text" 
                                           class="form-control form-control-solid" 
                                           name="subject" 
                                           id="subject" 
                                           placeholder="e.g. Position Automatically Closed | [{JOB_TITLE}] | [{ClientCompany}]" 
                                           value="{{ old('subject', $th_email_template->subject ?? '') }}" 
                                           maxlength="255" 
                                           required>
                                </div>
                                <div class="form-text text-muted fs-8">Subject line seen by recipient. Dynamic merge tokens are supported.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Template Body & 1-Click Dynamic Token Palette -->
                    <div class="authoring-panel-card">
                        <div class="authoring-card-header">
                            <h3 class="authoring-card-title">
                                <i class="fas fa-code text-primary"></i>
                                Template Body & 96 Dynamic Merge Tokens
                            </h3>
                            <span class="fs-8 text-muted">Click any token pill to insert into cursor position</span>
                        </div>
                        <div class="authoring-card-body">
                            
                            <!-- 1-Click Dynamic Token Palette -->
                            <div class="token-palette-box">
                                <div class="token-palette-header">
                                    <div class="token-palette-tabs" id="paletteDomainTabs">
                                        <button type="button" class="token-tab-pill active" data-domain="all">All (96)</button>
                                        <button type="button" class="token-tab-pill" data-domain="job">Job & Requisition</button>
                                        <button type="button" class="token-tab-pill" data-domain="candidate">Candidate & Bench</button>
                                        <button type="button" class="token-tab-pill" data-domain="people">People & Roles</button>
                                        <button type="button" class="token-tab-pill" data-domain="partner">Partners & Clients</button>
                                        <button type="button" class="token-tab-pill" data-domain="action">Action Links</button>
                                    </div>
                                    <div style="width: 180px;">
                                        <input type="text" id="paletteSearch" class="form-control form-control-sm form-control-solid py-1 px-2 fs-8" placeholder="Filter tokens...">
                                    </div>
                                </div>

                                <div class="token-grid-container" id="tokenGrid">
                                    @if (!empty($token_palette))
                                        @foreach ($token_palette as $domainKey => $tokensList)
                                            @foreach ($tokensList as $item)
                                                <button type="button" 
                                                        class="token-insert-btn" 
                                                        data-token="{{ $item['token'] }}" 
                                                        data-domain="{{ $domainKey }}" 
                                                        title="{{ $item['label'] }} (Sample: {{ $item['mock'] }})">
                                                    <span>{{ $item['token'] }}</span>
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            @endforeach
                                        @endforeach
                                    @endif
                                </div>
                            </div>

                            <!-- Rich Text Editor (CKEditor) -->
                            <div class="mb-2">
                                <label class="form-label-custom" for="editor1">
                                    Template HTML Body <span class="text-danger">*</span>
                                </label>
                                <textarea id="editor1" name="editor1" rows="12">{{ $th_email_template->body_description ?? '' }}</textarea>
                                <div class="form-text text-muted fs-8 mt-2">
                                    Server-side allowlist sanitization actively neutralizes scripts, dangerous protocols, and unauthorized event handlers.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Metadata, Routing & RBAC Roles (4 Columns) -->
                <div class="col-lg-4">
                    <!-- Dispatch Category & Status -->
                    <div class="authoring-panel-card">
                        <div class="authoring-card-header">
                            <h3 class="authoring-card-title">
                                <i class="fas fa-sliders-h text-primary"></i>
                                Routing & Gateway
                            </h3>
                        </div>
                        <div class="authoring-card-body">
                            <!-- Category Select -->
                            <div class="mb-4">
                                <label class="form-label-custom" for="mailType_is">
                                    Notification Category <span class="text-danger">*</span>
                                </label>
                                @php
                                    $currentType = (int) ($th_email_template->mailType ?? 1);
                                @endphp
                                <select name="mailType_is" id="mailType_is" class="form-select form-select-solid" required>
                                    <option value="1" {{ $currentType === 1 ? 'selected' : '' }}>1. Recruitment Notification</option>
                                    <option value="2" {{ $currentType === 2 ? 'selected' : '' }}>2. Sales Notification</option>
                                    <option value="3" {{ $currentType === 3 ? 'selected' : '' }}>3. Mass Mail Template</option>
                                    <option value="4" {{ $currentType === 4 ? 'selected' : '' }}>4. Candidate Send Email</option>
                                    <option value="5" {{ $currentType === 5 ? 'selected' : '' }}>5. Client Send Email</option>
                                    <option value="6" {{ $currentType === 6 ? 'selected' : '' }}>6. Offer Accept</option>
                                    <option value="7" {{ $currentType === 7 ? 'selected' : '' }}>7. Offer Reject</option>
                                    <option value="8" {{ $currentType === 8 ? 'selected' : '' }}>8. Offer Approved</option>
                                    <option value="9" {{ $currentType === 9 ? 'selected' : '' }}>9. Offer Not Approved</option>
                                    <option value="10" {{ $currentType === 10 ? 'selected' : '' }}>10. Offer Approved HR</option>
                                </select>
                            </div>

                            <!-- Sender Email -->
                            <div class="mb-4">
                                <label class="form-label-custom" for="from_email_id">
                                    Sender Email Address <span class="text-danger">*</span>
                                </label>
                                <input type="email" 
                                       class="form-control form-control-solid" 
                                       name="from_email_id" 
                                       id="from_email_id" 
                                       value="{{ old('from_email_id', $th_email_template->from_email_id ?? 'noreply@jero.co.in') }}" 
                                       required>
                                <div class="form-text text-muted fs-8">Routed via dedicated Transactional Gateway #11.</div>
                            </div>

                            <!-- Operational Status -->
                            <div class="mb-2">
                                <label class="form-label-custom" for="status">
                                    Dispatch Status <span class="text-danger">*</span>
                                </label>
                                @php
                                    $currentStatus = (int) ($th_email_template->status ?? 1);
                                @endphp
                                <select name="status" id="status" class="form-select form-select-solid" required>
                                    <option value="1" {{ $currentStatus === 1 ? 'selected' : '' }}>Active (Operational Triggers)</option>
                                    <option value="0" {{ $currentStatus === 0 ? 'selected' : '' }}>Inactive (Suppressed)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Role Permissions (RBAC) -->
                    <div class="authoring-panel-card">
                        <div class="authoring-card-header">
                            <h3 class="authoring-card-title">
                                <i class="fas fa-user-shield text-primary"></i>
                                Recipient Roles (RBAC)
                            </h3>
                            <div class="d-flex align-items-center gap-2">
                                <a href="javascript:void(0);" id="btnSelectAllRoles" class="fs-8 fw-bold text-primary">Select All</a>
                                <span class="text-muted fs-8">&bull;</span>
                                <a href="javascript:void(0);" id="btnDeselectAllRoles" class="fs-8 fw-bold text-muted">Clear</a>
                            </div>
                        </div>
                        <div class="authoring-card-body">
                            <p class="fs-8 text-muted mb-3">
                                Select authorized roles that receive this transactional notification. If zero roles are selected, notification is unrestricted.
                            </p>

                            <div class="role-checklist-box">
                                @if (!empty($roles))
                                    @foreach ($roles as $role)
                                        @php
                                            $rName = $role->display_name;
                                            $isChecked = !empty($mailPermissionsArray) && in_array($rName, $mailPermissionsArray, true);
                                            $checkboxId = 'role_' . md5($rName);
                                        @endphp
                                        <div class="role-check-item">
                                            <div class="form-check form-check-custom form-check-solid">
                                                <input type="checkbox" 
                                                       class="form-check-input role_ck" 
                                                       name="role_ck[]" 
                                                       id="{{ $checkboxId }}" 
                                                       value="{{ $rName }}" 
                                                       {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label text-gray-800 fw-semibold fs-7 ms-2" for="{{ $checkboxId }}">
                                                    {{ $rName }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-muted fs-8">No roles configured.</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Outbox & Architecture Notice -->
                    <div class="p-4 rounded-3 border" style="background: #eff6ff; border-color: #bfdbfe !important;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fas fa-database text-primary fs-5 mt-1"></i>
                            <div>
                                <div class="fs-7 fw-bolder text-gray-900 mb-1">Dual-Engine Persistence</div>
                                <div class="fs-8 text-gray-600">
                                    Changes are atomically committed to MySQL and synced to Elasticsearch via transactional outbox (<span class="font-mono">sys_outbox_events</span>).
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </form>

    </div>
</div>
@endsection

@push('footer-script')
    <script src="{{ asset('demo/ckeditor/ckeditor.js') }}"></script>
    <script>
    $(document).ready(function() {
        'use strict';

        const csrfToken = "{{ csrf_token() }}";
        const saveUrl = "{{ route('superadmin.company.addEditnotificationupdate') }}";
        const redirectUrl = "{{ route('superadmin.company.notification') }}";
        const previewUrl = "{{ url('super-admin/notification/preview') }}/{{ $templateId }}";

        // Initialize CKEditor
        CKEDITOR.replace('editor1', {
            height: 380,
            width: 'auto',
            removePlugins: 'elementspath',
            resize_enabled: true
        });

        // 1-Click Token Insertion Palette
        $(document).on('click', '.token-insert-btn', function() {
            const token = $(this).attr('data-token');
            if (CKEDITOR.instances.editor1) {
                CKEDITOR.instances.editor1.insertHtml(token);
                // Visual feedback on button
                const $btn = $(this);
                $btn.addClass('btn-success text-white').removeClass('text-info');
                setTimeout(() => {
                    $btn.removeClass('btn-success text-white');
                }, 400);
            }
        });

        // Filter Token Palette by Domain Tabs
        $('#paletteDomainTabs').on('click', '.token-tab-pill', function() {
            $('#paletteDomainTabs .token-tab-pill').removeClass('active');
            $(this).addClass('active');

            const domain = $(this).attr('data-domain');
            const search = ($('#paletteSearch').val() || '').trim().toLowerCase();

            filterTokens(domain, search);
        });

        // Filter Token Palette by Search Input
        $('#paletteSearch').on('input', function() {
            const search = $(this).val().trim().toLowerCase();
            const activeDomain = $('#paletteDomainTabs .token-tab-pill.active').attr('data-domain') || 'all';

            filterTokens(activeDomain, search);
        });

        function filterTokens(domain, query) {
            $('.token-insert-btn').each(function() {
                const btnDomain = $(this).attr('data-domain');
                const btnToken = $(this).attr('data-token').toLowerCase();

                const matchesDomain = (domain === 'all' || btnDomain === domain);
                const matchesQuery = (!query || btnToken.indexOf(query) !== -1);

                if (matchesDomain && matchesQuery) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        // Select All / Deselect All Roles
        $('#btnSelectAllRoles').on('click', function() {
            $('.role_ck').prop('checked', true);
        });

        $('#btnDeselectAllRoles').on('click', function() {
            $('.role_ck').prop('checked', false);
        });

        // Quick Test Preview Button (opens preview in modal/new window)
        $('#btnQuickTestPreview').on('click', function() {
            window.open(previewUrl, '_blank', 'width=850,height=700,scrollbars=yes');
        });

        // AJAX Form Submission
        $('#btnSaveTemplate').on('click', function() {
            const $btn = $(this);
            const purpose = $('#purpose').val().trim();
            const subject = $('#subject').val().trim();
            const fromEmail = $('#from_email_id').val().trim();
            const mailType = $('#mailType_is').val();
            const status = $('#status').val();
            const tokenId = $('#tokenId').val();
            const editorContent = CKEDITOR.instances.editor1 ? CKEDITOR.instances.editor1.getData() : '';

            const selectedRoles = $('.role_ck:checked').map(function() {
                return this.value;
            }).get();

            // Client validation check
            if (!purpose) {
                Swal.fire({ icon: 'warning', title: 'Purpose Required', text: 'Please enter the notification purpose.' });
                $('#purpose').focus();
                return;
            }

            if (!subject) {
                Swal.fire({ icon: 'warning', title: 'Subject Required', text: 'Please enter the email subject line.' });
                $('#subject').focus();
                return;
            }

            if (!fromEmail) {
                Swal.fire({ icon: 'warning', title: 'Sender Email Required', text: 'Please provide a valid sender email address.' });
                $('#from_email_id').focus();
                return;
            }

            if (!editorContent || editorContent.trim() === '') {
                Swal.fire({ icon: 'warning', title: 'Template Body Required', text: 'Please enter the email template body content.' });
                return;
            }

            $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Saving...');

            $.ajax({
                url: saveUrl,
                type: 'POST',
                data: {
                    _token: csrfToken,
                    tokenId: tokenId,
                    purpose: purpose,
                    subject: subject,
                    from_email_id: fromEmail,
                    mailType_is: mailType,
                    status: status,
                    role_ck: selectedRoles,
                    editor1: editorContent
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Changes');

                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Template Saved Successfully',
                            text: 'Authoritative database and Elasticsearch projections are synchronized.',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = redirectUrl;
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Save Failed',
                            text: response.message || 'Unable to persist changes.'
                        });
                    }
                },
                error: function(xhr) {
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Save Changes');

                    let errMsg = 'An unexpected error occurred while saving.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.errors) {
                            const errList = [];
                            $.each(xhr.responseJSON.errors, function(k, v) {
                                errList.push(Array.isArray(v) ? v.join(', ') : v);
                            });
                            errMsg = errList.join('<br>');
                        } else if (xhr.responseJSON.message) {
                            errMsg = xhr.responseJSON.message;
                        }
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Failed',
                        html: errMsg
                    });
                }
            });
        });

    });
    </script>
@endpush
