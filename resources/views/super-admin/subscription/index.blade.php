@extends('layouts.superadmin_layout')

@section('header_title')
<div class="subscription-header-title d-flex flex-column justify-content-center my-0">
    <h1 class="d-flex align-items-center text-gray-900 fw-bold my-0">Subscription Management</h1>
    <span class="header-subtitle">Manage company packages, coin balances and subscription activity.</span>
</div>
@endsection

@push('head-script')
<style>
    /* Super Admin Subscription Isolated Header & Top Space Management */
    .subscription-header-title h1 {
        font-family: 'Poppins', sans-serif !important;
        font-size: 1.35rem !important;
        font-weight: 600 !important;
        color: #181c32 !important;
        line-height: 1.2 !important;
        margin: 0 !important;
    }
    .subscription-header-title .header-subtitle {
        font-size: 0.8rem !important;
        font-weight: 500 !important;
        color: #7e8299 !important;
        line-height: 1.2 !important;
        margin-top: 3px !important;
    }
    @media (max-width: 991.98px) {
        .subscription-header-title h1 {
            font-size: 1.1rem !important;
        }
        .subscription-header-title .header-subtitle {
            display: none !important;
        }
    }

    #kt_header_search {
        display: none !important;
    }

    #kt_header {
        border-bottom: 1px solid #eff2f5 !important;
    }

    @media (min-width: 992px) {
        .superadmin-subscription-page {
            margin-top: -48px;
        }
    }

    @media (max-width: 991.98px) {
        .superadmin-subscription-page {
            margin-top: -38px;
        }
    }

    .superadmin-subscription-page #kt_content_container {
        padding-top: 0.5rem !important;
    }

    /* Enterprise Typography & Layout Scoped to Subscription Page */
    .superadmin-subscription-page .kpi-card {
        background: #ffffff;
        border: 1px solid #f1f1f4;
        border-radius: 12px;
        padding: 1.25rem 1.25rem 1rem 1.25rem;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
        min-width: 0;
    }

    .superadmin-subscription-page .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.05);
        border-color: #e4e6ef;
    }

    .superadmin-subscription-page .kpi-card.active-kpi {
        border-color: #0095e8 !important;
        box-shadow: 0 0 0 2px rgba(0, 149, 232, 0.15) !important;
    }

    .superadmin-subscription-page .kpi-icon-wrap {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .superadmin-subscription-page .kpi-accent-bar {
        height: 5px;
        border-radius: 10px;
        margin-top: 1rem;
        width: 100%;
        max-width: 90px;
    }

    /* Segmented Tab Pill Buttons */
    .superadmin-subscription-page .package-tab-btn {
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        background: #ffffff;
        color: #5e6278;
    }

    .superadmin-subscription-page .package-tab-btn.active {
        background: #0095e8 !important;
        color: #ffffff !important;
        box-shadow: 0 3px 10px rgba(0, 149, 232, 0.25);
    }

    .superadmin-subscription-page .package-tab-btn:not(.active) {
        border-color: #e4e6ef;
        color: #7e8299;
    }

    .superadmin-subscription-page .package-tab-btn:not(.active):hover {
        background: #f9f9f9;
        color: #3f4254;
    }

    /* Table Toolbar Controls */
    .superadmin-subscription-page .filter-search-box {
        position: relative;
        min-width: 280px;
        max-width: 380px;
    }

    .superadmin-subscription-page .filter-search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #a1a5b7;
        font-size: 0.9rem;
    }

    .superadmin-subscription-page .filter-search-input {
        padding-left: 2.5rem !important;
        border-radius: 8px !important;
        border: 1px solid #e4e6ef !important;
        background-color: #ffffff !important;
        height: 42px;
        font-size: 0.9rem;
    }

    .superadmin-subscription-page .filter-search-input:focus {
        border-color: #0095e8 !important;
        box-shadow: 0 0 0 2px rgba(0, 149, 232, 0.12) !important;
    }

    .superadmin-subscription-page .toolbar-select {
        height: 42px;
        border-radius: 8px;
        border: 1px solid #e4e6ef;
        background-color: #ffffff;
        font-size: 0.875rem;
        font-weight: 500;
        color: #4b5675;
        padding-left: 0.85rem;
        padding-right: 2rem;
    }

    .superadmin-subscription-page .toolbar-select:focus {
        border-color: #0095e8;
    }

    .superadmin-subscription-page .btn-reset-filters {
        height: 42px;
        border-radius: 8px;
        border: 1px solid #e4e6ef;
        background: #ffffff;
        color: #4b5675;
        font-weight: 600;
        font-size: 0.875rem;
        padding: 0 1rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .superadmin-subscription-page .btn-reset-filters:hover {
        background: #f9f9f9;
        color: #181c32;
    }

    /* Table Styling & Screen Responsiveness */
    .superadmin-subscription-page .subscription-table {
        margin-bottom: 0;
        vertical-align: middle;
        width: 100%;
    }

    .superadmin-subscription-page .subscription-table th {
        font-size: 0.8rem;
        font-weight: 600;
        color: #7e8299;
        text-transform: capitalize;
        border-bottom: 1px solid #eff2f5;
        padding-top: 1rem;
        padding-bottom: 1rem;
        white-space: nowrap;
    }

    .superadmin-subscription-page .subscription-table td {
        font-size: 0.875rem;
        padding-top: 0.85rem;
        padding-bottom: 0.85rem;
        vertical-align: middle;
    }

    .superadmin-subscription-page .sortable-th {
        cursor: pointer;
        user-select: none;
    }

    .superadmin-subscription-page .sortable-th:hover {
        color: #0095e8;
    }

    /* Standardized Company Logo and Avatar Display */
    .superadmin-subscription-page .company-logo {
        width: 48px !important;
        height: 48px !important;
        min-width: 48px !important;
        min-height: 48px !important;
        max-width: 48px !important;
        max-height: 48px !important;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid #f1f1f4;
        border-radius: 8px;
        padding: 3px;
        box-sizing: border-box;
    }

    .superadmin-subscription-page .company-logo .company-logo-img,
    .superadmin-subscription-page .company-logo img {
        width: 100% !important;
        height: 100% !important;
        max-width: 100% !important;
        max-height: 100% !important;
        object-fit: contain !important;
        object-position: center !important;
        display: block;
    }

    .superadmin-subscription-page .company-avatar,
    .superadmin-subscription-page .company-avatar.symbol {
        width: 48px !important;
        height: 48px !important;
        min-width: 48px !important;
        min-height: 48px !important;
        max-width: 48px !important;
        max-height: 48px !important;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .superadmin-subscription-page .company-avatar .symbol-label {
        width: 48px !important;
        height: 48px !important;
        min-width: 48px !important;
        min-height: 48px !important;
        max-width: 48px !important;
        max-height: 48px !important;
        font-size: 1.1rem;
        font-weight: 700;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Subtle loading overlay */
    .superadmin-subscription-page .table-loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        backdrop-filter: blur(1px);
    }

    /* Drawer width overrides — 15.6-inch Screen & Viewport Optimization */
    #kt_activitiesClient, #kt_activitiesWallet {
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
    body:has(#kt_activitiesWallet.drawer-on) {
        overflow: hidden !important;
    }

    #kt_activitiesClient, #kt_activitiesWallet {
        width: 80% !important;
        max-width: 80vw !important;
    }

    @media only screen and (max-width: 767.98px) {
        #kt_activitiesClient, #kt_activitiesWallet {
            width: 100% !important;
            max-width: 100vw !important;
        }
    }

    /* Scoped Drawer Header Standards (Matching Consultancy) */
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

    /* Clean Pagination Styling & Isolation */
    .superadmin-subscription-page #paginationsubscription .pagination-info {
        display: none !important;
    }

    .superadmin-subscription-page #paginationsubscription .pagination-wrapper {
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
        justify-content: flex-end !important;
        width: auto !important;
        margin: 0 !important;
    }

    .superadmin-subscription-page #paginationsubscription .custom-pagination {
        display: flex !important;
        align-items: center !important;
        gap: 5px !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        padding-left: 0 !important;
        list-style: none !important;
    }

    .superadmin-subscription-page #paginationsubscription .custom-pagination .page-link {
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

    .superadmin-subscription-page #paginationsubscription .custom-pagination .page-link:hover:not(.previous_disabled):not(.next_disabled):not(.active_disabled) {
        background-color: #f1f1f4 !important;
        color: #0095e8 !important;
        border-color: #d8d8e0 !important;
    }

    .superadmin-subscription-page #paginationsubscription .custom-pagination .page-link.active {
        background-color: #0095e8 !important;
        color: #ffffff !important;
        border-color: #0095e8 !important;
        cursor: default !important;
    }

    .superadmin-subscription-page #paginationsubscription .custom-pagination .page-link.previous_disabled,
    .superadmin-subscription-page #paginationsubscription .custom-pagination .page-link.next_disabled {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
        background-color: #f9f9f9 !important;
        border-color: #eff2f5 !important;
        color: #a1a5b7 !important;
    }

    .superadmin-subscription-page #paginationsubscription .custom-pagination .dots {
        background: transparent !important;
        border: none !important;
        cursor: default !important;
        color: #a1a5b7 !important;
    }
