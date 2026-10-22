@extends('layouts.superadmin_layout')

@section('header_title')
<div class="page-title sa-page-title-stack my-0">
    <h1 class="d-flex align-items-center text-gray-900 fw-bold fs-3 my-0">Companies</h1>
    <span class="sa-header-sub">Manage and monitor all registered companies in the system</span>
</div>
@endsection

@push('head-script')

<link rel="stylesheet" href="//cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
/* =========================================================================
   SUPER ADMIN COMPANIES PAGE — SCOPED ENTERPRISE STYLES
   Isolated strictly to .sa-companies-page and .sa-page-title-stack
   ========================================================================= */

/* Top Navigation Header Title Stack & Subtitle */
#kt_header_nav .page-title.sa-page-title-stack {
    display: flex !important;
    flex-direction: column !important;
    align-items: flex-start !important;
    justify-content: center !important;
}

#kt_header_nav .page-title.sa-page-title-stack .sa-header-sub {
    font-size: 0.785rem !important;
    color: #7E8299 !important;
    font-weight: 500 !important;
    line-height: 1.2 !important;
    margin-top: 2px !important;
}

@media (max-width: 991.98px) {
    #kt_header_nav .page-title.sa-page-title-stack .sa-header-sub {
        font-size: 0.7rem !important;
    }
}

/* Top Space & Header Gap Management (Collapse redundant toolbar whitespace) */
#kt_header_search {
    display: none !important;
}

#kt_header {
    border-bottom: 1px solid #eff2f5 !important;
}

@media (min-width: 992px) {
    .sa-companies-page {
        margin-top: -48px;
    }
}

@media (max-width: 991.98px) {
    .sa-companies-page {
        margin-top: -38px;
    }
}

.sa-companies-page #kt_content_container {
    padding-top: 0.5rem !important;
}

.sa-companies-page {
    --sa-primary: #0066FF;
    --sa-primary-hover: #0052cc;
    --sa-border: #EFF2F5;
    --sa-card-bg: #FFFFFF;
    --sa-text-dark: #181C32;
    --sa-text-muted: #7E8299;
}

/* Page Header & Spacing */
.sa-companies-page .sa-header-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--sa-text-dark);
    margin-bottom: 0.25rem;
    letter-spacing: -0.02em;
}

.sa-companies-page .sa-header-subtitle {
    font-size: 0.825rem;
    color: var(--sa-text-muted);
}

.sa-companies-page .btn-add-company {
    background-color: var(--sa-primary);
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.825rem;
    height: 38px;
    border-radius: 8px;
    padding: 0 16px;
    transition: all 0.2s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none !important;
}

.sa-companies-page .btn-add-company:hover {
    background-color: var(--sa-primary-hover);
    box-shadow: 0 4px 12px rgba(0, 102, 255, 0.25);
    color: #ffffff !important;
}

/* KPI Cards Section */
.sa-companies-page .kpi-card {
    border-radius: 12px;
    padding: 20px 24px;
    border: 1px solid transparent;
    position: relative;
    overflow: hidden;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.sa-companies-page .kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
}

.sa-companies-page .kpi-card-total {
    background-color: #FEF7EC;
    border-color: #FDE8C7;
}

.sa-companies-page .kpi-card-active {
    background-color: #EDFAF3;
    border-color: #D1F4E0;
}

.sa-companies-page .kpi-card-inactive {
    background-color: #FDF2F4;
    border-color: #FCE0E5;
}

.sa-companies-page .kpi-icon-wrapper {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.sa-companies-page .kpi-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--sa-text-dark);
    margin-bottom: 2px;
}

.sa-companies-page .kpi-subtitle {
    font-size: 0.775rem;
    color: var(--sa-text-muted);
}

.sa-companies-page .kpi-count {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1;
}

.sa-companies-page .kpi-bar-indicator {
    height: 4px;
    border-radius: 2px;
    width: 65px;
    margin-top: 14px;
}

/* Filter Toolbar */
.sa-companies-page .filter-toolbar-card {
    background: #ffffff;
    border: 1px solid var(--sa-border);
    border-radius: 12px;
    padding: 14px 18px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.sa-companies-page .search-input-box {
    min-width: 280px;
}

.sa-companies-page .search-input-box input {
    height: 44px;
    border-radius: 8px;
    font-size: 0.85rem;
    border: 1px solid #E4E6EF;
    background-color: #F9F9FB;
}

.sa-companies-page .search-input-box input:focus {
    background-color: #ffffff;
    border-color: var(--sa-primary);
    box-shadow: 0 0 0 3px rgba(0, 102, 255, 0.1);
}

.sa-companies-page .filter-select-wrapper {
    min-width: 165px;
}

.sa-companies-page .filter-select-wrapper select {
    height: 44px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 500;
    border: 1px solid #E4E6EF;
    background-color: #F9F9FB;
    cursor: pointer;
}

.sa-companies-page .btn-clear-filter {
    height: 44px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid #E4E6EF;
    background-color: #F9F9FB;
    color: #5E6278;
    padding: 0 16px;
    transition: all 0.2s ease;
}

.sa-companies-page .btn-clear-filter:hover {
    background-color: #F1F3F5;
    color: var(--sa-text-dark);
}

/* Sub-Bar (Export & Columns) */
.sa-companies-page .sub-bar-btn {
    height: 38px;
    border-radius: 8px;
    font-size: 0.825rem;
    font-weight: 600;
    border: 1px solid #E4E6EF;
    background: #ffffff;
    color: #4B5675;
    padding: 0 14px;
    transition: all 0.2s ease;
}

.sa-companies-page .sub-bar-btn:hover {
    background: #F9F9FB;
    border-color: #D8DADD;
    color: var(--sa-text-dark);
}

/* Table Container & Rows */
.sa-companies-page .table-wrapper-card {
    background: #ffffff;
    border: 1px solid var(--sa-border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}

.sa-companies-page table.sa-companies-table {
    margin-bottom: 0;
}

.sa-companies-page table.sa-companies-table th {
    background: #FAFAFC;
    color: #7E8299;
    font-weight: 600;
    font-size: 0.775rem;
    text-transform: capitalize;
    letter-spacing: 0.2px;
    padding: 14px 14px;
    border-bottom: 1px solid #EFF2F5;
    white-space: nowrap;
}

.sa-companies-page table.sa-companies-table td {
    padding: 12px 14px;
    vertical-align: middle;
    font-size: 0.825rem;
    border-bottom: 1px solid #F4F6F8;
}

.sa-companies-page table.sa-companies-table tr.sa-company-row:hover td {
    background-color: #F9FBFE;
}

.sa-companies-page table.sa-companies-table tr.sa-row-live-updated td {
    background-color: rgba(0, 102, 255, 0.08) !important;
    transition: background-color 1.5s ease;
}

.sa-companies-page table.sa-companies-table tr:last-child td {
    border-bottom: none;
}

.sa-companies-page .company-avatar {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    object-fit: cover;
    flex-shrink: 0;
}

.sa-companies-page .company-initial-badge {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    font-weight: 700;
    flex-shrink: 0;
}

/* Loading Overlay */
.sa-companies-page .table-loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(2px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    border-radius: 12px;
}

/* Drawer width overrides — Company Drawer 80% Width & 15.6-inch Screen Optimization */
#kt_activitiesClient, #kt_activitiesWallet, #kt_activitiesApplication {
    box-shadow: -8px 0 28px rgba(0, 0, 0, 0.12), -2px 0 8px rgba(0, 0, 0, 0.04) !important;
    border-left: 1px solid #e2e8f0 !important;
}

/* Drawer scrollbars — Single smooth scroll container, remove extra/nested scrollers */
#kt_activitiesClient_body,
#kt_activitiesWallet_body,
#kt_billing_address_body {
    overflow-y: auto !important;
    overflow-x: hidden !important;
    max-height: calc(100vh - 60px) !important;
    -webkit-overflow-scrolling: touch;
}

#kt_activitiesClient_body > .card-body,
#kt_activitiesWallet_body > .card-body,
#kt_activitiesClient_body #kt_activities_scroll,
#kt_activitiesWallet_body #kt_activities_scroll {
    overflow: visible !important;
    height: auto !important;
    max-height: none !important;
}

/* Slim scrollbar matching Consultancy reference */
#kt_activitiesClient_body::-webkit-scrollbar,
#kt_activitiesWallet_body::-webkit-scrollbar,
#kt_billing_address_body::-webkit-scrollbar {
    width: 6px;
}

#kt_activitiesClient_body::-webkit-scrollbar-track,
#kt_activitiesWallet_body::-webkit-scrollbar-track,
#kt_billing_address_body::-webkit-scrollbar-track {
    background: transparent;
}

#kt_activitiesClient_body::-webkit-scrollbar-thumb,
#kt_activitiesWallet_body::-webkit-scrollbar-thumb,
#kt_billing_address_body::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

#kt_activitiesClient_body::-webkit-scrollbar-thumb:hover,
#kt_activitiesWallet_body::-webkit-scrollbar-thumb:hover,
#kt_billing_address_body::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Prevent background page scrolling when drawer is open */
body.drawer-open,
body:has(.drawer.drawer-on),
body:has(#kt_activitiesClient.drawer-on),
body:has(#kt_activitiesWallet.drawer-on),
body:has(#kt_activitiesApplication.drawer-on) {
    overflow: hidden !important;
}

#kt_activitiesClient, #kt_activitiesWallet, #kt_activitiesApplication, #kt_activitiesDirectClient {
    width: 80% !important;
    max-width: 80vw !important;
}

@media only screen and (max-width: 767.98px) {
    #kt_activitiesClient, #kt_activitiesWallet, #kt_activitiesApplication, #kt_activitiesDirectClient {
        width: 100% !important;
        max-width: 100vw !important;
    }
}

