@extends('layouts.superadmin_layout')

<style>
.cpr-stat-card {
    border-radius: 8px !important;
    border: 1px solid #EFF2F5;
    background: #ffffff;
    transition: all 0.2s ease-in-out;
}
.cpr-stat-card:hover {
    box-shadow: 0 4px 14px rgba(0,0,0,0.06);
    transform: translateY(-2px);
}
.cpr-stat-title {
    font-size: 0.85rem;
    color: #7E8299;
    font-weight: 500;
}
.cpr-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1.2;
    transition: transform 0.3s ease, color 0.3s ease;
}
.table td, .table th {
    vertical-align: middle !important;
}
.cursor-pointer {
    cursor: pointer;
}
.cpr-stat-card.active-filter-card {
    box-shadow: 0 0 0 2px #009ef7, 0 4px 14px rgba(0,158,247,0.18) !important;
    transform: translateY(-2px);
}
.cpr-hero-wrapper {
    margin-bottom: 24px !important;
}
@keyframes cprHighlightPulse {
    0% { background-color: rgba(0, 158, 247, 0.22) !important; }
    35% { background-color: rgba(80, 205, 137, 0.25) !important; }
    100% { background-color: transparent !important; }
}
.row-cpr-updated {
    animation: cprHighlightPulse 2.8s ease-out forwards;
}
.counter-updated {
    animation: cprCounterPulse 0.8s ease-in-out;
}
@keyframes cprCounterPulse {
    0% { transform: scale(1); }
    40% { transform: scale(1.3); color: #009ef7 !important; }
    100% { transform: scale(1); }
}
.live-sync-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 11px;
    font-weight: 600;
    color: #50cd89;
    background: #e8fff3;
    padding: 5px 12px;
    border-radius: 12px;
    border: 1px solid #c9f7dd;
    user-select: none;
}
.live-sync-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #50cd89;
    box-shadow: 0 0 0 0 rgba(80, 205, 137, 0.7);
    animation: livePulse 2s infinite;
}
@keyframes livePulse {
    0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(80, 205, 137, 0.7); }
    70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(80, 205, 137, 0); }
    100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(80, 205, 137, 0); }
}
</style>