</style>
@endpush

@section('content')
<div class="superadmin-subscription-page">
<div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-4 pt-2 pb-4">

        {{-- Hidden State Registers --}}
        <input type="hidden" name="mstatus_id" id="mstatus_id" value="all" />
        <input type="hidden" name="pck_id" id="pck_id" value="1" />
        <input type="hidden" name="sort_col" id="sort_col" value="id" />
        <input type="hidden" name="sort_dir" id="sort_dir" value="desc" />

        {{-- Six Summary KPI Cards Row --}}
        <div class="row g-4 mb-6">
            {{-- Card 1: All Companies --}}
            <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                <div class="kpi-card active-kpi application_sttus_nav" rel="all" id="kpi_all">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon-wrap" style="background: #e8f4fc; color: #0095e8;">
                            <i class="fa-solid fa-building fs-3"></i>
                        </div>
                        <div>
                            <div class="fs-2x fw-bolder text-gray-900 lh-1" id="totalcnt">{{ $counts->all_count }}</div>
                            <div class="fs-7 fw-semibold text-gray-600 mt-1">All Companies</div>
                        </div>
                    </div>
                    <div class="kpi-accent-bar" style="background: #0095e8;"></div>
                </div>
            </div>

            {{-- Card 2: Free Plans --}}
            <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                <div class="kpi-card application_sttus_nav" rel="1" id="kpi_1">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon-wrap" style="background: #e8fff3; color: #50cd89;">
                            <i class="fa-solid fa-users fs-3"></i>
                        </div>
                        <div>
                            <div class="fs-2x fw-bolder text-gray-900 lh-1" id="bg_1">{{ $counts->free_count }}</div>
                            <div class="fs-7 fw-semibold text-gray-600 mt-1">Free Plans</div>
                        </div>
                    </div>
                    <div class="kpi-accent-bar" style="background: #50cd89;"></div>
                </div>
            </div>

            {{-- Card 3: Paid Plans --}}
            <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                <div class="kpi-card application_sttus_nav" rel="2" id="kpi_2">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon-wrap" style="background: #f8f5ff; color: #7239ea;">
                            <i class="fa-solid fa-crown fs-3"></i>
                        </div>
                        <div>
                            <div class="fs-2x fw-bolder text-gray-900 lh-1" id="bg_2">{{ $counts->paid_count }}</div>
                            <div class="fs-7 fw-semibold text-gray-600 mt-1">Paid Plans</div>
                        </div>
                    </div>
                    <div class="kpi-accent-bar" style="background: #7239ea;"></div>
                </div>
            </div>

            {{-- Card 4: Expiring in 30 Days --}}
            <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                <div class="kpi-card application_sttus_nav" rel="3" id="kpi_3">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon-wrap" style="background: #fff8dd; color: #ffab00;">
                            <i class="fa-regular fa-clock fs-3"></i>
                        </div>
                        <div>
                            <div class="fs-2x fw-bolder text-gray-900 lh-1" id="bg_3">{{ $counts->expiry_in_30_days_count }}</div>
                            <div class="fs-7 fw-semibold text-gray-600 mt-1">Expiring in 30 Days</div>
                        </div>
                    </div>
                    <div class="kpi-accent-bar" style="background: #ffab00;"></div>
                </div>
            </div>

            {{-- Card 5: Low Coins (100) --}}
            <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                <div class="kpi-card application_sttus_nav" rel="4" id="kpi_4">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon-wrap" style="background: #fff5f8; color: #f1416c;">
                            <i class="fa-solid fa-coins fs-3"></i>
                        </div>
                        <div>
                            <div class="fs-2x fw-bolder text-gray-900 lh-1" id="bg_4">{{ $counts->less_coins_count }}</div>
                            <div class="fs-7 fw-semibold text-gray-600 mt-1">Low Coins (100)</div>
                        </div>
                    </div>
                    <div class="kpi-accent-bar" style="background: #f1416c;"></div>
                </div>
            </div>

            {{-- Card 6: Inactive (7+ Days) --}}
            <div class="col-xl-2 col-lg-4 col-md-4 col-sm-6">
                <div class="kpi-card application_sttus_nav" rel="5" id="kpi_5">
                    <div class="d-flex align-items-center">
                        <div class="kpi-icon-wrap" style="background: #f5f8fa; color: #7e8299;">
                            <i class="fa-solid fa-user fs-3"></i>
                        </div>
                        <div>
                            <div class="fs-2x fw-bolder text-gray-900 lh-1" id="bg_5">{{ $counts->last_active_7_days_count }}</div>
                            <div class="fs-7 fw-semibold text-gray-600 mt-1">Inactive (7+ Days)</div>
                        </div>
                    </div>
                    <div class="kpi-accent-bar" style="background: #7e8299;"></div>
                </div>
            </div>
        </div>

        {{-- Operational vs Sales Package Segmented Switcher --}}
        <div class="d-flex align-items-center mb-5 gap-2">
            <button type="button" class="package-tab-btn pck_select active" data-kt-plan="1">
                Operational Package
            </button>
            <button type="button" class="package-tab-btn pck_select" data-kt-plan="2">
                Sales Package
            </button>
        </div>

        {{-- Main Table Container Card --}}
        <div class="card card-flush shadow-sm border-0 mb-6" style="border-radius: 12px;">
            
            {{-- Toolbar Card Header --}}
            <div class="card-header pt-5 pb-5 border-0 d-flex flex-wrap align-items-center justify-content-between gap-3">
                
                {{-- Search Box --}}
                <div class="filter-search-box flex-grow-1">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" 
                           id="searchCompany" 
                           class="form-control form-control-solid filter-search-input" 
                           placeholder="Search by company name, location..." />
                </div>

                {{-- Filter Dropdowns & Actions --}}
                <div class="d-flex flex-wrap align-items-center gap-2">
                    {{-- Location Dropdown --}}
                    <div class="position-relative">
                        <select id="filterLocation" class="toolbar-select">
                            <option value="">All Locations</option>
                            <option value="Noida">Noida</option>
                            <option value="Gurgaon">Gurgaon / Gurugram</option>
                            <option value="Delhi">Delhi / New Delhi</option>
                            <option value="Hyderabad">Hyderabad</option>
                            <option value="Mumbai">Mumbai</option>
                        </select>
                    </div>

                    {{-- Package Dropdown --}}
                    <div class="position-relative">
                        <select id="filterPackage" class="toolbar-select">
                            <option value="">All Packages</option>
                            <option value="Free">Free</option>
                            <option value="Silver">Silver</option>
                            <option value="Gold">Gold</option>
                            <option value="Platinum">Platinum</option>
                            <option value="Custom">Custom Package</option>
                        </select>
                    </div>

                    {{-- Status Dropdown --}}
                    <div class="position-relative">
                        <select id="filterStatus" class="toolbar-select">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    {{-- Reset Button --}}
                    <button type="button" id="btnResetFilters" class="btn-reset-filters">
                        <i class="fa-solid fa-arrow-rotate-right fs-7"></i> Reset
                    </button>

                    {{-- Add Company Primary Button --}}
                    <a href="{{ route('superadmin.company.create') }}" class="btn btn-primary fw-bold px-4 py-2 d-inline-flex align-items-center gap-2" style="height: 42px; border-radius: 8px;">
                        <i class="fa-solid fa-plus fs-7"></i> Add Company
                    </a>
                </div>
            </div>

            {{-- Table Card Body --}}
            <div class="card-body py-0 position-relative">
                <div class="table-loading-overlay" id="subscriptionLoading" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table subscription-table align-middle table-row-dashed fs-6 gy-3" id="subscriptionTable">
                        <thead>
                            <tr class="text-start text-gray-500 fw-bold fs-7 text-uppercase gs-0">
                                <th class="min-w-220px ps-4">Company Name</th>
                                <th class="min-w-160px">Location</th>
                                <th class="min-w-140px sortable-th" data-sort="created_at">
                                    Date <i class="fa-solid fa-sort ms-1 fs-8 text-muted"></i>
                                </th>
                                <th class="min-w-160px">Operational Package</th>
                                <th class="min-w-110px sortable-th" data-sort="op_coins">
                                    Op. Coins <i class="fa-solid fa-sort ms-1 fs-8 text-muted"></i>
                                </th>
                                <th class="min-w-140px">Sales</th>
                                <th class="min-w-110px sortable-th" data-sort="sales_coins">
                                    Sales Coins <i class="fa-solid fa-sort ms-1 fs-8 text-muted"></i>
                                </th>
                                <th class="min-w-150px sortable-th" data-sort="last_login">
                                    Last Active <i class="fa-solid fa-sort ms-1 fs-8 text-muted"></i>
                                </th>
                                <th class="min-w-110px">Status</th>
                                <th class="min-w-80px text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="subscriptionList" class="fw-semibold text-gray-600">
                            {{-- Dynamically populated via AJAX --}}
                        </tbody>
                    </table>
                </div>

                {{-- Table Footer Pagination --}}
                <div class="d-flex flex-stack flex-wrap pt-4 pb-4 align-items-center justify-content-between border-top border-gray-200">
                    <div class="fs-7 fw-semibold text-gray-700" id="subscriptionSummary">
                        Showing 0 to 0 of 0 records.
                    </div>

                    <div id="paginationsubscription" class="d-flex align-items-center"></div>
                </div>

            </div>
        </div>

    </div>
