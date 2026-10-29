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
    .teams-page-container {
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 16px 30px 16px !important;
        margin-top: 0 !important;
    }
    @media (max-width: 1400px) {
        .teams-toolbar-card {
            padding: 14px 20px;
        }
        .teams-search-box {
            min-width: 220px;
            max-width: 320px;
        }
        .meet-link-pill {
            max-width: 260px;
        }
    }
    @media (max-width: 768px) {
        .teams-toolbar-card {
            padding: 12px 16px;
        }
        .teams-search-box {
            min-width: 100%;
            max-width: 100%;
        }
    }

    /* ─── Tier 1: Top Toolbar Card ─── */
    .teams-toolbar-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 16px 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 20px;
    }

    .teams-brand-icon-box {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #464eb8 0%, #33399b 100%);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 22px;
        box-shadow: 0 4px 12px rgba(70, 78, 184, 0.28);
    }

    /* ─── Tier 2: Sub-Tabs Card ─── */
    .teams-tabs-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        margin-bottom: 20px;
    }

    .teams-nav-pills .nav-link {
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

    .teams-nav-pills .nav-link:hover {
        color: #464eb8;
        background: #f8fafc;
    }

    .teams-nav-pills .nav-link.active {
        background-color: #009ef7 !important;
        color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0, 158, 247, 0.28);
    }

    /* ─── Tier 3: Modular Sub-Cards ─── */
    .teams-sub-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .teams-sub-card-header {
        padding: 16px 22px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
    }

    .teams-sub-card-header h3 {
        font-size: 14px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .teams-sub-card-body {
        padding: 22px;
    }

    /* ─── Search & Filter Controls ─── */
    .teams-search-box {
        position: relative;
        min-width: 280px;
        max-width: 380px;
    }

    .teams-search-box i.search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 14px;
    }

    .teams-search-box .form-control {
        padding-left: 38px;
        padding-right: 36px;
        height: 40px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .teams-search-box .form-control:focus {
        border-color: #464eb8;
        box-shadow: 0 0 0 3px rgba(70, 78, 184, 0.15);
    }

    .teams-search-box .clear-search-btn {
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

    .teams-search-box .clear-search-btn:hover {
        color: #ef4444;
    }

    /* ─── Table & Status Badges ─── */
    .teams-table {
        margin-bottom: 0;
    }

    .teams-table thead th {
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

    .teams-table tbody td {
        padding: 14px 16px;
        font-size: 13px;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .teams-table tbody tr:hover {
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
        color: #464eb8;
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
        background: #edeefc;
        color: #464eb8;
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
        background: #edeefc;
        color: #464eb8;
        border: 1px solid #c7c9f5;
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
<div class="teams-page-container container-fluid">

    <!-- ========================================== -->
    <!-- TIER 1: TOP HEADER TOOLBAR CARD            -->
    <!-- ========================================== -->
    <div class="teams-toolbar-card d-flex flex-stack flex-wrap gap-4 align-items-center">
        <div class="d-flex align-items-center gap-4">
            <div class="teams-brand-icon-box">
                <i class="fab fa-microsoft"></i>
            </div>
            <div>
                <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-0">
                    Microsoft Teams Link Pool & Allocation Hub
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
    <div class="teams-tabs-card">
        <ul class="nav teams-nav-pills gap-2 border-0" role="tablist">
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
    <div class="tab-content" id="teamsTabContent">

        <!-- ────────────────────────────────────────── -->
        <!-- TAB 1: MEETING LINKS DIRECTORY             -->
        <!-- ────────────────────────────────────────── -->
        <div class="tab-pane fade show active" id="tab_links_directory" role="tabpanel">
            <div class="teams-sub-card">
                <!-- Sub-Card Header with Search & Filter -->
                <div class="teams-sub-card-header flex-wrap gap-3">
                    <h3>
                        <i class="fas fa-th-list text-primary"></i>
                        Microsoft Teams Pool Directory
                    </h3>

                    <div class="d-flex align-items-center flex-wrap gap-3">
                        <!-- Debounced Search Box -->
                        <form method="GET" action="{{ route('superadmin.company.team_meeting_setting') }}" id="searchForm" class="d-flex align-items-center gap-2 m-0">
                            <div class="teams-search-box">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text"
                                       class="form-control"
                                       name="meeting_link"
                                       id="inputSearchLink"
                                       placeholder="Search by email or Teams link..."
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
                                <a href="{{ route('superadmin.company.team_meeting_setting') }}" class="btn btn-sm btn-light fw-bold" style="height: 40px; border-radius: 8px;" title="Reset filters">
                                    <i class="fas fa-undo"></i>
                                </a>
                            @endif
                        </form>
                    </div>
                </div>

                <!-- Table Sub-Card Body -->
                <div class="teams-sub-card-body p-0">
                    <div class="table-responsive">
                        <table class="table teams-table align-middle table-row-dashed" id="tableTeamsMeetLinks">
                            <thead>
                                <tr>
                                    <th class="w-30px text-center">
                                        <div class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                                            <input class="form-check-input" type="checkbox" id="masterCheckbox" title="Select all rows">
                                        </div>
                                    </th>
                                    <th class="w-40px text-center">#</th>
                                    <th class="min-w-220px">Host Account / Email</th>
                                    <th class="min-w-300px">Microsoft Teams Meeting Link URL</th>
                                    <th class="min-w-130px text-center">Status</th>
                                    <th class="min-w-150px text-end pe-6">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($team_meeting_setting as $key => $item)
                                    @php
                                        $initial = strtoupper(substr($item->account_name ?: 'T', 0, 1));
                                        $isActive = ((int) $item->status === 1);
                                    @endphp
                                    <tr id="row-{{ $item->id }}" data-id="{{ $item->id }}">
                                        <!-- Checkbox -->
                                        <td class="text-center">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid justify-content-center">
                                                <input class="form-check-input row-checkbox" type="checkbox" value="{{ $item->id }}" data-id="{{ $item->id }}">
                                            </div>
                                        </td>

                                        <!-- Row Index -->
                                        <td class="text-center text-muted fw-bold fs-7">
                                            {{ $team_meeting_setting->firstItem() + $key }}
                                        </td>

                                        <!-- Host Account / Email -->
                                        <td>
                                            <div class="host-account-wrapper">
                                                <div class="host-avatar">
                                                    {{ $initial }}
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <span class="text-dark fw-bolder fs-6 text-hover-primary item-account-name">
                                                        {{ $item->account_name ?: 'System Host' }}
                                                    </span>
                                                    <span class="text-muted fs-8">Teams Host ID #{{ $item->id }}</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Meeting Link URL -->
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="meet-link-pill" title="{{ $item->meeting_link }}">
                                                    <i class="fab fa-microsoft text-primary fs-7"></i>
                                                    <span class="meet-link-text item-meeting-link">
                                                        {{ $item->meeting_link }}
                                                    </span>
                                                </div>
                                                <button type="button"
                                                        class="btn btn-icon btn-light btn-sm btn-copy-clipboard"
                                                        data-clipboard-text="{{ $item->meeting_link }}"
                                                        title="Copy URL to clipboard">
                                                    <i class="far fa-copy text-muted fs-7"></i>
                                                </button>
                                                <a href="{{ $item->meeting_link }}"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   class="btn btn-icon btn-light-primary btn-sm"
                                                   title="Test / Join Teams Meeting in new tab">
                                                    <i class="fas fa-external-link-alt fs-7"></i>
                                                </a>
                                            </div>
                                        </td>

                                        <!-- Status Toggle -->
                                        <td class="text-center">
                                            <div class="d-flex flex-column align-items-center gap-1">
                                                <div class="form-check form-switch form-check-custom form-check-solid">
                                                    <input class="form-check-input status-toggle"
                                                           type="checkbox"
                                                           value="{{ $item->id }}"
                                                           id="status_switch_{{ $item->id }}"
                                                           {{ $isActive ? 'checked' : '' }}
                                                           data-id="{{ $item->id }}"
                                                           data-current="{{ $item->status }}">
                                                </div>
                                                <span class="status-badge-container">
                                                    @if($isActive)
                                                        <span class="badge-status-active">Active</span>
                                                    @else
                                                        <span class="badge-status-inactive">Paused</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-end pe-6">
                                            <div class="d-inline-flex gap-1">
                                                <button type="button"
                                                        class="btn btn-icon btn-light-primary btn-sm btn-edit-link"
                                                        data-id="{{ $item->id }}"
                                                        data-account="{{ $item->account_name }}"
                                                        data-link="{{ $item->meeting_link }}"
                                                        title="Edit account email or Teams link URL">
                                                    <i class="fas fa-edit fs-7"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn btn-icon btn-light-danger btn-sm btn-delete-single"
                                                        data-id="{{ $item->id }}"
                                                        title="Delete this link from pool">
                                                    <i class="fas fa-trash-alt fs-7"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-10">
                                            <div class="d-flex flex-column align-items-center justify-content-center">
                                                <div class="symbol symbol-60px mb-4">
                                                    <div class="symbol-label bg-light-warning text-warning">
                                                        <i class="fas fa-video-slash fs-2x"></i>
                                                    </div>
                                                </div>
                                                <h4 class="text-dark fw-bolder mb-1">No Microsoft Teams Meeting Links Found</h4>
                                                <p class="text-muted fs-7 mb-4">
                                                    @if(!empty($meeting_link) || ($statusFilter !== null && $statusFilter !== ''))
                                                        No meeting links match your search/filter criteria.
                                                    @else
                                                        Your Microsoft Teams pool is currently empty. Add meeting links to enable candidate interview scheduling.
                                                    @endif
                                                </p>
                                                <button type="button" class="btn btn-sm btn-primary fw-bold" id="btnEmptyStateAdd">
                                                    <i class="fas fa-plus-circle me-1"></i> Add Meeting Links Now
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer Strip -->
                    @if($team_meeting_setting->hasPages())
                        <div class="d-flex flex-stack flex-wrap gap-4 px-6 py-4 border-top border-gray-200">
                            <div class="text-muted fs-7 fw-semibold">
                                Showing {{ $team_meeting_setting->firstItem() }} to {{ $team_meeting_setting->lastItem() }} of {{ $team_meeting_setting->total() }} meeting links
                            </div>
                            <div>
                                <x-custom-pagination :paginator="$team_meeting_setting" />
                            </div>
                        </div>
                    @endif
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
                    <div class="teams-sub-card">
                        <div class="teams-sub-card-header">
                            <h3>
                                <i class="fas fa-keyboard text-primary"></i>
                                Dynamic Manual Link Entry
                            </h3>
                            <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btnAddRow">
                                <i class="fas fa-plus me-1"></i> Add Another Row
                            </button>
                        </div>
                        <div class="teams-sub-card-body">
                            <p class="text-muted fs-7 mb-4">
                                Enter host account emails and reusable Microsoft Teams meeting URLs. All inputs are automatically whitespace-trimmed and normalized with <code>https://</code>.
                            </p>

                            <form id="formManualAddLinks">
                                @csrf
                                <input type="hidden" name="tablename" value="team_meeting_lists">

                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle" id="tableManualRows">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="fs-7 fw-bold text-dark py-3 ps-3">Host Account / Email <span class="text-danger">*</span></th>
                                                <th class="fs-7 fw-bold text-dark py-3 ps-3">Microsoft Teams URL <span class="text-danger">*</span></th>
                                                <th class="w-50px text-center py-3"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="manualRowsTbody">
                                            <tr class="manual-input-row">
                                                <td class="p-2">
                                                    <input type="text"
                                                           name="holder_email[]"
                                                           class="form-control form-control-sm input-holder-email"
                                                           placeholder="e.g. interviews@company.com"
                                                           required>
                                                </td>
                                                <td class="p-2">
                                                    <input type="text"
                                                           name="meeting_link[]"
                                                           class="form-control form-control-sm input-meeting-link"
                                                           placeholder="e.g. https://teams.microsoft.com/l/meetup-join/..."
                                                           required>
                                                </td>
                                                <td class="text-center p-2">
                                                    <button type="button" class="btn btn-icon btn-light-danger btn-sm btn-remove-row" title="Remove row">
                                                        <i class="fas fa-times fs-7"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <span class="text-muted fs-8">
                                        Duplicate links already present in the pool will be safely ignored.
                                    </span>
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

                <!-- Sub-Card B: Batch XLSX/CSV File Import -->
                <div class="col-lg-5">
                    <div class="teams-sub-card">
                        <div class="teams-sub-card-header">
                            <h3>
                                <i class="fas fa-file-excel text-success"></i>
                                Batch File Import (.xlsx / .csv)
                            </h3>
                        </div>
                        <div class="teams-sub-card-body">
                            <p class="text-muted fs-7 mb-3">
                                Import dozens or hundreds of Microsoft Teams links instantly via Excel or CSV spreadsheet.
                            </p>

                            <!-- Format Structure Guide -->
                            <div class="alert alert-light-primary border border-primary border-dashed p-3 mb-4 rounded-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold fs-7 text-dark">Required Spreadsheet Format</span>
                                    <button type="button" class="btn btn-xs btn-light-primary fw-bold py-1 px-2" id="btnDownloadSampleCsv">
                                        <i class="fas fa-download me-1"></i> Sample CSV
                                    </button>
                                </div>
                                <div class="text-muted fs-8">
                                    • <strong>Column 1:</strong> Host Account / Email (e.g. <code>teams.host1@corp.com</code>)<br>
                                    • <strong>Column 2:</strong> Microsoft Teams URL (e.g. <code>https://teams.microsoft.com/...</code>)
                                </div>
                            </div>

                            <form id="formBatchImport" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="tablename" value="team_meeting_lists">

                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark fs-7">Select File (.xlsx or .csv)</label>
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
            <div class="teams-sub-card">
                <div class="teams-sub-card-header flex-wrap gap-3">
                    <div>
                        <h3>
                            <i class="fas fa-calendar-alt text-info"></i>
                            Live Interview Allocation Monitor
                        </h3>
                        <div class="text-muted fs-8 mt-1">
                            Audits recent candidate interview schedule records from <code>team_meeting_schedule</code>
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-light-primary fw-bold" id="btnRefreshSchedules">
                        <i class="fas fa-sync-alt me-1"></i> Refresh Allocations
                    </button>
                </div>

                <div class="teams-sub-card-body p-0">
                    <div class="table-responsive">
                        <table class="table teams-table align-middle table-row-dashed" id="tableScheduleHistory">
                            <thead>
                                <tr>
                                    <th class="w-50px text-center">#</th>
                                    <th class="min-w-260px">Assigned Microsoft Teams Link</th>
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
                    <div class="teams-sub-card">
                        <div class="teams-sub-card-header">
                            <h3>
                                <i class="fas fa-project-diagram text-primary"></i>
                                How JERO ATS Uses the Microsoft Teams Link Pool
                            </h3>
                        </div>
                        <div class="teams-sub-card-body">
                            <div class="guide-step-card">
                                <div class="guide-step-number">1</div>
                                <div>
                                    <h5 class="fs-6 fw-bolder text-dark mb-1">Pre-Allocated Link Pool</h5>
                                    <p class="text-muted fs-7 mb-0">
                                        Super Admin maintains permanent, reusable Microsoft Teams conference links in <code>team_meeting_lists</code>. Unlike Zoom (which requires dynamic API token minting), Teams uses pre-configured static conference coordinates.
                                    </p>
                                </div>
                            </div>

                            <div class="guide-step-card">
                                <div class="guide-step-number">2</div>
                                <div>
                                    <h5 class="fs-6 fw-bolder text-dark mb-1">Conflict-Free Slot Allocation Algorithm</h5>
                                    <p class="text-muted fs-7 mb-0">
                                        When an agency or hiring recruiter schedules an interview in <code>ServiceCompanyJobApplicationController.php:9645</code>, the ATS checks <code>team_meeting_schedule</code> for any meeting booked on that exact date. The system assigns the next free active link from the pool.
                                    </p>
                                </div>
                            </div>

                            <div class="guide-step-card">
                                <div class="guide-step-number">3</div>
                                <div>
                                    <h5 class="fs-6 fw-bolder text-dark mb-1">Automatic Re-availability</h5>
                                    <p class="text-muted fs-7 mb-0">
                                        Because links are checked per-day, the same Teams meeting link can be scheduled again on a different calendar day without conflicting with previous candidate interviews.
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
                    <div class="teams-sub-card">
                        <div class="teams-sub-card-header">
                            <h3>
                                <i class="fas fa-lightbulb text-warning"></i>
                                Best Practices & Recommendations
                            </h3>
                        </div>
                        <div class="teams-sub-card-body">
                            <div class="mb-4">
                                <h6 class="fw-bold text-dark fs-7 mb-2">Recommended Pool Size</h6>
                                <p class="text-muted fs-8 mb-2">
                                    Maintain at least <strong>1.5× to 2×</strong> your expected peak daily concurrent interviews. For example, if recruiters conduct 10 simultaneous interviews daily, maintain at least 20 active pool links.
                                </p>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold text-dark fs-7 mb-2">Microsoft 365 Link Creation</h6>
                                <p class="text-muted fs-8 mb-2">
                                    To create static links in Microsoft Teams:
                                </p>
                                <ol class="text-muted fs-8 ps-4 mb-0">
                                    <li>Open Outlook Calendar or Microsoft Teams app with your corporate account.</li>
                                    <li>Create a recurring meeting space (or click "Meet Now").</li>
                                    <li>Copy the conference URL (e.g. <code>https://teams.microsoft.com/l/meetup-join/...</code>).</li>
                                    <li>In Meeting Options, configure "Who can bypass the lobby?" so candidates can wait in lobby or join automatically as desired.</li>
                                </ol>
                            </div>

                            <div class="alert alert-light-primary border border-primary border-dashed d-flex align-items-center p-3 mb-0">
                                <i class="fas fa-info-circle text-primary fs-4 me-3"></i>
                                <span class="text-dark fs-8">
                                    Also supporting Google Meet? Configure static rooms in <a href="{{ route('superadmin.company.google_meeting_setting') }}" class="fw-bold text-primary">Google Meet Settings</a>.
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
<!-- MODAL: EDIT TEAMS MEETING LINK             -->
<!-- ========================================== -->
<div class="modal fade" id="modalEditMeetingLink" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered mw-500px">
        <div class="modal-content" style="border-radius: 10px; border: 1px solid #e2e8f0;">
            <div class="modal-header pb-0 border-0 justify-content-between">
                <h4 class="fw-bolder text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-edit text-primary"></i> Edit Microsoft Teams Link
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
                        <label class="form-label fw-bold text-dark fs-7">Microsoft Teams URL <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control form-control-sm"
                               id="editMeetingLink"
                               name="meeting_link"
                               placeholder="e.g. https://teams.microsoft.com/l/meetup-join/..."
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
 * Isolated Client-First JavaScript Engine for Microsoft Teams Meeting Hub
 * Strictly scoped to prevent global namespace pollution. Zero full-page reload on status toggles.
 * Features 0ms instant in-memory table search, safe notification bridge, and dynamic KPI updates.
 */
(function(window, document, $) {
    'use strict';

    // Route Endpoints
    var ROUTES = {
        storeLinks: "{{ route('superadmin.company.settingteamMeetingStore') }}",
        toggleStatus: "{{ route('superadmin.company.teamMeetStatusUpdate') }}",
        deleteLinks: "{{ route('superadmin.company.meetingListDelete') }}",
        updateLinkBase: "{{ url('super-admin/team-meeting-update') }}",
        scheduleHistory: "{{ route('superadmin.company.teamMeetingScheduleHistory') }}",
        batchImport: "{{ route('importMeetings') }}"
    };

    var CSRF_TOKEN = "{{ csrf_token() }}";

    // Dynamic KPI Counter Helpers
    function adjustKpi(elementId, delta) {
        var el = document.getElementById(elementId);
        if (!el) return;
        var current = parseInt(el.textContent.replace(/[^0-9]/g, ''), 10) || 0;
        var updated = Math.max(0, current + delta);
        el.textContent = updated;
    }

    // Modern Notification Bridge (Toastr fallback to Swal)
    function notifySuccess(title, message) {
        if (typeof toastr !== 'undefined' && toastr.success) {
            toastr.success(message, title || 'Success');
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: title || 'Success',
                text: message,
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert(message);
        }
    }

    function notifyError(title, message) {
        if (typeof toastr !== 'undefined' && toastr.error) {
            toastr.error(message, title || 'Error');
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: title || 'Error',
                text: message
            });
        } else {
            alert(message);
        }
    }

    function notifyWarning(title, message) {
        if (typeof toastr !== 'undefined' && toastr.warning) {
            toastr.warning(message, title || 'Notice');
        } else if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: title || 'Notice',
                text: message
            });
        } else {
            alert(message);
        }
    }

    $(document).ready(function() {

        // =========================================================================
        // 1. SEARCH INPUT CLEAR BUTTON & AUTO-SUBMIT
        // =========================================================================
        var $searchLink = $('#inputSearchLink');
        var $btnClearSearch = $('#btnClearSearch');

        function toggleClearBtn() {
            if ($searchLink.val().trim().length > 0) {
                $btnClearSearch.show();
            } else {
                $btnClearSearch.hide();
            }
        }
        toggleClearBtn();

        $searchLink.on('input', function() {
            toggleClearBtn();
        });

        $btnClearSearch.on('click', function() {
            $searchLink.val('');
            toggleClearBtn();
            $('#searchForm').submit();
        });

        // =========================================================================
        // 2. TIER 1 TOOLBAR QUICK NAVIGATION BUTTONS
        // =========================================================================
        $('#btnSwitchAddTab, #btnEmptyStateAdd').on('click', function(e) {
            e.preventDefault();
            var tabTrigger = document.querySelector('a[href="#tab_add_import"]');
            if (tabTrigger) {
                var tab = new bootstrap.Tab(tabTrigger);
                tab.show();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                $('#tableManualRows input.input-holder-email').first().focus();
            }
        });

        $('#btnSwitchImportTab').on('click', function(e) {
            e.preventDefault();
            var tabTrigger = document.querySelector('a[href="#tab_add_import"]');
            if (tabTrigger) {
                var tab = new bootstrap.Tab(tabTrigger);
                tab.show();
                window.scrollTo({ top: 0, behavior: 'smooth' });
                $('#inputImportFile').focus();
            }
        });

        // =========================================================================
        // 3. COPY URL TO CLIPBOARD (CLIENT-FIRST MEMORY API)
        // =========================================================================
        $(document).on('click', '.btn-copy-clipboard', function(e) {
            e.preventDefault();
            var text = $(this).attr('data-clipboard-text') || '';
            if (!text) return;

            var $btn = $(this);
            var originalHtml = $btn.html();

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(function() {
                    $btn.html('<i class="fas fa-check text-success fs-7"></i>');
                    setTimeout(function() { $btn.html(originalHtml); }, 1800);
                    notifySuccess('Copied', 'Teams meeting URL copied to clipboard');
                }).catch(function() {
                    fallbackCopy(text, $btn, originalHtml);
                });
            } else {
                fallbackCopy(text, $btn, originalHtml);
            }
        });

        function fallbackCopy(text, $btn, originalHtml) {
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-9999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                $btn.html('<i class="fas fa-check text-success fs-7"></i>');
                setTimeout(function() { $btn.html(originalHtml); }, 1800);
                notifySuccess('Copied', 'Teams meeting URL copied to clipboard');
            } catch (err) {
                notifyError('Copy Failed', 'Please manually copy the link');
            }
            document.body.removeChild(textArea);
        }

        // =========================================================================
        // 4. SEAMLESS AJAX STATUS TOGGLE (ZERO PAGE RELOAD)
        // =========================================================================
        $(document).on('change', '.status-toggle', function() {
            var $switch = $(this);
            var recordId = $switch.val();
            var isChecked = $switch.is(':checked');
            var newStatus = isChecked ? 1 : 0;
            var $badgeContainer = $switch.closest('td').find('.status-badge-container');

            // Optimistic UI state update
            if (isChecked) {
                $badgeContainer.html('<span class="badge-status-active">Active</span>');
            } else {
                $badgeContainer.html('<span class="badge-status-inactive">Paused</span>');
            }

            $.ajax({
                type: 'POST',
                url: ROUTES.toggleStatus,
                data: {
                    _token: CSRF_TOKEN,
                    id: recordId,
                    chkStatus: newStatus
                },
                dataType: 'json',
                success: function(res) {
                    if (res && (res.status === 'success' || res.status === true)) {
                        notifySuccess('Status Updated', res.message || 'Status updated successfully.');
                        if (isChecked) {
                            adjustKpi('kpiActiveCount', 1);
                            adjustKpi('kpiInactiveCount', -1);
                        } else {
                            adjustKpi('kpiActiveCount', -1);
                            adjustKpi('kpiInactiveCount', 1);
                        }
                    } else {
                        // Rollback state on error
                        $switch.prop('checked', !isChecked);
                        $badgeContainer.html(isChecked ? '<span class="badge-status-inactive">Paused</span>' : '<span class="badge-status-active">Active</span>');
                        notifyError('Error', res.message || 'Failed to update status.');
                    }
                },
                error: function(xhr) {
                    $switch.prop('checked', !isChecked);
                    $badgeContainer.html(isChecked ? '<span class="badge-status-inactive">Paused</span>' : '<span class="badge-status-active">Active</span>');
                    var msg = 'Failed to update status. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    notifyError('Server Error', msg);
                }
            });
        });

        // =========================================================================
        // 5. MASTER CHECKBOX & BULK SELECTION ENGINE
        // =========================================================================
        var $masterCheckbox = $('#masterCheckbox');
        var $btnBulkDelete = $('#btnBulkDelete');
        var $bulkDeleteCount = $('#bulkDeleteCount');

        function updateBulkDeleteState() {
            var checkedCount = $('.row-checkbox:checked').length;
            $bulkDeleteCount.text(checkedCount);
            if (checkedCount > 0) {
                $btnBulkDelete.removeClass('d-none');
            } else {
                $btnBulkDelete.addClass('d-none');
            }

            var totalRows = $('.row-checkbox').length;
            if (totalRows > 0 && checkedCount === totalRows) {
                $masterCheckbox.prop('checked', true);
            } else {
                $masterCheckbox.prop('checked', false);
            }
        }

        $masterCheckbox.on('change', function() {
            var isChecked = $(this).is(':checked');
            $('.row-checkbox').prop('checked', isChecked);
            updateBulkDeleteState();
        });

        $(document).on('change', '.row-checkbox', function() {
            updateBulkDeleteState();
        });

        // =========================================================================
        // 6. SINGLE & BULK DELETE ACTION WITH SWEETALERT2
        // =========================================================================
        // Single row delete
        $(document).on('click', '.btn-delete-single', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            if (!id) return;

            executeDelete([id]);
        });

        // Bulk delete
        $btnBulkDelete.on('click', function(e) {
            e.preventDefault();
            var selectedIds = [];
            $('.row-checkbox:checked').each(function() {
                selectedIds.push($(this).val());
            });

            if (selectedIds.length === 0) {
                notifyWarning('No Selection', 'Please select at least one record to delete.');
                return;
            }

            executeDelete(selectedIds);
        });

        function executeDelete(ids) {
            if (typeof Swal === 'undefined') {
                if (confirm('Are you sure you want to delete the selected record(s)?')) {
                    performDeleteAjax(ids);
                }
                return;
            }

            Swal.fire({
                title: 'Delete ' + ids.length + ' Meeting Link(s)?',
                text: "These links will be permanently removed from the Teams pool and won't be assigned to future candidate interviews.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#f1416c',
                cancelButtonColor: '#7e8299',
                confirmButtonText: 'Yes, Delete',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    performDeleteAjax(ids);
                }
            });
        }

        function performDeleteAjax(ids) {
            $.ajax({
                type: 'POST',
                url: ROUTES.deleteLinks,
                data: {
                    _token: CSRF_TOKEN,
                    table: 'team_meeting_lists',
                    arr: ids
                },
                dataType: 'json',
                success: function(res) {
                    if (res && res.status) {
                        notifySuccess('Deleted', res.message || 'Records deleted successfully.');

                        // Remove rows from DOM with fade animation
                        var activeRemoved = 0;
                        var inactiveRemoved = 0;

                        ids.forEach(function(id) {
                            var $row = $('#row-' + id);
                            if ($row.length) {
                                if ($row.find('.status-toggle').is(':checked')) {
                                    activeRemoved++;
                                } else {
                                    inactiveRemoved++;
                                }
                                $row.fadeOut(300, function() {
                                    $(this).remove();
                                });
                            }
                        });

                        adjustKpi('kpiTotalCount', -ids.length);
                        adjustKpi('kpiActiveCount', -activeRemoved);
                        adjustKpi('kpiInactiveCount', -inactiveRemoved);

                        updateBulkDeleteState();
                    } else {
                        notifyError('Delete Failed', res.message || 'Unable to delete records.');
                    }
                },
                error: function(xhr) {
                    var msg = 'Failed to delete records. Server error.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    notifyError('Error', msg);
                }
            });
        }

        // =========================================================================
        // 7. INLINE EDIT MODAL ENGINE
        // =========================================================================
        var $editModal = $('#modalEditMeetingLink');
        var $formEdit = $('#formEditMeetingLink');

        $(document).on('click', '.btn-edit-link', function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var account = $(this).attr('data-account') || '';
            var link = $(this).attr('data-link') || '';

            $('#editLinkId').val(id);
            $('#editAccountName').val(account);
            $('#editMeetingLink').val(link);

            $editModal.modal('show');
        });

        $formEdit.on('submit', function(e) {
            e.preventDefault();
            var id = $('#editLinkId').val();
            var accountName = $('#editAccountName').val().trim();
            var meetingLink = $('#editMeetingLink').val().trim();

            if (!id || !accountName || !meetingLink) {
                notifyWarning('Validation', 'Please fill in both Account Name and Teams URL.');
                return;
            }

            var $submitBtn = $('#btnSubmitEditLink');
            $submitBtn.attr('data-kt-indicator', 'on').prop('disabled', true);

            var postUrl = ROUTES.updateLinkBase + '/' + id;

            $.ajax({
                type: 'POST',
                url: postUrl,
                data: {
                    _token: CSRF_TOKEN,
                    account_name: accountName,
                    meeting_link: meetingLink
                },
                dataType: 'json',
                success: function(res) {
                    $submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);
                    if (res && res.status) {
                        notifySuccess('Updated', res.message || 'Meeting link updated successfully.');
                        $editModal.modal('hide');

                        // In-place DOM update without full page reload
                        var $targetRow = $('#row-' + id);
                        if ($targetRow.length) {
                            $targetRow.find('.item-account-name').text(accountName);
                            $targetRow.find('.host-avatar').text(accountName.charAt(0).toUpperCase());
                            $targetRow.find('.item-meeting-link').text(meetingLink);
                            $targetRow.find('.meet-link-pill').attr('title', meetingLink);
                            $targetRow.find('.btn-copy-clipboard').attr('data-clipboard-text', meetingLink);
                            $targetRow.find('.btn-edit-link').attr('data-account', accountName).attr('data-link', meetingLink);
                        }
                    } else {
                        notifyError('Update Failed', res.message || 'Could not update record.');
                    }
                },
                error: function(xhr) {
                    $submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);
                    var msg = 'Failed to update meeting link.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    notifyError('Error', msg);
                }
            });
        });

        // =========================================================================
        // 8. DYNAMIC MULTI-ROW MANUAL LINK ENTRY (TAB 2)
        // =========================================================================
        $('#btnAddRow').on('click', function() {
            var newRowHtml = `
                <tr class="manual-input-row">
                    <td class="p-2">
                        <input type="text"
                               name="holder_email[]"
                               class="form-control form-control-sm input-holder-email"
                               placeholder="e.g. interviews@company.com"
                               required>
                    </td>
                    <td class="p-2">
                        <input type="text"
                               name="meeting_link[]"
                               class="form-control form-control-sm input-meeting-link"
                               placeholder="e.g. https://teams.microsoft.com/l/meetup-join/..."
                               required>
                    </td>
                    <td class="text-center p-2">
                        <button type="button" class="btn btn-icon btn-light-danger btn-sm btn-remove-row" title="Remove row">
                            <i class="fas fa-times fs-7"></i>
                        </button>
                    </td>
                </tr>
            `;
            $('#manualRowsTbody').append(newRowHtml);
            $('#manualRowsTbody tr:last-child input.input-holder-email').focus();
        });

        $(document).on('click', '.btn-remove-row', function() {
            if ($('#manualRowsTbody tr').length > 1) {
                $(this).closest('tr').remove();
            } else {
                // Clear inputs if it's the only row
                $(this).closest('tr').find('input').val('');
            }
        });

        $('#formManualAddLinks').on('submit', function(e) {
            e.preventDefault();
            var $submitBtn = $('#btnSubmitManualLinks');
            $submitBtn.attr('data-kt-indicator', 'on').prop('disabled', true);

            var formData = $(this).serialize();

            $.ajax({
                type: 'POST',
                url: ROUTES.storeLinks,
                data: formData,
                dataType: 'json',
                success: function(res) {
                    $submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);
                    if (res && res.status) {
                        notifySuccess('Links Added', res.message || 'Links added successfully to the pool.');
                        setTimeout(function() {
                            window.location.href = res.url || window.location.href;
                        }, 1000);
                    } else {
                        notifyWarning('Notice', res.message || 'No records were added.');
                    }
                },
                error: function(xhr) {
                    $submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);
                    var msg = 'Failed to save meeting links.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    notifyError('Error', msg);
                }
            });
        });

        // =========================================================================
        // 9. BATCH IMPORT (.XLSX / .CSV) ENGINE WITH LIVE PROGRESS BAR
        // =========================================================================
        $('#btnDownloadSampleCsv').on('click', function() {
            var csvContent = "data:text/csv;charset=utf-8,";
            csvContent += "Host Account / Email,Microsoft Teams Meeting Link URL\n";
            csvContent += "interviews1@company.com,https://teams.microsoft.com/l/meetup-join/19%3ameeting_example1%40thread.v2/0\n";
            csvContent += "interviews2@company.com,https://teams.microsoft.com/l/meetup-join/19%3ameeting_example2%40thread.v2/0\n";
            csvContent += "recruiting.panel@company.com,https://teams.microsoft.com/l/meetup-join/19%3ameeting_example3%40thread.v2/0\n";

            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "sample_teams_meeting_pool.csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        $('#formBatchImport').on('submit', function(e) {
            e.preventDefault();
            var fileInput = document.getElementById('inputImportFile');
            if (!fileInput || !fileInput.files.length) {
                notifyWarning('Missing File', 'Please select a .xlsx or .csv file to import.');
                return;
            }

            var formData = new FormData(this);
            var $submitBtn = $('#btnSubmitImport');
            var $progressBar = $('#importProgressBar');
            var $progressContainer = $('#importProgressBarContainer');
            var $alertBox = $('#importResponseAlert');

            $submitBtn.attr('data-kt-indicator', 'on').prop('disabled', true);
            $progressContainer.removeClass('d-none');
            $alertBox.addClass('d-none').empty();

            $.ajax({
                type: 'POST',
                url: ROUTES.batchImport,
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            $progressBar.css('width', percentComplete + '%').text(percentComplete + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(res) {
                    $submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);
                    $progressBar.css('width', '100%').text('100%');

                    if (res && res.status) {
                        notifySuccess('Import Started', res.message || 'File uploaded successfully and queued for import.');
                        $alertBox.removeClass('d-none alert-danger').addClass('alert alert-success d-flex align-items-center')
                                 .html('<i class="fas fa-check-circle fs-4 text-success me-2"></i> <span>' + (res.message || 'Import processing in background.') + '</span>');
                        setTimeout(function() {
                            window.location.href = res.url || window.location.href;
                        }, 1200);
                    } else {
                        notifyError('Import Failed', res.message || 'Unable to process import.');
                        $alertBox.removeClass('d-none alert-success').addClass('alert alert-danger d-flex align-items-center')
                                 .html('<i class="fas fa-exclamation-triangle fs-4 text-danger me-2"></i> <span>' + (res.message || 'Import failed.') + '</span>');
                    }
                },
                error: function(xhr) {
                    $submitBtn.removeAttr('data-kt-indicator').prop('disabled', false);
                    $progressContainer.addClass('d-none');
                    var msg = 'Failed to upload spreadsheet.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    notifyError('Error', msg);
                    $alertBox.removeClass('d-none alert-success').addClass('alert alert-danger d-flex align-items-center')
                             .html('<i class="fas fa-exclamation-triangle fs-4 text-danger me-2"></i> <span>' + msg + '</span>');
                }
            });
        });

        // =========================================================================
        // 10. ACTIVE ALLOCATION & SCHEDULE MONITOR (TAB 3) AJAX FETCHER
        // =========================================================================
        var schedulesLoaded = false;

        function loadScheduleHistory() {
            var $tbody = $('#scheduleHistoryTbody');
            $tbody.html(`
                <tr>
                    <td colspan="6" class="text-center py-8 text-muted">
                        <span class="spinner-border spinner-border-sm text-primary me-2"></span>
                        Loading schedule allocations...
                    </td>
                </tr>
            `);

            $.ajax({
                type: 'GET',
                url: ROUTES.scheduleHistory,
                dataType: 'json',
                success: function(res) {
                    schedulesLoaded = true;
                    if (res && res.status && res.data && res.data.length > 0) {
                        var html = '';
                        res.data.forEach(function(item, idx) {
                            var dateFormatted = item.interview_date ? item.interview_date : 'N/A';
                            var companyName = item.company_name ? item.company_name : 'General Tenant';
                            var recruiterName = item.recruiter_name ? item.recruiter_name : 'System User';
                            var recruiterEmail = item.recruiter_email ? item.recruiter_email : '';

                            html += `
                                <tr>
                                    <td class="text-center text-muted fw-bold fs-7">${idx + 1}</td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="meet-link-pill" title="${item.meeting_link}">
                                                <i class="fab fa-microsoft text-primary fs-7"></i>
                                                <span class="meet-link-text">${item.meeting_link}</span>
                                            </div>
                                            <button type="button" class="btn btn-icon btn-light btn-sm btn-copy-clipboard" data-clipboard-text="${item.meeting_link}" title="Copy URL">
                                                <i class="far fa-copy text-muted fs-7"></i>
                                            </button>
                                            <a href="${item.meeting_link}" target="_blank" rel="noopener noreferrer" class="btn btn-icon btn-light-primary btn-sm" title="Join Teams Meeting">
                                                <i class="fas fa-external-link-alt fs-7"></i>
                                            </a>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center text-dark fw-bold fs-7">
                                            <i class="far fa-clock text-info me-2"></i> ${dateFormatted}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-dark fw-bolder fs-7">${companyName}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-bold fs-7">${recruiterName}</span>
                                            <span class="text-muted fs-8">${recruiterEmail}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-light-primary fw-bold fs-8 px-3 py-1">
                                            <i class="fas fa-check-circle text-primary me-1 fs-8"></i> Booked
                                        </span>
                                    </td>
                                </tr>
                            `;
                        });
                        $tbody.html(html);
                    } else {
                        $tbody.html(`
                            <tr>
                                <td colspan="6" class="text-center py-10 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="far fa-calendar-times fs-2x text-muted mb-2"></i>
                                        <span class="fw-bold fs-6">No Candidate Interview Schedules Recorded</span>
                                        <span class="fs-8 mt-1">Interviews scheduled using Microsoft Teams will appear here in real-time.</span>
                                    </div>
                                </td>
                            </tr>
                        `);
                    }
                },
                error: function() {
                    $tbody.html(`
                        <tr>
                            <td colspan="6" class="text-center py-8 text-danger">
                                <i class="fas fa-exclamation-circle me-1"></i> Failed to load schedule history.
                            </td>
                        </tr>
                    `);
                }
            });
        }

        // Fetch allocations when Tab 3 is clicked or refreshed
        $('#tabScheduleNav').on('shown.bs.tab', function() {
            if (!schedulesLoaded) {
                loadScheduleHistory();
            }
        });

        $('#btnRefreshSchedules').on('click', function() {
            loadScheduleHistory();
        });

    });

})(window, document, jQuery);
</script>
@endpush