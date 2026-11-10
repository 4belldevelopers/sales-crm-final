@extends('layouts.superadmin_layout')

@php
    use App\Helper\GeneralMethod;
@endphp

@push('head-script')
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
    .content {
        padding-top: 0 !important;
        margin-top: 0 !important;
    }
    .gmeet-page-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 16px 30px 16px !important;
        margin-top: 0 !important;
    }
    @media (max-width: 1400px) {
        .gmeet-toolbar-card {
            padding: 14px 20px;
        }
        .gmeet-search-box {
            min-width: 220px;
            max-width: 320px;
        }
        .meet-link-pill {
            max-width: 260px;
        }
    }
    @media (max-width: 768px) {
        .gmeet-toolbar-card {
            padding: 12px 16px;
        }
        .gmeet-search-box {
            min-width: 100%;
            max-width: 100%;
        }
    }

    /* ─── Tier 1: Top Toolbar Card ─── */
    .gmeet-toolbar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 20px;
    }

    .gmeet-brand-icon-box {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #00ac47 0%, #00832d 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 22px;
        box-shadow: 0 4px 12px rgba(0, 172, 71, 0.25);
    }

    /* ─── Tier 2: Sub-Tabs Card ─── */
    .gmeet-tabs-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 20px;
    }

    .gmeet-nav-pills .nav-link {
        font-size: 13px;
        font-weight: 600;
        color: #5e6278;
        padding: 10px 18px;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .gmeet-nav-pills .nav-link:hover {
        color: #009ef7;
        background: #f8fafc;
    }

    .gmeet-nav-pills .nav-link.active {
        background-color: #009ef7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0, 158, 247, 0.28);
    }

    /* ─── Tier 3: Modular Sub-Cards ─── */
    .gmeet-sub-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .gmeet-sub-card-header {
        padding: 16px 22px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
    }

    .gmeet-sub-card-header h3 {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .gmeet-sub-card-body {
        padding: 22px;
    }

    /* ─── Search & Filter Controls ─── */
    .gmeet-search-box {
        position: relative;
        min-width: 280px;
        max-width: 380px;
    }

    .gmeet-search-box i.search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
    }

    .gmeet-search-box .form-control {
        padding-left: 38px;
        padding-right: 36px;
        height: 40px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .gmeet-search-box .form-control:focus {
        border-color: #009ef7;
        box-shadow: 0 0 0 3px rgba(0, 158, 247, 0.12);
    }

    .gmeet-search-box .clear-search-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 0;
        font-size: 14px;
        display: none;
    }

    .gmeet-search-box .clear-search-btn:hover {
        color: #ef4444;
    }

    /* ─── Table & Status Badges ─── */
    .gmeet-table {
        margin-bottom: 0;
    }

    .gmeet-table thead th {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 16px;
        vertical-align: middle;
    }

    .gmeet-table tbody td {
        padding: 14px 16px;
        font-size: 13px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .gmeet-table tbody tr:hover {
        background-color: #fbfcfe;
    }

    .badge-status-active {
        background: #e8fff3;
        color: #50cd89;
        border: 1px solid #b7f5d0;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-status-inactive {
        background: #fff8dd;
        color: #e49e00;
        border: 1px solid #ffe89e;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-status-booked {
        background: #e1f0ff;
        color: #009ef7;
        border: 1px solid #bce1ff;
        font-size: 11px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* ─── Metric Mini-Cards ─── */
    .metric-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: all 0.2s ease;
    }

    .metric-card:hover {
        border-color: #cbd5e1;
        background: #f1f5f9;
    }

    .metric-card-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .metric-card-label {
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    .metric-card-value {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
    }

    /* ─── URL Truncation & Copy Action ─── */
    .meet-link-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 4px 10px;
        border-radius: 6px;
        max-width: 340px;
    }

    .meet-link-text {
        font-family: 'Consolas', 'Monaco', monospace;
        font-size: 12px;
        color: #0284c7;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* ─── Host Account Avatar & Text ─── */
    .host-account-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .host-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e0f2fe;
        color: #0284c7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        flex-shrink: 0;
    }

    /* ─── Architecture Step Card ─── */
    .guide-step-card {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 16px;
        background: #ffffff;
        margin-bottom: 12px;
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .guide-step-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e8fff3;
        color: #00ac47;
        border: 1px solid #b7f5d0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="gmeet-page-container container-fluid">

    <!-- ========================================== -->
    <!-- TIER 1: TOP HEADER TOOLBAR CARD            -->
    <!-- ========================================== -->
    <div class="gmeet-toolbar-card d-flex flex-stack flex-wrap gap-4 align-items-center">
        <div class="d-flex align-items-center gap-4">
            <div class="gmeet-brand-icon-box">
                <i class="fas fa-video"></i>
            </div>
            <div>
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-0">
                    Google Meet Link Management & Allocation Hub
                </h1>
                <div class="text-muted fs-7 fw-semibold mt-1">
                    Curate static conference link pools and monitor live ATS interview scheduling allocations
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="button" class="btn btn-sm btn-light-danger fw-bold d-none" id="btnBulkDelete">
                <i class="fas fa-trash-alt me-1"></i> Delete Selected (<span id="bulkDeleteCount">0</span>)
            </button>

            <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btnSwitchImportTab">
                <i class="fas fa-file-import me-1"></i> Batch Import (.xlsx)
            </button>

            <button type="button" class="btn btn-sm btn-primary fw-bold" id="btnSwitchAddTab">
                <i class="fas fa-plus-circle me-1"></i> Add Meeting Links
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- KPI METRIC MINI-CARDS STRIP                -->
    <!-- ========================================== -->
    <div class="row g-4 mb-5">
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-card-icon bg-light-primary text-primary">
                    <i class="fas fa-link"></i>
                </div>
                <div>
                    <div class="metric-card-label">Total Pool Links</div>
                    <div class="metric-card-value" id="kpiTotalCount">{{ $totalLinksCount ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-card-icon bg-light-success text-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="metric-card-label">Active & Available</div>
                    <div class="metric-card-value" id="kpiActiveCount">{{ $activeLinksCount ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-card-icon bg-light-warning text-warning">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div>
                    <div class="metric-card-label">Inactive / Paused</div>
                    <div class="metric-card-value" id="kpiInactiveCount">{{ $inactiveLinksCount ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="metric-card">
                <div class="metric-card-icon bg-light-info text-info">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <div class="metric-card-label">Recorded Schedules</div>
                    <div class="metric-card-value" id="kpiScheduledCount">{{ $scheduledMeetingsCount ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- TIER 2: SUB-TABS NAVIGATION CARD           -->
    <!-- ========================================== -->
    <div class="gmeet-tabs-card">
        <ul class="nav gmeet-nav-pills gap-2 border-0" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#tab_links_directory" role="tab">
                    <i class="fas fa-list-ul"></i>
                    <span>Meeting Links Directory</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_add_import" role="tab">
                    <i class="fas fa-plus-square"></i>
                    <span>Quick Add & Batch Import</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_schedule_monitor" role="tab" id="tabScheduleNav">
                    <i class="fas fa-history"></i>
                    <span>Active Allocation & Schedule Monitor</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#tab_architecture_guide" role="tab">
                    <i class="fas fa-book-open"></i>
                    <span>Integration Architecture & Guide</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- ========================================== -->
    <!-- TIER 3: MODULAR SUB-CARDS CONTENT STREAM   -->
    <!-- ========================================== -->
    <div class="tab-content" id="gmeetTabContent">

        <!-- ────────────────────────────────────────── -->
        <!-- TAB 1: MEETING LINKS DIRECTORY             -->
        <!-- ────────────────────────────────────────── -->
        <div class="tab-pane fade show active" id="tab_links_directory" role="tabpanel">
            <div class="gmeet-sub-card">
                <!-- Sub-Card Header with Search & Filter -->
                <div class="gmeet-sub-card-header flex-wrap gap-3">
                    <h3>
                        <i class="fas fa-th-list text-primary"></i>
                        Google Meet Pool Directory
                    </h3>

                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <!-- Debounced Search Box -->
                        <form method="GET" action="{{ route('superadmin.company.google_meeting_setting') }}" id="searchForm" class="d-flex align-items-center gap-2 m-0">
                            <div class="gmeet-search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text"
                                       class="form-control"
                                       name="meeting_link"
                                       id="inputSearchLink"
                                       placeholder="Search by email or meeting link..."
                                       value="{{ $meeting_link ?? '' }}"
                                       autocomplete="off">
                                <button type="button" class="clear-search-btn" id="btnClearSearch" title="Clear search">
                                    <i class="fas fa-times-circle"></i>
                                </button>
                            </div>

                            <!-- Status Filter Dropdown -->
                            <select name="status" class="form-select form-select-sm" style="height: 40px; min-width: 140px; border-radius: 8px;" id="selectStatusFilter">
                                <option value="" {{ ($statusFilter === null || $statusFilter === '') ? 'selected' : '' }}>All Statuses</option>
                                <option value="1" {{ ($statusFilter === '1' || $statusFilter === 1) ? 'selected' : '' }}>Active Only</option>
                                <option value="0" {{ ($statusFilter === '0' || $statusFilter === 0) ? 'selected' : '' }}>Inactive Only</option>
                            </select>

                            <button type="submit" class="btn btn-sm btn-primary fw-bold" style="height: 40px; border-radius: 8px;">
                                Filter
                            </button>

                            @if(!empty($meeting_link) || ($statusFilter !== null && $statusFilter !== ''))
                                <a href="{{ route('superadmin.company.google_meeting_setting') }}" class="btn btn-sm btn-light fw-bold" style="height: 40px; border-radius: 8px;" title="Reset filters">
                                    <i class="fas fa-undo"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Table Sub-Card Body -->
                <div class="gmeet-sub-card-body p-0">
                    <div class="table-responsive">
                        <table class="table gmeet-table align-middle table-row-dashed" id="tableGoogleMeetLinks">
                            <thead>
                                <tr>
                                    <th class="w-30px text-center">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="masterCheckbox" title="Select all rows">
                                        </div>
                                    </th>
                                    <th class="w-40px text-center">#</th>
                                    <th class="min-w-220px">Host Account / Email</th>
                                    <th class="min-w-300px">Google Meet Link URL</th>
                                    <th class="min-w-130px text-center">Status</th>
                                    <th class="min-w-150px text-end pe-6">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="tableGoogleMeetLinksBody">
                                @include('super-admin.meeting.partials.meeting_list_rows', [
                                    'google_meeting_setting' => $google_meeting_setting,
                                    'meeting_link' => $meeting_link,
                                    'statusFilter' => $statusFilter,
                                ])
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer Strip (Super Admin Standard) -->
                    <div id="gmeetPaginationContainer">
                        {!! $paginationHtml ?? '' !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- ────────────────────────────────────────── -->
        <!-- TAB 2: QUICK ADD & BATCH IMPORT HUB        -->
        <!-- ────────────────────────────────────────── -->
        <div class="tab-pane fade" id="tab_add_import" role="tabpanel">
            <div class="row g-5">
                <!-- Sub-Card A: Dynamic Multi-Row Manual Entry -->
                <div class="col-lg-7">
                    <div class="gmeet-sub-card">
                        <div class="gmeet-sub-card-header">
                            <h3>
                                <i class="fas fa-keyboard text-primary"></i>
                                Dynamic Manual Link Entry
                            </h3>
                            <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btnAddRow">
                                <i class="fas fa-plus me-1"></i> Add Another Row
                            </button>
                        </div>
                        <div class="gmeet-sub-card-body">
                            <p class="text-muted fs-7 mb-4">
                                Enter host account emails and reusable Google Meet URLs. All inputs are automatically whitespace-trimmed and normalized with <code>https://</code>.
                            </p>

                            <form id="formManualAddLinks">
                                @csrf
                                <input type="hidden" name="tablename" value="google_meeting_lists">

                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered align-middle" id="tableManualRows">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="fs-7 fw-bold text-muted" style="width: 40%;">Host Account / Email</th>
                                                <th class="fs-7 fw-bold text-muted" style="width: 50%;">Google Meet URL</th>
                                                <th class="fs-7 fw-bold text-muted text-center" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="manualRowsBody">
                                            <!-- Row 1 -->
                                            <tr class="manual-input-row">
                                                <td>
                                                    <input type="email"
                                                           name="holder_email[]"
                                                           class="form-control form-control-sm"
                                                           placeholder="e.g. host1@jeroworks.com"
                                                           required>
                                                </td>
                                                <td>
                                                    <input type="text"
                                                           name="meeting_link[]"
                                                           class="form-control form-control-sm"
                                                           placeholder="e.g. https://meet.google.com/abc-defg-hij"
                                                           required>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-icon btn-light-danger btn-sm btn-remove-row" title="Remove row">
                                                        <i class="fas fa-trash-alt fs-7"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                            <!-- Row 2 -->
                                            <tr class="manual-input-row">
                                                <td>
                                                    <input type="email"
                                                           name="holder_email[]"
                                                           class="form-control form-control-sm"
                                                           placeholder="e.g. host2@jeroworks.com">
                                                </td>
                                                <td>
                                                    <input type="text"
                                                           name="meeting_link[]"
                                                           class="form-control form-control-sm"
                                                           placeholder="e.g. https://meet.google.com/xyz-uvwx-rst">
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-icon btn-light-danger btn-sm btn-remove-row" title="Remove row">
                                                        <i class="fas fa-trash-alt fs-7"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-end gap-3">
                                    <button type="button" class="btn btn-sm btn-light fw-bold" id="btnResetManualForm">
                                        Reset Rows
                                    </button>
                                    <button type="submit" class="btn btn-sm btn-primary fw-bold" id="btnSubmitManualLinks">
                                        <span class="indicator-label">
                                            <i class="fas fa-save me-1"></i> Save Links to Pool
                                        </span>
                                        <span class="indicator-progress">
                                            Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Sub-Card B: Batch File Import -->
                <div class="col-lg-5">
                    <div class="gmeet-sub-card">
                        <div class="gmeet-sub-card-header">
                            <h3>
                                <i class="fas fa-file-excel text-success"></i>
                                Spreadsheet Batch Import
                            </h3>
                            <a href="{{ url('/img/importDemo.xlsx') }}" class="btn btn-sm btn-light-success fw-bold" download>
                                <i class="fas fa-download me-1"></i> Download Sample (.xlsx)
                            </a>
                        </div>
                        <div class="gmeet-sub-card-body">
                            <p class="text-muted fs-7 mb-4">
                                Import hundreds of meeting links in bulk using an Excel (<code>.xlsx</code>) or CSV file. The file must have <strong>Column 1: Host Email</strong> and <strong>Column 2: Meeting Link</strong>.
                            </p>

                            <form id="formBatchImport" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="tablename" value="google_meeting_lists">

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark fs-7">Select Spreadsheet File</label>
                                    <input type="file"
                                           name="meeting_link_import"
                                           id="inputImportFile"
                                           class="form-control form-control-sm"
                                           accept=".xlsx,.csv"
                                           required>
                                    <div class="form-text fs-8 text-muted mt-1">
                                        Accepted formats: Microsoft Excel (<code>.xlsx</code>), Comma-Separated Values (<code>.csv</code>). Max size: 10MB.
                                    </div>
                                </div>

                                <!-- Progress Bar (Hidden by Default) -->
                                <div class="progress mb-4 d-none" id="importProgressBarContainer" style="height: 18px; border-radius: 6px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                         role="progressbar"
                                         style="width: 0%;"
                                         id="importProgressBar">
                                        0%
                                    </div>
                                </div>

                                <div id="importResponseAlert" class="mb-4 d-none"></div>

                                <button type="submit" class="btn btn-sm btn-success fw-bold w-100" id="btnSubmitImport">
                                    <span class="indicator-label">
                                        <i class="fas fa-cloud-upload-alt me-1"></i> Upload & Import to Pool
                                    </span>
                                    <span class="indicator-progress">
                                        Uploading... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ────────────────────────────────────────── -->
        <!-- TAB 3: ACTIVE ALLOCATION & SCHEDULE MONITOR-->
        <!-- ────────────────────────────────────────── -->
        <div class="tab-pane fade" id="tab_schedule_monitor" role="tabpanel">
            <div class="gmeet-sub-card">
                <div class="gmeet-sub-card-header flex-wrap gap-3">
                    <div>
                        <h3>
                            <i class="fas fa-calendar-alt text-info"></i>
                            Live Interview Allocation Monitor
                        </h3>
                        <div class="text-muted fs-8 mt-1">
                            Audits recent candidate interview schedule records from <code>google_meeting_schedule</code>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btnRefreshSchedules">
                        <i class="fas fa-sync-alt me-1"></i> Refresh Allocations
                    </button>
                </div>

                <div class="gmeet-sub-card-body p-0">
                    <div class="table-responsive">
                        <table class="table gmeet-table align-middle table-row-dashed" id="tableScheduleHistory">
                            <thead>
                                <tr>
                                    <th class="w-50px text-center">#</th>
                                    <th class="min-w-260px">Assigned Google Meet Link</th>
                                    <th class="min-w-180px">Scheduled Date & Time</th>
                                    <th class="min-w-180px">Tenant Company</th>
                                    <th class="min-w-180px">Recruiter</th>
                                    <th class="min-w-110px text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="scheduleHistoryTbody">
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-muted">
                                        <span class="spinner-border spinner-border-sm text-primary me-2"></span>
                                        Loading schedule allocations...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ────────────────────────────────────────── -->
        <!-- TAB 4: INTEGRATION ARCHITECTURE & GUIDE    -->
        <!-- ────────────────────────────────────────── -->
        <div class="tab-pane fade" id="tab_architecture_guide" role="tabpanel">
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="gmeet-sub-card">
                        <div class="gmeet-sub-card-header">
                            <h3>
                                <i class="fas fa-project-diagram text-primary"></i>
                                How JERO ATS Uses the Google Meet Link Pool
                            </h3>
                        </div>
                        <div class="gmeet-sub-card-body">
                            <div class="guide-step-card">
                                <div class="guide-step-number">1</div>
                                <div>
                                    <h5 class="fs-6 fw-bolder text-dark mb-1">Pre-Allocated Link Pool</h5>
                                    <p class="text-muted fs-7 mb-0">
                                        Super Admin maintains permanent, reusable Google Meet URLs in <code>google_meeting_lists</code>. Unlike Zoom (which requires dynamic API token minting), Google Meet uses static conference spaces.
                                    </p>
                                </div>
                            </div>

                            <div class="guide-step-card">
                                <div class="guide-step-number">2</div>
                                <div>
                                    <h5 class="fs-6 fw-bolder text-dark mb-1">Conflict-Free Slot Allocation Algorithm</h5>
                                    <p class="text-muted fs-7 mb-0">
                                        When an agency or hiring recruiter schedules an interview in <code>ServiceCompanyJobApplicationController.php:9527</code>, the ATS checks <code>google_meeting_schedule</code> for any meeting booked on that exact date. The system assigns the next free active link from the pool.
                                    </p>
                                </div>
                            </div>

                            <div class="guide-step-card">
                                <div class="guide-step-number">3</div>
                                <div>
                                    <h5 class="fs-6 fw-bolder text-dark mb-1">Automatic Re-availability</h5>
                                    <p class="text-muted fs-7 mb-0">
                                        Because links are checked per-day, the same Google Meet link can be scheduled again on a different calendar day without conflicting with previous candidate interviews.
                                    </p>
                                </div>
                            </div>

                            <div class="guide-step-card">
                                <div class="guide-step-number">4</div>
                                <div>
                                    <h5 class="fs-6 fw-bolder text-dark mb-1">Pool Capacity Shielding</h5>
                                    <p class="text-muted fs-7 mb-0">
                                        If all pool links are booked for a given date, the ATS gracefully reports <code>status: expired</code>, advising the recruiter to choose an alternate time or notifying the Super Admin to expand the pool.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="gmeet-sub-card">
                        <div class="gmeet-sub-card-header">
                            <h3>
                                <i class="fas fa-lightbulb text-warning"></i>
                                Best Practices & Recommendations
                            </h3>
                        </div>
                        <div class="gmeet-sub-card-body">
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark fs-7 mb-2">Recommended Pool Size</h6>
                                <p class="text-muted fs-8 mb-2">
                                    Maintain at least <strong>1.5× to 2×</strong> your expected peak daily concurrent interviews. For example, if recruiters conduct 10 simultaneous interviews daily, maintain at least 20 active pool links.
                                </p>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold text-dark fs-7 mb-2">Google Workspace Link Creation</h6>
                                <p class="text-muted fs-8 mb-2">
                                    To create static links:
                                </p>
                                <ol class="text-muted fs-8 ps-4 mb-0">
                                    <li>Open Google Calendar with your corporate account.</li>
                                    <li>Create a recurring meeting or open meet.google.com/new.</li>
                                    <li>Copy the conference URL (e.g. <code>https://meet.google.com/xxx-xxxx-xxx</code>).</li>
                                    <li>Ensure "Host management" settings in Google Meet allow external candidates to knock or join without manual admission if desired.</li>
                                </ol>
                            </div>

                            <div class="alert alert-light-primary border border-primary border-dashed d-flex align-items-center p-3 mb-0">
                                <i class="fas fa-info-circle text-primary fs-4 me-3"></i>
                                <span class="text-dark fs-8">
                                    Need direct Server-to-Server dynamic generation? Check out <a href="{{ route('superadmin.company.zoom_setting') }}" class="fw-bold text-primary">Zoom Video Integration</a>.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: EDIT GOOGLE MEETING LINK            -->
<!-- ========================================== -->
<div class="modal fade" id="modalEditMeetingLink" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content" style="border-radius: 10px; border: 1px solid #e2e8f0;">
            <div class="modal-header pb-0 border-0 justify-content-between">
                <h4 class="fw-bolder text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-edit text-primary"></i> Edit Google Meet Link
                </h4>
                <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal">
                    <i class="fas fa-times fs-6"></i>
                </button>
            </div>
            <div class="modal-body py-6">
                <form id="formEditMeetingLink">
                    @csrf
                    <input type="hidden" id="editLinkId" name="id">

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-7">Host Account / Email <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control form-control-sm"
                               id="editAccountName"
                               name="account_name"
                               placeholder="e.g. interviews@company.com"
                               required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark fs-7">Google Meet URL <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control form-control-sm"
                               id="editMeetingLink"
                               name="meeting_link"
                               placeholder="e.g. https://meet.google.com/xxx-xxxx-xxx"
                               required>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-6">
                        <button type="button" class="btn btn-sm btn-light fw-bold" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-sm btn-primary fw-bold" id="btnSubmitEditLink">
                            <span class="indicator-label">
                                <i class="fas fa-save me-1"></i> Update Link
                            </span>
                            <span class="indicator-progress">
                                Updating... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('footer-script')
<script>
/**
 * Isolated Client-First JavaScript Engine for Google Meeting Hub
 * Strictly scoped to prevent global namespace pollution. Zero full-page reload on status toggles.
 * Features 0ms instant in-memory table search, safe notification bridge, and dynamic KPI updates.
 */
(function($) {
    'use strict';

    $(document).ready(function() {

        // ─── Resilient Notification Bridge (SweetAlert2 Toast + Fallback) ───
        function showNotify(type, message) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true,
                    icon: type === 'error' ? 'error' : (type === 'warning' ? 'warning' : (type === 'info' ? 'info' : 'success')),
                    title: message
                });
            } else if (typeof toastr !== 'undefined') {
                if (typeof toastr[type] === 'function') {
                    toastr[type](message);
                } else {
                    toastr.info(message);
                }
            }
        }

        // ─── Search Box Input Helper & 0ms In-Memory Fast Filter ───
        const $searchInput = $('#inputSearchLink');
        const $clearBtn = $('#btnClearSearch');
        let fastSearchTimer;

        function toggleClearBtn() {
            if ($searchInput.val().trim().length > 0) {
                $clearBtn.show();
            } else {
                $clearBtn.hide();
            }
        }
        toggleClearBtn();

        // 0ms instant in-memory table filter while typing
        $searchInput.on('input', function() {
            toggleClearBtn();
            const query = $(this).val().toLowerCase().trim();

            clearTimeout(fastSearchTimer);
            fastSearchTimer = setTimeout(function() {
                $('#tableGoogleMeetLinks tbody tr:not(.empty-row)').each(function() {
                    const account = $(this).find('.item-account-name').text().toLowerCase();
                    const link = $(this).find('.item-meeting-link').text().toLowerCase();

                    if (query === '' || account.indexOf(query) !== -1 || link.indexOf(query) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            }, 120);
        });

        // ─── Super Admin Standard AJAX Pagination & Filter Engine ───
        let activePageXhr = null;

        function fetchGoogleMeetingLinks(page) {
            page = page || 1;
            const searchQuery = $.trim($searchInput.val());
            const statusFilter = $('#selectStatusFilter').val();

            if (activePageXhr) {
                activePageXhr.abort();
            }

            $('#tableGoogleMeetLinks tbody').css({ opacity: '0.45', transition: 'opacity 0.15s ease' });

            activePageXhr = $.ajax({
                url: "{{ route('superadmin.company.google_meeting_setting') }}",
                type: 'GET',
                data: {
                    page: page,
                    meeting_link: searchQuery,
                    status: statusFilter
                },
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(res) {
                    $('#tableGoogleMeetLinks tbody').css('opacity', '1');
                    if (res && res.status === 'success') {
                        $('#tableGoogleMeetLinksBody').html(res.html);
                        $('#gmeetPaginationContainer').html(res.pagination);

                        // Reset master checkbox and bulk delete state
                        $('#masterCheckbox').prop('checked', false);
                        updateBulkDeleteState();

                        // Sync URL query string without reloading
                        const newUrl = new URL(window.location.href);
                        newUrl.searchParams.set('page', page);
                        if (searchQuery) {
                            newUrl.searchParams.set('meeting_link', searchQuery);
                        } else {
                            newUrl.searchParams.delete('meeting_link');
                            newUrl.searchParams.delete('q');
                        }
                        if (statusFilter !== '' && statusFilter !== null && typeof statusFilter !== 'undefined') {
                            newUrl.searchParams.set('status', statusFilter);
                        } else {
                            newUrl.searchParams.delete('status');
                        }
                        window.history.pushState({ page: page }, '', newUrl.toString());

                        // Sync KPI counters
                        if (res.counts) {
                            if (typeof res.counts.total !== 'undefined') $('#kpiTotalCount').text(res.counts.total);
                            if (typeof res.counts.active !== 'undefined') $('#kpiActiveCount').text(res.counts.active);
                            if (typeof res.counts.inactive !== 'undefined') $('#kpiInactiveCount').text(res.counts.inactive);
                            if (typeof res.counts.scheduled !== 'undefined') $('#kpiScheduledCount').text(res.counts.scheduled);
                        }
                    }
                },
                error: function(xhr, status) {
                    $('#tableGoogleMeetLinks tbody').css('opacity', '1');
                    if (status !== 'abort') {
                        // Fallback to standard URL navigation if AJAX is blocked
                        const fallbackUrl = new URL(window.location.href);
                        fallbackUrl.searchParams.set('page', page);
                        if (searchQuery) fallbackUrl.searchParams.set('meeting_link', searchQuery);
                        if (statusFilter !== '' && statusFilter !== null) fallbackUrl.searchParams.set('status', statusFilter);
                        window.location.href = fallbackUrl.toString();
                    }
                },
                complete: function() {
                    activePageXhr = null;
                }
            });
        }

        // ─── Super Admin Standard Pagination Click Handling ───
        $(document).on('click', '#gmeetPaginationContainer .page-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled') || $(this).hasClass('dots') || $(this).hasClass('pagi-disabled') || $(this).hasClass('active') || $(this).hasClass('pagi-active')) {
                return;
            }
            fetchGoogleMeetingLinks(parseInt(page, 10));

            const tableElem = document.getElementById('tableGoogleMeetLinks');
            if (tableElem) {
                tableElem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });

        // Browser Back/Forward navigation support
        window.addEventListener('popstate', function() {
            const url = new URL(window.location.href);
            const p = parseInt(url.searchParams.get('page') || '1', 10);
            fetchGoogleMeetingLinks(p);
        });

        $clearBtn.on('click', function() {
            $searchInput.val('');
            toggleClearBtn();
            fetchGoogleMeetingLinks(1);
        });

        // Auto fetch search/filter when status dropdown changes or form submitted
        $('#selectStatusFilter').on('change', function() {
            fetchGoogleMeetingLinks(1);
        });

        $('#searchForm').on('submit', function(e) {
            e.preventDefault();
            fetchGoogleMeetingLinks(1);
        });

        // ─── Quick Switch Tab Buttons in Toolbar ───
        $('#btnSwitchAddTab, #btnEmptyStateAdd').on('click', function() {
            $('.gmeet-nav-pills a[href="#tab_add_import"]').tab('show');
            setTimeout(function() {
                $('#tableManualRows input:first').focus();
            }, 150);
        });

        $('#btnSwitchImportTab').on('click', function() {
            $('.gmeet-nav-pills a[href="#tab_add_import"]').tab('show');
            setTimeout(function() {
                $('#inputImportFile').focus();
            }, 150);
        });

        // ─── Clipboard Copy Utility ───
        $(document).on('click', '.btn-copy-clipboard', function(e) {
            e.preventDefault();
            const textToCopy = $(this).attr('data-clipboard-text');
            const $btn = $(this);

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(textToCopy).then(function() {
                    showCopiedFeedback($btn);
                }).catch(function() {
                    fallbackCopyText(textToCopy, $btn);
                });
            } else {
                fallbackCopyText(textToCopy, $btn);
            }
        });

        function showCopiedFeedback($btn) {
            const originalHtml = $btn.html();
            $btn.html('<i class="fas fa-check text-success fs-7"></i>');
            showNotify('success', 'Google Meet link copied to clipboard!');
            setTimeout(function() {
                $btn.html(originalHtml);
            }, 1500);
        }

        function fallbackCopyText(text, $btn) {
            const $temp = $('<textarea>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            showCopiedFeedback($btn);
        }

        // ─── Two-Tier Idempotency & Enterprise Sync Engine ───
        function generateUUID() {
            if (typeof crypto !== 'undefined' && crypto.randomUUID) {
                return crypto.randomUUID();
            }
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                const r = Math.random() * 16 | 0, v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        }

        const gmeetChannel = (typeof BroadcastChannel !== 'undefined') ? new BroadcastChannel('jero_google_meeting_hub') : null;
        if (gmeetChannel) {
            gmeetChannel.onmessage = function(e) {
                const data = e.data;
                if (!data) return;
                if (data.action === 'STATUS_UPDATED') {
                    const $r = $('#row-' + data.id);
                    if ($r.length) {
                        const $sw = $r.find('.status-toggle');
                        const $bc = $r.find('.status-badge-container');
                        $sw.prop('checked', data.status == 1).data('current', data.status);
                        if (data.status == 1) {
                            $bc.html('<span class="badge-status-active">Active</span>');
                        } else {
                            $bc.html('<span class="badge-status-inactive">Paused</span>');
                        }
                    }
                } else if (data.action === 'LINK_UPDATED' && data.data) {
                    const $r = $('#row-' + data.id);
                    if ($r.length) {
                        $r.find('.item-account-name').text(data.data.account_name);
                        $r.find('.item-meeting-link').text(data.data.meeting_link);
                        $r.find('.meet-link-pill').attr('title', data.data.meeting_link);
                        $r.find('.btn-copy-clipboard').attr('data-clipboard-text', data.data.meeting_link);
                        $r.find('a[target="_blank"]').attr('href', data.data.meeting_link);
                    }
                } else if (data.action === 'LINKS_DELETED' && Array.isArray(data.ids)) {
                    data.ids.forEach(function(delId) {
                        $('#row-' + delId).remove();
                    });
                    updateBulkDeleteState();
                }
            };
        }

        const inFlightToggles = new Set();
        let inFlightBulkDelete = false;
        let inFlightSingleDelete = false;
        let activeScheduleXhr = null;
        const DRAFT_KEY = 'jero_gmeet_manual_draft';

        // ─── Instant Status Toggle (Zero Page Reload + Two-Tier Idempotency) ───
        $(document).on('change', '.status-toggle', function() {
            const $switch = $(this);
            const linkId = $switch.data('id');
            if (inFlightToggles.has(linkId)) {
                return;
            }
            inFlightToggles.add(linkId);

            const newStatus = $switch.is(':checked') ? 1 : 0;
            const $row = $('#row-' + linkId);
            const $badgeContainer = $row.find('.status-badge-container');

            $switch.prop('disabled', true);
            const idempotencyKey = generateUUID();

            $.ajax({
                url: "{{ route('superadmin.company.googleMeetStatusUpdate') }}",
                type: 'POST',
                headers: {
                    'X-Idempotency-Key': idempotencyKey
                },
                data: {
                    '_token': '{{ csrf_token() }}',
                    'id': linkId,
                    'chkStatus': newStatus,
                    'idempotency_key': idempotencyKey
                },
                success: function(res) {
                    inFlightToggles.delete(linkId);
                    $switch.prop('disabled', false);
                    if (res.status === 'success') {
                        $switch.data('current', newStatus);

                        if (newStatus === 1) {
                            $badgeContainer.html('<span class="badge-status-active">Active</span>');
                            showNotify('success', res.message || 'Link activated successfully');
                            updateKpiCounts(1);
                        } else {
                            $badgeContainer.html('<span class="badge-status-inactive">Paused</span>');
                            showNotify('info', res.message || 'Link paused successfully');
                            updateKpiCounts(-1);
                        }

                        if (gmeetChannel) {
                            gmeetChannel.postMessage({ action: 'STATUS_UPDATED', id: linkId, status: newStatus });
                        }
                    } else {
                        revertSwitch($switch, $badgeContainer);
                        Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to update status.' });
                    }
                },
                error: function(xhr) {
                    inFlightToggles.delete(linkId);
                    $switch.prop('disabled', false);
                    revertSwitch($switch, $badgeContainer);
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error updating status.';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                }
            });
        });

        function revertSwitch($switch, $badgeContainer) {
            const prev = $switch.data('current') == 1;
            $switch.prop('checked', prev);
            if (prev) {
                $badgeContainer.html('<span class="badge-status-active">Active</span>');
            } else {
                $badgeContainer.html('<span class="badge-status-inactive">Paused</span>');
            }
        }

        function updateKpiCounts(activeDelta) {
            const $active = $('#kpiActiveCount');
            const $inactive = $('#kpiInactiveCount');
            let a = parseInt($active.text()) || 0;
            let i = parseInt($inactive.text()) || 0;

            a += activeDelta;
            i -= activeDelta;

            if (a < 0) a = 0;
            if (i < 0) i = 0;

            $active.text(a);
            $inactive.text(i);
        }

        // ─── Master Checkbox & Bulk Actions ───
        const $masterCheckbox = $('#masterCheckbox');
        const $btnBulkDelete = $('#btnBulkDelete');
        const $bulkDeleteCount = $('#bulkDeleteCount');

        function updateBulkDeleteState() {
            const checkedCount = $('.row-checkbox:checked').length;
            $bulkDeleteCount.text(checkedCount);
            if (checkedCount > 0) {
                $btnBulkDelete.removeClass('d-none');
            } else {
                $btnBulkDelete.addClass('d-none');
            }

            const totalRows = $('.row-checkbox').length;
            if (totalRows > 0 && checkedCount === totalRows) {
                $masterCheckbox.prop('checked', true);
            } else {
                $masterCheckbox.prop('checked', false);
            }
        }

        $masterCheckbox.on('change', function() {
            const isChecked = $(this).is(':checked');
            $('.row-checkbox').prop('checked', isChecked);
            updateBulkDeleteState();
        });

        $(document).on('change', '.row-checkbox', function() {
            updateBulkDeleteState();
        });

        // ─── Bulk Delete Execution with Idempotency Fencing ───
        $btnBulkDelete.on('click', function() {
            if (inFlightBulkDelete) return;

            const selectedIds = [];
            $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                Swal.fire({ icon: 'warning', title: 'No Selection', text: 'Please select at least one meeting link to delete.' });
                return;
            }

            Swal.fire({
                title: 'Delete ' + selectedIds.length + ' Meeting Link(s)?',
                text: 'These links will be permanently removed from the Google Meet pool.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete selected!'
            }).then(function(result) {
                if (result.isConfirmed) {
                    inFlightBulkDelete = true;
                    $btnBulkDelete.prop('disabled', true);
                    const idempotencyKey = generateUUID();

                    $.ajax({
                        url: "{{ route('superadmin.company.meetingListDelete') }}",
                        type: 'POST',
                        headers: {
                            'X-Idempotency-Key': idempotencyKey
                        },
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'arr': selectedIds,
                            'table': 'google_meeting_lists',
                            'idempotency_key': idempotencyKey
                        },
                        success: function(res) {
                            inFlightBulkDelete = false;
                            $btnBulkDelete.prop('disabled', false);
                            if (res.status) {
                                if (gmeetChannel) {
                                    gmeetChannel.postMessage({ action: 'LINKS_DELETED', ids: selectedIds });
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: res.message || 'Records deleted successfully.',
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error', text: res.message || 'Failed to delete.' });
                            }
                        },
                        error: function(xhr) {
                            inFlightBulkDelete = false;
                            $btnBulkDelete.prop('disabled', false);
                            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Something went wrong on the server.';
                            Swal.fire({ icon: 'error', title: 'Server Error', text: msg });
                        }
                    });
                }
            });
        });

        // ─── Single Delete Action with Idempotency Fencing ───
        $(document).on('click', '.btn-delete-single', function() {
            if (inFlightSingleDelete) return;

            const id = $(this).data('id');
            const $row = $('#row-' + id);
            const isRowActive = $row.find('.status-toggle').is(':checked');

            Swal.fire({
                title: 'Delete Meeting Link?',
                text: 'This link will be removed from the available pool.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then(function(result) {
                if (result.isConfirmed) {
                    inFlightSingleDelete = true;
                    const idempotencyKey = generateUUID();

                    $.ajax({
                        url: "{{ route('superadmin.company.meetingListDelete') }}",
                        type: 'POST',
                        headers: {
                            'X-Idempotency-Key': idempotencyKey
                        },
                        data: {
                            '_token': '{{ csrf_token() }}',
                            'arr': [id],
                            'table': 'google_meeting_lists',
                            'idempotency_key': idempotencyKey
                        },
                        success: function(res) {
                            inFlightSingleDelete = false;
                            if (res.status) {
                                if (gmeetChannel) {
                                    gmeetChannel.postMessage({ action: 'LINKS_DELETED', ids: [id] });
                                }

                                $row.fadeOut(300, function() {
                                    $(this).remove();
                                    updateBulkDeleteState();

                                    // Dynamic KPI counter update
                                    let total = parseInt($('#kpiTotalCount').text()) || 0;
                                    if (total > 0) $('#kpiTotalCount').text(total - 1);

                                    if (isRowActive) {
                                        let a = parseInt($('#kpiActiveCount').text()) || 0;
                                        if (a > 0) $('#kpiActiveCount').text(a - 1);
                                    } else {
                                        let i = parseInt($('#kpiInactiveCount').text()) || 0;
                                        if (i > 0) $('#kpiInactiveCount').text(i - 1);
                                    }
                                });
                                showNotify('success', 'Meeting link deleted successfully');
                            } else {
                                Swal.fire({ icon: 'error', title: 'Error', text: res.message });
                            }
                        },
                        error: function(xhr) {
                            inFlightSingleDelete = false;
                            const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error occurred.';
                            Swal.fire({ icon: 'error', title: 'Error', text: msg });
                        }
                    });
                }
            });
        });

        // ─── Edit Link Modal Handler with Idempotency Fencing ───
        const $modalEdit = $('#modalEditMeetingLink');
        const $formEdit = $('#formEditMeetingLink');
        const $btnSubmitEdit = $('#btnSubmitEditLink');

        $(document).on('click', '.btn-edit-link', function() {
            const id = $(this).data('id');
            const account = $(this).data('account');
            const link = $(this).data('link');

            $('#editLinkId').val(id);
            $('#editAccountName').val(account);
            $('#editMeetingLink').val(link);

            $modalEdit.modal('show');
        });

        $formEdit.on('submit', function(e) {
            e.preventDefault();
            const id = $('#editLinkId').val();
            const url = "{{ url('super-admin/google-meeting-update') }}/" + id;

            $btnSubmitEdit.attr('data-kt-indicator', 'on').prop('disabled', true);
            const idempotencyKey = generateUUID();

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-Idempotency-Key': idempotencyKey
                },
                data: $formEdit.serialize() + '&idempotency_key=' + encodeURIComponent(idempotencyKey),
                success: function(res) {
                    $btnSubmitEdit.removeAttr('data-kt-indicator').prop('disabled', false);
                    if (res.status) {
                        $modalEdit.modal('hide');

                        // Update in-place row data seamlessly
                        const $row = $('#row-' + id);
                        $row.find('.item-account-name').text(res.data.account_name);
                        $row.find('.item-meeting-link').text(res.data.meeting_link);
                        $row.find('.meet-link-pill').attr('title', res.data.meeting_link);
                        $row.find('.btn-copy-clipboard').attr('data-clipboard-text', res.data.meeting_link);
                        $row.find('a[target="_blank"]').attr('href', res.data.meeting_link);
                        $row.find('.btn-edit-link').data('account', res.data.account_name).data('link', res.data.meeting_link);

                        const initial = (res.data.account_name || 'G').charAt(0).toUpperCase();
                        $row.find('.host-avatar').text(initial);

                        if (gmeetChannel) {
                            gmeetChannel.postMessage({ action: 'LINK_UPDATED', id: id, data: res.data });
                        }

                        showNotify('success', res.message || 'Meeting link updated successfully');
                    } else {
                        Swal.fire({ icon: res.type || 'warning', title: 'Notice', text: res.message });
                    }
                },
                error: function(xhr) {
                    $btnSubmitEdit.removeAttr('data-kt-indicator').prop('disabled', false);
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Failed to update meeting link.';
                    Swal.fire({ icon: 'error', title: 'Validation / Server Error', text: msg });
                }
            });
        });

        // ─── Dynamic Manual Entry Rows (Tab 2) & Draft Autosave ───
        function saveManualDraft() {
            try {
                const rows = [];
                $('.manual-input-row').each(function() {
                    const email = $(this).find('input[name="holder_email[]"]').val() || '';
                    const link = $(this).find('input[name="meeting_link[]"]').val() || '';
                    if (email.trim() !== '' || link.trim() !== '') {
                        rows.push({ email: email, link: link });
                    }
                });
                if (rows.length > 0) {
                    sessionStorage.setItem(DRAFT_KEY, JSON.stringify(rows));
                } else {
                    sessionStorage.removeItem(DRAFT_KEY);
                }
            } catch (e) {}
        }

        function restoreManualDraft() {
            try {
                const raw = sessionStorage.getItem(DRAFT_KEY);
                if (raw) {
                    const rows = JSON.parse(raw);
                    if (Array.isArray(rows) && rows.length > 0) {
                        $('#manualRowsBody').empty();
                        rows.forEach(function(r) {
                            $('#manualRowsBody').append(`
                                <tr class="manual-input-row">
                                    <td>
                                        <input type="email" name="holder_email[]" class="form-control form-control-sm" placeholder="e.g. host@jeroworks.com" value="${r.email || ''}">
                                    </td>
                                    <td>
                                        <input type="text" name="meeting_link[]" class="form-control form-control-sm" placeholder="e.g. https://meet.google.com/xxx-xxxx-xxx" value="${r.link || ''}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-icon btn-light-danger btn-sm btn-remove-row" title="Remove row">
                                            <i class="fas fa-trash-alt fs-7"></i>
                                        </button>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                }
            } catch (e) {}
        }

        restoreManualDraft();

        $(document).on('input', '.manual-input-row input', function() {
            saveManualDraft();
        });

        $('#btnAddRow').on('click', function() {
            const rowHtml = `
                <tr class="manual-input-row">
                    <td>
                        <input type="email" name="holder_email[]" class="form-control form-control-sm" placeholder="e.g. host@jeroworks.com">
                    </td>
                    <td>
                        <input type="text" name="meeting_link[]" class="form-control form-control-sm" placeholder="e.g. https://meet.google.com/xxx-xxxx-xxx">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-icon btn-light-danger btn-sm btn-remove-row" title="Remove row">
                            <i class="fas fa-trash-alt fs-7"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#manualRowsBody').append(rowHtml);
        });

        $(document).on('click', '.btn-remove-row', function() {
            if ($('.manual-input-row').length > 1) {
                $(this).closest('tr').remove();
                saveManualDraft();
            } else {
                showNotify('warning', 'At least one input row must be maintained.');
            }
        });

        $('#btnResetManualForm').on('click', function() {
            $('#formManualAddLinks')[0].reset();
            sessionStorage.removeItem(DRAFT_KEY);
        });

        // Submit Manual Add Links Form with Idempotency Fencing
        $('#formManualAddLinks').on('submit', function(e) {
            e.preventDefault();
            const $btn = $('#btnSubmitManualLinks');
            $btn.attr('data-kt-indicator', 'on').prop('disabled', true);
            const idempotencyKey = generateUUID();

            $.ajax({
                url: "{{ route('superadmin.company.settingGoogleMeetingStore') }}",
                type: 'POST',
                headers: {
                    'X-Idempotency-Key': idempotencyKey
                },
                data: $(this).serialize() + '&idempotency_key=' + encodeURIComponent(idempotencyKey),
                success: function(res) {
                    $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                    if (res.status) {
                        sessionStorage.removeItem(DRAFT_KEY);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message || 'Meeting links inserted successfully.',
                            timer: 1800,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = "{{ route('superadmin.company.google_meeting_setting') }}";
                        });
                    } else {
                        Swal.fire({ icon: res.type || 'warning', title: 'Notice', text: res.message });
                    }
                },
                error: function(xhr) {
                    $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Server error occurred.';
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                }
            });
        });

        // ─── Batch File Import (Tab 2) ───
        $('#formBatchImport').on('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const $btn = $('#btnSubmitImport');
            const $progressContainer = $('#importProgressBarContainer');
            const $progressBar = $('#importProgressBar');
            const $alert = $('#importResponseAlert');

            $btn.attr('data-kt-indicator', 'on').prop('disabled', true);
            $progressContainer.removeClass('d-none');
            $progressBar.css('width', '10%').text('10%');
            $alert.addClass('d-none').empty();

            $.ajax({
                url: "{{ url('/import-meetings') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    const xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            const percent = Math.round((e.loaded / e.total) * 100);
                            $progressBar.css('width', percent + '%').text(percent + '%');
                        }
                    });
                    return xhr;
                },
                success: function(res) {
                    $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                    $progressBar.css('width', '100%').text('Upload Complete');

                    if (res.status) {
                        $alert.removeClass('d-none alert-danger').addClass('alert alert-light-success border border-success')
                              .html('<i class="fas fa-check-circle text-success me-2"></i>' + (res.message || 'Import job queued successfully.'));
                        setTimeout(function() {
                            window.location.href = res.url || "{{ route('superadmin.company.google_meeting_setting') }}";
                        }, 2000);
                    } else {
                        $alert.removeClass('d-none alert-success').addClass('alert alert-light-danger border border-danger')
                              .html('<i class="fas fa-exclamation-circle text-danger me-2"></i>' + (res.message || 'Import failed.'));
                    }
                },
                error: function(xhr) {
                    $btn.removeAttr('data-kt-indicator').prop('disabled', false);
                    const msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'File upload failed.';
                    $alert.removeClass('d-none alert-success').addClass('alert alert-light-danger border border-danger')
                          .html('<i class="fas fa-exclamation-triangle text-danger me-2"></i>' + msg);
                }
            });
        });

        // ─── Active Allocation & Schedule Monitor (Tab 3) with Active XHR Abort ───
        let scheduleHistoryLoaded = false;

        // Auto-fetch when tab is clicked or refreshed
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            const targetTab = $(e.target).attr('href');
            if (targetTab === '#tab_schedule_monitor' && !scheduleHistoryLoaded) {
                loadScheduleHistory();
            }
        });

        $('#tabScheduleNav, #btnRefreshSchedules').on('click', function() {
            loadScheduleHistory();
        });

        function loadScheduleHistory() {
            if (activeScheduleXhr && activeScheduleXhr.readyState !== 4) {
                activeScheduleXhr.abort();
            }

            const $tbody = $('#scheduleHistoryTbody');
            $tbody.html('<tr><td colspan="6" class="text-center py-8 text-muted"><span class="spinner-border spinner-border-sm text-primary me-2"></span>Loading schedule allocations...</td></tr>');

            activeScheduleXhr = $.ajax({
                url: "{{ route('superadmin.company.googleMeetingScheduleHistory') }}",
                type: 'GET',
                success: function(res) {
                    if (res.status && res.data && res.data.length > 0) {
                        let html = '';
                        res.data.forEach(function(item, idx) {
                            const now = new Date();
                            const schedDate = new Date(item.interview_date);
                            const isUpcoming = schedDate >= now;
                            const statusBadge = isUpcoming
                                ? '<span class="badge-status-booked"><i class="fas fa-clock"></i> Upcoming</span>'
                                : '<span class="badge-status-active"><i class="fas fa-check"></i> Completed</span>';

                            html += `
                                <tr>
                                    <td class="text-center text-muted fw-bold fs-7">${idx + 1}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="meet-link-pill" title="${item.meeting_link}">
                                                <i class="fas fa-video text-success fs-7"></i>
                                                <span class="meet-link-text">${item.meeting_link}</span>
                                            </span>
                                            <a href="${item.meeting_link}" target="_blank" rel="noopener noreferrer" class="btn btn-icon btn-light-success btn-sm" title="Join conference">
                                                <i class="fas fa-external-link-alt fs-7"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-bold fs-7">${item.interview_date}</span>
                                            <span class="text-muted fs-8">Candidate Job Con #${item.interview_job_wise_con_id}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bolder text-dark fs-7">${item.company_name || 'N/A'}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark fs-7">${item.recruiter_name || 'N/A'}</span>
                                            <span class="text-muted fs-8">${item.recruiter_email || ''}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        ${statusBadge}
                                    </td>
                                </tr>
                            `;
                        });
                        $tbody.html(html);
                        scheduleHistoryLoaded = true;
                    } else {
                        $tbody.html('<tr><td colspan="6" class="text-center py-8 text-muted"><i class="fas fa-info-circle me-1"></i> No recorded interview allocations found in schedule database.</td></tr>');
                    }
                },
                error: function(xhr) {
                    if (xhr.statusText === 'abort') return;
                    $tbody.html('<tr><td colspan="6" class="text-center py-8 text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Failed to load schedule history.</td></tr>');
                }
            });
        }

    });
})(jQuery);
</script>
@endpush