/* Scoped Drawer Header & Action Standards (Matching Consultancy) */
.sa-drawer-header {
    height: 60px;
    padding: 14px 24px;
    background: #ffffff;
    border-bottom: 1px solid #edf2f7;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.sa-drawer-title {
    font-size: 17px;
    font-weight: 600;
    color: #111827;
    line-height: 1.4;
    font-family: 'Poppins', sans-serif;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}
.sa-drawer-close {
    width: 32px;
    height: 32px;
    padding: 0;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #ffffff;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.15s ease;
}
.sa-drawer-close:hover,
.sa-drawer-close:focus {
    background: #f8fafc;
    color: #0f172a;
    border-color: #cbd5e1;
    outline: none;
}

/* Clean Pagination Styling & Isolation for Super Admin Companies Page */
.sa-companies-page #paginationcopanylist .pagination-info {
    display: none !important;
}

.sa-companies-page #paginationcopanylist .pagination-wrapper {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: flex-end !important;
    width: auto !important;
    margin: 0 !important;
}

.sa-companies-page #paginationcopanylist .custom-pagination {
    display: flex !important;
    align-items: center !important;
    gap: 5px !important;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
    padding-left: 0 !important;
    list-style: none !important;
}

.sa-companies-page #paginationcopanylist .custom-pagination .page-link {
    height: 32px !important;
    min-width: 32px !important;
    padding: 0 10px !important;
    font-size: 0.8125rem !important;
    font-weight: 600 !important;
    border-radius: 6px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    border: 1px solid #eff2f5 !important;
    background-color: #ffffff !important;
    color: #5e6278 !important;
    transition: all 0.2s ease !important;
    line-height: 1 !important;
    cursor: pointer !important;
}

.sa-companies-page #paginationcopanylist .custom-pagination .page-link:hover:not(.previous_disabled):not(.next_disabled):not(.active_disabled) {
    background-color: #f1f1f4 !important;
    color: #0095e8 !important;
    border-color: #d8d8e0 !important;
}

.sa-companies-page #paginationcopanylist .custom-pagination .page-link.active {
    background-color: #0095e8 !important;
    color: #ffffff !important;
    border-color: #0095e8 !important;
    cursor: default !important;
}

.sa-companies-page #paginationcopanylist .custom-pagination .page-link.previous_disabled,
.sa-companies-page #paginationcopanylist .custom-pagination .page-link.next_disabled {
    opacity: 0.5 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
    background-color: #f9f9f9 !important;
    border-color: #eff2f5 !important;
    color: #a1a5b7 !important;
}

.sa-companies-page #paginationcopanylist .custom-pagination .dots {
    background: transparent !important;
    border: none !important;
    cursor: default !important;
    color: #a1a5b7 !important;
}
</style>
@endpush

@php
    use App\Helper\GeneralMethod;
    use Illuminate\Support\Facades\DB;
    $currency = GeneralMethod::convertINRToUSD();
    $payment_settings = DB::table('payment_settings')->select('payment_settings.*')->first();
@endphp

@section('content')

{{-- Super Admin Companies Page Shell (Exact Match to Subscription Page Reference) --}}
<div class="sa-companies-page">
<div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-4 pt-2 pb-4">

    {{-- KPI Cards Row (Total, Active, Inactive) --}}
    <div class="row g-4 mb-4">
        {{-- Card 1: Total Companies --}}
        <div class="col-12 col-md-4">
            <div class="kpi-card kpi-card-total d-flex flex-column justify-content-between h-100">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon-wrapper" style="background-color: rgba(217, 119, 6, 0.12);">
                            <i class="bi bi-stack fs-3" style="color: #D97706;"></i>
                        </div>
                        <div>
                            <div class="kpi-title">@lang('modules.dashboard.totalCompanies')</div>
                            <div class="kpi-subtitle">Due in 2 Days</div>
                        </div>
                    </div>
                    <div class="kpi-count" id="kpiTotalCompaniesCount" style="color: #D97706;">
                        {{ number_format($totalCompanies) }}
                    </div>
                </div>
                <div class="kpi-bar-indicator" style="background-color: #D97706;"></div>
            </div>
        </div>

        {{-- Card 2: Active Companies --}}
        <div class="col-12 col-md-4">
            <div class="kpi-card kpi-card-active d-flex flex-column justify-content-between h-100">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon-wrapper" style="background-color: rgba(5, 150, 105, 0.12);">
                            <i class="bi bi-stack fs-3" style="color: #059669;"></i>
                        </div>
                        <div>
                            <div class="kpi-title">@lang('modules.dashboard.activeCompanies')</div>
                            <div class="kpi-subtitle">Due in 2 Days</div>
                        </div>
                    </div>
                    <div class="kpi-count" id="kpiActiveCompaniesCount" style="color: #059669;">
                        {{ number_format($activeCompanies) }}
                    </div>
                </div>
                <div class="kpi-bar-indicator" style="background-color: #059669;"></div>
            </div>
        </div>

        {{-- Card 3: Inactive Companies --}}
        <div class="col-12 col-md-4">
            <div class="kpi-card kpi-card-inactive d-flex flex-column justify-content-between h-100">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="kpi-icon-wrapper" style="background-color: rgba(225, 29, 72, 0.12);">
                            <i class="bi bi-stack fs-3" style="color: #E11D48;"></i>
                        </div>
                        <div>
                            <div class="kpi-title">@lang('modules.dashboard.inactiveCompanies')</div>
                            <div class="kpi-subtitle">Due in 5 Days</div>
                        </div>
                    </div>
                    <div class="kpi-count" id="kpiInactiveCompaniesCount" style="color: #E11D48;">
                        {{ number_format($inactiveCompanies) }}
                    </div>
                </div>
                <div class="kpi-bar-indicator" style="background-color: #E11D48;"></div>
            </div>
        </div>
    </div>

    {{-- Unified Search & Filter Toolbar --}}
    <div class="filter-toolbar-card mb-4">
        <div class="d-flex align-items-center flex-wrap gap-3">
            {{-- Search Box --}}
            <div class="search-input-box position-relative flex-grow-1">
                <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted fs-6"></i>
                <input type="text" id="searchInput" class="form-control ps-10" placeholder="Search for company name, location...">
            </div>

            {{-- Location Filter --}}
            <div class="filter-select-wrapper position-relative">
                <i class="bi bi-geo-alt position-absolute top-50 start-0 translate-middle-y ms-3 text-muted fs-6" style="z-index: 2;"></i>
                <select id="locationFilter" class="form-select ps-9 pe-8">
                    <option value="">All Locations</option>
                    @if (!empty($locations) && is_array($locations))
                        @foreach ($locations as $loc)
                            <option value="{{ $loc }}">{{ $loc }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- Status Filter --}}
            <div class="filter-select-wrapper position-relative">
                <i class="bi bi-stack position-absolute top-50 start-0 translate-middle-y ms-3 text-muted fs-6" style="z-index: 2;"></i>
                <select id="statusFilter" class="form-select ps-9 pe-8">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Suspended</option>
                </select>
            </div>

            {{-- All Companies Filter --}}
            <div class="filter-select-wrapper position-relative">
                <i class="bi bi-building position-absolute top-50 start-0 translate-middle-y ms-3 text-muted fs-6" style="z-index: 2;"></i>
                <select id="sortingfilter" class="form-select ps-9 pe-8">
                    <option value="" selected>All Companies</option>
                    <option value="active">Active</option>
                    <option value="inactive">Suspended</option>
                </select>
            </div>

            {{-- Clear Filters Button --}}
            <button type="button" id="btnClearFilters" class="btn-clear-filter d-flex align-items-center gap-2">
                <i class="bi bi-arrow-counterclockwise fs-6"></i> Clear Filters
            </button>
        </div>
    </div>

    {{-- Sub-Bar: Export & Columns Control --}}
    <div class="d-flex justify-content-end align-items-center gap-3 mb-3">
        {{-- Export Dropdown --}}
        <div class="dropdown">
            <button class="sub-bar-btn dropdown-toggle d-flex align-items-center gap-2" type="button" id="exportDropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download fs-6 text-muted"></i> Export
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" aria-labelledby="exportDropdownMenu">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 export-trigger" href="javascript:void(0);" data-format="csv">
                        <i class="bi bi-file-earmark-spreadsheet text-success fs-6"></i> Export CSV
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 export-trigger" href="javascript:void(0);" data-format="excel">
                        <i class="bi bi-file-earmark-excel text-primary fs-6"></i> Export Excel
                    </a>
                </li>
            </ul>
        </div>

        {{-- Columns Visibility Dropdown --}}
        <div class="dropdown">
            <button class="sub-bar-btn dropdown-toggle d-flex align-items-center gap-2" type="button" id="columnsDropdownMenu" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                <i class="bi bi-layout-three-columns fs-6 text-muted"></i> Columns
            </button>
            <div class="dropdown-menu dropdown-menu-end p-3 shadow-sm border-0" style="min-width: 210px;" aria-labelledby="columnsDropdownMenu">
                <span class="fw-bold fs-7 text-gray-800 d-block mb-2">Display Columns</span>
                <div class="d-flex flex-column gap-2 fs-7">
                    <label class="form-check form-check-custom form-check-sm">
                        <input class="form-check-input column-toggle-check" type="checkbox" checked data-col="col-location">
                        <span class="form-check-label text-gray-700">Location</span>
                    </label>
                    <label class="form-check form-check-custom form-check-sm">
                        <input class="form-check-input column-toggle-check" type="checkbox" checked data-col="col-date">
                        <span class="form-check-label text-gray-700">Date</span>
                    </label>
                    <label class="form-check form-check-custom form-check-sm">
                        <input class="form-check-input column-toggle-check" type="checkbox" checked data-col="col-jobs">
                        <span class="form-check-label text-gray-700">Jobs</span>
                    </label>
                    <label class="form-check form-check-custom form-check-sm">
                        <input class="form-check-input column-toggle-check" type="checkbox" checked data-col="col-join">
                        <span class="form-check-label text-gray-700">Join</span>
                    </label>
                    <label class="form-check form-check-custom form-check-sm">
                        <input class="form-check-input column-toggle-check" type="checkbox" checked data-col="col-bench">
                        <span class="form-check-label text-gray-700">Bench</span>
                    </label>
                    <label class="form-check form-check-custom form-check-sm">
                        <input class="form-check-input column-toggle-check" type="checkbox" checked data-col="col-oppack">
                        <span class="form-check-label text-gray-700">Operational Package</span>
                    </label>
                    <label class="form-check form-check-custom form-check-sm">
                        <input class="form-check-input column-toggle-check" type="checkbox" checked data-col="col-salespack">
                        <span class="form-check-label text-gray-700">Sales Package</span>
                    </label>
                    <label class="form-check form-check-custom form-check-sm">
                        <input class="form-check-input column-toggle-check" type="checkbox" checked data-col="col-sp">
                        <span class="form-check-label text-gray-700">SP.</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Add Company Action --}}
        <a href="{{ route('superadmin.company.create') }}" class="btn-add-company d-flex align-items-center gap-2">
            <i class="bi bi-plus-lg fs-6"></i> Add Company
        </a>
    </div>

    {{-- Persistent Multi-Page Selection Bar --}}
    <div id="bulkActionBar" class="bulk-action-bar d-flex align-items-center justify-content-between p-2 px-4 mb-3 rounded-2" style="display: none; background: #EEF6FF; border: 1px solid #C9E2FF;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check2-circle text-primary fs-5"></i>
            <span class="fw-bold fs-7 text-gray-800"><span id="selectedCountNumber" class="text-primary fw-bolder">0</span> companies selected across pages</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" id="btnClearSelection" class="btn btn-sm btn-light-primary py-1 px-3 fs-8 fw-semibold">
                <i class="bi bi-x-circle me-1"></i> Clear Selection
            </button>
        </div>
    </div>

    {{-- Main Companies Data Table Card --}}
    <div class="table-wrapper-card position-relative mb-4">
        {{-- Loading Spinner Overlay --}}
        <div id="tableLoadingOverlay" class="table-loading-overlay" style="display: none;">
            <div class="d-flex flex-column align-items-center">
                <div class="spinner-border text-primary" role="status" style="width: 2.2rem; height: 2.2rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <span class="text-muted fs-7 fw-semibold mt-2">Loading companies...</span>
            </div>
        </div>

        <div class="table-responsive">
            <table id="myTable2" class="table sa-companies-table align-middle">
                <thead>
                    <tr>
                        <th class="col-chkbox ps-4" style="width: 40px;">
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" id="checkAllRows" type="checkbox" value="1">
                            </div>
                        </th>
                        <th class="col-company">Company Name</th>
                        <th class="col-location">Location</th>
                        <th class="col-date">Date</th>
                        <th class="col-jobs">Jobs</th>
                        <th class="col-join">Join</th>
                        <th class="col-bench">Bench</th>
                        <th class="col-oppack">Operational Package</th>
                        <th class="col-salespack">Sales Package</th>
                        <th class="col-sp">SP.</th>
                        <th class="col-actions text-end pe-4" style="width: 70px;">Actions</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 fw-bold" id="companyListajax">
                    {{-- Dynamically Injected via AJAX --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- Footer Pagination Bar --}}
    <div class="d-flex flex-stack flex-wrap pt-4 pb-4 align-items-center justify-content-between border-top border-gray-200">
        <div class="fs-7 fw-semibold text-gray-700" id="showingCount">
            Showing 1 to 4 of 4 records.
        </div>
        <div id="paginationcopanylist" class="d-flex align-items-center"></div>
    </div>