</div>

{{-- Preserved Drawers --}}
<div id="kt_activitiesClient_toggle" style="display: none;"></div>
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

<div id="kt_activitiesWallet_toggle" style="display: none;"></div>
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
    <div class="card-body position-relative px-0 py-0 flex-grow-1 overflow-auto" id="kt_activitiesWallet_body"> &nbsp; </div>
  </div>
</div>
</div>

@endsection

@push('footer-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script>
(function($) {
    'use strict';

    /**
     * Super Admin Subscription Page Isolation Controller
     * Scoped exclusively to .superadmin-subscription-page root container.
     * Prevents global handler pollution, eliminates checkbox overhead,
     * and guarantees 0% cross-module regression.
     */
    const rootSelector = '.superadmin-subscription-page';

    function initializeSubscriptionPage() {
        const $root = $(rootSelector);

        if (!$root.length) {
            return;
        }

        // Active in-flight XHR tracker
        let activeFetchXhr = null;
        let activeKpiXhr = null;
        let searchDebounceTimer = null;
        let lastFetchTimestamp = Date.now();

        // Structured Client State
        const SubscriptionState = {
            currentPage: 1,
            pageSize: 10,
            packageTab: 1, // 1: Operational, 2: Sales
            filterStatus: 'all', // 'all', 1, 2, 3, 4, 5
            searchQuery: '',
            locationVal: '',
            packageVal: '',
            statusVal: '',
            sortCol: 'id',
            sortDir: 'desc'
        };

        // Session RAM Cache (Non-authoritative static lookups & KPI caching with short TTL)
        const SessionRAMCache = {
            kpiCounts: new Map(), // key: pck_id -> { data, timestamp }
            kpiTtlMs: 30000, // 30 seconds TTL

            getKpi: function(pckId) {
                const cached = this.kpiCounts.get(String(pckId));
                if (cached && (Date.now() - cached.timestamp < this.kpiTtlMs)) {
                    return cached.data;
                }
                return null;
            },

            setKpi: function(pckId, data) {
                this.kpiCounts.set(String(pckId), {
                    data: data,
                    timestamp: Date.now()
                });
            },

            invalidateKpi: function() {
                this.kpiCounts.clear();
            }
        };

        // Abort In-Flight Read Request to prevent race conditions
        function abortPendingRequests() {
            if (activeFetchXhr && activeFetchXhr.readyState !== 4) {
                activeFetchXhr.abort();
                activeFetchXhr = null;
            }
        }

        // Authoritative Data Fetch via Elasticsearch
        function fetchSubscription(page = 1) {
            SubscriptionState.currentPage = page;

            // Abort previous in-flight table request
            abortPendingRequests();

            $root.find('#subscriptionLoading').show();

            // Read current state values scoped to root
            SubscriptionState.packageTab = $root.find('#pck_id').val();
            SubscriptionState.filterStatus = $root.find('#mstatus_id').val();
            SubscriptionState.searchQuery = $root.find('#searchCompany').val().trim();
            SubscriptionState.locationVal = $root.find('#filterLocation').val();
            SubscriptionState.packageVal = $root.find('#filterPackage').val();
            SubscriptionState.statusVal = $root.find('#filterStatus').val();
            SubscriptionState.sortCol = $root.find('#sort_col').val();
            SubscriptionState.sortDir = $root.find('#sort_dir').val();
            SubscriptionState.pageSize = 10;

            activeFetchXhr = $.ajax({
                url: "{{ route('superadmin.company.allSubscriptionDataAjax') }}",
                type: 'GET',
                data: {
                    page: SubscriptionState.currentPage,
                    size: SubscriptionState.pageSize,
                    pck_id: SubscriptionState.packageTab,
                    mstatus_id: SubscriptionState.filterStatus,
                    q: SubscriptionState.searchQuery,
                    location: SubscriptionState.locationVal,
                    package: SubscriptionState.packageVal,
                    status: SubscriptionState.statusVal,
                    sort: SubscriptionState.sortCol,
                    order: SubscriptionState.sortDir
                },
                success: function(response) {
                    lastFetchTimestamp = Date.now();
                    $root.find('#subscriptionLoading').hide();
                    $root.find('#subscriptionList').html(response.html);
                    $root.find('#paginationsubscription').html(response.pagination);

                    // Update footer summary text
                    const total = response.total || 0;
                    const size = SubscriptionState.pageSize;
                    const from = total === 0 ? 0 : (SubscriptionState.currentPage - 1) * size + 1;
                    const to = Math.min(SubscriptionState.currentPage * size, total);
                    $root.find('#subscriptionSummary').text(`Showing ${from} to ${to} of ${total} records.`);
                },
                error: function(xhr, status) {
                    // Do not display error if request was intentionally aborted
                    if (status === 'abort') return;
                    $root.find('#subscriptionLoading').hide();
                    $root.find('#subscriptionList').html('<tr><td colspan="10" class="text-center py-5 text-danger">Failed to load subscriptions. Please check network connection and try again.</td></tr>');
                },
                complete: function() {
                    activeFetchXhr = null;
                }
            });
        }

        // Refresh KPI Counts with Session RAM Caching
        function refreshKpiCounts(pckId, forceRefresh = false) {
            if (!forceRefresh) {
                const cachedCounts = SessionRAMCache.getKpi(pckId);
                if (cachedCounts) {
                    applyKpiCounts(cachedCounts);
                    return;
                }
            }

            if (activeKpiXhr && activeKpiXhr.readyState !== 4) {
                activeKpiXhr.abort();
            }

            const url = "{{ route('superadmin.company.countaccountviewmore', ':id') }}".replace(':id', pckId);
            activeKpiXhr = $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response && response.counts) {
                        SessionRAMCache.setKpi(pckId, response.counts);
                        applyKpiCounts(response.counts);
                    }
                },
                complete: function() {
                    activeKpiXhr = null;
                }
            });
        }

        function applyKpiCounts(counts) {
            $root.find('#totalcnt').text(counts.all_count || 0);
            $root.find('#bg_1').text(counts.free_count || 0);
            $root.find('#bg_2').text(counts.paid_count || 0);
            $root.find('#bg_3').text(counts.expiry_in_30_days_count || 0);
            $root.find('#bg_4').text(counts.less_coins_count || 0);
            $root.find('#bg_5').text(counts.last_active_7_days_count || 0);
        }

        // Initial load
        fetchSubscription(1);

        // Package Tab Switcher (Operational vs Sales)
        $root.on('click', '.pck_select', function() {
            const planId = $(this).attr('data-kt-plan');
            $root.find('.pck_select').removeClass('active');
            $(this).addClass('active');

            $root.find('#pck_id').val(planId);
            fetchSubscription(1);
            refreshKpiCounts(planId);
        });

        // KPI Card Selection
        $root.on('click', '.application_sttus_nav', function() {
            const rel = $(this).attr('rel');
            $root.find('.kpi-card').removeClass('active-kpi');
            $(this).addClass('active-kpi');

            $root.find('#mstatus_id').val(rel);

            // Update browser history state
            const targetUrl = "{{ url('/super-admin/subscription/') }}/" + rel;
            window.history.pushState({ page: rel }, 'Subscription', targetUrl);

            fetchSubscription(1);
        });

        // Search Input with 300ms Debounce
        $root.find('#searchCompany').on('input', function() {
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(function() {
                fetchSubscription(1);
            }, 300);
        });

        // Filter Dropdowns
        $root.find('#filterLocation, #filterPackage, #filterStatus').on('change', function() {
            fetchSubscription(1);
        });

        // Reset Filters Button
        $root.find('#btnResetFilters').on('click', function() {
            $root.find('#searchCompany').val('');
            $root.find('#filterLocation').val('');
            $root.find('#filterPackage').val('');
            $root.find('#filterStatus').val('');
            $root.find('#sort_col').val('id');
            $root.find('#sort_dir').val('desc');

            // Reset KPI card to All
            $root.find('#mstatus_id').val('all');
            $root.find('.kpi-card').removeClass('active-kpi');
            $root.find('#kpi_all').addClass('active-kpi');

            fetchSubscription(1);
        });

        // Sortable Columns Click
        $root.on('click', '.sortable-th', function() {
            const col = $(this).data('sort');
            const currentCol = $root.find('#sort_col').val();
            let currentDir = $root.find('#sort_dir').val();

            if (currentCol === col) {
                currentDir = (currentDir === 'asc') ? 'desc' : 'asc';
            } else {
                currentDir = 'desc';
            }

            $root.find('#sort_col').val(col);
            $root.find('#sort_dir').val(currentDir);

            // Update sort icons
            $root.find('.sortable-th i').removeClass('fa-sort-up fa-sort-down').addClass('fa-sort text-muted');
            const iconClass = (currentDir === 'asc') ? 'fa-sort-up text-primary' : 'fa-sort-down text-primary';
            $(this).find('i').removeClass('fa-sort text-muted').addClass(iconClass);

            fetchSubscription(1);
        });

        // Pagination Click
        $root.on('click', '#paginationsubscription .page-link', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (!page || $(this).hasClass('disabled') || $(this).hasClass('active')) return;
            fetchSubscription(page);
        });

        // Company Details Drawer Trigger
        $root.on('click', '.show-detail', function() {
            $root.find('#kt_activitiesClient_toggle').trigger('click');
            const id = $(this).data('row-id');
            const url = "{{ route('superadmin.company.viewmore', ':id') }}".replace(':id', id);

            $root.find('#kt_activitiesClient_body').html('<div class="d-flex justify-content-center py-10"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    $root.find('#kt_activitiesClient_body').html(response.view);
                },
                error: function() {
                    $root.find('#kt_activitiesClient_body').html('<div class="p-5 text-danger">Failed to load company details.</div>');
                }
            });
        });

        // Wallet Drawer Trigger
        $root.on('click', '.action-edit-wallet', function(e) {
            e.preventDefault();
            const id = $(this).data('row-id');
            const url = "{{ route('superadmin.custompackages.editWallet', ':id') }}".replace(':id', id);

            $root.find('#kt_activitiesWallet_toggle').trigger('click');
            $root.find('#kt_activitiesWallet_body').html('<div class="d-flex justify-content-center py-10"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>');

            $.ajax({
                type: 'GET',
                url: url,
                success: function(response) {
                    if (response.status === 'success' || response.view) {
                        $root.find('#kt_activitiesWallet_body').html(response.view);
                    } else {
                        $root.find('#kt_activitiesWallet_body').html('<div class="p-5 text-muted">' + (response.message || 'No wallet data available.') + '</div>');
                    }
                },
                error: function() {
                    $root.find('#kt_activitiesWallet_body').html('<div class="p-5 text-danger">Failed to load company wallet editor.</div>');
                }
            });
        });

        // Action: Optimistic Toggle Company Active Status
        $root.on('click', '.action-toggle-active', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const id = $btn.data('row-id');
            const token = "{{ csrf_token() }}";
            const url = "{{ route('superadmin.company.checkActive') }}";

            const $row = $btn.closest('tr');
            const $badgeCell = $row.find('.status-cell');
            const wasActive = $btn.find('i').hasClass('text-danger'); // Red power icon indicates Deactivate action on an Active company

            // Disable button during in-flight mutation to prevent double-clicks
            $btn.addClass('disabled pe-none');

            // Optimistic DOM update
            if (wasActive) {
                $badgeCell.html('<span class="badge badge-light-danger d-inline-flex align-items-center py-1 px-3 fs-7" style="border-radius: 12px; font-weight: 600;"><span class="bullet bullet-dot bg-danger h-6px w-6px me-2"></span> Inactive</span>');
                $btn.html('<i class="fa-solid fa-power-off me-2 text-success fs-7"></i> Activate');
            } else {
                $badgeCell.html('<span class="badge badge-light-success d-inline-flex align-items-center py-1 px-3 fs-7" style="border-radius: 12px; font-weight: 600;"><span class="bullet bullet-dot bg-success h-6px w-6px me-2"></span> Active</span>');
                $btn.html('<i class="fa-solid fa-power-off me-2 text-danger fs-7"></i> Deactivate');
            }

            $.ajax({
                type: 'POST',
                url: url,
                data: { _token: token, id: id },
                success: function(response) {
                    if (response.status === 'success') {
                        const isNowActive = response.pack_status === 'active';
                        if (isNowActive) {
                            $badgeCell.html('<span class="badge badge-light-success d-inline-flex align-items-center py-1 px-3 fs-7" style="border-radius: 12px; font-weight: 600;"><span class="bullet bullet-dot bg-success h-6px w-6px me-2"></span> Active</span>');
                            $btn.html('<i class="fa-solid fa-power-off me-2 text-danger fs-7"></i> Deactivate');
                        } else {
                            $badgeCell.html('<span class="badge badge-light-danger d-inline-flex align-items-center py-1 px-3 fs-7" style="border-radius: 12px; font-weight: 600;"><span class="bullet bullet-dot bg-danger h-6px w-6px me-2"></span> Inactive</span>');
                            $btn.html('<i class="fa-solid fa-power-off me-2 text-success fs-7"></i> Activate');
                        }
                        SessionRAMCache.invalidateKpi();
                        refreshKpiCounts($root.find('#pck_id').val(), true);
                    } else {
                        revertToggle();
                    }
                },
                error: function() {
                    revertToggle();
                    if (typeof swal !== 'undefined') {
                        swal("@lang('app.error')", "Failed to update company status. Please try again.", "error");
                    }
                },
                complete: function() {
                    $btn.removeClass('disabled pe-none');
                }
            });

            function revertToggle() {
                if (wasActive) {
                    $badgeCell.html('<span class="badge badge-light-success d-inline-flex align-items-center py-1 px-3 fs-7" style="border-radius: 12px; font-weight: 600;"><span class="bullet bullet-dot bg-success h-6px w-6px me-2"></span> Active</span>');
                    $btn.html('<i class="fa-solid fa-power-off me-2 text-danger fs-7"></i> Deactivate');
                } else {
                    $badgeCell.html('<span class="badge badge-light-danger d-inline-flex align-items-center py-1 px-3 fs-7" style="border-radius: 12px; font-weight: 600;"><span class="bullet bullet-dot bg-danger h-6px w-6px me-2"></span> Inactive</span>');
                    $btn.html('<i class="fa-solid fa-power-off me-2 text-success fs-7"></i> Activate');
                }
            }
        });

        // Action: SweetAlert Delete Company with DOM Reconciliation
        $root.on('click', '.sa-params', function(e) {
            e.preventDefault();
            const $btn = $(this);
            const id = $btn.data('row-id');
            const $row = $btn.closest('tr');

            swal({
                title: "@lang('errors.areYouSure')",
                text: "@lang('errors.deleteWarning')",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "@lang('app.delete')",
                cancelButtonText: "@lang('app.cancel')",
                closeOnConfirm: true
            }, function(isConfirm) {
                if (isConfirm) {
                    const url = "{{ route('superadmin.company.destroy', ':id') }}".replace(':id', id);
                    const token = "{{ csrf_token() }}";

                    // Optimistic opacity reduction
                    $row.css('opacity', '0.5');

                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: { _token: token, _method: 'DELETE' },
                        success: function(response) {
                            $row.fadeOut(300, function() {
                                $(this).remove();
                                SessionRAMCache.invalidateKpi();
                                fetchSubscription(SubscriptionState.currentPage);
                                refreshKpiCounts($root.find('#pck_id').val(), true);
                            });
                        },
                        error: function() {
                            $row.css('opacity', '1');
                            if (typeof swal !== 'undefined') {
                                swal("@lang('app.error')", "Failed to delete company.", "error");
                            }
                        }
                    });
                }
            });
        });

        // Drawer Close State Reconciliation: Ensures fresh data when returning to table
        $root.on('click', '#kt_activitiesClient_close, #kt_activitiesWallet_close, .drawer-overlay', function() {
            SessionRAMCache.invalidateKpi();
            fetchSubscription(SubscriptionState.currentPage);
            refreshKpiCounts($root.find('#pck_id').val(), true);
        });

        // Metronic KTDrawer event hooks for programmatic or overlay dismissals
        if (typeof KTDrawer !== 'undefined') {
            ['#kt_activitiesClient', '#kt_activitiesWallet'].forEach(function(selector) {
                const el = document.querySelector(selector);
                if (el) {
                    const drawerObj = KTDrawer.getInstance(el);
                    if (drawerObj && typeof drawerObj.on === 'function') {
                        drawerObj.on('kt.drawer.after.hidden', function() {
                            SessionRAMCache.invalidateKpi();
                            fetchSubscription(SubscriptionState.currentPage);
                            refreshKpiCounts($root.find('#pck_id').val(), true);
                        });
                    }
                }
            });
        }

        // Zero Stale Data: Automatic re-validation on tab focus if data is older than 60 seconds
        const visibilityHandler = function() {
            if (document.visibilityState === 'visible') {
                const now = Date.now();
                if (now - lastFetchTimestamp > 60000) {
                    fetchSubscription(SubscriptionState.currentPage);
                    refreshKpiCounts($root.find('#pck_id').val(), true);
                }
            }
        };
        document.addEventListener('visibilitychange', visibilityHandler);

        // Network Reconnection Recovery
        const onlineHandler = function() {
            fetchSubscription(SubscriptionState.currentPage);
            refreshKpiCounts($root.find('#pck_id').val(), true);
        };
        window.addEventListener('online', onlineHandler);

        // Memory cleanup on page unload
        window.addEventListener('beforeunload', function() {
            abortPendingRequests();
            clearTimeout(searchDebounceTimer);
            SessionRAMCache.invalidateKpi();
            document.removeEventListener('visibilitychange', visibilityHandler);
            window.removeEventListener('online', onlineHandler);
        });
    }

    $(document).ready(initializeSubscriptionPage);

})(jQuery);
</script>
@endpush
