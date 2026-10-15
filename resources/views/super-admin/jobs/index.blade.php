@extends('layouts.superadmin_layout')
@php
    use App\Helper\GeneralMethod;
    $currencySymbole = GeneralMethod::currencySymbole();
    $salaryMode = GeneralMethod::salaryMode();
    $activeTab = $currentTab ?? 'pending';
@endphp

@push('head-script')
    <link href="{{ asset('demo/plugins/custom/datatables/datatables.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .company-logo { max-height: 40px; max-width: 100px; object-fit: contain; }
        .kpi-card { transition: transform 0.15s ease, box-shadow 0.15s ease; border-radius: 0.65rem; }
        .kpi-card:hover { transform: translateY(-2px); box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08); }
        .risk-gauge-pill { display: inline-flex; align-items: center; gap: 0.35rem; font-weight: 700; border-radius: 1rem; padding: 0.2rem 0.65rem; }
        .table-custom-class tbody tr { vertical-align: middle; }
        .table-custom-class tbody tr:hover { background-color: #f8fafc; }
        @media only screen and (min-width: 1024px) {
            #kt_activitiesMarketplaceJob { width: 1250px !important; }
        }
        @media only screen and (max-width: 1023px) {
            #kt_activitiesMarketplaceJob { width: 95vw !important; }
        }
    </style>
@endpush

@section('content')

<!--begin::Toolbar-->
<div class="toolbar py-3" id="kt_toolbar">
    <div id="kt_toolbar_container" class="container-fluid d-flex flex-stack flex-wrap gap-2">
        <div class="page-title d-flex align-items-center flex-wrap me-3">
            <h1 class="d-flex align-items-center text-dark fw-bolder fs-3 my-1">
                Marketplace Command Center
                <span class="h-20px border-gray-200 border-start ms-3 mx-2"></span>
                <span class="text-muted fs-7 fw-bold">{{ $status ?? 'All Marketplace Jobs' }}</span>
            </h1>
        </div>

        <!-- Auto-Approval Toggle & Quick Actions -->
        <div class="d-flex align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center bg-white border border-gray-300 rounded px-3 py-2 shadow-sm">
                <span class="fw-bolder text-gray-800 me-2 fs-7">Auto-Approval:</span>
                <div class="form-check form-switch form-check-custom form-check-solid form-check-sm">
                    <input class="form-check-input chkAutoApproval" type="checkbox" id="chkAutoApproval" 
                           value="{{ @$globalSetting->id }}" {{ @$globalSetting->public_market_job_status ? 'checked' : '' }}>
                </div>
                <span class="badge badge-light-{{ @$globalSetting->public_market_job_status ? 'success' : 'secondary' }} ms-2 fs-8 fw-bold" id="autoApprovalStatusBadge">
                    {{ @$globalSetting->public_market_job_status ? 'ACTIVE (Risk < 0.20)' : 'PAUSED (Manual Review)' }}
                </span>
            </div>

            <a href="{{ route('superadmin.company.job-public-market', $activeTab) }}" class="btn btn-sm btn-icon btn-light" title="Refresh Feed">
                <i class="fas fa-sync-alt text-gray-600"></i>
            </a>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-2" id="kt_content">
    <div class="container-fluid px-4" id="kt_content_container">
        
        <!-- ================= TOP KPI DECK (6 Metrics) ================= -->
        <div class="row g-4 mb-5">
            <!-- Total Market Jobs -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card kpi-card bg-white border border-gray-200 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold fs-7">Total Market Jobs</span>
                        <span class="badge badge-light-primary"><i class="fas fa-briefcase text-primary"></i></span>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2x fw-bolder text-gray-900">{{ number_format($stats['total'] ?? 0) }}</span>
                    </div>
                    <span class="text-muted fs-8 mt-1">Across all tenants</span>
                </div>
            </div>

            <!-- Pending Review (Urgent) -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <a href="{{ route('superadmin.company.job-public-market', 'pending') }}" class="card kpi-card bg-white border border-warning shadow-sm p-4 h-100 text-decoration-none">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-warning fw-bold fs-7">Pending Review</span>
                        @if(($stats['pending'] ?? 0) > 0)
                            <span class="bullet bullet-dot bg-warning h-10px w-10px animation-blink"></span>
                        @else
                            <span class="badge badge-light-warning"><i class="fas fa-clock text-warning"></i></span>
                        @endif
                    </div>
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2x fw-bolder text-warning">{{ number_format($stats['pending'] ?? 0) }}</span>
                        @if(($stats['pending'] ?? 0) > 0)
                            <span class="badge badge-light-danger ms-2 fs-8">Requires Action</span>
                        @endif
                    </div>
                    <span class="text-muted fs-8 mt-1">SLA Target < 24 Hours</span>
                </a>
            </div>

            <!-- Live & Approved -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <a href="{{ route('superadmin.company.job-public-market', 'approve') }}" class="card kpi-card bg-white border border-gray-200 shadow-sm p-4 h-100 text-decoration-none">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold fs-7">Live Approved</span>
                        <span class="badge badge-light-success"><i class="fas fa-check-circle text-success"></i></span>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2x fw-bolder text-success">{{ number_format($stats['approved'] ?? 0) }}</span>
                    </div>
                    <span class="text-muted fs-8 mt-1">Active on Marketplace</span>
                </a>
            </div>

            <!-- Bench Sharing Enabled -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <a href="{{ route('superadmin.company.job-public-market', 'bench') }}" class="card kpi-card bg-white border border-gray-200 shadow-sm p-4 h-100 text-decoration-none">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold fs-7">Bench Sharing</span>
                        <span class="badge badge-light-info"><i class="fas fa-users text-info"></i></span>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2x fw-bolder text-info">{{ number_format($stats['bench_enabled'] ?? 0) }}</span>
                    </div>
                    <span class="text-muted fs-8 mt-1">Accepting bench talent</span>
                </a>
            </div>

            <!-- Capacity Limit Reached -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <a href="{{ route('superadmin.company.job-public-market', 'hold') }}" class="card kpi-card bg-white border border-gray-200 shadow-sm p-4 h-100 text-decoration-none">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold fs-7">Vendor Limit</span>
                        <span class="badge badge-light-warning"><i class="fas fa-lock text-warning"></i></span>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2x fw-bolder text-gray-700">{{ number_format($stats['hold'] ?? 0) }}</span>
                    </div>
                    <span class="text-muted fs-8 mt-1">Max claims reached</span>
                </a>
            </div>

            <!-- Agency Claims Total -->
            <div class="col-xl-2 col-md-4 col-sm-6">
                <div class="card kpi-card bg-white border border-gray-200 shadow-sm p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-bold fs-7">Agency Claims</span>
                        <span class="badge badge-light-primary"><i class="fas fa-handshake text-primary"></i></span>
                    </div>
                    <div class="d-flex align-items-baseline">
                        <span class="fs-2x fw-bolder text-primary">{{ number_format($stats['agency_requests'] ?? 0) }}</span>
                    </div>
                    <span class="text-muted fs-8 mt-1">Submitted claim requests</span>
                </div>
            </div>
        </div>

        <!-- ================= MAIN DATA CARD ================= -->
        <div class="card border border-gray-200 shadow-sm">
            
            <!-- Navigation Tabs & Search Toolbar -->
            <div class="card-header border-bottom py-3 px-5 d-flex align-items-center justify-content-between flex-wrap gap-3">
                
                <!-- Quick Filter Tabs -->
                <ul class="nav nav-stretch nav-line-tabs nav-line-tabs-2x border-transparent fs-6 fw-bolder">
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-2 px-3 {{ $activeTab === 'pending' ? 'active' : '' }}" 
                           href="{{ route('superadmin.company.job-public-market', 'pending') }}">
                            <i class="fas fa-clock me-1 text-warning"></i> Pending Review
                            <span class="badge badge-circle badge-sm badge-warning ms-1">{{ $stats['pending'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-2 px-3 {{ $activeTab === 'approve' ? 'active' : '' }}" 
                           href="{{ route('superadmin.company.job-public-market', 'approve') }}">
                            <i class="fas fa-check-circle me-1 text-success"></i> Live Approved
                            <span class="badge badge-circle badge-sm badge-success ms-1">{{ $stats['approved'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-2 px-3 {{ $activeTab === 'bench' ? 'active' : '' }}" 
                           href="{{ route('superadmin.company.job-public-market', 'bench') }}">
                            <i class="fas fa-users me-1 text-info"></i> Bench Sharing Active
                            <span class="badge badge-circle badge-sm badge-info ms-1">{{ $stats['bench_enabled'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-2 px-3 {{ $activeTab === 'hold' ? 'active' : '' }}" 
                           href="{{ route('superadmin.company.job-public-market', 'hold') }}">
                            <i class="fas fa-lock me-1 text-warning"></i> Vendor Limit
                            <span class="badge badge-circle badge-sm badge-light-warning ms-1">{{ $stats['hold'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-2 px-3 {{ $activeTab === 'reject' ? 'active' : '' }}" 
                           href="{{ route('superadmin.company.job-public-market', 'reject') }}">
                            <i class="fas fa-times-circle me-1 text-danger"></i> Rejected
                            <span class="badge badge-circle badge-sm badge-light-danger ms-1">{{ $stats['rejected'] ?? 0 }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-active-primary py-2 px-3 {{ $activeTab === 'all' ? 'active' : '' }}" 
                           href="{{ route('superadmin.company.job-public-market', 'all') }}">
                            All Market Jobs
                        </a>
                    </li>
                </ul>

                <!-- Search & Bulk Action Controls -->
                <div class="d-flex align-items-center gap-2">
                    <!-- Bulk Actions Toolbar (shown on row select) -->
                    <div id="bulkActionsToolbar" class="d-none align-items-center gap-2">
                        <span class="text-muted fs-8 fw-bold"><span id="selectedCount">0</span> selected</span>
                        <button type="button" class="btn btn-sm btn-success fw-bolder btn-bulk-approve">
                            <i class="fas fa-check me-1"></i> Approve Selected
                        </button>
                        <button type="button" class="btn btn-sm btn-light-danger fw-bolder btn-bulk-reject">
                            <i class="fas fa-times me-1"></i> Reject Selected
                        </button>
                    </div>

                    <!-- Search Input -->
                    <form method="GET" action="{{ route('superadmin.company.job-public-market', $activeTab) }}" class="d-flex align-items-center">
                        <div class="position-relative">
                            <i class="fas fa-search position-absolute top-50 translate-middle-y ms-3 text-gray-500 fs-7"></i>
                            <input type="text" class="form-control form-control-sm form-control-solid ps-9 w-200px w-lg-250px" 
                                   name="search" value="{{ request('search') }}" placeholder="Search title, ID, company...">
                        </div>
                        @if(request('search'))
                            <a href="{{ route('superadmin.company.job-public-market', $activeTab) }}" class="btn btn-sm btn-icon btn-light ms-1" title="Clear">
                                <i class="fas fa-times text-gray-500"></i>
                            </a>
                        @endif
                    </form>
                </div>

            </div>

            <!-- Table Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle table-row-dashed fs-7 gy-3 px-5 mb-0 table-custom-class" id="marketplaceMasterTable">
                        <thead>
                            <tr class="text-start text-muted fw-bolder fs-7 text-uppercase gs-0 bg-light">
                                <th class="w-20px ps-4">
                                    <div class="form-check form-check-sm form-check-custom form-check-solid">
                                        <input class="form-check-input" type="checkbox" id="selectAllCheckbox">
                                    </div>
                                </th>
                                <th class="min-w-250px">Job & Tenant Profile</th>
                                <th class="min-w-120px">Channel Mode</th>
                                <th class="min-w-110px">State / Moderation</th>
                                <th class="min-w-110px">Risk Index</th>
                                <th class="min-w-130px">Compensation</th>
                                <th class="min-w-100px">Agency Activity</th>
                                <th class="min-w-90px">Submitted</th>
                                <th class="text-end min-w-120px pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 fw-bold">
                            @if(!empty($newJobs) && $newJobs->count() > 0)
                                @foreach($newJobs as $row)
                                    @php
                                        $riskScore = floatval($row->risk_eval['risk_score'] ?? 0.0);
                                        $flags = $row->risk_eval['flags'] ?? [];
                                        $hasCircumvention = !empty($flags);
                                        
                                        $minCurr = $currencySymbole[@$row->min_salary_currency] ?? '₹';
                                        $salModeText = $salaryMode[@$row->min_sal_mode] ?? ($salaryMode[@$row->max_sal_mode] ?? 'Per Annum');
                                    @endphp
                                    <tr>
                                        <!-- Checkbox -->
                                        <td class="ps-4">
                                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                                <input class="form-check-input row-select-checkbox" type="checkbox" 
                                                       value="{{ $row->id }}" data-job-title="{{ $row->title }}">
                                            </div>
                                        </td>

                                        <!-- Job & Tenant Profile -->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="symbol symbol-40px me-3">
                                                    @if(!empty($row->company_logo))
                                                        <img src="{{ asset('user-uploads/company-logo/' . $row->company_logo) }}" 
                                                             alt="Logo" class="company-logo rounded border" onerror="this.src='{{ asset('assets/images/building.png') }}';">
                                                    @else
                                                        <div class="symbol-label bg-light-primary text-primary fw-bolder fs-6">
                                                            {{ strtoupper(substr($row->company_name ?? 'C', 0, 2)) }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="d-flex flex-column">
                                                    <a href="javascript:void(0);" class="text-dark fw-bolder text-hover-primary fs-6 btn-open-cockpit" 
                                                       data-job-id="{{ $row->id }}" title="Open Moderation Cockpit">
                                                        {{ $row->title }}
                                                    </a>
                                                    <div class="d-flex align-items-center gap-2 mt-1">
                                                        <span class="badge badge-light-secondary fs-8">#JOB-{{ $row->id }}</span>
                                                        <span class="text-muted fs-8">{{ $row->company_name ?? 'Tenant' }}</span>
                                                        @if(!empty($row->clients_name))
                                                            <span class="text-muted fs-8">• Client: <strong class="text-gray-800">{{ $row->clients_name }}</strong></span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Channel Mode -->
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                @if($row->bench_submission === 'enabled')
                                                    <span class="badge badge-light-info fw-bold fs-8">
                                                        <i class="fas fa-users text-info me-1"></i>Bench Sharing
                                                    </span>
                                                @endif
                                                @if($row->claim_capacity === 'open' || $row->public_market_status == 2)
                                                    <span class="badge badge-light-success fw-bold fs-8">
                                                        <i class="fas fa-handshake text-success me-1"></i>Outsource Open
                                                    </span>
                                                @elseif($row->claim_capacity === 'limit_reached' || $row->public_market_status == 4)
                                                    <span class="badge badge-light-warning fw-bold fs-8">Limit Reached</span>
                                                @else
                                                    <span class="badge badge-light-secondary fs-8">Restricted</span>
                                                @endif
                                            </div>
                                        </td>

                                        <!-- State / Moderation -->
                                        <td>
                                            <div class="d-flex flex-column gap-1">
                                                @if(in_array($row->market_mod_state, ['approved', 'auto_approved']) || $row->public_market_status == 2)
                                                    <span class="badge badge-light-success fw-bold">Live Approved</span>
                                                @elseif($row->market_mod_state === 'pending' || $row->public_market_status == 1)
                                                    <span class="badge badge-light-warning fw-bold">Pending Review</span>
                                                @elseif($row->market_mod_state === 'rejected' || $row->public_market_status == 3)
                                                    <span class="badge badge-light-danger fw-bold">Rejected</span>
                                                @elseif($row->claim_capacity === 'limit_reached' || $row->public_market_status == 4)
                                                    <span class="badge badge-light-warning fw-bold">Capacity Full</span>
                                                @else
                                                    <span class="badge badge-light-secondary fw-bold">Unpublished</span>
                                                @endif

                                                <span class="text-muted fs-8">Pub: {{ $row->market_pub_state ?? ($row->public_market_status == 2 ? 'published' : 'pending') }}</span>
                                            </div>
                                        </td>

                                        <!-- Automated Risk Index -->
                                        <td>
                                            @if($riskScore > 0.50 || $hasCircumvention)
                                                <span class="risk-gauge-pill bg-light-danger text-danger fs-8" 
                                                      data-bs-toggle="tooltip" title="{{ implode(', ', $flags) ?: 'High risk flagged' }}">
                                                    <i class="fas fa-exclamation-triangle text-danger"></i> {{ number_format($riskScore, 2) }} High Risk
                                                </span>
                                            @elseif($riskScore > 0.20)
                                                <span class="risk-gauge-pill bg-light-warning text-warning fs-8" 
                                                      data-bs-toggle="tooltip" title="Requires manual inspection">
                                                    <i class="fas fa-shield-alt text-warning"></i> {{ number_format($riskScore, 2) }} Review
                                                </span>
                                            @else
                                                <span class="risk-gauge-pill bg-light-success text-success fs-8" 
                                                      data-bs-toggle="tooltip" title="Passed anti-circumvention scan">
                                                    <i class="fas fa-check-circle text-success"></i> {{ number_format($riskScore, 2) }} Clean
                                                </span>
                                            @endif
                                        </td>

                                        <!-- Compensation & Openings -->
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-900 fw-bolder fs-7">
                                                    {!! $minCurr !!} {{ number_format($row->min_salary) }} - {{ number_format($row->max_salary) }}
                                                </span>
                                                <span class="text-muted fs-8">{{ $salModeText }} • <strong>{{ $row->total_positions }} Openings</strong></span>
                                            </div>
                                        </td>

                                        <!-- Agency Activity -->
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-light fw-bold fs-7">
                                                    <i class="fas fa-briefcase text-muted me-1"></i> {{ $row->agency_claims_count ?? 0 }} Claims
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Submitted / Age -->
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-gray-800 fs-8">{{ \Carbon\Carbon::parse($row->created_at)->format('d M, Y') }}</span>
                                                <span class="text-muted fs-8">{{ \Carbon\Carbon::parse($row->created_at)->diffForHumans() }}</span>
                                            </div>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-end pe-4">
                                            <div class="d-flex align-items-center justify-content-end gap-1">
                                                <button type="button" class="btn btn-sm btn-icon btn-light-primary btn-open-cockpit" 
                                                        data-job-id="{{ $row->id }}" title="Open 3-Panel Cockpit">
                                                    <i class="fas fa-desktop fs-6"></i>
                                                </button>

                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-icon btn-light-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-v fs-6"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm fs-7">
                                                        <li>
                                                            <a class="dropdown-item py-2 text-success fw-bold btn-table-action" href="javascript:void(0);" 
                                                               data-action="approve_all" data-job-id="{{ $row->id }}">
                                                                <i class="fas fa-check-circle text-success me-2"></i> Approve Outsource & Bench
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item py-2 text-info fw-bold btn-table-action" href="javascript:void(0);" 
                                                               data-action="approve_bench" data-job-id="{{ $row->id }}">
                                                                <i class="fas fa-users text-info me-2"></i> Approve Bench Only
                                                            </a>
                                                        </li>
                                                        <li><hr class="dropdown-divider my-1"></li>
                                                        <li>
                                                            <a class="dropdown-item py-2 text-danger fw-bold btn-table-reject" href="javascript:void(0);" 
                                                               data-job-id="{{ $row->id }}" data-job-title="{{ $row->title }}">
                                                                <i class="fas fa-times-circle text-danger me-2"></i> Reject Job
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="text-center py-10">
                                        <div class="d-flex flex-column align-items-center justify-content-center">
                                            <i class="fas fa-folder-open fs-2x text-muted mb-2"></i>
                                            <span class="text-gray-800 fw-bolder fs-6">No jobs found in this section.</span>
                                            <span class="text-muted fs-7">Try selecting another filter tab or clearing search parameters.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if(!empty($newJobs))
                    <div class="px-5 py-3 border-top d-flex justify-content-between align-items-center flex-wrap">
                        <div class="text-muted fs-7">
                            Showing {{ $newJobs->firstItem() ?? 0 }} to {{ $newJobs->lastItem() ?? 0 }} of {{ $newJobs->total() ?? 0 }} records
                        </div>
                        <div>
                            {{ $newJobs->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
<!--end::Content-->

<!-- ================= 3-PANEL MODERATION COCKPIT DRAWER ================= -->
<div id="kt_activitiesMarketplaceJob_toggle" class="d-none"></div>
<div id="kt_activitiesMarketplaceJob" class="bg-body" data-kt-drawer="true"
    data-kt-drawer-name="activitiesMarketplaceJob" data-kt-drawer-activate="true" data-kt-drawer-overlay="true"
    data-kt-drawer-width="{default:'300px', 'md': '750px', 'lg': '1150px', 'xl': '1350px'}" data-kt-drawer-direction="end"
    data-kt-drawer-toggle="#kt_activitiesMarketplaceJob_toggle"
    data-kt-drawer-close="#kt_activitiesMarketplaceJob_close">
    <div class="card shadow-none rounded-0 w-100 h-100" id="marketplace_job_cockpit_body">
        <!-- Dynamically loaded via AJAX from superadmin.company.marketshow-job-details -->
    </div>
</div>

<!-- ================= QUICK REJECT MODAL ================= -->
<div class="modal fade" id="marketplaceRejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title fw-bolder text-danger"><i class="fas fa-times-circle me-1 text-danger"></i> Reject Marketplace Job</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <input type="hidden" id="modalRejectJobId" value="">
                <div class="mb-3">
                    <label class="form-label fw-bold fs-7">Job Title:</label>
                    <div class="fw-bolder text-gray-900 fs-6" id="modalRejectJobTitle">-</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold fs-7">Rejection Reason Code:</label>
                    <select class="form-select form-select-sm form-select-solid" id="modalRejectReasonCode">
                        <option value="CONTACT_CIRCUMVENTION">Prohibited Contact Details (Email, Phone, WhatsApp)</option>
                        <option value="COMPENSATION_ANOMALY">Unrealistic / Below Minimum Compensation</option>
                        <option value="LOW_JD_QUALITY">Low Quality / Incomplete Job Description</option>
                        <option value="DUPLICATE_POSTING">Duplicate Job Listing</option>
                        <option value="COMMERCIAL_DISPUTE">Commercial Terms Incomplete or Disputed</option>
                        <option value="OTHER">Other Custom Reason</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold fs-7">Moderation Notes / Comments for Recruiter:</label>
                    <textarea class="form-control form-control-solid fs-7" rows="4" id="modalRejectNotes" 
                              placeholder="Explain specific reasons or required changes before this job can be approved on the marketplace..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-sm btn-danger fw-bolder" id="btnConfirmReject">Confirm & Reject</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('footer-script')
<script>
    $(document).ready(function() {
        var token = "{{ csrf_token() }}";
        var showDetailsUrl = "{{ route('superadmin.company.marketshow-job-details') }}";
        var approvalUrl = "{{ route('superadmin.company.job-approval') }}";
        var autoApprovalUrl = "{{ route('superadmin.company.market-auto-approval') }}";

        // ---------- OPEN 3-PANEL COCKPIT DRAWER ----------
        $(document).on('click', '.btn-open-cockpit', function(e) {
            e.preventDefault();
            var jobId = $(this).data('job-id');
            if (!jobId) return;

            $('#marketplace_job_cockpit_body').html(
                '<div class="d-flex flex-column align-items-center justify-content-center h-100 py-20">' +
                '<div class="spinner-border text-primary mb-3" role="status"></div>' +
                '<span class="text-muted fw-bold fs-6">Loading God-Level Moderation Cockpit...</span>' +
                '</div>'
            );

            $('#kt_activitiesMarketplaceJob_toggle').trigger('click');

            $.ajax({
                type: 'GET',
                url: showDetailsUrl,
                data: { id: jobId },
                success: function(res) {
                    if (res.status === 'success') {
                        $('#marketplace_job_cockpit_body').html(res.view);
                    } else {
                        $('#marketplace_job_cockpit_body').html(
                            '<div class="alert alert-danger m-5">Failed to load cockpit: ' + (res.message || 'Unknown error') + '</div>'
                        );
                    }
                },
                error: function(xhr) {
                    $('#marketplace_job_cockpit_body').html(
                        '<div class="alert alert-danger m-5">Error connecting to server. Please try again.</div>'
                    );
                }
            });
        });

        // Close cockpit drawer
        $(document).on('click', '#kt_activitiesMarketplaceJob_close', function() {
            var drawer = KTDrawer.getInstance(document.querySelector("#kt_activitiesMarketplaceJob"));
            if (drawer) { drawer.hide(); }
        });

        // ---------- GLOBAL AUTO-APPROVAL SWITCH TOGGLE ----------
        $('#chkAutoApproval').on('change', function() {
            var settingId = $(this).val();
            var $badge = $('#autoApprovalStatusBadge');

            $.ajax({
                type: 'POST',
                url: autoApprovalUrl,
                data: {
                    '_token': token,
                    'id': settingId
                },
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Auto-Approval Updated',
                            text: res.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        if (res.auto_status === 1) {
                            $badge.removeClass('badge-light-secondary').addClass('badge-light-success').text('ACTIVE (Risk < 0.20)');
                        } else {
                            $badge.removeClass('badge-light-success').addClass('badge-light-secondary').text('PAUSED (Manual Review)');
                        }
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Unable to update auto-approval setting.', 'error');
                }
            });
        });

        // ---------- APPROVE JOB ACTIONS (TABLE & COCKPIT) ----------
        $(document).on('click', '.btn-table-action, .btn-cockpit-action', function(e) {
            e.preventDefault();
            var actionType = $(this).data('action');
            var jobId = $(this).data('job-id');

            var promptText = (actionType === 'approve_bench')
                ? 'Approve this job for Bench Talent Sharing only? Partner agencies will submit bench profiles, but standard claim lockout will remain inactive.'
                : 'Approve and publish this job on the Agency Marketplace for full outsource claims & bench talent proposals?';

            Swal.fire({
                title: 'Confirm Approval',
                text: promptText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve Job',
                cancelButtonText: 'Cancel',
                customClass: { confirmButton: 'btn btn-primary', cancelButton: 'btn btn-light' }
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: approvalUrl,
                        data: {
                            '_token': token,
                            'datatype': actionType,
                            'job_id': [jobId]
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Approved!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(function() {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error', res.message || 'Operation failed', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Server error while approving job.', 'error');
                        }
                    });
                }
            });
        });

        // ---------- REJECT JOB MODAL TRIGGER ----------
        $(document).on('click', '.btn-table-reject, .btn-cockpit-reject', function(e) {
            e.preventDefault();
            var jobId = $(this).data('job-id');
            var jobTitle = $(this).data('job-title') || ('#JOB-' + jobId);

            $('#modalRejectJobId').val(jobId);
            $('#modalRejectJobTitle').text(jobTitle);
            $('#modalRejectNotes').val('');

            var modal = new bootstrap.Modal(document.getElementById('marketplaceRejectModal'));
            modal.show();
        });

        // Confirm rejection submission
        $('#btnConfirmReject').on('click', function() {
            var jobId = $('#modalRejectJobId').val();
            var reasonCode = $('#modalRejectReasonCode').val();
            var notes = $('#modalRejectNotes').val();

            if (!jobId) return;

            $.ajax({
                type: 'POST',
                url: approvalUrl,
                data: {
                    '_token': token,
                    'datatype': 'reject',
                    'job_id': [jobId],
                    'reason_code': reasonCode,
                    'notes': notes
                },
                success: function(res) {
                    $('#marketplaceRejectModal').modal('hide');
                    if (res.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Job Rejected',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', res.message || 'Failed to reject job', 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Server communication failure.', 'error');
                }
            });
        });

        // ---------- ROW CHECKBOXES & BULK ACTIONS ----------
        function updateBulkToolbar() {
            var checkedCount = $('.row-select-checkbox:checked').length;
            $('#selectedCount').text(checkedCount);
            if (checkedCount > 0) {
                $('#bulkActionsToolbar').removeClass('d-none').addClass('d-flex');
            } else {
                $('#bulkActionsToolbar').removeClass('d-flex').addClass('d-none');
            }
        }

        $('#selectAllCheckbox').on('change', function() {
            $('.row-select-checkbox').prop('checked', $(this).is(':checked'));
            updateBulkToolbar();
        });

        $(document).on('change', '.row-select-checkbox', function() {
            updateBulkToolbar();
        });

        // Bulk Approve
        $('.btn-bulk-approve').on('click', function() {
            var selectedIds = $('.row-select-checkbox:checked').map(function() { return $(this).val(); }).get();
            if (selectedIds.length === 0) return;

            Swal.fire({
                title: 'Bulk Approval',
                text: 'Approve and publish all ' + selectedIds.length + ' selected jobs on the marketplace?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, Approve All',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: approvalUrl,
                        data: {
                            '_token': token,
                            'datatype': 'approve_all',
                            'job_id': selectedIds
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Success', res.message, 'success').then(function() {
                                    location.reload();
                                });
                            }
                        }
                    });
                }
            });
        });

        // Bulk Reject
        $('.btn-bulk-reject').on('click', function() {
            var selectedIds = $('.row-select-checkbox:checked').map(function() { return $(this).val(); }).get();
            if (selectedIds.length === 0) return;

            Swal.fire({
                title: 'Bulk Rejection',
                text: 'Reject all ' + selectedIds.length + ' selected jobs from the marketplace?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, Reject All',
                cancelButtonText: 'Cancel'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: approvalUrl,
                        data: {
                            '_token': token,
                            'datatype': 'reject',
                            'job_id': selectedIds,
                            'notes': 'Bulk rejected by Super Admin'
                        },
                        success: function(res) {
                            if (res.status === 'success') {
                                Swal.fire('Success', res.message, 'success').then(function() {
                                    location.reload();
                                });
                            }
                        }
                    });
                }
            });
        });

    });
</script>
@endpush