</div>
</div>
{{-- End .sa-companies-page --}}



<div id="kt_activitiesClient_toggle"></div>
<div id="kt_activitiesClient" class="bg-body sa-company-drawer-shell" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'100%', 'md': '80%'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
  <div class="card shadow-none rounded-0 w-100 h-100 d-flex flex-column">
    <div class="sa-drawer-header" id="kt_activities_header">
      <h3 class="sa-drawer-title">
        <i class="fa-solid fa-building text-primary fs-5"></i>
        <span>Company Details</span>
      </h3>
      <button type="button" class="sa-drawer-close" id="kt_activitiesClient_close" aria-label="Close">
        <i class="fa-solid fa-xmark fs-5"></i>
      </button>
    </div>
    <div class="card-body position-relative px-0 py-0 flex-grow-1 overflow-auto" id="kt_activitiesClient_body"> &nbsp; </div>
  </div>
</div>

<div id="kt_activitiesWallet_toggle"></div>
<div id="kt_activitiesWallet" class="bg-body sa-wallet-drawer-shell" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'100%', 'md': '80%'}" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesWallet_toggle" data-kt-drawer-close="#kt_activitiesWallet_close">
  <div class="card shadow-none rounded-0 w-100 h-100 d-flex flex-column">
    <div class="sa-drawer-header" id="kt_activitiesWallet_header">
      <h3 class="sa-drawer-title">
        <i class="fa-solid fa-wallet text-warning fs-5"></i>
        <span>Edit Company Wallet</span>
      </h3>
      <button type="button" class="sa-drawer-close" id="kt_activitiesWallet_close" aria-label="Close">
        <i class="fa-solid fa-xmark fs-5"></i>
      </button>
    </div>
    <div class="card-body position-relative px-0 py-0 flex-grow-1 overflow-auto" id="kt_activitiesWallet_body"></div>
  </div>
</div>

<div id="kt_billing_address_toggle"></div>

<div id="kt_activitiesApplication" class="bg-body sa-billing-drawer-shell" data-kt-drawer="true" data-kt-drawer-name="activitiesApplication"
    data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-width="{default:'100%', 'md': '80%'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_billing_address_toggle" data-kt-drawer-close="#kt_activitiesApplication_close">
    <div class="card shadow-none rounded-0 w-100 h-100 d-flex flex-column" id="application_show_right_bar">
        <div class="sa-drawer-header" id="kt_activitiesApplication_header">
            <h3 class="sa-drawer-title">
                <i class="fa-solid fa-map-location-dot text-info fs-5"></i>
                <span>Billing Address</span>
            </h3>
            <button type="button" class="sa-drawer-close" id="kt_activitiesApplication_close" aria-label="Close">
                <i class="fa-solid fa-xmark fs-5"></i>
            </button>
        </div>
        <div class="card-body position-relative px-4 py-4 flex-grow-1 overflow-auto" id="kt_billing_address_body">
        </div>
    </div>
</div>







<!--end::content-->


