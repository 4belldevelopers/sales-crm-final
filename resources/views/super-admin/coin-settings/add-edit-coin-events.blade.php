@extends('layouts.superadmin_layout')

@push('head-script')
    <style>
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

        .cf-form-wrapper, .cf-form-wrapper * {
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, sans-serif !important;
            box-sizing: border-box !important;
        }

        .cf-form-wrapper {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: hidden !important;
        }

        /* Tier 1 Header Toolbar Card */
        .cf-header-card {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            padding: 12px 18px !important;
            margin-bottom: 16px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 14px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;
            flex-wrap: wrap !important;
            width: 100% !important;
        }

        .cf-header-left {
            display: flex !important;
            align-items: center !important;
            gap: 12px !important;
        }

        .cf-header-icon {
            width: 40px !important;
            height: 40px !important;
            border-radius: 9px !important;
            background: #fffbeb !important;
            border: 1px solid #fef08a !important;
            color: #d97706 !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 18px !important;
            flex-shrink: 0 !important;
        }

        .cf-header-title {
            font-size: 15.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            line-height: 1.25 !important;
        }

        .cf-header-sub {
            font-size: 11.5px !important;
            color: #64748b !important;
            margin-top: 2px !important;
            line-height: 1.2 !important;
        }

        /* Form Master Card */
        .cf-card-container {
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 10px !important;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.03) !important;
            padding: 24px !important;
            max-width: 900px !important;
            margin: 0 auto !important;
        }

        .cf-section-title {
            font-size: 13.5px !important;
            font-weight: 700 !important;
            color: #0f172a !important;
            padding-bottom: 8px !important;
            border-bottom: 1px solid #f1f5f9 !important;
            margin-bottom: 18px !important;
            display: flex !important;
            align-items: center !important;
            gap: 8px !important;
        }

        .cf-form-label {
            font-size: 12px !important;
            font-weight: 600 !important;
            color: #334155 !important;
            margin-bottom: 6px !important;
        }

        .cf-input {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            font-size: 12.5px !important;
            color: #0f172a !important;
            background: #ffffff !important;
            height: 38px !important;
            width: 100% !important;
            outline: none !important;
            transition: all 0.15s ease !important;
        }

        .cf-input:focus {
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        }

        .cf-textarea {
            border: 1px solid #e2e8f0 !important;
            border-radius: 8px !important;
            padding: 10px 12px !important;
            font-size: 12.5px !important;
            color: #0f172a !important;
            background: #ffffff !important;
            width: 100% !important;
            outline: none !important;
            transition: all 0.15s ease !important;
            resize: vertical !important;
            min-height: 90px !important;
        }

        .cf-textarea:focus {
            border-color: #009ef7 !important;
            box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12) !important;
        }
    </style>
@endpush

