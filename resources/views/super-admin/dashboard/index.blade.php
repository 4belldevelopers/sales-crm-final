@extends('layouts.superadmin_layout')

@section('header_title')
<div class="page-title d-flex align-items-center flex-wrap my-0">
    <h1 class="d-flex align-items-center text-gray-900 fw-bold fs-3 my-0">Dashboard</h1>
</div>
@endsection

@push('head-script')
<style>
    /* Super Admin Dashboard - Isolated Page Styles */
    #kt_body {
        --kt-toolbar-height: 0px !important;
        --kt-toolbar-height-tablet-and-mobile: 0px !important;
        overflow-x: hidden;
    }
    .header-fixed.toolbar-fixed #kt_wrapper {
        padding-top: 65px !important;
    }
    .header-tablet-and-mobile-fixed.toolbar-tablet-and-mobile-fixed #kt_wrapper {
        padding-top: 55px !important;
    }
    #kt_toolbar {
        display: none !important;
        height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
    }
    #kt_header_nav .page-title {
        display: flex !important;
        align-items: center !important;
    }
    #kt_header_nav .page-title h1 {
        font-family: 'Poppins', sans-serif !important;
        font-size: 1.35rem !important;
        font-weight: 600 !important;
        color: #181c32 !important;
        line-height: 1.2 !important;
        margin: 0 !important;
    }
    #kt_post {
        margin-top: 0 !important;
        padding-top: 18px !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
    #kt_content_container {
        padding-top: 0 !important;
        padding-left: 15px !important;
        padding-right: 15px !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }
    @media (max-width: 767.98px) {
        #kt_content_container {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
    }
    .sa-card {
        border-radius: 0.65rem;
        border: 1px solid #eef2f6;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        background: #ffffff;
        transition: box-shadow 0.2s ease;
    }
    .sa-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    }
    .sa-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .sa-filter-btn-group .btn {
        padding: 3px 10px;
        font-size: 0.75rem;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        color: #64748b;
        background-color: #f8fafc;
        border-radius: 4px;
        transition: all 0.15s ease-in-out;
    }
    .sa-filter-btn-group .btn.active {
        background-color: #009ef7 !important;
        border-color: #009ef7 !important;
        color: #ffffff !important;
    }
    .sa-filter-btn-group .btn:hover:not(.active) {
        background-color: #edf2f7;
        color: #334155;
    }
    .sa-chart-container {
        position: relative;
        width: 100%;
    }
    .sa-empty-state {
        min-height: 180px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 1.5rem;
    }
    .sa-legend-item {
        display: flex;
        align-items: center;
        margin-bottom: 6px;
        font-size: 0.78rem;
    }
    .sa-legend-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        margin-right: 8px;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
    <div class="post d-flex flex-column-fluid" id="kt_post">
        <div id="kt_content_container" class="container-fluid">
            
            {{-- ROW 1: Core Top 4 KPI Summary Cards --}}
            <div class="row g-4 mb-5">
                <!-- Card 1: Total Companies -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-body d-flex align-items-center p-5">
                            <div class="sa-icon-box me-4" style="background-color: #e8f4fd;">
                                <i class="fa-solid fa-building fs-3 text-primary"></i>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="fs-2hx fw-bolder text-gray-900 lh-1 mb-1">{{ $total_company_count ?? 4 }}</span>
                                <span class="text-gray-600 fw-semibold fs-7">Total Companies</span>
                                <span class="text-success fs-8 fw-semibold mt-1">
                                    <i class="fa-solid fa-arrow-trend-up fs-9 text-success me-1"></i>+0% vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Active Companies -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-body d-flex align-items-center p-5">
                            <div class="sa-icon-box me-4" style="background-color: #e8fbf0;">
                                <i class="fa-solid fa-users fs-3 text-success"></i>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="fs-2hx fw-bolder text-gray-900 lh-1 mb-1">{{ $activecompanies ?? 4 }}</span>
                                <span class="text-gray-600 fw-semibold fs-7">Active Companies</span>
                                <span class="text-success fs-8 fw-semibold mt-1">
                                    <i class="fa-solid fa-arrow-trend-up fs-9 text-success me-1"></i>+0% vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Bench Candidates -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-body d-flex align-items-center p-5">
                            <div class="sa-icon-box me-4" style="background-color: #f3e8fd;">
                                <i class="fa-solid fa-user-tie fs-3" style="color: #8b5cf6;"></i>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="fs-2hx fw-bolder text-gray-900 lh-1 mb-1">{{ $bench_total ?? 6 }}</span>
                                <span class="text-gray-600 fw-semibold fs-7">Bench Candidates</span>
                                <span class="text-success fs-8 fw-semibold mt-1">
                                    <i class="fa-solid fa-arrow-trend-up fs-9 text-success me-1"></i>+0% vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4: Total Job Posts -->
                <div class="col-xl-3 col-md-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-body d-flex align-items-center p-5">
                            <div class="sa-icon-box me-4" style="background-color: #fef3e8;">
                                <i class="fa-solid fa-file-lines fs-3 text-warning"></i>
                            </div>
                            <div class="d-flex flex-column flex-grow-1">
                                <span class="fs-2hx fw-bolder text-gray-900 lh-1 mb-1">{{ $jobPostTotal ?? 6 }}</span>
                                <span class="text-gray-600 fw-semibold fs-7">Total Job Posts</span>
                                <span class="text-success fs-8 fw-semibold mt-1">
                                    <i class="fa-solid fa-arrow-trend-up fs-9 text-success me-1"></i>+0% vs last month
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ROW 2: Core Analytics (4 equal columns on desktop) --}}
            <div class="row g-4 mb-5">
                <!-- Card 2.1: Subscription Overview -->
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                            <h3 class="card-title fw-bold text-gray-900 fs-6 mb-0">Subscription Overview</h3>
                            <div class="btn-group btn-group-sm sa-filter-btn-group" role="group" data-chart="subscription_overview">
                                <button type="button" class="btn" data-period="week">Week</button>
                                <button type="button" class="btn active" data-period="month">Month</button>
                                <button type="button" class="btn" data-period="custom">Custom</button>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <div class="sa-chart-container" style="height: 185px;">
                                <canvas id="subscription_overview_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2.2: Company Types -->
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                            <h3 class="card-title fw-bold text-gray-900 fs-6 mb-0">Company Types</h3>
                            <div class="btn-group btn-group-sm sa-filter-btn-group" role="group" data-chart="company_types">
                                <button type="button" class="btn" data-period="week">Week</button>
                                <button type="button" class="btn active" data-period="month">Month</button>
                                <button type="button" class="btn" data-period="custom">Custom</button>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <div class="sa-chart-container" style="height: 185px;">
                                <canvas id="companyCountData"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2.3: Company Activity -->
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                            <h3 class="card-title fw-bold text-gray-900 fs-6 mb-0">Company Activity</h3>
                            <div class="btn-group btn-group-sm sa-filter-btn-group" role="group" data-chart="company_activity">
                                <button type="button" class="btn" data-period="week">Week</button>
                                <button type="button" class="btn active" data-period="month">Month</button>
                                <button type="button" class="btn" data-period="custom">Custom</button>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <div class="sa-chart-container" style="height: 185px;">
                                <canvas id="active_companies_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2.4: Bench Candidate Status -->
                <div class="col-xl-3 col-lg-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                            <h3 class="card-title fw-bold text-gray-900 fs-6 mb-0">Bench Candidate Status</h3>
                            <div class="btn-group btn-group-sm sa-filter-btn-group" role="group" data-chart="bench_candidate">
                                <button type="button" class="btn" data-period="week">Week</button>
                                <button type="button" class="btn active" data-period="month">Month</button>
                                <button type="button" class="btn" data-period="custom">Custom</button>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <div class="sa-chart-container" style="height: 185px;">
                                <canvas id="bench_candidate_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ROW 3: Mid-tier Analytics (2 columns) --}}
            <div class="row g-4 mb-5">
                <!-- Card 3.1: Job Posts Trend -->
                <div class="col-lg-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                            <h3 class="card-title fw-bold text-gray-900 fs-6 mb-0">Job Posts Trend</h3>
                            <div class="btn-group btn-group-sm sa-filter-btn-group" role="group" data-chart="job_posts_trend">
                                <button type="button" class="btn" data-period="week">Week</button>
                                <button type="button" class="btn active" data-period="month">Month</button>
                                <button type="button" class="btn" data-period="custom">Custom</button>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <div class="sa-chart-container" style="height: 210px;">
                                <canvas id="jobs_post_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3.2: Job Status -->
                <div class="col-lg-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                            <h3 class="card-title fw-bold text-gray-900 fs-6 mb-0">Job Status</h3>
                            <div class="btn-group btn-group-sm sa-filter-btn-group" role="group" data-chart="job_status">
                                <button type="button" class="btn" data-period="week">Week</button>
                                <button type="button" class="btn active" data-period="month">Month</button>
                                <button type="button" class="btn" data-period="custom">Custom</button>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <div class="sa-chart-container" style="height: 210px;">
                                <canvas id="jobs_status_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ROW 4: Recruitment Pipeline & Interview Analytics (2 columns) --}}
            <div class="row g-4 mb-5">
                <!-- Card 4.1: Candidate Pipeline -->
                <div class="col-lg-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                            <h3 class="card-title fw-bold text-gray-900 fs-6 mb-0">Candidate Pipeline</h3>
                            <div class="btn-group btn-group-sm sa-filter-btn-group" role="group" data-chart="candidate_pipeline">
                                <button type="button" class="btn" data-period="week">Week</button>
                                <button type="button" class="btn active" data-period="month">Month</button>
                                <button type="button" class="btn" data-period="custom">Custom</button>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <div class="sa-chart-container" style="height: 230px;">
                                <canvas id="total_candidate_uploads_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 4.2: Interview Status -->
                <div class="col-lg-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                            <h3 class="card-title fw-bold text-gray-900 fs-6 mb-0">Interview Status</h3>
                            <div class="btn-group btn-group-sm sa-filter-btn-group" role="group" data-chart="interview_status">
                                <button type="button" class="btn" data-period="week">Week</button>
                                <button type="button" class="btn active" data-period="month">Month</button>
                                <button type="button" class="btn" data-period="custom">Custom</button>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <div class="sa-chart-container" style="height: 230px;">
                                <canvas id="total_interview_uploads_chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ROW 5: Communication Activity Panels (2 columns) --}}
            <div class="row g-4 mb-5">
                <!-- Card 5.1: Email Activity -->
                <div class="col-lg-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                            <h3 class="card-title fw-bold text-gray-900 fs-6 mb-0">Email Activity</h3>
                            <div class="btn-group btn-group-sm sa-filter-btn-group" role="group" data-chart="email_activity">
                                <button type="button" class="btn" data-period="week">Week</button>
                                <button type="button" class="btn active" data-period="month">Month</button>
                                <button type="button" class="btn" data-period="custom">Custom</button>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <div class="sa-chart-container {{ ($emailSummaryData_total ?? 0) > 0 ? '' : 'd-none' }}" id="container_total_email_chart" style="height: 190px;">
                                <canvas id="total_email_chart"></canvas>
                            </div>
                            <div class="sa-empty-state {{ ($emailSummaryData_total ?? 0) > 0 ? 'd-none' : 'd-flex' }}" id="empty_total_email_chart">
                                <div class="sa-icon-box rounded-circle mx-auto mb-3" style="background-color: #e8f4fd; width: 56px; height: 56px;">
                                    <i class="fa-solid fa-envelope fs-2 text-primary"></i>
                                </div>
                                <div class="fw-bold fs-6 text-gray-800 mb-1">No activity in this period</div>
                                <div class="text-muted fs-7">There are no email activities for the selected period.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 5.2: WhatsApp Activity -->
                <div class="col-lg-6 col-12">
                    <div class="card sa-card h-100">
                        <div class="card-header pt-4 pb-2 px-5 border-0 d-flex align-items-center justify-content-between">
                            <h3 class="card-title fw-bold text-gray-900 fs-6 mb-0">WhatsApp Activity</h3>
                            <div class="btn-group btn-group-sm sa-filter-btn-group" role="group" data-chart="whatsapp_activity">
                                <button type="button" class="btn" data-period="week">Week</button>
                                <button type="button" class="btn active" data-period="month">Month</button>
                                <button type="button" class="btn" data-period="custom">Custom</button>
                            </div>
                        </div>
                        <div class="card-body pt-1 pb-4 px-5">
                            <div class="sa-chart-container {{ ($whatsappSummary_total ?? 0) > 0 ? '' : 'd-none' }}" id="container_total_whatsapp_chart" style="height: 190px;">
                                <canvas id="total_whatsapp_chart"></canvas>
                            </div>
                            <div class="sa-empty-state {{ ($whatsappSummary_total ?? 0) > 0 ? 'd-none' : 'd-flex' }}" id="empty_total_whatsapp_chart">
                                <div class="sa-icon-box rounded-circle mx-auto mb-3" style="background-color: #e8fbf0; width: 56px; height: 56px;">
                                    <i class="fa-brands fa-whatsapp fs-2 text-success"></i>
                                </div>
                                <div class="fw-bold fs-6 text-gray-800 mb-1">No activity in this period</div>
                                <div class="text-muted fs-7">There are no WhatsApp messages for the selected period.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Custom Date Range Picker Modal --}}
    <div class="modal fade" id="saCustomDateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered mw-450px">
            <div class="modal-content border-0 shadow-sm" style="border-radius: 0.65rem;">
                <div class="modal-header pb-2 border-0 justify-content-between">
                    <div>
                        <h4 class="modal-title fw-bold text-gray-800 fs-5" id="saCustomModalTitle">Select Date Range</h4>
                        <div class="text-muted fs-8 mt-1" id="saCustomModalSubtitle">Filter metrics for selected chart</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-icon btn-active-light-primary" data-bs-dismiss="modal" id="sa_custom_modal_close_btn">
                        <i class="fa-solid fa-xmark fs-4 text-gray-500"></i>
                    </button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-4">
                        <label class="form-label fs-7 fw-semibold text-gray-700 mb-1">Start Date</label>
                        <input type="date" class="form-control form-control-sm form-control-solid" id="sa_custom_start_date">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fs-7 fw-semibold text-gray-700 mb-1">End Date</label>
                        <input type="date" class="form-control form-control-sm form-control-solid" id="sa_custom_end_date">
                    </div>
                    <div id="sa_custom_modal_error" class="text-danger fs-8 fw-semibold mt-2 d-none">
                        Start Date must be before or equal to End Date.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-sm btn-primary" id="sa_custom_modal_apply_btn">
                        <i class="fa-solid fa-filter fs-8 me-1"></i> Apply Range
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
<script>
(function() {
    'use strict';

    // =========================================================================
    // ENSURE HEADER PAGE TITLE
    // =========================================================================
    try {
        var headerNav = document.getElementById('kt_header_nav');
        if (headerNav && !headerNav.querySelector('.page-title')) {
            var titleDiv = document.createElement('div');
            titleDiv.className = 'page-title d-flex align-items-center flex-wrap my-0 me-3';
            titleDiv.innerHTML = '<h1 class="d-flex align-items-center text-gray-900 fw-bold fs-3 my-0">Dashboard</h1>';
            headerNav.prepend(titleDiv);
        }
    } catch (e) {}

    // =========================================================================
    // CHART.JS PLUGINS (Donut Center Text & Data Labels)
    // =========================================================================

    const centerTextPlugin = {
        id: 'centerTextPlugin',
        beforeDraw(chart) {
            const pluginOpts = chart.config.options.plugins.centerText;
            if (!pluginOpts || !pluginOpts.display) return;

            const { ctx, chartArea } = chart;
            if (!chartArea) return;

            const centerX = (chartArea.left + chartArea.right) / 2;
            const centerY = (chartArea.top + chartArea.bottom) / 2;

            ctx.save();
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';

            // Main bold count
            ctx.font = "bold 22px 'Poppins', 'Segoe UI', sans-serif";
            ctx.fillStyle = '#0f172a';
            ctx.fillText(pluginOpts.text || '0', centerX, centerY - (pluginOpts.subtext ? 8 : 0));

            // Subtitle
            if (pluginOpts.subtext) {
                ctx.font = "500 11px 'Poppins', 'Segoe UI', sans-serif";
                ctx.fillStyle = '#94a3b8';
                ctx.fillText(pluginOpts.subtext, centerX, centerY + 14);
            }
            ctx.restore();
        }
    };

    const barValueLabelsPlugin = {
        id: 'barValueLabelsPlugin',
        afterDatasetsDraw(chart) {
            const { ctx } = chart;
            chart.data.datasets.forEach((dataset, i) => {
                const meta = chart.getDatasetMeta(i);
                meta.data.forEach((bar, index) => {
                    const value = dataset.data[index];
                    if (value === undefined || value === null) return;

                    ctx.save();
                    ctx.font = "bold 11px 'Poppins', 'Segoe UI', sans-serif";
                    ctx.fillStyle = '#1e293b';

                    if (chart.options.indexAxis === 'y') {
                        // Horizontal bar: render value to the right
                        ctx.textAlign = 'left';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(value, bar.x + 6, bar.y);
                    } else if (chart.config.type === 'line') {
                        // Line chart: render value above point
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        ctx.fillText(value, bar.x, bar.y - 6);
                    } else {
                        // Vertical bar: render value above bar
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        ctx.fillText(value, bar.x, bar.y - 4);
                    }
                    ctx.restore();
                });
            });
        }
    };

    // Register plugins safely
    Chart.register(centerTextPlugin, barValueLabelsPlugin);

    // =========================================================================
    // PHASE 2: LIFECYCLE INSTANCE REGISTRY, RAM CACHE & ANTI-RACE CONTROLLER
    // =========================================================================

    window.SuperAdminDashboard = {
        charts: new Map(),            // canvasId => ChartInstance
        cache: new Map(),             // "chartKey:period" => { data, timestamp }
        abortControllers: new Map(),  // chartKey => AbortController
        requestSeq: new Map(),        // chartKey => currentSeqNumber
        lastActiveTime: Date.now(),
        defaultTtlMs: 30000,          // 30 seconds RAM cache TTL

        activeFilters: {
            subscription_overview: 'month',
            company_types: 'month',
            company_activity: 'month',
            bench_candidate: 'month',
            job_posts_trend: 'month',
            job_status: 'month',
            candidate_pipeline: 'month',
            interview_status: 'month',
            email_activity: 'month',
            whatsapp_activity: 'month'
        },

        // Chart Instance Management
        initChart(canvasId, config) {
            if (this.charts.has(canvasId)) {
                try {
                    this.charts.get(canvasId).destroy();
                } catch (e) {}
                this.charts.delete(canvasId);
            }
            const canvas = document.getElementById(canvasId);
            if (!canvas) return null;

            const chart = new Chart(canvas.getContext('2d'), config);
            this.charts.set(canvasId, chart);
            return chart;
        },

        getChart(canvasId) {
            return this.charts.get(canvasId) || null;
        },

        // Dynamic in-place chart update without canvas re-creation
        updateChartData(canvasId, labels, data, total = null) {
            // Dynamic empty-state toggling
            const containerEl = document.getElementById('container_' + canvasId);
            const emptyEl = document.getElementById('empty_' + canvasId);
            const sumData = (data && Array.isArray(data)) ? data.reduce((a, b) => a + b, 0) : (total || 0);

            if (containerEl && emptyEl) {
                if (sumData > 0) {
                    containerEl.classList.remove('d-none');
                    emptyEl.classList.add('d-none');
                    emptyEl.classList.remove('d-flex');
                } else {
                    containerEl.classList.add('d-none');
                    emptyEl.classList.remove('d-none');
                    emptyEl.classList.add('d-flex');
                }
            }

            const chart = this.charts.get(canvasId);
            if (!chart) return false;

            if (labels && Array.isArray(labels)) {
                chart.data.labels = labels;
            }
            if (data && Array.isArray(data) && chart.data.datasets && chart.data.datasets.length > 0) {
                chart.data.datasets[0].data = data;
            }
            if (total !== null && chart.config.options.plugins && chart.config.options.plugins.centerText) {
                chart.config.options.plugins.centerText.text = total.toString();
            }
            // Dynamically calibrate numeric value axis only (do not corrupt categorical/date axis)
            if (chart.config.options.scales && Array.isArray(data)) {
                const maxVal = Math.max(...data, 0);
                const suggestedMax = maxVal > 0 ? maxVal + Math.ceil(maxVal * 0.15) + 1 : 5;
                const isHorizontal = chart.config.options.indexAxis === 'y';

                if (isHorizontal && chart.config.options.scales.x) {
                    chart.config.options.scales.x.max = suggestedMax;
                } else if (!isHorizontal && chart.config.options.scales.y) {
                    chart.config.options.scales.y.max = suggestedMax;
                }
            }
            chart.update();
            return true;
        },

        // Session RAM Caching (30s TTL, isolated to User RAM)
        getCache(key, ttlMs = this.defaultTtlMs) {
            if (!this.cache.has(key)) return null;
            const entry = this.cache.get(key);
            if (Date.now() - entry.timestamp > ttlMs) {
                this.cache.delete(key);
                return null;
            }
            return entry.data;
        },

        setCache(key, data) {
            this.cache.set(key, { data: data, timestamp: Date.now() });
        },

        clearCache() {
            this.cache.clear();
        },

        // Anti-Race AbortController Management
        getAbortController(chartKey) {
            if (this.abortControllers.has(chartKey)) {
                try {
                    this.abortControllers.get(chartKey).abort();
                } catch (e) {}
            }
            const controller = new AbortController();
            this.abortControllers.set(chartKey, controller);
            return controller;
        },

        // Monotonically Increasing Sequence IDs (Drop slow earlier responses)
        nextSeq(chartKey) {
            const seq = (this.requestSeq.get(chartKey) || 0) + 1;
            this.requestSeq.set(chartKey, seq);
            return seq;
        },

        isLatestSeq(chartKey, seq) {
            return (this.requestSeq.get(chartKey) || 0) === seq;
        },

        // Tab Focus & Visibility Re-Validation
        initVisibilityRevalidation() {
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'visible') {
                    const elapsed = Date.now() - this.lastActiveTime;
                    if (elapsed > 60000) { // Inactive tab for > 60 seconds
                        this.clearCache();
                        console.info('[SuperAdminDashboard] Tab focused after >60s. Cache cleared for fresh re-sync.');
                    }
                    this.lastActiveTime = Date.now();
                } else {
                    this.lastActiveTime = Date.now();
                }
            });
        },

        // Network Recovery Handler
        initNetworkRecovery() {
            window.addEventListener('online', () => {
                this.clearCache();
                console.info('[SuperAdminDashboard] Network connectivity recovered. Cache purged.');
            });
        },

        // Bounded Retry with Exponential Jitter (Max 2 retries, 300ms base delay)
        async fetchWithRetry(url, options, retries = 2, delay = 300) {
            try {
                const res = await fetch(url, options);
                if (!res.ok) {
                    if (res.status === 401 || res.status === 403 || res.status === 422) {
                        throw new Error(`HTTP ${res.status}`);
                    }
                    if (retries > 0) {
                        const jitter = Math.floor(Math.random() * 100);
                        await new Promise(r => setTimeout(r, delay + jitter));
                        return this.fetchWithRetry(url, options, retries - 1, delay * 2);
                    }
                    throw new Error(`HTTP ${res.status}`);
                }
                return await res.json();
            } catch (err) {
                if (err.name === 'AbortError' || retries <= 0) throw err;
                const jitter = Math.floor(Math.random() * 100);
                await new Promise(r => setTimeout(r, delay + jitter));
                return this.fetchWithRetry(url, options, retries - 1, delay * 2);
            }
        },

        // Memory Teardown Lifecycle (Eliminates Browser Leaks)
        destroyAll() {
            this.charts.forEach((chart) => {
                try { chart.destroy(); } catch (e) {}
            });
            this.charts.clear();

            this.abortControllers.forEach((ctrl) => {
                try { ctrl.abort(); } catch (e) {}
            });
            this.abortControllers.clear();

            this.clearCache();
            this.requestSeq.clear();
        }
    };

    // Initialize lifecycle listeners
    window.SuperAdminDashboard.initVisibilityRevalidation();
    window.SuperAdminDashboard.initNetworkRecovery();

    window.addEventListener('beforeunload', () => {
        window.SuperAdminDashboard.destroyAll();
    });

    // =========================================================================
    // INITIALIZE CHARTS WITH AUTHORITATIVE DATA
    // =========================================================================

    // 1. Subscription Overview (Horizontal Bar: Free vs Paid)
    const subLabels = {!! json_encode($oprationalPlanLabels ?? ['Free', 'Paid']) !!};
    const subValues = {!! json_encode($oprationalPlanValues ?? [2, 2]) !!};
    const subMaxVal = Math.max(...subValues, 2) + 1;

    window.SuperAdminDashboard.initChart('subscription_overview_chart', {
        type: 'bar',
        data: {
            labels: subLabels,
            datasets: [{
                data: subValues,
                backgroundColor: '#009ef7',
                borderRadius: 4,
                borderSkipped: false,
                barPercentage: 0.55,
                categoryPercentage: 0.7
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.label}: ${ctx.raw} companies`
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: subMaxVal,
                    ticks: { stepSize: 1, font: { size: 10, family: 'Poppins' } },
                    grid: { color: '#f1f5f9' }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: '600', family: 'Poppins' }, color: '#475569' }
                }
            }
        }
    });

    // 2. Company Types (Donut Chart with Center Text & Division-by-Zero Guard)
    const compLabels = {!! json_encode($companies_labels ?? ["Staffing", "Service", "Product", "Other"]) !!};
    const compValues = {!! json_encode($companiesValues ?? [2, 2, 0, 0]) !!};
    const compTotal = {{ $companies_total ?? 4 }};

    window.SuperAdminDashboard.initChart('companyCountData', {
        type: 'doughnut',
        data: {
            labels: compLabels,
            datasets: [{
                data: compValues,
                backgroundColor: ['#22c55e', '#ffab00', '#ef4444', '#94a3b8'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6,
                cutout: '72%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                centerText: {
                    display: true,
                    text: compTotal.toString(),
                    subtext: 'Total'
                },
                legend: {
                    display: true,
                    position: 'right',
                    align: 'center',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 10,
                        font: { size: 11, family: 'Poppins', weight: '500' },
                        generateLabels(chart) {
                            const data = chart.data;
                            const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                            return data.labels.map((label, i) => {
                                const val = data.datasets[0].data[i];
                                // Guard division by zero:
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : '0.0';
                                return {
                                    text: `${label}  ${val} (${pct}%)`,
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    strokeStyle: '#ffffff',
                                    lineWidth: 1,
                                    index: i
                                };
                            });
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label(ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : '0.0';
                            return ` ${ctx.label}: ${ctx.raw} (${pct}%)`;
                        }
                    }
                }
            }
        }
    });

    // 3. Company Activity (Vertical Bars: Daily, Weekly, Monthly Active)
    const actLabels = {!! json_encode($activecompanylabels ?? ["Daily Active", "Weekly Active", "Monthly Active"]) !!};
    const actValues = {!! json_encode($activecompanyValues ?? [2, 2, 2]) !!};
    const actMaxVal = Math.max(...actValues, 2) + 1;

    window.SuperAdminDashboard.initChart('active_companies_chart', {
        type: 'bar',
        data: {
            labels: actLabels,
            datasets: [{
                data: actValues,
                backgroundColor: '#009ef7',
                borderRadius: 4,
                borderSkipped: false,
                barPercentage: 0.5,
                categoryPercentage: 0.7
            }]
        },
        options: {
            indexAxis: 'x',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.label}: ${ctx.raw} companies`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: actMaxVal,
                    ticks: { stepSize: 1, font: { size: 10, family: 'Poppins' } },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: '600', family: 'Poppins' }, color: '#475569' }
                }
            }
        }
    });

    // 4. Bench Candidate Status (Donut Chart with Center Text & Division-by-Zero Guard)
    const benchLabels = {!! json_encode($bench_labels ?? ["Pool", "Bench", "Placed", "Hold", "Exited"]) !!};
    const benchValues = {!! json_encode($benchcandidateValues ?? [0, 6, 0, 0, 0]) !!};
    const benchTotal = {{ $bench_total ?? 6 }};

    window.SuperAdminDashboard.initChart('bench_candidate_chart', {
        type: 'doughnut',
        data: {
            labels: benchLabels,
            datasets: [{
                data: benchValues,
                backgroundColor: ['#64748b', '#009ef7', '#22c55e', '#ffab00', '#ef4444'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6,
                cutout: '72%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                centerText: {
                    display: true,
                    text: benchTotal.toString(),
                    subtext: 'Total'
                },
                legend: {
                    display: true,
                    position: 'right',
                    align: 'center',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 8,
                        font: { size: 11, family: 'Poppins', weight: '500' },
                        generateLabels(chart) {
                            const data = chart.data;
                            const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                            return data.labels.map((label, i) => {
                                const val = data.datasets[0].data[i];
                                const pct = total > 0 ? ((val / total) * 100).toFixed(1) : '0.0';
                                return {
                                    text: `${label}  ${val} (${pct}%)`,
                                    fillStyle: data.datasets[0].backgroundColor[i],
                                    strokeStyle: '#ffffff',
                                    lineWidth: 1,
                                    index: i
                                };
                            });
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label(ctx) {
                            const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : '0.0';
                            return ` ${ctx.label}: ${ctx.raw} candidates (${pct}%)`;
                        }
                    }
                }
            }
        }
    });

    // 5. Job Posts Trend (Smooth Line Chart with Area Gradient)
    const trendLabels = {!! json_encode(!empty($jobPostsTrendLabels) ? $jobPostsTrendLabels : ['16 Oct', '17 Oct', '18 Oct', '19 Oct', '20 Oct', '21 Oct', '22 Oct']) !!};
    const trendValues = {!! json_encode(!empty($jobPostsTrendValues) ? $jobPostsTrendValues : [1, 0, 2, 3, 1, 2, 1]) !!};
    const trendMaxVal = Math.max(...trendValues, 4) + 2;

    const trendCanvas = document.getElementById('jobs_post_chart');
    let trendGradient = '#009ef7';
    if (trendCanvas) {
        const tCtx = trendCanvas.getContext('2d');
        trendGradient = tCtx.createLinearGradient(0, 0, 0, 200);
        trendGradient.addColorStop(0, 'rgba(0, 158, 247, 0.28)');
        trendGradient.addColorStop(1, 'rgba(0, 158, 247, 0.00)');
    }

    window.SuperAdminDashboard.initChart('jobs_post_chart', {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [{
                label: 'Job Posts',
                data: trendValues,
                borderColor: '#009ef7',
                borderWidth: 2.5,
                backgroundColor: trendGradient,
                fill: true,
                tension: 0.35,
                pointBackgroundColor: '#009ef7',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.raw} job posts`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: trendMaxVal,
                    ticks: { stepSize: 2, font: { size: 10, family: 'Poppins' } },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10, weight: '500', family: 'Poppins' }, color: '#475569' }
                }
            }
        }
    });

    // 6. Job Status (Horizontal Bar: Hiring, Hold, Closed)
    const statusLabels = {!! json_encode($jobsStatusLabels ?? ["Hiring", "Hold", "Closed"]) !!};
    const statusValues = {!! json_encode($jobsStatusValues ?? [6, 0, 0]) !!};
    const statusMaxVal = Math.max(...statusValues, 6) + 2;

    window.SuperAdminDashboard.initChart('jobs_status_chart', {
        type: 'bar',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusValues,
                backgroundColor: ['#22c55e', '#ffab00', '#ef4444'],
                borderRadius: 4,
                borderSkipped: false,
                barPercentage: 0.5,
                categoryPercentage: 0.75
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.label}: ${ctx.raw} jobs`
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: statusMaxVal,
                    ticks: { stepSize: 2, font: { size: 10, family: 'Poppins' } },
                    grid: { color: '#f1f5f9' }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: '600', family: 'Poppins' }, color: '#475569' }
                }
            }
        }
    });

    // 7. Candidate Pipeline (Horizontal Bar: Multi-Stage Statuses)
    const pipeLabels = {!! json_encode($allcandidateslabels ?? ["In Review","Sourcing","Screening","Evaluation","Negotiation","Offered","Joined","Rejected"]) !!};
    const pipeValues = {!! json_encode($allcandidatesValues ?? [36, 1, 0, 0, 0, 2, 1, 6]) !!};
    const pipeMaxVal = Math.max(...pipeValues, 36) + 4;

    window.SuperAdminDashboard.initChart('total_candidate_uploads_chart', {
        type: 'bar',
        data: {
            labels: pipeLabels,
            datasets: [{
                data: pipeValues,
                backgroundColor: [
                    '#8b6727', // In Review
                    '#1e293b', // Sourcing
                    '#a855f7', // Screening
                    '#3b82f6', // Evaluation
                    '#ec4899', // Negotiation
                    '#ffab00', // Offered
                    '#22c55e', // Joined
                    '#ef4444'  // Rejected
                ],
                borderRadius: 4,
                borderSkipped: false,
                barPercentage: 0.65,
                categoryPercentage: 0.85
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.label}: ${ctx.raw} candidates`
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: pipeMaxVal,
                    ticks: { stepSize: 5, font: { size: 10, family: 'Poppins' } },
                    grid: { color: '#f1f5f9' }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 10.5, weight: '500', family: 'Poppins' }, color: '#475569' }
                }
            }
        }
    });

    // 8. Interview Status (Horizontal Bar)
    const intLabels = {!! json_encode($interviewDatalabels ?? ["Available","Not Available","Rescheduled","Cancel Interview","Other Reason"]) !!};
    const intValues = {!! json_encode($interviewDataValues ?? [1, 0, 0, 0, 0]) !!};

    window.SuperAdminDashboard.initChart('total_interview_uploads_chart', {
        type: 'bar',
        data: {
            labels: intLabels,
            datasets: [{
                data: intValues,
                backgroundColor: ['#22c55e', '#ef4444', '#ffab00', '#64748b', '#a855f7'],
                borderRadius: 4,
                borderSkipped: false,
                barPercentage: 0.55,
                categoryPercentage: 0.8
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ` ${ctx.label}: ${ctx.raw} interviews`
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    max: 2,
                    ticks: { stepSize: 0.5, font: { size: 10, family: 'Poppins' } },
                    grid: { color: '#f1f5f9' }
                },
                y: {
                    grid: { display: false },
                    ticks: { font: { size: 10.5, weight: '500', family: 'Poppins' }, color: '#475569' }
                }
            }
        }
    });

    // 9. Email Activity (Horizontal Bar if > 0)
    const emailTotal = {{ $emailSummaryData_total ?? 0 }};
    if (emailTotal > 0 && document.getElementById('total_email_chart')) {
        const emailLabels = {!! json_encode($emailSummaryDatalabels ?? ["Mass Mail","Candidate mail & CV Fwd","Interview Reminder","Notification","Accounts & Offer"]) !!};
        const emailValues = {!! json_encode($emailSummaryDataValues ?? [1124, 0, 0, 0, 0]) !!};

        window.SuperAdminDashboard.initChart('total_email_chart', {
            type: 'bar',
            data: {
                labels: emailLabels,
                datasets: [{
                    data: emailValues,
                    backgroundColor: ['#009ef7', '#22c55e', '#ffab00', '#8b5cf6', '#ec4899'],
                    borderRadius: 4,
                    borderSkipped: false,
                    barPercentage: 0.55,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { font: { size: 10, family: 'Poppins' } },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 10.5, weight: '500', family: 'Poppins' }, color: '#475569' }
                    }
                }
            }
        });
    }

    // 10. WhatsApp Activity (Horizontal Bar)
    const waTotal = {{ $whatsappSummary_total ?? 0 }};
    const waLabels = {!! json_encode($whatsappSummaryDatalabels ?? ["Custom message","Reminder message"]) !!};
    const waValues = {!! json_encode($whatsappSummaryDataValues ?? [0, 0]) !!};

    if (document.getElementById('total_whatsapp_chart')) {
        window.SuperAdminDashboard.initChart('total_whatsapp_chart', {
            type: 'bar',
            data: {
                labels: waLabels,
                datasets: [{
                    data: waValues,
                    backgroundColor: ['#22c55e', '#009ef7'],
                    borderRadius: 4,
                    borderSkipped: false,
                    barPercentage: 0.5,
                    categoryPercentage: 0.7
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 10, family: 'Poppins' } },
                        grid: { color: '#f1f5f9' }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { size: 10.5, weight: '500', family: 'Poppins' }, color: '#475569' }
                    }
                }
            }
        });
    }

    // =========================================================================
    // PHASE 2: RAM CACHE PRE-POPULATION (User RAM First Architecture)
    // =========================================================================

    const trend30Labels = {!! json_encode($jobPostsTrend30dLabels ?? $jobPostsTrendLabels ?? []) !!};
    const trend30Values = {!! json_encode($jobPostsTrend30dValues ?? $jobPostsTrendValues ?? []) !!};
    const trend7Labels = {!! json_encode($jobPostsTrend7dLabels ?? []) !!};
    const trend7Values = {!! json_encode($jobPostsTrend7dValues ?? []) !!};

    // Pre-populate initial server-rendered snapshots into RAM Cache (30s TTL)
    window.SuperAdminDashboard.setCache('subscription_overview:month', {
        labels: subLabels,
        data: subValues
    });
    window.SuperAdminDashboard.setCache('company_types:month', {
        labels: compLabels,
        data: compValues,
        total: compTotal
    });
    window.SuperAdminDashboard.setCache('company_activity:month', {
        labels: actLabels,
        data: actValues
    });
    window.SuperAdminDashboard.setCache('bench_candidate:month', {
        labels: benchLabels,
        data: benchValues,
        total: benchTotal
    });
    window.SuperAdminDashboard.setCache('job_posts_trend:month', {
        labels: trend30Labels,
        data: trend30Values,
        total: trend30Values.reduce((a, b) => a + b, 0)
    });
    window.SuperAdminDashboard.setCache('job_posts_trend:week', {
        labels: trend7Labels,
        data: trend7Values,
        total: trend7Values.reduce((a, b) => a + b, 0)
    });
    window.SuperAdminDashboard.setCache('job_status:month', {
        labels: statusLabels,
        data: statusValues
    });
    window.SuperAdminDashboard.setCache('candidate_pipeline:month', {
        labels: pipeLabels,
        data: pipeValues
    });
    window.SuperAdminDashboard.setCache('interview_status:month', {
        labels: intLabels,
        data: intValues
    });
    window.SuperAdminDashboard.setCache('email_activity:month', {
        labels: typeof emailLabels !== 'undefined' ? emailLabels : ["Mass Mail","Candidate mail & CV Fwd","Interview Reminder","Notification","Accounts & Offer"],
        data: typeof emailValues !== 'undefined' ? emailValues : [0, 0, 0, 0, 0],
        total: typeof emailTotal !== 'undefined' ? emailTotal : 0
    });
    window.SuperAdminDashboard.setCache('whatsapp_activity:month', {
        labels: waLabels,
        data: waValues,
        total: waTotal
    });

    // Map chartKeys to canvasIds
    const chartCanvasMap = {
        subscription_overview: 'subscription_overview_chart',
        company_types: 'companyCountData',
        company_activity: 'active_companies_chart',
        bench_candidate: 'bench_candidate_chart',
        job_posts_trend: 'jobs_post_chart',
        job_status: 'jobs_status_chart',
        candidate_pipeline: 'total_candidate_uploads_chart',
        interview_status: 'total_interview_uploads_chart',
        email_activity: 'total_email_chart',
        whatsapp_activity: 'total_whatsapp_chart'
    };

    const chartDisplayNames = {
        subscription_overview: 'Subscription Overview',
        company_types: 'Company Types',
        company_activity: 'Company Activity',
        bench_candidate: 'Bench Candidate Status',
        job_posts_trend: 'Job Posts Trend',
        job_status: 'Job Status',
        candidate_pipeline: 'Candidate Pipeline',
        interview_status: 'Interview Status',
        email_activity: 'Email Activity',
        whatsapp_activity: 'WhatsApp Activity'
    };

    // Server-synchronized reference dates
    const SERVER_DATE_TODAY = '{{ now()->format('Y-m-d') }}';
    const SERVER_DATE_PAST30 = '{{ now()->subDays(29)->format('Y-m-d') }}';

    // Dynamic API URL resolver supporting non-root directories (e.g. /git/JWMaincode28June2026/super-admin/dashboard)
    const getChartApiUrl = (chartKey, queryString) => {
        const basePath = window.location.pathname.replace(/\/+$/, '');
        return `${basePath}/api/chart/${encodeURIComponent(chartKey)}?${queryString}`;
    };

    // Custom Date Range State
    const customDateStore = {};
    let activeCustomTarget = null;

    const modalEl = document.getElementById('saCustomDateModal');
    const startDateInput = document.getElementById('sa_custom_start_date');
    const endDateInput = document.getElementById('sa_custom_end_date');
    const modalErrorEl = document.getElementById('sa_custom_modal_error');
    const modalApplyBtn = document.getElementById('sa_custom_modal_apply_btn');

    function openModalSafely(el) {
        if (!el) return;
        if (window.jQuery && typeof window.jQuery(el).modal === 'function') {
            window.jQuery(el).modal('show');
            return;
        }
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                let inst = (typeof bootstrap.Modal.getInstance === 'function') ? bootstrap.Modal.getInstance(el) : null;
                if (!inst) {
                    inst = new bootstrap.Modal(el);
                }
                inst.show();
                return;
            } catch (e) {}
        }
        el.classList.add('show');
        el.style.display = 'block';
        el.removeAttribute('aria-hidden');
        el.setAttribute('aria-modal', 'true');
        document.body.classList.add('modal-open');
        let backdrop = document.querySelector('.modal-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);
        }
    }

    function closeModalSafely(el) {
        if (!el) return;
        if (window.jQuery && typeof window.jQuery(el).modal === 'function') {
            window.jQuery(el).modal('hide');
            return;
        }
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            try {
                const inst = (typeof bootstrap.Modal.getInstance === 'function') ? bootstrap.Modal.getInstance(el) : null;
                if (inst) {
                    inst.hide();
                    return;
                }
            } catch (e) {}
        }
        el.classList.remove('show');
        el.style.display = 'none';
        el.setAttribute('aria-hidden', 'true');
        el.removeAttribute('aria-modal');
        document.body.classList.remove('modal-open');
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) backdrop.remove();
    }

    function showCustomModal(chartKey, group, btn) {
        activeCustomTarget = { chartKey, group, btn };
        const chartName = chartDisplayNames[chartKey] || 'Chart';
        const titleEl = document.getElementById('saCustomModalTitle');
        const subtitleEl = document.getElementById('saCustomModalSubtitle');
        if (titleEl) titleEl.textContent = `Filter ${chartName} by Date Range`;
        if (subtitleEl) subtitleEl.textContent = `Choose custom start and end dates for ${chartName}`;
        if (modalErrorEl) modalErrorEl.classList.add('d-none');

        const existing = customDateStore[chartKey];
        if (existing) {
            if (startDateInput) startDateInput.value = existing.start;
            if (endDateInput) endDateInput.value = existing.end;
        } else {
            if (endDateInput) endDateInput.value = SERVER_DATE_TODAY;
            if (startDateInput) startDateInput.value = SERVER_DATE_PAST30;
        }

        openModalSafely(modalEl);
    }

    function hideCustomModal() {
        closeModalSafely(modalEl);
    }

    // Modal dismiss controls
    if (modalEl) {
        modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', hideCustomModal);
        });
        modalEl.addEventListener('click', (e) => {
            if (e.target === modalEl) {
                hideCustomModal();
            }
        });
    }

    // Custom Date Apply Button Handler
    if (modalApplyBtn) {
        modalApplyBtn.addEventListener('click', () => {
            if (!activeCustomTarget) return;

            const start = startDateInput ? startDateInput.value : '';
            const end = endDateInput ? endDateInput.value : '';

            if (!start || !end || start > end) {
                if (modalErrorEl) modalErrorEl.classList.remove('d-none');
                return;
            }

            if (modalErrorEl) modalErrorEl.classList.add('d-none');
            const { chartKey, group, btn } = activeCustomTarget;
            customDateStore[chartKey] = { start, end };
            hideCustomModal();

            // Update button active state
            group.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            window.SuperAdminDashboard.activeFilters[chartKey] = 'custom';

            // Check RAM cache
            const cacheKey = `${chartKey}:custom:${start}_${end}`;
            const cached = window.SuperAdminDashboard.getCache(cacheKey);
            const canvasId = chartCanvasMap[chartKey];

            if (cached && canvasId) {
                window.SuperAdminDashboard.updateChartData(
                    canvasId,
                    cached.labels || null,
                    cached.data || null,
                    cached.total !== undefined ? cached.total : null
                );
                return;
            }

            if (!canvasId) return;

            const seq = window.SuperAdminDashboard.nextSeq(chartKey);
            const abortController = window.SuperAdminDashboard.getAbortController(chartKey);
            const url = getChartApiUrl(
                chartKey,
                `period=custom&start_date=${encodeURIComponent(start)}&end_date=${encodeURIComponent(end)}`
            );

            window.SuperAdminDashboard.fetchWithRetry(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: abortController.signal
            })
            .then(res => {
                if (!window.SuperAdminDashboard.isLatestSeq(chartKey, seq)) return;
                if (!res || !res.success) return;

                window.SuperAdminDashboard.setCache(cacheKey, {
                    labels: res.labels,
                    data: res.data,
                    total: res.total
                });

                window.SuperAdminDashboard.updateChartData(
                    canvasId,
                    res.labels,
                    res.data,
                    res.total !== undefined ? res.total : null
                );
            })
            .catch(err => {
                if (err.name === 'AbortError') return;
                console.warn(`[SuperAdminDashboard] Filter fetch warning for ${chartKey}:`, err.message || err);
            });
        });
    }

    // =========================================================================
    // PERIOD FILTER BUTTON TOGGLE INTERACTION (Client RAM First)
    // =========================================================================

    document.querySelectorAll('.sa-filter-btn-group').forEach((group) => {
        group.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn');
            if (!btn) return;

            const chartKey = group.getAttribute('data-chart');
            const period = btn.getAttribute('data-period');
            if (!chartKey || !period) return;

            if (period === 'custom') {
                showCustomModal(chartKey, group, btn);
                return;
            }

            if (btn.classList.contains('active')) return;

            // Toggle active pill styling
            group.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Update tracked state
            window.SuperAdminDashboard.activeFilters[chartKey] = period;

            // Check Client-Side RAM Cache (0 ms instant transition)
            const cacheKey = `${chartKey}:${period}`;
            const cached = window.SuperAdminDashboard.getCache(cacheKey);
            const canvasId = chartCanvasMap[chartKey];

            if (cached && canvasId) {
                window.SuperAdminDashboard.updateChartData(
                    canvasId,
                    cached.labels || null,
                    cached.data || null,
                    cached.total !== undefined ? cached.total : null
                );
                return;
            }

            // On Cache Miss: Dispatch lightweight JSON fetch with dynamic URL resolution
            if (!canvasId) return;

            const seq = window.SuperAdminDashboard.nextSeq(chartKey);
            const abortController = window.SuperAdminDashboard.getAbortController(chartKey);
            const url = getChartApiUrl(chartKey, `period=${encodeURIComponent(period)}`);

            window.SuperAdminDashboard.fetchWithRetry(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: abortController.signal
            })
            .then(res => {
                if (!window.SuperAdminDashboard.isLatestSeq(chartKey, seq)) return;
                if (!res || !res.success) return;

                // Cache in User RAM (30s TTL)
                window.SuperAdminDashboard.setCache(cacheKey, {
                    labels: res.labels,
                    data: res.data,
                    total: res.total
                });

                // In-place dynamic chart update
                window.SuperAdminDashboard.updateChartData(
                    canvasId,
                    res.labels,
                    res.data,
                    res.total !== undefined ? res.total : null
                );
            })
            .catch(err => {
                if (err.name === 'AbortError') return; // Clean cancellation
                console.warn(`[SuperAdminDashboard] Circuit breaker/fetch warning for ${chartKey}:`, err.message || err);
            });
        });
    });

})();
</script>
@endpush