<!--<div class="row">
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-primary"><i class="icon-badge"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">@lang('modules.dashboard.totalCompanies')</span>
                <span class="info-box-number">{{ number_format($totalCompanies) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-success"><i class="icon-badge"></i></span>

            <div class="info-box-content">
                <span class="info-box-text">@lang('modules.dashboard.activeCompanies')</span>
                <span class="info-box-number">{{ number_format($activeCompanies) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 col-12">
        <div class="info-box">
            <span class="info-box-icon bg-danger"><i class="icon-badge"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">@lang('modules.dashboard.inactiveCompanies')</span>
                <span class="info-box-number">{{ number_format($inactiveCompanies) }}</span>
            </div>
        </div>
    </div>
</div>-->



@endsection
@push('footer-script')

<!--<script src="//cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="//cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>-->
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('js/responsive.bootstrap.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('assets/node_modules/bootstrap-datepicker/bootstrap-datepicker.min.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/node_modules/moment/moment.js') }}<?php echo TM_VERSION; ?>" type="text/javascript"></script>
<script src="{{ asset('assets/plugins/calendar/dist/fullcalendar.min.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="{{ asset('assets/plugins/calendar/dist/locale-all.js') }}<?php echo TM_VERSION; ?>"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>




	    // var table = $('#myTable2').dataTable({
            
		// 		responsive     	: true, 
      	// 		processing      : true,
		// 		ordering		: false, //disable ordering      			
      	// 		destroy       	: true,   
      	// 		stateSave     	: true,      
      	// 		"bLengthChange"   : false,   
		// 		sDom			: 'lrtip',   
		// 	searching: false,
        //     serverSide: true,
        //     ajax: '{!! route('superadmin.company.data') !!}',
        //     language: {
        //         "url": "<?php echo __('app.datatable') ?>"
        //     },
        //     "fnDrawCallback": function( oSettings ) {
        //         $("body").tooltip({
        //             selector: '[data-toggle="tooltip"]'
        //         });
        //     },
        //     columns: [
        //         { data: 'chkbox', name: 'chkbox'},
		// 	    { data: 'Name', name: 'Name' },
        //         { data: 'Location', name: 'Location' },
        //         { data: 'Connected', name: 'Connected' },
        //         { data: 'Requisitions', name: 'Requisitions' },
        //         { data: 'Joining', name: 'Joining' },
        //         { data: 'Bench', name: 'Bench' },
        //         { data: 'OperationalPack', name: 'OperationalPack' },
        //         { data: 'SalesPack', name: 'SalesPack' },
        //         { data: 'SpartnerAll', name: 'SpartnerAll' }
        //     ]
        // });

        // new $.fn.dataTable.FixedHeader( table );


        $(document).on('click', '.show-detail', function ()
        {
        
            $('#kt_activitiesClient_toggle').trigger('click');	
            var id    		= $(this).data('row-id');
            var url   		= "{{ route('superadmin.company.viewmore',':id') }}";
            url     		= url.replace(':id', id);
            
            var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
                    $.ajax({
                            type  : 'GET',
                            url   : url,
                            beforeSend: function() {
                                    $('#kt_activitiesClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            },
                            success : function (response) {
                                    $('#kt_activitiesClient_body').html(response.view);
                            }
                    });
        });

        $(document).on('click', '.show-detail-direct', function ()
        {
            $('#kt_activitiesDirectClient_toggle').trigger('click');	
            var id    		= $(this).data('row-id');
            var url   		= "{{ route('superadmin.company.viewmore',':id') }}";
            url     		= url.replace(':id', id);
            
            var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
            $.ajax({
                            type  : 'GET',
                            url   : url,
                            beforeSend: function() {
                                    $('#kt_activitiesDirectClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            },
                            success : function (response) {
                                    $('#kt_activitiesDirectClient_body').html(response.view);
                            }
                    });
        }); 

		$('body').on('click', '.sa-params', function(){
            var id = $(this).data('row-id');
            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true,
                closeOnCancel: true
            }, function(isConfirm){
                if (isConfirm) {

                    var url = "{{ route('superadmin.company.destroy',':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";
                    var idempotencyKey = 'comp_del_' + id + '_' + Date.now();

                    $.ajax({
                        type: 'POST',
                        url: url,
                        headers: { 'X-Idempotency-Key': idempotencyKey },
                        data: {'_token': token, '_method': 'DELETE', 'idempotency_key': idempotencyKey},
                        success: function (response) {
                            if (response.status == "success") {
                                $.unblockUI();
                                if (typeof SessionRAMCache !== 'undefined') {
                                    SessionRAMCache.invalidate();
                                }
                                if (typeof broadcastLocalCompanyMutation === 'function') {
                                    broadcastLocalCompanyMutation({
                                        type: 'COMPANY_DELTA',
                                        company_id: id,
                                        action: 'deleted',
                                        delta: { id: id }
                                    });
                                }
                                if (typeof applyCompanyDeltaPatch === 'function') {
                                    applyCompanyDeltaPatch({
                                        company_id: id,
                                        action: 'deleted',
                                        delta: { id: id }
                                    });
                                } else if (typeof fetchCompanyAjax === 'function') {
                                    fetchCompanyAjax(typeof currentPage !== 'undefined' ? currentPage : 1, { bypassCache: true });
                                }
                            }
                        }
                    });
                }
            });
        });

        $(document).on('click','.code_generator',function(){
                // e.preventDefault();
                var id = $(this).data('id');
                // alert(id);
                // return false;
                var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
                var url       	=	"{{ route('superadmin.company.updateAccessCode') }}";
                var token       =	"{{ csrf_token() }}";

                                        $.ajax({
													type		: 'POST',
													url			: url,
													data		: {'_token': token, '_method': 'POST', 'id' : id},
													beforeSend	: function() {
																				$('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
													},
													success 	: function (response) {
                                                        if(response.status==="success"){
                                                            Swal.fire({
                                                                title: "Success",
                                                                html: `
                                                                    <p>This code for login</p>
                                                                    <p>Copy this code.</p>
                                                                    <input type="text" id="accessCode" value="${response.access_code}" readonly style="width: 100%; padding: 5px;">
                                                                    <button id="copyCodeBtn" style="margin-top: 10px; padding: 5px 10px;">Copy Code</button>
                                                                `,
                                                                icon: "success",
                                                                showConfirmButton: true
                                                            }).then(function(){
                                                
                                                            });	
                                                        }else{
                                                            $('#checkActive').removeClass('btn-light-success').addClass('btn-light-danger');
                                                            $('#checkActive').text("Inactive");
                                                        }
                                                        //  return false;
														}
										});	

               

        });

        $(document).on('click', '#copyCodeBtn', function() {
            var copyText = document.getElementById("accessCode");
            copyText.select();
            document.execCommand("copy");
            Swal.fire("Copied!", "The Access code has been copied to your clipboard.", "success");
        });

        $(document).on('click', '#copyText', function() {
            // Create a temporary input to hold the text to be copied
            var tempInput = document.createElement("input");
            
            // Set the input's value to the URL
            tempInput.value = "{{ route('super.login.form') }}";
            
            // Append the input to the document body
            document.body.appendChild(tempInput);
            
            // Select the input's content
            tempInput.select();
            
            // Copy the content to clipboard
            document.execCommand("copy");
            
            // Remove the temporary input element
            document.body.removeChild(tempInput);

            // Show a confirmation message
            $('#copyText').text('URL copied');
            setTimeout(() => {
                $('#copyText').text('URL Copy');
            }, 2000);
            
        });

        $(document).on('click', '.show-billing-address', function() {
            var id = $('#hid_client_id').val();
            var url       	=	"{{ route('superadmin.company.billinDetails') }}";
            $('#kt_billing_address_toggle').trigger('click');
            var token       =	"{{ csrf_token() }}";
                var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 

            $.ajax({
                                                type		: 'POST',
                                                url			: url,
                                                data		: {'_token': token, '_method': 'POST', 'id' : id},
                                                beforeSend	: function() {
                                                                            $('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
                                                },
                                                success 	: function (response) {
                                                    
                                                        $('#kt_billing_address_body').html(response.view);

                                                        var country_id = response.countrie_id;
                                                        var state_id = response.state_id;
                                                        var city_id = response.city_id;
                                                        setTimeout(() => {
                                                            $('#country').val(country_id).change();
                                                        }, 200);

                                                    
                                                    //  return false;
                                                    }
                                    });	


        
                var countrie_id = $('#country').val();
                if(countrie_id==101){
                    
                    $('#gstin_text').text('GSTIN')
                }else{
                    $('#gstin_text').text('TAXIN')
                }




        });

        $(document).on('click','.checkActive',function(){
            // e.preventDefault();
            var id = $('#hid_client_id').val();
            var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
            var url       	=	"{{ route('superadmin.company.checkActive') }}";
            var token       =	"{{ csrf_token() }}";

            $.ajax({
                                                type		: 'POST',
                                                url			: url,
                                                data		: {'_token': token, '_method': 'POST', 'id' : id},
                                                beforeSend	: function() {
                                                                            $('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
                                                },
                                                success 	: function (response) {
                                                    if(response.pack_status==="active"){
                                                        $('.checkActivetext').removeClass('text-danger').addClass('text-success');
                                                        $('.checkActivetext').text("Active");
                                                    }else{
                                                        $('.checkActivetext').removeClass('text-success').addClass('text-danger');
                                                        $('.checkActivetext').text("Suspend");
                                                    }
                                                    //  return false;
                                                    }
                                    });	

            

        });

        $(document).on('click','.checkStatusOperational',function(){
        //    e.preventDefault();
            var id = $('#hid_client_id').val();
            var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
            var url       	=	"{{ route('superadmin.company.checkStatusOperational') }}";
            var token       =	"{{ csrf_token() }}";

            $.ajax({
                                                type		: 'POST',
                                                url			: url,
                                                data		: {'_token': token, '_method': 'POST', 'id' : id},
                                                beforeSend	: function() {
                                                                            $('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
                                                },
                                                success 	: function (response) {

                                                    $('.checkStatusOperational_text').text(response.pack_status);


                                                    }
                        });	

            

        });

        $(document).on('click','.checkStatusSales',function(){
            // e.preventDefault();
              var id = $('#hid_client_id').val();
              var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
              var url       	=	"{{ route('superadmin.company.checkStatusSales') }}";
              var token       =	"{{ csrf_token() }}";

                   $.ajax({
                        type		: 'POST',
                        url			: url,
                        data		: {'_token': token, '_method': 'POST', 'id' : id},
                        beforeSend	: function() {
                                      $('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
                        },
                        success 	: function (response) {
                                if (response.status == "success") {
                                    
                                    $('.checkStatusSales_text').text(response.pack_status);
                                                                                                    
                                }
                          }
                  });	

        });

        $(document).on('click','.app_int_amount',function(){

            var selectedValue = $("input[name='app_int_amount']:checked").val();
            //   alert(selectedValue);
            // e.preventDefault();
            var currency_get = $('#currency_get').val();
            var gst_on_get = $('#gst_on_get').val();
            var id = $('#hid_client_id').val();
            var baseurl     =	"{{ asset('assets/images/Loading2.gif') }}"; 
            var url       	=	"{{ route('superadmin.company.topuptypegetprice') }}";
            var token       =	"{{ csrf_token() }}";

                    $.ajax({
                                                type		: 'POST',
                                                url			: url,
                                                data		: {'_token': token, '_method': 'POST', 'id' : selectedValue,'currency_get':currency_get,'gst_on_get':gst_on_get},
                                                beforeSend	: function() {
                                                                            $('#approve').html('<img src="'+baseurl+'" height="35px" width="100%">');
                                                },
                                                success 	: function (response) {
                                                    $('.price_get').val(response.price);
                                                    $('.coins_get').val(response.coins);
                                                    $('.gsttax_text').html(response.gst+'%');
                                                    $('.subtotal_text').html(response.currency+' '+response.price);
                                                    $('.total_price_text').html(response.currency+' '+response.total_price);

                                                    $('.gsttax_hidden_value').val(response.gst);
                                                    $('.gst_amount_hidden_value').val(response.gst_amount);
                                                    $('.total_price_hidden_value').val(response.total_price);
                                                    $('#discount').val(response.discount);


                                                    $('.total_price_hidden_value').attr('readonly', true);

                                                    if(response.custome_status){
                                                        $('.price_get').removeAttr('readonly');
                                                        $('.coins_get').removeAttr('readonly');
                                                    }else{
                                                        $('.price_get').attr('readonly', true);
                                                        $('.coins_get').attr('readonly', true);
                                                    }
                                                    //  return false;
                                                    }
                    });	

        });

        $(document).on('change','#payment-mode-selector',function(e){
                var paymentMode = $(this).val();
                var paymentMethodSelector = $('#payment-method-selector');
                var paymenLink = $('#payment-method-selector').val();
                $('#paymentLinkPaymentstatus').val('');
                $('#paymentLinkpayment_orderId').val('');
                $('#paymentLinkshort_url').val('');
                $('.paymentLinkshow').hide();
                if (paymenLink === 'razorpay') {
                        $('.paymentLinkGenerate').show();
                } else { //paymentLinkGenerate
                    $('.paymentLinkGenerate').hide();
                }

                // Clear current payment method options
                paymentMethodSelector.html('<option value="">Select Payment Method</option>');

                if (paymentMode === 'Payment Link') {
                    // Show only Razorpay and PhonePe options
                    paymentMethodSelector.append('<option value="razorpay">Razorpay</option>');
                    // paymentMethodSelector.append('<option value="phonepe">PhonePe</option>');
                } else {
                    // Reset to default options
                    paymentMethodSelector.append('<option value="offline">Offline</option>');
                    paymentMethodSelector.append('<option value="online">Online</option>');
                }
        });

        $(document).on('change','#payment-method-selector',function(e){
            var paymentMode = $(this).val();
                if (paymentMode === 'razorpay') {
                        $('.paymentLinkGenerate').show();
                } else { //paymentLinkGenerate
                    $('.paymentLinkGenerate').hide();
                }
        });


        $(document).on('change','#currency_get,#topUptype',function(e){
            e.preventDefault();

            var currency_get = $('#currency_get').val();
            var topUptype  	= $('#topUptype').val();
            var url   		= "{{ route('superadmin.company.priceCurrencyWise') }}";
            
            var baseurl    = "{{ asset('assets/images/Loading2.gif') }}"; 
            $.ajax({
                            type  : 'GET',
                            url   : url,
                            data: {'currency_get':currency_get,'topUptype':topUptype},
                            beforeSend: function() {
                                    $('#currencyPrice').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            },
                            success : function (response) {

                                    $('#currencyPrice').html(response);

                                    $('#discount').val('');
                                    $('#gst').val('');
                                    $('.price_get').val('');
                                    $('.coins_get').val('');
                                    $('.gst_amount_hidden_value').val('');
                                    $('.total_price_hidden_value').val('');
                            }
                    });

        });   

        $(document).on('submit','#candidate_new_form', function(e) {
                    e.preventDefault(); // Prevent the form from submitting via the browser
                    var form = $(this);
                    // var url = form.attr('action');
                    var formData = form.serialize();
                    $('.indicator-label-can').hide();
                    $('.indicator-progress-can').show();
                    $('#candidate_new_submit').prop('disabled',true);
                    $.ajax({
                        type: "POST",
                        url: '{{route('superadmin.company.googleSheetCandidateData')}}'+'?_token=' + '{{ csrf_token() }}',		
                        data: formData,
                        success: function(response) {
                        $('#candidate_new_submit').prop('disabled',false);
                        if(response.status=='error'){
                            alert(response.message);
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                        }
                        // $("#candidate_new_form")[0].reset();
                        $('.indicator-label-can').show();
                        $('.indicator-progress-can').hide();
                        $('#successButtoncan').show();
                        $('#errorButtoncan').show();
                        $('#successButtoncan').text(response.done+" candidate uploaded successfully");
                        $('#errorButtoncan').text(response.pendind+" candidate pending");
                        setTimeout(() => {
                            $('#successButtoncan').hide();
                            $('#errorButtoncan').hide();
                        }, 2500);

                        },
                        error: function(xhr, status, error) {

                        var errors = xhr.responseJSON.errors;
                            $('.error').empty(); // Clear any previous errors

                            if (errors.googlesheetid) {
                            alert("Candidate google sheet Id id required");
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                                // $('#name-error').html(errors.googlesheetid[0]);
                            }
                            if (errors.job_id) {
                            alert("Job is required");
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                            if (errors.teams) {
                            alert("Team is required");
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                            }
                            if (errors.current_ctc_cur) {
                            alert("Currency is required");
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                            if (errors.current_ctc_mode) {
                            alert("Currency Mode is required");
                            $('.indicator-label-can').show();
                            $('.indicator-progress-can').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                        }
                    });
        });

        $(document).on('submit','#candidate_new_form_job', function(e) {
                    e.preventDefault(); // Prevent the form from submitting via the browser
                    var form = $(this);
                    // var url = form.attr('action');
                    var formData = form.serialize();
                    $('.indicator-label-can-job').hide();
                    $('.indicator-progress-can-job').show();
                    $('#candidate_new_submit').prop('disabled',true);
                    $.ajax({
                        type: "POST",
                        url: '{{route('superadmin.company.googleSheetCandidateDataJob')}}'+'?_token=' + '{{ csrf_token() }}',		
                        data: formData,
                        success: function(response) {
                        $('#candidate_new_submit_job').prop('disabled',false);
                        if(response.status=='error'){
                            alert(response.message);
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                        }
                        // $("#candidate_new_form")[0].reset();
                        $('.indicator-label-can-job').show();
                        $('.indicator-progress-can-job').hide();
                        $('#successButtoncanJob').show();
                        $('#errorButtoncanJob').show();
                        $('#successButtoncanJob').text(response.done+" Jobs uploaded successfully");
                        $('#errorButtoncanJob').text(response.pendind+" Jobs pending");
                        setTimeout(() => {
                            $('#successButtoncanJob').hide();
                            $('#errorButtoncanJob').hide();
                        }, 2500);

                        },
                        error: function(xhr, status, error) {

                        var errors = xhr.responseJSON.errors;
                            $('.error').empty(); // Clear any previous errors

                            if (errors.googlesheetid) {
                            alert("Job google sheet Id id required");
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                                // $('#name-error').html(errors.googlesheetid[0]);
                            }
                            if (errors.job_id) {
                            alert("Job is required");
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                            if (errors.teams) {
                            alert("Team is required");
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                            }
                            if (errors.current_ctc_cur) {
                            alert("Currency is required");
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                            if (errors.current_ctc_mode) {
                            alert("Currency Mode is required");
                            $('.indicator-label-can-job').show();
                            $('.indicator-progress-can-job').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                        }
                    });
        });

        $(document).on('change','#clientID',function(e){
            e.preventDefault();
                var client_id 	=	$(this).val();				
                        
                if(client_id > 0)
                {
                    
                    $('#kt_activitiesRecruiter_toggle').trigger('click');   						
                    
                    var clientName    	=   $("#clientId option:selected"); 
                    var text_value 		=	clientName.text();              
        
                    var strVal			=	'';		var bgTextDecore	=	'';		var str				=	'';						
                    str					=	text_value;
                    str					=	str.substring(0, 1);
                    str	= strVal		=	str.toLowerCase();
                    var str_bg			=	str.charCodeAt(0);
                    
                    if(str_bg >=97 && str_bg <=101){
                    bgTextDecore		=	'fs-3 bg-light-danger text-danger';	
                    }else if(str_bg >=102 && str_bg <=106){
                    bgTextDecore		=	'fs-3 bg-light-warning text-warning';	
                    }else if(str_bg >=107 && str_bg <=111){
                    bgTextDecore		=	'fs-3 bg-light-primary text-primary';	
                    }else if(str_bg >=112 && str_bg <=116){
                    bgTextDecore		=	'fs-3 bg-light-success text-success';	
                    }else if(str_bg >=117 && str_bg <=121){
                    bgTextDecore		=	'fs-3 bg-dark text-light';	
                    }else {
                    bgTextDecore		=	'fs-3 bg-light text-dark';	
                    }											
                    $('#light_recruiter_caption').html(strVal.toUpperCase()); 		
                    $('#light_recruiter_caption').addClass(bgTextDecore); 						
                    
                    
                    var recruiter	=	$('#recruiter').val();
                    var token 		=	"{{ csrf_token() }}";
                    
                        var id    		=	client_id;
                            var url   		=	"{{ route('superadmin.company.hiring_manager',':id') }}"; 
                            url     		=	url.replace(':id', id);  		
                            var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";    		
                    $.ajax({
                            type  		: 'GET',
                            url   		: url,
                            data		: {'_token': token, 'hiring_manager' : client_id},
                            beforeSend	: function() {
                                    $('#kt_activitiesRecruiter_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                            },
                            success 	: function (response) 
                            {
                                                    // Get the select element where you want to add options
                            const selectElement = $('#hiring_namager'); // Replace with your actual select element ID

                            // Clear any existing options first
                            selectElement.empty();

                            // Loop through the hiring_manager data
                            response.hiring_manager.forEach(function(manager) {
                                // Append each manager as an option in the select element
                                selectElement.append(
                                    $('<option>', {
                                        value: manager.contact_person_name, // Set the option's value
                                        text: manager.contact_person_name // Set the text displayed in the dropdown
                                    })
                                );
                            });   
                            }
                        }); 
                            
                }
                else
                {
                    alert("Please select any client after that choose our Recruiters team");
                }	
                                            
        });


        $(document).on('submit','#client_new_form', function(e) {
                    e.preventDefault(); // Prevent the form from submitting via the browser
                    var form = $(this);
                    // var url = form.attr('action');
                    var formData = form.serialize();
                    $('.indicator-label-client').hide();
                    $('.indicator-progress-client').show();
                    $('#client_new_submit').prop('disabled',true);
                    $.ajax({
                        type: "POST",
                        url: '{{route('superadmin.company.googleSheetInsertdataInDatabase')}}'+'?_token=' + '{{ csrf_token() }}',		
                        data: formData,
                        success: function(response) {
                        $('#client_new_submit').prop('disabled',false);
                        if(response.status=='error'){
                            alert(response.message);
                            $('.indicator-label-client').show();
                            $('.indicator-progress-client').hide();
                            return false;
                        }
                        $("#client_new_form")[0].reset();
                        $('.indicator-label-client').show();
                        $('.indicator-progress-client').hide();
                        $('#successButtonclient').show();
                        $('#errorButtonclient').show();
                        $('#successButtonclient').text(response.done+" client uploaded successfully");
                        $('#errorButtonclient').text(response.pendind+" client pending");
                        setTimeout(() => {
                            $('#successButtonclient').hide();
                            $('#errorButtonclient').hide();
                        }, 2500);

                        },
                        error: function(xhr, status, error) {

                        var errors = xhr.responseJSON.errors;
                            $('.error').empty(); // Clear any previous errors

                            if (errors.googlesheetid) {
                            alert("Client google sheet Id id required");
                            $('.indicator-label-client').show();
                            $('.indicator-progress-client').hide();
                            return false;
                                // $('#name-error').html(errors.googlesheetid[0]);
                            }
                            if (errors.teams) {
                            alert("Team is required");
                            $('.indicator-label-client').show();
                            $('.indicator-progress-client').hide();
                            return false;
                                // $('#email-error').html(errors.job_id[0]);
                            }
                        }
                    });
        });

        $(document).on('submit','#operational_top_up_new_form', function(e) {
                    e.preventDefault(); // Prevent the form from submitting via the browser

                    // var selectedValue = $("input[name='app_int_amount']:checked").val();
                    // if(selectedValue==''){
                    //     swal("Required!", response.message, "error");
                    //     return false;
                    // }
                    

                    var form = $(this);
                    // var url = form.attr('action');
                    
                    var formData = $('#operational_top_up_new_form').serialize();
                    var formData = form.serialize();
                    $('.indicator-label-top-up').hide();
                    $('.indicator-progress-top-up').show();
                    // $('#operational_top_up_new_submit').prop('disabled',true);
                    $.ajax({
                        type: "POST",
                        url: '{{route('superadmin.company.topUpCoinsAdded')}}'+'?_token=' + '{{ csrf_token() }}',		
                        data: formData,
                        success: function(response) {
                        $('#operational_top_up_new_submit').prop('disabled',false);
                        if(response.status=='error'){
                            alert(response.message);
                            $('.indicator-label-top-up').show();
                            $('.indicator-progress-top-up').hide();
                            return false;
                        }else if(response.status=='offline'){
                            Swal.fire("Success", "Payment has been successfully ", "success").then(function(){
                                $('#kt_activitiesClient_close').click();
                            });	
                            return false;
                        }else if(response.status=='razorpay'){
                            Swal.fire("Success", "Payment link has been send successfully ", "success").then(function(){
                                $('#kt_activitiesClient_close').click();
                            });	
                            return false;
                        }

                        initiateRazorpayPayment(response.orderId, response.amount,response.update_coins,response.company_id,response.event_type);

                        // $("#operational_top_up_new_form")[0].reset();
                        $('.indicator-label-top-up').show();
                        $('.indicator-progress-top-up').hide();
                        $('#successButtonoperationaltopup').show();
                        $('#errorButtonoperationaltopup').hide();
                        $('#successButtonoperationaltopup').text(" Top Up Coins uploaded successfully");
                        $('#errorButtonoperationaltopup').text(" ");
                        setTimeout(() => {
                            $('#successButtonoperationaltopup').hide();
                            $('#errorButtonoperationaltopup').hide();
                        }, 2500);

                        },
                        error: function(xhr, status, error) {
                            // Check if the response has validation errors
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                var errors = xhr.responseJSON.errors;

                                // Clear any previous error messages
                                $('.error').empty(); 

                                // Display specific error messages based on validation errors
                                if (errors.topUptype) {
                                    alert("Top Up Type is required");
                                }
                                if (errors.price) {
                                    alert("Price is required");
                                }
                                if (errors.paymentmodeselector) {
                                    alert("Payment mode is required");
                                }
                                if (errors.paymentmethodselector) {
                                    alert("Payment method is required");
                                }
                                if (errors.currency) {
                                    alert("Currency is required");
                                }
                                if (errors.chequenumber) {
                                    alert("Cheque Number is required");
                                }
                                if (errors.chequedate) {
                                    alert("Cheque Date is required");
                                }
                                if (errors.bankname) {
                                    alert("Bank Name is required");
                                }
                                if (errors.transactionid) {
                                    alert("Transaction ID is required");
                                }
                                if (errors.bankreference) {
                                    alert("Bank Reference Number is required");
                                }

                                // Show/Hide progress indicators as needed
                                $('.indicator-label-top-up').show();
                                $('.indicator-progress-top-up').hide();
                            } else {
                                // Handle other types of errors (e.g., server errors)
                                alert("An unexpected error occurred. Please try again later.");
                            }
                        }


                    });


        });

        $(document).on('submit','#boost_keyword_form', function(e) {
            e.preventDefault();
            var form = $(this);
             var formData = $('#boost_keyword_form').serialize();
             var formData = form.serialize();
            $.ajax({
                type: "POST",
                url: '{{route('superadmin.company.boostkeywordadd')}}'+'?_token=' + '{{ csrf_token() }}',
                data: formData,
                success: function(response) {
                    Swal.fire("Success", "Keyword has been added successfully ", "success").then(function(){
                                $('#kt_activitiesClient_close').click();
                            });	
                },
                error: function(xhr, status, error) {
                   
                }
            });
        });
        

        function paymentLinkGenerate(){
            var total_price  = $('.total_price_hidden_value').val();
            var currency_get  = $('#currency_get').val();
            var company_id =  $('#company_id_topup').val();
            var packages_id = $("input[name='app_int_amount']:checked").val();

            $.ajax({
                    url: '{{ route('superadmin.custompackages.generatecustompackages', @$company_id) }}',
                    type: "GET",
                    data: {'total_price':total_price,'currency_get':currency_get,'company_id':company_id,'packages_id':packages_id,'mainType':2},
                    success: function(response) {
                        //
                        if(response.status=='success'){
                            // alert(response.short_url);
                            $('#paymentLinktextToCopy').text(response.short_url);
                            $('#paymentLinkPaymentstatus').val(response.Paymentstatus);
                            $('#paymentLinkpayment_orderId').val(response.payment_orderId);
                            $('#paymentLinkshort_url').val(response.short_url);
                            $('.paymentLinkshow').show();
                            $('#paymentLinkshow').val(response.short_url);
                        }else{
                            $('.paymentLinkshow').hide();
                            alert('Failed to create payment link');
                        }
                        
                    },
                    error: function(response) {
                        handleFails(response);
                        alert('Failed to create payment link');
                        $('.paymentLinkshow').hide();
                    }
                });

        }

        function initiateRazorpayPayment(orderId, amount,update_coins,company_id,event_type) {
            var options = {
                "key": "{{ $payment_settings->razorpay_key}}", // Razorpay API Key
                "amount": amount, // Amount in paisa
                "currency": "INR",
                "name": name,
                "description": "Test Transaction",
                "order_id": orderId, // Order ID created in backend
                "handler": function (response){

                

                    var formData = {
                        _token: "{{ csrf_token() }}", // CSRF token for security
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id, // Optional, if you want to pass it back
                        razorpay_signature: response.razorpay_signature, // Optional, if needed
                        razorpay_update_coins: update_coins, // Optional, if needed
                        razorpay_company_id: company_id, // Optional, if needed
                        razorpay_event_type: event_type, // Optional, if needed
                        razorpay_amount: amount // Optional, if needed
                    };


                    // var formData = $('#operational_top_up_new_form').serialize();
                    // formData.push({ name: '_token', value: "{{ csrf_token() }}" });
                    // formData.push({ name: 'razorpay_payment_id', value: response.razorpay_payment_id });
                    // formData.push({ name: 'razorpay_order_id', value: response.razorpay_order_id });
                    // formData.push({ name: 'razorpay_signature', value: response.razorpay_signature });
                    // console.log(response);
                    $.ajax({
                        url: "{{ route('payment.callback') }}", // Your callback route
                        method: 'POST',
                        data: formData,
                        success: function (response) {
                            if (response.status) {
                                alert("Payment successful! Payment ID: " + response.razorpay_payment_id);
                                // Handle success (e.g., redirect or show success message)
                                // window.location.href = response.redirect_url;
                            } else {
                                // Handle failure (e.g., show error message)
                                alert('Payment failed. Please try again.');
                            }
                            },
                            error: function (error) {
                                console.log(error);
                                alert('An error occurred. Please try again.');
                            }
                    });
                            
                },
                // "prefill": {
                //     "name": name,
                //     "email": email,
                // },
                "theme": {
                    "color": "#F37254"
                }
            };

            var rzp = new Razorpay(options);
            rzp.open(); // Open the Razorpay payment window
        }

        $(document).on('keyup','.price_get,.coins_get,#gst', function(){
            // alert("dk");
            gst_calculate();
        });
        $(document).on('change','#gst_on_get', function(){
            gst_calculate();
        });
        function gst_calculate(){
            var currency_get = $('#currency_get').val(); // Get the selected currency
            var price = parseFloat($('.price_get').val()); // Get and parse the price value
            var gst_on_get = $('#gst_on_get').val(); // Get and parse the GST percentage (if needed)
            var gst = parseFloat($('#gst').val()); // Get and parse the GST percentage (if needed)

            // alert(gst);
            var discount = parseFloat($('#discount').val()); // Get and parse the price value
            var discount_amount = price * discount / 100; // 
            discount_amount_final = price;
            if(discount>0){
                var discount_amount_final = price-discount_amount;
            }


            if (currency_get === "INR" && gst_on_get==="Yes") {
                var gst_amount = discount_amount_final * gst / 100; // Calculate GST amount at 18%
                var total_amount = discount_amount_final + gst_amount; // Calculate total amount with GST
               
               
                $('.gst_amount_hidden_value').val(gst_amount.toFixed(2)); // Show GST amount (2 decimal places)
                $('.gsttax_hidden_value').val(gst); // Show GST amount (2 decimal places)
                $('.total_price_hidden_value').val(total_amount.toFixed(2)); // Show total amount (2 decimal places)

                $('.subtotal_text').text(discount_amount_final.toFixed(2)); // Show GST amount (2 decimal places)
                $('.gsttax_text').text(gst); // Show GST amount (2 decimal places)
                $('.total_price_text').text(total_amount.toFixed(2)); // Show total amount (2 decimal places)

            }else if(currency_get === "INR" && gst_on_get==="No") {
               
                 var gst_amount = discount_amount_final * 0 / 100; // Calculate GST amount at 18%
                 var total_amount = discount_amount_final + gst_amount; // Calculate total amount with GST
                
                // Displaying the calculated amounts (you can update this part as needed)
                $('.gst_amount_hidden_value').val(gst_amount.toFixed(2)); // Show GST amount (2 decimal places)
                $('.gsttax_hidden_value').val(0); // Show GST amount (2 decimal places)
                $('.total_price_hidden_value').val(total_amount.toFixed(2)); // Show total amount (2 decimal places)
            } else {
                $('.total_price_hidden_value').val(discount_amount_final.toFixed(2)); // Show total amount (2 decimal places)
                $('.gsttax_hidden_value').val(0);
                $('.gst_amount_hidden_value').val(0);
                // If needed, handle cases for other currencies
                // alert('GST calculation is only applicable for INR currency.');
            }
        }
        $(document).on('keyup','.coins_get', function(){
               total_coins();
        });
        $(document).on('keyup','#discount', function(){
        gst_calculate();
        });

        function total_coins(){
                let value = parseFloat($('.coins_get').val());
                let percentage = parseFloat($('#discount').val());

                if (isNaN(value)) {
                    value = 0; // Default to 0 if invalid
                }
                if (isNaN(percentage)) {
                    percentage = 0; // Default to 0 if invalid
                }

                var total_coins = value; // Add the percentage amount to the original value
                if(percentage !=''){
                    let additionalAmount = (percentage / 100) * value;
                    var total_coins = value - additionalAmount; // Add the percentage amount to the original value
                }
                
            
                // $('.price_get').val(total_coins.toFixed(2)); // Set the value with two decimal points
                gst_calculate();

        }
		
</script>

<script>
    /**
     * Phase 2 — SessionRAMCache & Client-First Asynchronous State Controller
     * 
     * Features:
     * 1. SessionRAMCache: Memory-bounded (max 50 entries, FIFO eviction), 30s TTL.
     * 2. Persistent Multi-Page Selection: selectedCompanyIds Set with dynamic UI bulk action bar.
     * 3. Tab Visibility Re-validation: Background silent refresh on tab focus after 15s.
     * 4. Circuit-Breaker Offline Resilience: Automatic offline warning & serving from cache.
     * 5. Session Preference Persistence: Column visibility and limit preferences stored in sessionStorage.
     * 6. Anti-Race Debouncing: 300ms debounce + AbortController.
     * 7. Client-First CSV Export: Zero server strain.
     */
    let currentPage = 1;
    let searchDebounceTimer = null;
    let inFlightAbortController = null;

    const SessionRAMCache = {
        cache: new Map(),
        maxEntries: 50,
        ttlMs: 30000, // 30 seconds
        selectedCompanyIds: new Set(),
        lastFetchTimestamp: Date.now(),

        get: function(key) {
            if (!this.cache.has(key)) return null;
            const entry = this.cache.get(key);
            if (Date.now() - entry.timestamp > this.ttlMs) {
                this.cache.delete(key);
                return null;
            }
            return entry.data;
        },

        set: function(key, data) {
            if (this.cache.size >= this.maxEntries) {
                const oldestKey = this.cache.keys().next().value;
                this.cache.delete(oldestKey);
            }
            this.cache.set(key, {
                data: data,
                timestamp: Date.now()
            });
            this.lastFetchTimestamp = Date.now();
        },

        invalidate: function() {
            this.cache.clear();
        },

        clear: function() {
            this.invalidate();
        },

        toggleSelect: function(id, isChecked) {
            id = String(id);
            if (isChecked) {
                this.selectedCompanyIds.add(id);
            } else {
                this.selectedCompanyIds.delete(id);
            }
            this.updateSelectionUI();
        },

        selectAllVisible: function(isChecked) {
            $('.company-row-checkbox').each(function() {
                const id = String($(this).val());
                $(this).prop('checked', isChecked);
                if (isChecked) {
                    SessionRAMCache.selectedCompanyIds.add(id);
                } else {
                    SessionRAMCache.selectedCompanyIds.delete(id);
                }
            });
            this.updateSelectionUI();
        },

        clearSelection: function() {
            this.selectedCompanyIds.clear();
            $('.company-row-checkbox').prop('checked', false);
            $('#checkAllRows').prop('checked', false);
            this.updateSelectionUI();
        },

        updateSelectionUI: function() {
            const count = this.selectedCompanyIds.size;
            $('#selectedCountNumber').text(count);
            if (count > 0) {
                $('#bulkActionBar').slideDown(150);
            } else {
                $('#bulkActionBar').slideUp(150);
            }

            const visibleCheckboxes = $('.company-row-checkbox');
            if (visibleCheckboxes.length > 0) {
                const allVisibleChecked = visibleCheckboxes.filter(':checked').length === visibleCheckboxes.length;
                $('#checkAllRows').prop('checked', allVisibleChecked);
            } else {
                $('#checkAllRows').prop('checked', false);
            }
        },

        syncCheckboxesWithState: function() {
            $('.company-row-checkbox').each(function() {
                const id = String($(this).val());
                $(this).prop('checked', SessionRAMCache.selectedCompanyIds.has(id));
            });
            this.updateSelectionUI();
        }
    };

    // Global alias for compatibility with existing hooks
    const clientCache = SessionRAMCache;

    function getFilterParams(page = 1) {
        const search = ($('#searchInput').val() || '').trim();
        const location = $('#locationFilter').val() || '';
        const status = $('#statusFilter').val() || '';
        const sortingfilter = $('#sortingfilter').val() || '';
        const limit = 10;
        const effectiveStatus = status || sortingfilter;

        return {
            page: page,
            size: limit,
            limit: limit,
            search: search,
            location: location,
            status: effectiveStatus,
            sortingfilter: sortingfilter,
            parm: true
        };
    }

    function getCacheKey(params) {
        return `${params.page}_${params.size}_${params.search}_${params.location}_${params.status}`;
    }

    function renderTable(data, page, limit) {
        $('#tableLoadingOverlay').hide();
        $('.loadingMessagecompanyListajax').hide();
        $('#companyListajax').html(data.html);

        $('#paginationcopanylist').html(data.pagination);

        const total = parseInt(data.total || 0, 10);
        const from = total === 0 ? 0 : (page - 1) * limit + 1;
        const to = Math.min(page * limit, total);
        $('#showingCount').text(`Showing ${from} to ${to} of ${total} records.`);

        // Re-apply column visibility preferences
        applyColumnPreferences();

        // Synchronize row selection checkboxes across pages
        SessionRAMCache.syncCheckboxesWithState();
    }

    function applyColumnPreferences() {
        $('.column-toggle-check').each(function() {
            const colClass = $(this).data('col');
            const isVisible = $(this).is(':checked');
            $('.' + colClass).toggle(isVisible);
        });
    }

    function saveColumnPreferences() {
        try {
            const prefs = {};
            $('.column-toggle-check').each(function() {
                prefs[$(this).data('col')] = $(this).is(':checked');
            });
            sessionStorage.setItem('sa_companies_col_prefs', JSON.stringify(prefs));
        } catch (e) {}
    }

    function loadColumnPreferences() {
        try {
            const raw = sessionStorage.getItem('sa_companies_col_prefs');
            if (raw) {
                const prefs = JSON.parse(raw);
                $('.column-toggle-check').each(function() {
                    const col = $(this).data('col');
                    if (prefs.hasOwnProperty(col)) {
                        $(this).prop('checked', prefs[col]);
                    }
                });
            }
        } catch (e) {}
        applyColumnPreferences();
    }

    function fetchCompanyAjax(page = 1, options = {}) {
        currentPage = page;
        const params = getFilterParams(page);
        const cacheKey = getCacheKey(params);

        // Fast RAM Cache lookup
        if (!options.bypassCache && !options.silentBackgroundRevalidate) {
            const cached = SessionRAMCache.get(cacheKey);
            if (cached) {
                renderTable(cached, page, params.size);
                return;
            }
        }

        if (inFlightAbortController) {
            inFlightAbortController.abort();
        }
        inFlightAbortController = new AbortController();

        if (!options.silentBackgroundRevalidate) {
            $('#tableLoadingOverlay').show();
            $('.loadingMessagecompanyListajax').show();
        }

        $.ajax({
            url: "{!! route('superadmin.company.data_ajax') !!}",
            type: 'GET',
            data: params,
            signal: inFlightAbortController.signal,
            success: function(response) {
                SessionRAMCache.set(cacheKey, response);
                renderTable(response, page, params.size);
            },
            error: function(xhr, status) {
                if (status === 'abort') return;
                $('#tableLoadingOverlay').hide();
                $('.loadingMessagecompanyListajax').hide();

                // Offline fallback to stale cache if available
                const staleEntry = SessionRAMCache.cache.get(cacheKey);
                if (staleEntry) {
                    renderTable(staleEntry.data, page, params.size);
                    if (window.SuperAdminOfflineCircuitBreaker) {
                        window.SuperAdminOfflineCircuitBreaker.show();
                    } else {
                        $('#superadminOfflineBannerContainer').slideDown(150);
                    }
                } else {
                    $('#companyListajax').html('<tr><td colspan="11" class="text-center py-8 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Failed to load company records. Please retry.</td></tr>');
                }
            }
        });
    }

    function exportTableToCSV(filename) {
        const rows = document.querySelectorAll('#myTable2 tr');
        const csv = [];
        for (let i = 0; i < rows.length; i++) {
            const row = [];
            const cols = rows[i].querySelectorAll('th, td');
            // Skip checkbox (index 0) and actions (last index)
            for (let j = 1; j < cols.length - 1; j++) {
                let text = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/\s+/g, ' ').trim();
                text = text.replace(/"/g, '""');
                row.push('"' + text + '"');
            }
            if (row.length > 0) {
                csv.push(row.join(','));
            }
        }

        const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    $(document).ready(function() {
        // Load saved session preferences
        loadColumnPreferences();

        // Initial Data Fetch
        fetchCompanyAjax(1);

        // 300ms Debounced Search Input
        $('#searchInput').on('input', function() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(function() {
                fetchCompanyAjax(1);
            }, 300);
        });

        // Filter Dropdowns
        $('#locationFilter, #statusFilter, #sortingfilter').on('change', function() {
            fetchCompanyAjax(1);
        });

        // Clear Filters
        $('#btnClearFilters').on('click', function() {
            $('#searchInput').val('');
            $('#locationFilter').val('');
            $('#statusFilter').val('');
            $('#sortingfilter').val('');
            fetchCompanyAjax(1);
        });

        // Pagination Clicks
        $(document).on('click', '#paginationcopanylist .page-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled') || $(this).hasClass('active_disabled') || $(this).hasClass('previous_disabled') || $(this).hasClass('next_disabled')) return;
            fetchCompanyAjax(parseInt(page, 10));
        });

        // Column Visibility Toggle & Persistence
        $(document).on('change', '.column-toggle-check', function() {
            applyColumnPreferences();
            saveColumnPreferences();
        });

        // Persistent Row Selection
        $(document).on('change', '.company-row-checkbox', function() {
            const id = $(this).val();
            const isChecked = $(this).is(':checked');
            SessionRAMCache.toggleSelect(id, isChecked);
        });

        $(document).on('change', '#checkAllRows', function() {
            const isChecked = $(this).is(':checked');
            SessionRAMCache.selectAllVisible(isChecked);
        });

        $(document).on('click', '#btnClearSelection', function() {
            SessionRAMCache.clearSelection();
        });

        // Tab Visibility Synchronization (Stale-While-Revalidate after 15s)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                const elapsed = Date.now() - SessionRAMCache.lastFetchTimestamp;
                if (elapsed > 15000) {
                    fetchCompanyAjax(currentPage, { silentBackgroundRevalidate: true });
                }
            }
        });

        // Circuit-Breaker Network Listeners
        window.addEventListener('online', function() {
            if (window.SuperAdminOfflineCircuitBreaker) {
                window.SuperAdminOfflineCircuitBreaker.hide();
            } else {
                $('#superadminOfflineBannerContainer').slideUp(150);
            }
            fetchCompanyAjax(currentPage, { bypassCache: true });
        });

        window.addEventListener('offline', function() {
            if (window.SuperAdminOfflineCircuitBreaker) {
                window.SuperAdminOfflineCircuitBreaker.show();
            } else {
                $('#superadminOfflineBannerContainer').slideDown(150);
            }
        });

        // Client-Side CSV / Excel Export
        $(document).on('click', '.export-trigger', function(e) {
            e.preventDefault();
            const format = $(this).data('format');
            const today = new Date().toISOString().slice(0, 10);
            exportTableToCSV(`companies_export_${today}.csv`);
        });

        // =========================================================================
        // REAL-TIME SYNCHRONIZATION: Laravel Reverb WebSockets + BroadcastChannel
        // =========================================================================

        // 1. Cross-Tab Synchronization Bus via BroadcastChannel
        let companySyncBus = null;
        try {
            if (typeof window.BroadcastChannel !== 'undefined') {
                companySyncBus = new BroadcastChannel('jero_sa_company_sync_bus');
                companySyncBus.onmessage = function(event) {
                    if (event.data && event.data.type === 'COMPANY_DELTA') {
                        handleCompanyDeltaSync(event.data);
                    }
                };
            }
        } catch (e) {
            console.warn('[SA_COMPANY_SYNC] BroadcastChannel init notice:', e);
        }

        // 2. Storage Event Fallback for Cross-Tab Sync
        window.addEventListener('storage', function(e) {
            if (e.key === 'jero_sa_company_mutation' && e.newValue) {
                try {
                    const data = JSON.parse(e.newValue);
                    if (data && data.type === 'COMPANY_DELTA' && (Date.now() - data.sent_at < 5000)) {
                        handleCompanyDeltaSync(data);
                    }
                } catch (err) {}
            }
        });

        function broadcastLocalCompanyMutation(payload) {
            payload.sent_at = Date.now();
            if (companySyncBus) {
                try {
                    companySyncBus.postMessage(payload);
                } catch (e) {}
            }
            try {
                localStorage.setItem('jero_sa_company_mutation', JSON.stringify(payload));
            } catch (e) {}
        }

        // 3. Laravel Echo Private Channel via Reverb WebSockets
        if (typeof window.Echo !== 'undefined') {
            try {
                window.Echo.private('superadmin.companies')
                    .listen('.company.delta', function(e) {
                        handleCompanyDeltaSync(e);
                    });
            } catch (e) {
                console.warn('[SA_COMPANY_ECHO] Echo private channel subscription notice:', e);
            }
        }

        // 4. Central Incoming Delta Handler
        function handleCompanyDeltaSync(data) {
            if (!data || !data.company_id) return;

            // Invalidate local RAM cache to ensure fresh state on next page change or filter
            SessionRAMCache.invalidate();

            applyCompanyDeltaPatch(data);
        }

        // 5. Zero-Latency DOM Delta Patcher
        function applyCompanyDeltaPatch(data) {
            const companyId = parseInt(data.company_id, 10);
            const action = data.action;
            const $row = $(`tr.sa-company-row[data-company-id="${companyId}"]`);

            if (action === 'verified' || action === 'unverified') {
                const isVerified = action === 'verified';

                if ($row.length > 0) {
                    // Update verify icon badge
                    const $badgeContainer = $row.find('.company-verify-badge-container');
                    if ($badgeContainer.length > 0) {
                        if (isVerified) {
                            $badgeContainer.html('<i class="bi bi-patch-check-fill text-primary fs-6 verify-badge-icon" title="Verified Organization" data-bs-toggle="tooltip"></i>');
                        } else {
                            $badgeContainer.html('<i class="bi bi-hourglass-split text-warning fs-6 verify-badge-icon" title="Pending Verification" data-bs-toggle="tooltip"></i>');
                        }
                    }

                    // Update dropdown toggle action
                    const $toggleBtn = $row.find('.toggle-account-verify');
                    if ($toggleBtn.length > 0) {
                        if (isVerified) {
                            $toggleBtn.html('<i class="bi bi-x-circle text-danger fs-6"></i> Mark Unverified');
                        } else {
                            $toggleBtn.html('<i class="bi bi-check-circle text-success fs-6"></i> Verify Company');
                        }
                    }

                    // Live row update highlight
                    $row.addClass('sa-row-live-updated');
                    setTimeout(() => $row.removeClass('sa-row-live-updated'), 1500);

                    // Re-initialize Bootstrap tooltips if available
                    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                        $row.find('[data-bs-toggle="tooltip"]').each(function() {
                            new bootstrap.Tooltip(this);
                        });
                    }
                }
            } else if (action === 'deleted') {
                if ($row.length > 0) {
                    $row.css('transition', 'all 0.3s ease-out')
                        .css('opacity', '0')
                        .css('transform', 'translateX(20px)');
                    setTimeout(() => {
                        $row.remove();

                        // Decrement KPI total companies count if visible
                        const $totalCount = $('#kpiTotalCompaniesCount');
                        if ($totalCount.length > 0) {
                            const currentTotal = parseInt($totalCount.text().replace(/,/g, ''), 10) || 0;
                            if (currentTotal > 0) {
                                $totalCount.text(new Intl.NumberFormat().format(currentTotal - 1));
                            }
                        }

                        // If no visible rows remain, re-fetch active page
                        if ($('tr.sa-company-row').length === 0) {
                            fetchCompanyAjax(currentPage, { bypassCache: true });
                        }
                    }, 300);
                } else {
                    const $totalCount = $('#kpiTotalCompaniesCount');
                    if ($totalCount.length > 0) {
                        const currentTotal = parseInt($totalCount.text().replace(/,/g, ''), 10) || 0;
                        if (currentTotal > 0) {
                            $totalCount.text(new Intl.NumberFormat().format(currentTotal - 1));
                        }
                    }
                }
            } else if (action === 'created' || action === 'package_updated' || action === 'updated') {
                // Non-blocking silent background revalidation without screen flicker
                fetchCompanyAjax(currentPage, { silentBackgroundRevalidate: true });
            }
        }

        // Toggle Account Verification
        $(document).on('click', '.toggle-account-verify', function(e) {
            e.preventDefault();
            const id = $(this).data('row-id');
            const url = "{{ route('superadmin.company.toggleAccountVerify', ':id') }}".replace(':id', id);
            const token = "{{ csrf_token() }}";
            const idempotencyKey = 'comp_ver_' + id + '_' + Date.now();

            $.ajax({
                type: 'POST',
                url: url,
                headers: { 'X-Idempotency-Key': idempotencyKey },
                data: { _token: token, idempotency_key: idempotencyKey },
                success: function(response) {
                    if (response.status === 'success') {
                        SessionRAMCache.invalidate();
                        const newAction = parseInt(response.accountVerify, 10) === 1 ? 'verified' : 'unverified';
                        const deltaPayload = {
                            type: 'COMPANY_DELTA',
                            company_id: id,
                            action: newAction,
                            delta: { accountVerify: response.accountVerify }
                        };
                        broadcastLocalCompanyMutation(deltaPayload);
                        applyCompanyDeltaPatch(deltaPayload);
                    }
                },
                error: function() {
                    Swal.fire("Error", "Failed to update company verification status.", "error");
                }
            });
        });

        // Login As Company
        $(document).on('click', '.btn-login-company', function(e) {
            e.preventDefault();
            const id = $(this).data('id');
            const url = "{{ route('superadmin.company.loginAsCompany', ':id') }}".replace(':id', id);
            const token = "{{ csrf_token() }}";

            Swal.fire({
                title: "Login as Company?",
                text: "You will be logged in as an administrator of this company.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, login"
            }).then((result) => {
                if (result.isConfirmed || result.value) {
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: { _token: token },
                        success: function(response) {
                            window.location.href = "{{ route('admin.dashboard') }}";
                        },
                        error: function() {
                            Swal.fire("Error", "Unable to log in as company admin.", "error");
                        }
                    });
                }
            });
        });
    });
</script>

@endpush