@section('content')
    <div class="content d-flex flex-column flex-column-fluid pt-0 px-3 px-lg-6 cf-form-wrapper" id="kt_content">

        <!-- Tier 1 Top Header Toolbar Card -->
        <div class="cf-header-card">
            <div class="cf-header-left">
                <div class="cf-header-icon">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <div>
                    <div class="cf-header-title">
                        {{ $update ? 'Edit Coin Feature / Event' : 'Create New Coin Feature' }}
                    </div>
                    <div class="cf-header-sub">
                        {{ $update ? 'Update deduction rates, wallet gating limits, and operational rules.' : 'Define a new platform coin consumption event for ATS operations or sales.' }}
                    </div>
                </div>
            </div>
            <div>
                <a href="{{ route('superadmin.company.coinFeatures') }}" class="btn btn-sm btn-light fw-bold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Features List
                </a>
            </div>
        </div>

        <!-- Form Master Card -->
        <div class="cf-card-container">
            <form id="editSettingsForm" autocomplete="off">
                @csrf
                <input type="hidden" name="token_id" id="token_id" value="{{ ($update && isset($coin_packages->id)) ? $coin_packages->id : '' }}">

                <!-- Section 1: Event Scope & Model -->
                <div class="cf-section-title">
                    <i class="fa-solid fa-layer-group text-primary fs-7"></i>
                    <span>Feature Scope & Charging Model</span>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="cf-form-label required" for="event_type">Event Scope Type</label>
                        <select name="event_type" id="event_type" class="form-select cf-input" required>
                            <option value="">-- Select Event Scope --</option>
                            <option value="operational" {{ ($update && $coin_packages->event_type == 'operational') ? 'selected' : '' }}>
                                Operational Feature (ATS workflows, CV unlocks, emails)
                            </option>
                            <option value="sales" {{ ($update && $coin_packages->event_type == 'sales') ? 'selected' : '' }}>
                                Sales & Marketplace Lead (Auction, Bidding, Ads)
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="cf-form-label required" for="deduction_type">Deduction Cycle Model</label>
                        <select name="deduction_type" id="deduction_type" class="form-select cf-input" required>
                            <option value="">-- Select Deduction Model --</option>
                            <option value="onetime" {{ ($update && $coin_packages->deduction_type == 'onetime') ? 'selected' : '' }}>
                                One-time Pay-Per-Use (Deducted once upon trigger)
                            </option>
                            <option value="daily" {{ ($update && $coin_packages->deduction_type == 'daily') ? 'selected' : '' }}>
                                Daily Recurring (Deducted each active day)
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Section 2: Details & Pricing -->
                <div class="cf-section-title mt-4">
                    <i class="fa-solid fa-coins text-warning fs-7"></i>
                    <span>Feature Details & Coin Economics</span>
                </div>

                <div class="mb-4">
                    <label class="cf-form-label required" for="title">Feature / Event Title</label>
                    <input type="text" 
                           class="form-control cf-input" 
                           id="title" 
                           name="title" 
                           placeholder="e.g. CV Access, Mass Candidate Mail, Video Interview"
                           value="{{ ($update && isset($coin_packages->title)) ? $coin_packages->title : '' }}" 
                           required>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="cf-form-label required" for="default_rate">Default Coin Rate</label>
                        <div class="position-relative">
                            <input type="number" 
                                   step="0.01" 
                                   min="0" 
                                   class="form-control cf-input ps-9" 
                                   id="default_rate" 
                                   name="default_rate"
                                   placeholder="1.00"
                                   value="{{ ($update && isset($coin_packages->default_rate)) ? $coin_packages->default_rate : '1.00' }}" 
                                   required>
                            <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-warning">
                                <i class="fa-solid fa-coins fs-7"></i>
                            </span>
                        </div>
                        <div class="text-muted fs-8 mt-1">Number of coins charged per execution. Enter 0 for Free features.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="cf-form-label required" for="minimum_required">Minimum Required Wallet Balance</label>
                        <div class="position-relative">
                            <input type="number" 
                                   step="1" 
                                   min="0" 
                                   class="form-control cf-input ps-9" 
                                   id="minimum_required" 
                                   name="minimum_required"
                                   placeholder="0"
                                   value="{{ ($update && isset($coin_packages->minimum_required)) ? $coin_packages->minimum_required : '0' }}" 
                                   required>
                            <span class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary">
                                <i class="fa-solid fa-shield-halved fs-7"></i>
                            </span>
                        </div>
                        <div class="text-muted fs-8 mt-1">Minimum wallet balance required to access this feature (Gating rule).</div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="cf-form-label" for="description">Detailed Description</label>
                    <textarea class="form-control cf-textarea" 
                              id="description" 
                              name="description" 
                              rows="3" 
                              placeholder="Provide clear details on how this event charges coins and what feature is unlocked in the ATS pipeline...">{{ ($update && isset($coin_packages->description)) ? $coin_packages->description : '' }}</textarea>
                </div>

                <!-- Section 3: Status & Controls -->
                <div class="cf-section-title mt-4">
                    <i class="fa-solid fa-toggle-on text-success fs-7"></i>
                    <span>Operational Availability</span>
                </div>

                <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <div class="fw-bold text-dark fs-7">Active Feature Status</div>
                        <div class="text-muted fs-8">When disabled, this billing event is paused system-wide.</div>
                    </div>
                    <div class="form-check form-switch form-switch-sm form-check-custom form-check-solid m-0">
                        <input class="form-check-input" 
                               type="checkbox" 
                               name="status" 
                               id="statusSwitch" 
                               value="1" 
                               {{ (!$update || ($update && !empty($coin_packages->status))) ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- Save Actions -->
                <div class="d-flex align-items-center justify-content-end gap-3 pt-4 border-top">
                    <a href="{{ route('superadmin.company.coinFeatures') }}" class="btn btn-light fw-bold">
                        Cancel
                    </a>
                    <button type="button" id="save-form-btn" class="btn btn-primary fw-bold">
                        <span class="indicator-label">
                            <i class="fa-solid fa-floppy-disk me-1"></i>
                            {{ $update ? 'Update Feature' : 'Create Feature' }}
                        </span>
                        <span class="indicator-progress d-none">
                            Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>

            </form>
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

            $('#save-form-btn').on('click', function(e) {
                e.preventDefault();

                var $btn = $(this);
                var $form = $('#editSettingsForm');

                // Validate required fields
                var title = $.trim($('#title').val());
                var eventType = $('#event_type').val();
                var deductionType = $('#deduction_type').val();

                if (!eventType) {
                    Swal.fire('Validation Error', 'Please select an Event Scope Type.', 'warning');
                    $('#event_type').focus();
                    return;
                }
                if (!deductionType) {
                    Swal.fire('Validation Error', 'Please select a Deduction Cycle Model.', 'warning');
                    $('#deduction_type').focus();
                    return;
                }
                if (!title) {
                    Swal.fire('Validation Error', 'Please enter a Feature / Event Title.', 'warning');
                    $('#title').focus();
                    return;
                }

                var formData = new FormData($form[0]);

                $btn.find('.indicator-label').addClass('d-none');
                $btn.find('.indicator-progress').removeClass('d-none');
                $btn.prop('disabled', true);

                $.ajax({
                    url: "{{ route('superadmin.company.coinEventsStore') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        $btn.find('.indicator-label').removeClass('d-none');
                        $btn.find('.indicator-progress').addClass('d-none');
                        $btn.prop('disabled', false);

                        if (response.status === 'success') {
                            Swal.fire({
                                text: response.message || 'Coin feature saved successfully!',
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn btn-primary"
                                }
                            }).then(function() {
                                window.location = response.url || "{{ route('superadmin.company.coinFeatures') }}";
                            });
                        } else {
                            Swal.fire('Error', response.message || 'Failed to save coin feature.', 'error');
                        }
                    },
                    error: function(xhr) {
                        $btn.find('.indicator-label').removeClass('d-none');
                        $btn.find('.indicator-progress').addClass('d-none');
                        $btn.prop('disabled', false);

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            var errors = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                            Swal.fire({
                                html: '<div class="text-start"><strong class="text-danger">Validation Errors:</strong><br>' + errors + '</div>',
                                icon: 'error',
                                buttonsStyling: false,
                                confirmButtonText: 'Ok, got it!',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                }
                            });
                        } else {
                            Swal.fire('Error', 'An error occurred while saving. Please try again.', 'error');
                        }
                    }
                });
            });
        });
    </script>
@endpush