@section('content')
<!--begin::Toolbar-->
<div class="toolbar py-3" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack">
        <div class="page-title d-flex align-items-center flex-wrap me-3 mb-2 mb-lg-0">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
                Custom Plan Requests
            </h1>
            <span class="h-20px border-gray-200 border-start mx-4"></span>
            <ul class="breadcrumb breadcrumb-separatorless fw-bold fs-7 my-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('superadmin.dashboard.index') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <span class="bullet bg-gray-200 w-5px h-2px"></span>
                </li>
                <li class="breadcrumb-item text-dark">Custom Plan Enquiries</li>
            </ul>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="live-sync-indicator" title="Real-Time Data Synchronization Active across all tabs & sessions">
                <span class="live-sync-dot"></span>
                <span>Live Sync Active</span>
            </span>
            <button type="button" id="btn-manual-sync" class="btn btn-sm btn-icon btn-light" title="Refresh Live Data">
                <i class="fa fa-sync-alt fs-7"></i>
            </button>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 pt-lg-6" id="kt_content">
    <div class="container-fluid px-4">

        <!--begin::Summary Stat Cards (Hero Section)-->
        <div class="row g-4 mb-5 cpr-hero-wrapper">
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card cpr-stat-card p-4 cursor-pointer" data-filter-status="all" title="Click to view All Requests">
                    <div class="cpr-stat-title">Total Requests</div>
                    <div class="cpr-stat-value text-dark" id="stat-total">{{ $summaryCounts['total'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card cpr-stat-card p-4 border-start border-4 border-info cursor-pointer" data-filter-status="New" title="Click to view New Requests">
                    <div class="cpr-stat-title">New Requests</div>
                    <div class="cpr-stat-value text-info" id="stat-new">{{ $summaryCounts['new'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card cpr-stat-card p-4 border-start border-4 border-warning cursor-pointer" data-filter-status="In Progress" title="Click to view In Progress Requests">
                    <div class="cpr-stat-title">In Progress</div>
                    <div class="cpr-stat-value text-warning" id="stat-in-progress">{{ $summaryCounts['in_progress'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card cpr-stat-card p-4 border-start border-4 border-primary cursor-pointer" data-filter-status="Contacted" title="Click to view Contacted Requests">
                    <div class="cpr-stat-title">Contacted</div>
                    <div class="cpr-stat-value text-primary" id="stat-contacted">{{ $summaryCounts['contacted'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card cpr-stat-card p-4 border-start border-4 border-success cursor-pointer" data-filter-status="Resolved" title="Click to view Resolved Requests">
                    <div class="cpr-stat-title">Resolved</div>
                    <div class="cpr-stat-value text-success" id="stat-resolved">{{ $summaryCounts['resolved'] ?? 0 }}</div>
                </div>
            </div>
            <div class="col-6 col-md-4 col-xl-2">
                <div class="card cpr-stat-card p-4 border-start border-4 border-secondary cursor-pointer" data-filter-status="Closed" title="Click to view Closed Requests">
                    <div class="cpr-stat-title">Closed</div>
                    <div class="cpr-stat-value text-muted" id="stat-closed">{{ $summaryCounts['closed'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <!--end::Summary Stat Cards-->

        <!--begin::Main Card-->
        <div class="card shadow-sm">
            <div class="card-header border-0 pt-4">
                <div class="card-title d-flex align-items-center flex-wrap gap-3">
                    <!-- Search Input -->
                    <div class="d-flex align-items-center position-relative my-1">
                        <span class="svg-icon svg-icon-2 position-absolute ms-3">
                            <i class="fa fa-search text-gray-500"></i>
                        </span>
                        <input type="text" id="cpr-search" class="form-control form-control-solid w-250px ps-10" placeholder="Search reference, customer..." />
                    </div>

                    <!-- Status Filter -->
                    <div class="d-flex align-items-center my-1">
                        <select id="cpr-status-filter" class="form-select form-select-solid w-180px">
                            <option value="all">All Statuses</option>
                            @foreach($allowedStatuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Reset Filter Button -->
                    <button type="button" id="btn-reset-filters" class="btn btn-sm btn-light">
                        <i class="fa fa-undo me-1"></i>Reset
                    </button>
                </div>
            </div>

            <div class="card-body pt-2 px-4">
                <div class="table-responsive">
                    <table id="customPlanRequestsTable" class="table table-row-bordered table-row-dashed gy-4 align-middle fs-6">
                        <thead class="bg-light text-muted fw-bolder fs-7 text-uppercase">
                            <tr>
                                <th>Request ID</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Company</th>
                                <th>Request Type</th>
                                <th>Status</th>
                                <th>Submitted On</th>
                                <th>Last Updated</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <!--end::Main Card-->

    </div>
</div>
<!--end::Content-->

<!--begin::View Details Modal-->
<div class="modal fade" id="modalViewDetails" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bolder">Request Details: <span id="view-reference" class="text-primary"></span></h5>
                <button type="button" class="btn btn-sm btn-icon btn-light btn-close-modal" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times fs-5 text-gray-700"></i>
                </button>
            </div>
            <div class="modal-body py-4" id="view-details-content">
                <div class="text-center py-5" id="view-details-loading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="view-details-body" style="display: none;">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="fw-bolder mb-2 text-dark">Customer Information</h6>
                                <p class="mb-1 text-gray-700"><strong>Name:</strong> <span id="detail-customer-name"></span></p>
                                <p class="mb-1 text-gray-700"><strong>Email:</strong> <span id="detail-customer-email"></span></p>
                                <p class="mb-1 text-gray-700"><strong>Phone:</strong> <span id="detail-customer-mobile"></span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light">
                                <h6 class="fw-bolder mb-2 text-dark">Company & Location</h6>
                                <p class="mb-1 text-gray-700"><strong>Company:</strong> <span id="detail-company-name"></span></p>
                                <p class="mb-1 text-gray-700"><strong>Company ID:</strong> <span id="detail-company-id"></span></p>
                                <p class="mb-1 text-gray-700"><strong>Location:</strong> <span id="detail-location"></span></p>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="border rounded p-3">
                                <h6 class="fw-bolder mb-2 text-dark">Plan Specifications</h6>
                                <p class="mb-1 text-gray-700"><strong>Enquiry Type:</strong> <span id="detail-request-type"></span></p>
                                <p class="mb-1 text-gray-700"><strong>Target Package:</strong> <span id="detail-package-target"></span></p>
                                <p class="mb-1 text-gray-700"><strong>Status:</strong> <span id="detail-status-badge"></span></p>
                                <p class="mb-1 text-gray-700"><strong>Submitted On:</strong> <span id="detail-created-at"></span></p>
                                <p class="mb-0 text-gray-700"><strong>Last Updated:</strong> <span id="detail-updated-at"></span> (by <span id="detail-updated-by"></span>)</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bolder text-dark mb-2">Admin Notes</h6>
                        <div class="p-3 bg-light rounded text-gray-800" id="detail-admin-notes">None</div>
                    </div>

                    <div>
                        <h6 class="fw-bolder text-dark mb-2">Status Audit History</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered fs-7 mb-0">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th>Transition</th>
                                        <th>Notes</th>
                                        <th>Updated By</th>
                                        <th>Timestamp</th>
                                    </tr>
                                </thead>
                                <tbody id="detail-logs-body"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-close-modal" data-bs-dismiss="modal" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--end::View Details Modal-->

<!--begin::Update Status Modal-->
<div class="modal fade" id="modalUpdateStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formUpdateStatus">
                @csrf
                <input type="hidden" id="update-request-id" name="id" />
                <input type="hidden" id="update-current-version" name="current_version" value="1" />
                <div class="modal-header">
                    <h5 class="modal-title fw-bolder">Update Request Status</h5>
                    <button type="button" class="btn btn-sm btn-icon btn-light btn-close-modal" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close">
                        <i class="fa fa-times fs-5 text-gray-700"></i>
                    </button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label text-muted fs-7">Request Reference</label>
                        <div class="fw-bolder fs-6 text-primary" id="update-reference-display"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label required fw-bold text-dark fs-7">New Status</label>
                        <select class="form-select form-select-solid" id="update-status-select" name="status" required>
                            @foreach($allowedStatuses as $status)
                                <option value="{{ $status }}">{{ $status }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-dark fs-7">Admin Notes / Remarks</label>
                        <textarea class="form-control form-control-solid" id="update-admin-notes" name="admin_notes" rows="3" placeholder="Enter notes about client conversation, agreement, or next steps..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-close-modal" data-bs-dismiss="modal" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="btn-save-status" class="btn btn-primary">
                        <span class="indicator-label">Save Status</span>
                        <span class="indicator-progress d-none">
                            Please wait... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--end::Update Status Modal-->
@endsection

@push('footer-script')
<script>
$(document).ready(function() {
    var updateStatusUrlTemplate = "{{ route('superadmin.custom-plan-requests.updateStatus', ':id') }}";
    var showUrlTemplate = "{{ route('superadmin.custom-plan-requests.show', ':id') }}";
    var syncUrl = "{{ route('superadmin.custom-plan-requests.sync') }}";

    var table = $('#customPlanRequestsTable').DataTable({
        responsive: true,
        processing: true,
        serverSide: true,
        ordering: false,
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        dom: 'rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        ajax: {
            url: "{{ route('superadmin.custom-plan-requests.data') }}",
            data: function(d) {
                d.status = $('#cpr-status-filter').val();
                var searchVal = $('#cpr-search').val() || '';
                if (d.search && typeof d.search === 'object') {
                    d.search.value = searchVal;
                } else {
                    d.search = { value: searchVal };
                }
            }
        },
        language: (typeof languageOptions === 'function') ? languageOptions() : {},
        columns: [
            { data: 'request_reference', name: 'request_reference' },
            { data: 'customer_name', name: 'customer_name' },
            { data: 'customer_email', name: 'customer_email' },
            { data: 'company_name', name: 'company_name' },
            { data: 'request_type', name: 'request_type' },
            { data: 'status', name: 'status' },
            { data: 'created_at', name: 'created_at' },
            { data: 'updated_at', name: 'updated_at' },
            { data: 'action', name: 'action', searchable: false, orderable: false, className: 'text-center' }
        ]
    });

    // Real-time search filter with debounce
    var searchTimer;
    $('#cpr-search').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            table.ajax.reload(null, false);
        }, 300);
    });

    function highlightActiveStatCard(status) {
        $('.cpr-stat-card').removeClass('active-filter-card');
        $('.cpr-stat-card[data-filter-status="' + status + '"]').addClass('active-filter-card');
    }

    // Quick filter by clicking stat cards
    $(document).on('click', '.cpr-stat-card[data-filter-status]', function() {
        var status = $(this).data('filter-status');
        $('#cpr-status-filter').val(status);
        highlightActiveStatCard(status);
        table.ajax.reload(null, true);
    });

    // Status filter dropdown
    $('#cpr-status-filter').on('change', function() {
        highlightActiveStatCard($(this).val());
        table.ajax.reload(null, true);
    });

    // Reset filters
    $('#btn-reset-filters').on('click', function() {
        $('#cpr-search').val('');
        $('#cpr-status-filter').val('all');
        highlightActiveStatCard('all');
        table.ajax.reload(null, true);
    });

    // Modal helper functions with dual Bootstrap 4/5 & jQuery compatibility
    function showCustomPlanModal(modalId) {
        var $m = $('#' + modalId);
        if (typeof $m.modal === 'function') {
            try {
                $m.modal('show');
                return;
            } catch (e) {}
        }
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                var el = document.getElementById(modalId);
                var bsM = (bootstrap.Modal.getOrCreateInstance)
                    ? bootstrap.Modal.getOrCreateInstance(el)
                    : new bootstrap.Modal(el);
                bsM.show();
                return;
            } catch (e) {}
        }
        $m.addClass('show').show().removeAttr('aria-hidden');
        $('body').addClass('modal-open');
    }

    function hideCustomPlanModal(modalId) {
        var $m = $('#' + modalId);
        if (typeof $m.modal === 'function') {
            try {
                $m.modal('hide');
            } catch (e) {}
        }
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                var el = document.getElementById(modalId);
                var bsM = (bootstrap.Modal.getInstance) ? bootstrap.Modal.getInstance(el) : null;
                if (bsM) {
                    bsM.hide();
                }
            } catch (e) {}
        }
        setTimeout(function() {
            if ($m.hasClass('show') || $m.is(':visible')) {
                $m.removeClass('show').hide().attr('aria-hidden', 'true');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open').css({'overflow': '', 'padding-right': ''});
            }
        }, 150);
    }

    // Explicit click handler for all modal close/cancel buttons
    $(document).on('click', '[data-dismiss="modal"], [data-bs-dismiss="modal"], .btn-close-modal', function(e) {
        e.preventDefault();
        var $modal = $(this).closest('.modal');
        if ($modal.length) {
            hideCustomPlanModal($modal.attr('id'));
        }
    });

    // Also close when clicking the modal backdrop
    $(document).on('click', '#modalViewDetails, #modalUpdateStatus', function(e) {
        if ($(e.target).hasClass('modal')) {
            hideCustomPlanModal($(this).attr('id'));
        }
    });

    // =========================================================================
    // REAL-TIME SYNCHRONIZATION HELPERS (Client-First / 0ms Latency)
    // =========================================================================

    // Smooth KPI counter updates with scale-up animation
    function updateCounters(counts) {
        if (!counts) return;
        var mapping = [
            { key: 'total', id: '#stat-total' },
            { key: 'new', id: '#stat-new' },
            { key: 'in_progress', id: '#stat-in-progress' },
            { key: 'contacted', id: '#stat-contacted' },
            { key: 'resolved', id: '#stat-resolved' },
            { key: 'closed', id: '#stat-closed' }
        ];

        mapping.forEach(function(item) {
            var $el = $(item.id);
            if ($el.length && counts[item.key] !== undefined) {
                var oldVal = parseInt($el.text().trim(), 10);
                var newVal = parseInt(counts[item.key], 10);
                if (!isNaN(newVal)) {
                    $el.text(newVal);
                    if (!isNaN(oldVal) && oldVal !== newVal) {
                        $el.addClass('counter-updated');
                        setTimeout(function() {
                            $el.removeClass('counter-updated');
                        }, 800);
                    }
                }
            }
        });
    }

    // In-memory version tracking for OCC and out-of-order delta rejection
    var localEntityVersions = {};

    // Instant DOM Delta Patch (0ms Latency) + Row Pulse Animation
    function applyStatusDeltaToDOM(id, reference, newStatus, statusBadgeHtml, lastUpdated, adminNotes, updatedBy, entityVersion) {
        if (entityVersion) {
            localEntityVersions[id] = entityVersion;
        }

        var $row = $('#cpr-row-' + id);
        if (!$row.length) {
            var $btn = $('.btn-update-status[data-id="' + id + '"]');
            if (!$btn.length && reference) {
                $btn = $('.btn-update-status[data-reference="' + reference + '"]');
            }
            if ($btn.length) {
                $row = $btn.closest('tr');
            }
        }

        if ($row.length) {
            // Update Status column (index 5)
            $row.find('td').eq(5).html(statusBadgeHtml);

            // Update Last Updated column (index 7)
            if (lastUpdated) {
                $row.find('td').eq(7).text(lastUpdated);
            }

            // Update data attributes on the action button
            var $statusBtn = $row.find('.btn-update-status');
            $statusBtn.attr('data-status', newStatus);
            if (entityVersion) {
                $statusBtn.attr('data-version', entityVersion);
                $row.attr('data-version', entityVersion);
            }

            // Add highlight pulse animation
            $row.removeClass('row-cpr-updated');
            void $row[0].offsetWidth; // Trigger browser reflow
            $row.addClass('row-cpr-updated');
            setTimeout(function() {
                $row.removeClass('row-cpr-updated');
            }, 2800);
        }

        // Real-time sync for View Details modal if currently viewing this request
        var currentModalRef = $('#view-reference').text().trim();
        var currentModalId = $('#modalViewDetails').data('active-id');
        if ($('#modalViewDetails').hasClass('show') && (currentModalRef === reference || currentModalId == id)) {
            $('#detail-status-badge').html(statusBadgeHtml);
            if (lastUpdated) {
                $('#detail-updated-at').text(lastUpdated);
            }
            if (updatedBy) {
                $('#detail-updated-by').text(updatedBy);
            }
            if (adminNotes) {
                $('#detail-admin-notes').text(adminNotes);
            }

            // Prepend latest transition into logs table
            var newLogHtml = '<tr>' +
                '<td><span class="badge badge-light-primary">' + newStatus + '</span></td>' +
                '<td>' + (adminNotes ? adminNotes : '<span class="text-muted">-</span>') + '</td>' +
                '<td>' + (updatedBy || 'Super Admin') + '</td>' +
                '<td>' + (lastUpdated || 'Just now') + '</td>' +
            '</tr>';
            $('#detail-logs-body').prepend(newLogHtml);
        }
    }

    // =========================================================================
    // REAL-TIME SYNCHRONIZATION: Laravel Reverb WebSockets + BroadcastChannel
    // =========================================================================

    // 1. Laravel Echo private channel subscription for Super Admin
    if (typeof window.Echo !== 'undefined') {
        try {
            window.Echo.private('superadmin.custom-plans')
                .listen('.custom.plan.delta', function(e) {
                    handleIncomingSync(e);
                });
        } catch (e) {
            console.warn('[CPR_ECHO] Echo private channel subscription notice:', e);
        }
    }

    // 2. BroadcastChannel for zero-latency multi-tab sync
    var cprChannel = null;
    try {
        if (typeof window.BroadcastChannel !== 'undefined') {
            cprChannel = new BroadcastChannel('jero_cpr_sync_bus');
            cprChannel.onmessage = function(event) {
                if (event.data && event.data.type === 'STATUS_UPDATED') {
                    handleIncomingSync(event.data);
                }
            };
        }
    } catch (e) {
        console.warn('BroadcastChannel init error:', e);
    }

    // Cross-tab fallback via storage events
    window.addEventListener('storage', function(e) {
        if (e.key === 'jero_cpr_status_mutation' && e.newValue) {
            try {
                var data = JSON.parse(e.newValue);
                if (data && data.type === 'STATUS_UPDATED' && (Date.now() - data.sent_at < 6000)) {
                    handleIncomingSync(data);
                }
            } catch (err) {}
        }
    });

    function broadcastStatusMutation(data) {
        data.sent_at = Date.now();
        if (cprChannel) {
            try {
                cprChannel.postMessage(data);
            } catch (e) {}
        }
        try {
            localStorage.setItem('jero_cpr_status_mutation', JSON.stringify(data));
        } catch (e) {}
    }

    function handleIncomingSync(data) {
        if (!data || !data.id) return;

        var incomingVersion = parseInt(data.entity_version, 10) || 1;
        var currentKnownVersion = localEntityVersions[data.id] || 0;

        // Reject stale out-of-order delta events
        if (incomingVersion < currentKnownVersion) {
            return;
        }
        localEntityVersions[data.id] = incomingVersion;

        // 1. Update KPI Counters live
        if (data.counts) {
            updateCounters(data.counts);
        }

        // 2. Patch DOM row live
        applyStatusDeltaToDOM(
            data.id,
            data.reference,
            data.new_status,
            data.status_badge,
            data.last_updated,
            data.admin_notes,
            data.updated_by,
            incomingVersion
        );

        // 3. Reconcile with DataTables server-side
        table.ajax.reload(null, false);

        // 4. Show non-intrusive notification toast
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
            Toast.fire({
                icon: 'info',
                title: 'Live Update: ' + data.reference,
                text: 'Status transitioned to ' + data.new_status
            });
        }
    }

    // =========================================================================
    // MANUAL ON-DEMAND SYNC (0% Server Strain — Strictly User-Initiated Only)
    // Zero background setInterval polling, zero auto-hitting server.
    // Real-time synchronization is 100% event-driven via Reverb & BroadcastChannel.
    // =========================================================================
    var lastKnownSyncHash = null;
    var isSyncing = false;

    function performManualSyncCheck() {
        if (isSyncing) return;

        isSyncing = true;
        $('#btn-manual-sync i').addClass('fa-spin');

        $.ajax({
            url: syncUrl,
            type: 'GET',
            cache: false,
            success: function(res) {
                isSyncing = false;
                $('#btn-manual-sync i').removeClass('fa-spin');

                if (res.status === 'success') {
                    var currentHash = JSON.stringify(res.counts) + '_' + (res.latest ? res.latest.timestamp : 0);
                    var hasChanged = (lastKnownSyncHash !== null && lastKnownSyncHash !== currentHash);
                    lastKnownSyncHash = currentHash;

                    if (hasChanged) {
                        updateCounters(res.counts);
                        table.ajax.reload(null, false);
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: hasChanged ? "Synchronized!" : "Up to Date",
                            text: hasChanged ? "Latest custom plan data loaded successfully." : "All request statuses and counters are currently up to date.",
                            icon: hasChanged ? "success" : "info",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            },
            error: function() {
                isSyncing = false;
                $('#btn-manual-sync i').removeClass('fa-spin');
            }
        });
    }

    // Manual sync button (User-initiated only)
    $('#btn-manual-sync').on('click', function(e) {
        e.preventDefault();
        performManualSyncCheck();
    });

    // =========================================================================
    // MODAL DIALOG HANDLERS
    // =========================================================================

    // View Details Modal
    $(document).on('click', '.btn-view-request', function() {
        var id = $(this).data('id');
        var url = showUrlTemplate.replace(':id', id);

        $('#modalViewDetails').data('active-id', id);
        $('#view-details-loading').show();
        $('#view-details-body').hide();
        showCustomPlanModal('modalViewDetails');

        $.ajax({
            url: url,
            type: 'GET',
            success: function(res) {
                if (res.status === 'success') {
                    var d = res.data;
                    $('#view-reference').text(d.request_reference);
                    $('#detail-customer-name').text(d.customer_name);
                    $('#detail-customer-email').text(d.customer_email);
                    $('#detail-customer-mobile').text(d.customer_mobile);
                    $('#detail-company-name').text(d.company_name);
                    $('#detail-company-id').text(d.company_id ? d.company_id : 'N/A');
                    $('#detail-location').text(d.location);
                    $('#detail-request-type').text(d.request_type);
                    $('#detail-package-target').text(d.package_type + ' - ' + d.package_title);
                    $('#detail-status-badge').html(d.status_badge);
                    $('#detail-created-at').text(d.created_at);
                    $('#detail-updated-at').text(d.updated_at);
                    $('#detail-updated-by').text(d.updated_by_name);
                    $('#detail-admin-notes').text(d.admin_notes ? d.admin_notes : 'No administrative notes recorded yet.');

                    var logsHtml = '';
                    if (d.logs && d.logs.length > 0) {
                        $.each(d.logs, function(idx, log) {
                            logsHtml += '<tr>' +
                                '<td><span class="badge badge-light-secondary">' + (log.previous_status ? log.previous_status : 'Created') + '</span> &rarr; <span class="badge badge-light-primary">' + log.new_status + '</span></td>' +
                                '<td>' + (log.notes ? log.notes : '<span class="text-muted">-</span>') + '</td>' +
                                '<td>' + log.updated_by + '</td>' +
                                '<td>' + log.created_at + '</td>' +
                            '</tr>';
                        });
                    } else {
                        logsHtml = '<tr><td colspan="4" class="text-center text-muted">No status transitions recorded.</td></tr>';
                    }
                    $('#detail-logs-body').html(logsHtml);

                    $('#view-details-loading').hide();
                    $('#view-details-body').show();
                }
            },
            error: function() {
                $('#view-details-loading').html('<div class="text-danger py-4">Failed to load request details. Please try again.</div>');
            }
        });
    });

    // Open Update Status Modal
    $(document).on('click', '.btn-update-status', function() {
        var id = $(this).data('id');
        var currentStatus = $(this).data('status');
        var reference = $(this).data('reference');
        var currentVersion = $(this).attr('data-version') || $(this).data('version') || 1;

        $('#update-request-id').val(id);
        $('#update-current-version').val(currentVersion);
        $('#update-reference-display').text(reference);
        $('#update-status-select').val(currentStatus);
        $('#update-admin-notes').val('');

        showCustomPlanModal('modalUpdateStatus');
    });

    // Submit Status Update
    $('#formUpdateStatus').on('submit', function(e) {
        e.preventDefault();

        var id = $('#update-request-id').val();
        var currentVersion = $('#update-current-version').val();
        var url = updateStatusUrlTemplate.replace(':id', id);
        var $btn = $('#btn-save-status');

        // Generate unique UUID-style Idempotency Key for mutation deduplication
        var idempotencyKey = 'cpr_' + id + '_' + Date.now() + '_' + Math.random().toString(36).substring(2, 9);

        $btn.prop('disabled', true);
        $btn.find('.indicator-label').addClass('d-none');
        $btn.find('.indicator-progress').removeClass('d-none');

        $.ajax({
            url: url,
            type: 'POST',
            headers: {
                'X-Idempotency-Key': idempotencyKey
            },
            data: {
                _token: "{{ csrf_token() }}",
                _method: 'PUT',
                status: $('#update-status-select').val(),
                admin_notes: $('#update-admin-notes').val(),
                current_version: currentVersion,
                idempotency_key: idempotencyKey
            },
            success: function(response) {
                $btn.prop('disabled', false);
                $btn.find('.indicator-label').removeClass('d-none');
                $btn.find('.indicator-progress').addClass('d-none');

                hideCustomPlanModal('modalUpdateStatus');

                if (response.status === 'success') {
                    var entityVersion = response.entity_version || (parseInt(currentVersion, 10) + 1);

                    // 1. Instant local DOM Delta Patch (0ms latency)
                    applyStatusDeltaToDOM(
                        response.id,
                        response.reference,
                        response.new_status,
                        response.status_badge,
                        response.last_updated,
                        response.admin_notes,
                        response.updated_by,
                        entityVersion
                    );

                    // 2. Update KPI counter cards with animation
                    if (response.counts) {
                        updateCounters(response.counts);
                    }

                    // 3. Broadcast to all open tabs / windows in real time
                    broadcastStatusMutation({
                        type: 'STATUS_UPDATED',
                        id: response.id,
                        reference: response.reference,
                        previous_status: response.previous_status,
                        new_status: response.new_status,
                        entity_version: entityVersion,
                        status_badge: response.status_badge,
                        counts: response.counts,
                        last_updated: response.last_updated,
                        admin_notes: response.admin_notes,
                        updated_by: response.updated_by
                    });

                    // 4. Authoritative DataTables server-side reload
                    table.ajax.reload(null, false);

                    // 5. Success feedback
                    Swal.fire({
                        title: "Status Synchronized!",
                        text: "Request " + response.reference + " has been updated to " + response.new_status + " in real time.",
                        icon: "success",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false);
                $btn.find('.indicator-label').removeClass('d-none');
                $btn.find('.indicator-progress').addClass('d-none');

                if (xhr.status === 409) {
                    hideCustomPlanModal('modalUpdateStatus');
                    table.ajax.reload(null, false);
                    var conflictMsg = (xhr.responseJSON && xhr.responseJSON.message) 
                        ? xhr.responseJSON.message 
                        : "Another administrator has updated this request. Data has been reloaded.";
                    Swal.fire({
                        title: "Concurrency Conflict",
                        text: conflictMsg,
                        icon: "warning"
                    });
                    return;
                }

                var errMsg = "Failed to update status.";
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    title: "Update Error",
                    text: errMsg,
                    icon: "error"
                });
            }
        });
    });
});
</script>
@endpush
