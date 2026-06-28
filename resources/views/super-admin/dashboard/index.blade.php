@extends('layouts.superadmin_layout')
<script type="text/javascript" src="{{ asset('assets/charts/loader.js') }}{{ TM_VERSION }}"></script>
<style>
    #kt_body {
        overflow-x: hidden;
    }
</style>

@section('content')
    @include('sections.super-admin.toolbar')

    <div class="row">
        <div class="post d-flex flex-column-fluid mt-5" id="kt_post">
            <div id="kt_content_container" class="container-fluid">
                
                <!--Graph section start-->
                    <div class="row gy-5 g-xl-8">
                        <div class="col-md-6">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $oprationalPlanTotal }}</span>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Oprational Subscription</span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->
                                <div class="card card-flush mb-5 mb-xl-10">
                                    <div id="" class="w-100" style="">
                                        <div id="" 
                                            style="">
                                            <canvas id="oprational_plan_chart" height="170"></canvas>
                                        </div>
                                    </div>

                                </div>
                        </div>
                        <div class="col-md-6">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $salesPlanTotal }}</span>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Sales Subscription</span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->
                                <div class="card card-flush mb-5 mb-xl-10">
                                    <div id="" class="w-100" style="">
                                        <div id="" 
                                            style="">
                                            <canvas id="sales_plan_chart" height="170"></canvas>
                                        </div>
                                    </div>

                                </div>

                        </div>
                    </div>
                    <div class="row gy-5 g-xl-8">
                        <!--begin::Col-->
                        <div class="col-md-4">
                            <!--begin::Card widget 4-->
                            <div class="card card-flush mb-5 mb-xl-10">
                                
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $companies_total }}</span>
                                            <!--end::Amount-->

                                            <!--begin::Badge-->
                                            {{-- <span class="badge badge-light-success fs-base">                                
                                                <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i> 
                                                2.2%
                                            </span>             --}}
                                            <!--end::Badge--> 
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">All Companies</span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->

                                <div id="" class="w-100" style="">
                                    <div id="" 
                                        style="">
                                        <canvas id="companyCountData" height="170"></canvas>
                                    </div>
                                </div>
                            </div>
                            <!--end::Card widget 4--> 
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-4">
                            <div class="card card-flush mb-5 mb-xl-10">
                                
                                
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $activecompanies }}</span>
                                            <!--end::Amount-->

                                            <!--begin::Badge-->
                                            {{-- <span class="badge badge-light-success fs-base">                                
                                                <i class="ki-outline ki-arrow-up fs-5 text-success ms-n1"></i> 
                                                2.2%
                                            </span>             --}}
                                            <!--end::Badge--> 
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">All Active Companies</span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->
                                <div id="kt_card_widget_6_chart" class="w-100" style="">
                                    <div id="" 
                                        style="">
                                        <canvas id="active_companies_chart" height="170"></canvas>
                                    </div>
                                </div>


                            </div>
                        </div>
                        <!--end::Col-->
                        <!--begin::Col-->
                        <div class="col-md-4">

                            <div class="card card-flush mb-5 mb-xl-10">
                                
                            <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $bench_total }}</span>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">All Bench Candidates</span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                            <!--end::Header-->

                            <div id="" class="w-100" style="">
                                <div id="" 
                                    style="">
                                    <canvas id="bench_candidate_chart" height="170"></canvas>
                                </div>
                            </div>
                                
                            </div>

                        </div>
                        <!--end::Col-->
                    </div>
                    <div class="row gy-5 g-xl-8">
                        <div class="col-md-4">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $jobPostTotal }}</span>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Job Post</span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->
                                <div class="card card-flush mb-5 mb-xl-10">
                                    <div id="" class="w-100" style="">
                                        <div id="" 
                                            style="">
                                            <canvas id="jobs_post_chart" height="170"></canvas>
                                        </div>
                                    </div>

                                </div>
                        </div>
                        <div class="col-md-4">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $jobsStatusTotal }}</span>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Jobs Status</span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->
                                <div class="card card-flush mb-5 mb-xl-10">
                                    <div id="" class="w-100" style="">
                                        <div id="" 
                                            style="">
                                            <canvas id="jobs_status_chart" height="170"></canvas>
                                        </div>
                                    </div>

                                </div>

                        </div>
                        <div class="col-md-4">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $recruiterStatsTotal }}</span>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Active Recruiters</span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->
                                <div class="card card-flush mb-5 mb-xl-10">
                                    <div id="" class="w-100" style="">
                                        <div id="" 
                                            style="">
                                            <canvas id="active_recruiters_chart" height="170"></canvas>
                                        </div>
                                    </div>

                                </div>
                        </div>
                    </div>
                    <div class="row gy-5 g-xl-8">                        
                        <div class="col-md-12">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $candidateUploadTotal }}</span>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Upload Candidates (Last 30 days)</span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->
                                <div class="card card-flush mb-5 mb-xl-10">
                                    <div id="" class="w-100" style="">
                                        <div id="" 
                                            style="">
                                            <canvas id="candidate_uploads_chart" height="170"></canvas>
                                        </div>
                                    </div>

                                </div>
                        </div>
                    </div>
                     <div class="row gy-5 g-xl-8">                        
                        <div class="col-md-6">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $allcandidates_total }}</span>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Upload Candidates </span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->
                                <div class="card card-flush mb-5 mb-xl-10">
                                    <div id="" class="w-100" style="">
                                        <div id="" style="width: 100%; height: 330px;">
                                            <canvas id="total_candidate_uploads_chart"></canvas>
                                        </div>
                                    </div>

                                </div>
                        </div>
                        <div class="col-md-6">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $interviewData_total }}</span>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Interview Candidates </span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->
                                <div class="card card-flush mb-5 mb-xl-10">
                                    <div id="" class="w-100" style="">
                                        <div id="" style="width: 100%; height: 330px;">
                                            <canvas id="total_interview_uploads_chart"></canvas>
                                        </div>
                                    </div>

                                </div>
                        </div>
                    </div>
                    
                     <div class="row gy-5 g-xl-8">                        
                        <div class="col-md-6">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $emailSummaryData_total }}</span>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Email </span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->
                                <div class="card card-flush mb-5 mb-xl-10">
                                    <div id="" class="w-100" style="">
                                        <div id="" style="width: 100%; height: 230px;">
                                            <canvas id="total_email_chart"></canvas>
                                        </div>
                                    </div>

                                </div>
                        </div>
                        <div class="col-md-6">
                                <!--begin::Header-->
                                <div class="card-header pt-5">
                                    <!--begin::Title-->
                                    <div class="card-title d-flex flex-column">   
                                        <!--begin::Info--> 
                                        <div class="d-flex align-items-center">
                                            <!--begin::Currency-->
                                            <span class="fs-4 fw-semibold text-gray-500 me-1 align-self-start"></span>
                                            <!--end::Currency-->

                                            <!--begin::Amount-->
                                            <span class="fs-2hx fw-bold text-gray-900 me-2 lh-1 ls-n2">{{ $whatsappSummary_total }}</span>
                                            <!--end::Amount-->
                                        </div>
                                        <!--end::Info--> 

                                        <!--begin::Subtitle-->
                                        <span class="text-gray-500 pt-1 fw-semibold fs-6">Total Whatsapp Message </span>
                                        <!--end::Subtitle--> 
                                    </div>
                                    <!--end::Title-->      
                                </div>
                                <!--end::Header-->
                                <div class="card card-flush mb-5 mb-xl-10">
                                    <div id="" class="w-100" style="">
                                        <div id="" style="width: 100%; height: 230px;">
                                            <canvas id="total_whatsapp_chart"></canvas>
                                        </div>
                                    </div>

                                </div>
                        </div>
                    </div>
                <!--Graph section end-->
                
            </div>
        </div>
    </div>
@endsection


@push('footer-script')
    <script>

        const showDataLabels = {
            id: 'showDataLabels',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                chart.data.datasets.forEach((dataset, i) => {
                    const meta = chart.getDatasetMeta(i);
                    meta.data.forEach((bar, index) => {
                        const value = dataset.data[index];
                        ctx.save();
                        ctx.fillStyle = 'black';
                        ctx.font = 'bold 12px sans-serif';
                        ctx.textAlign = 'center';
                        ctx.fillText(value, bar.x, bar.y - 1.5);
                        ctx.restore();
                    });
                });
            }
        };

        const showDataLabelsvertical = {
            id: 'showDataLabelsvertical',
            afterDatasetsDraw(chart) {
                const { ctx } = chart;
                chart.data.datasets.forEach((dataset, i) => {
                    const meta = chart.getDatasetMeta(i);
                    meta.data.forEach((bar, index) => {
                        const value = dataset.data[index];
                        ctx.save();
                        ctx.fillStyle = 'black';
                        ctx.font = 'bold 12px sans-serif';
                        ctx.textAlign = 'left';  // align left for horizontal bars
                        ctx.textBaseline = 'middle';

                        // place text at the end of bar (right side)
                        ctx.fillText(value, bar.x + 1, bar.y);
                        ctx.restore();
                    });
                });
            }
        };

        const ctx_job_div = document.getElementById('companyCountData').getContext('2d');
        new Chart(ctx_job_div, {
            type: 'pie',
            data: {
                labels: {!! json_encode($companies_labels) !!},
                datasets: [{
                    label: 'Total Companies',
                    data: {!! json_encode($companiesValues) !!},
                    backgroundColor: [
                        '#22c55e', // Staffing Company'
                        '#ffab00', // Service Company'
                        '#ff0202', // Product Company'
                        '#ff4098' // Other Company'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 15,
                    offset: 5,
                    cutout: '0%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        align: 'center',
                        labels: {
                            font: {
                                size: 12,
                                weight: 'bold',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 12,
                            boxHeight: 12,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label} (${value} - ${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: '#fff',
                                            lineWidth: 2,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} Companies (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        color: '#fff',
                        backgroundColor: 'rgba(0, 0, 0, 0.6)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value, context) => {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${value}\n(${percentage}%)`;
                        },
                        align: 'center',
                        offset: 8
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 5,
                        bottom: 5,
                        left: 5,
                        right: 5
                    }
                }
            }
        });

        const active_companies_chart = document.getElementById('active_companies_chart').getContext('2d');
        new Chart(active_companies_chart, {
            type: 'bar',
            data: {
                labels: {!! json_encode($activecompanylabels) !!},
                datasets: [{
                    label: 'Total Active Companies',
                    data: {!! json_encode($activecompanyValues) !!},
                    backgroundColor: [
                        '#2196f3', // Blue 
                        '#2196f3', // Blue
                        '#2196f3'  // Blue 
                    ],
                    borderRadius: 5,
                    borderSkipped: false,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0 // show integer values only
                        }
                    }
                }
            },
            plugins: [showDataLabels] // ✅ custom labels plugin
        });

        const bench_candidate_chart = document.getElementById('bench_candidate_chart').getContext('2d');
        new Chart(bench_candidate_chart, {
            type: 'pie',
            data: {
                labels: {!! json_encode($bench_labels) !!},
                datasets: [{
                    label: 'Total Bench Candidates',
                    data: {!! json_encode($benchcandidateValues) !!},
                    backgroundColor: [
                        '#565656',  // Level 1 - 
                        '#2896ff',  // Level 2 - 
                        '#06c732',  // Level 3 - 
                        '#ffab00',  // Level 4 - 
                        '#ff4098'   // Level 5 - 
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 15,
                    offset: 5,
                    cutout: '0%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        align: 'center',
                        labels: {
                            font: {
                                size: 12,
                                weight: 'bold',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 12,
                            boxHeight: 12,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label} (${value} - ${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: '#fff',
                                            lineWidth: 2,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} candidates (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        color: '#fff',
                        backgroundColor: 'rgba(0, 0, 0, 0.6)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value, context) => {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${value}\n(${percentage}%)`;
                        },
                        align: 'center',
                        offset: 8
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 5,
                        bottom: 5,
                        left: 5,
                        right: 5
                    }
                }
            }
        });

        const jobs_post_chart = document.getElementById('jobs_post_chart').getContext('2d');
        new Chart(jobs_post_chart, {
            type: 'bar',
            data: {
                labels: {!! json_encode($jobPostLabels) !!},
                datasets: [{
                    label: 'Total Job Post',
                    data: {!! json_encode($jobPostValues) !!},
                    backgroundColor: [
                        '#2196f3', // Blue
                        '#2196f3', // Blue
                        '#2196f3'  // Blue
                    ],
                    borderRadius: 5,
                    borderSkipped: false,
                    barPercentage: 0.9,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                indexAxis: 'y', // Makes it horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Total Jobs: ${context.parsed.x}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'right',
                        color: '#333',
                        backgroundColor: 'rgba(255, 255, 255, 0.8)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value) => value
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                size: 11,
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            }
                        },
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 8
                        },
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 8,
                        borderSkipped: false
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 10,
                        right: 20
                    }
                }
            },
            plugins: [showDataLabels] // ✅ custom labels plugin
        });

        const jobs_status_chart = document.getElementById('jobs_status_chart').getContext('2d');
        new Chart(jobs_status_chart, {
            type: 'bar',
            data: {
                labels: {!! json_encode($jobsStatusLabels) !!},
                datasets: [{
                    label: 'Total Job Post',
                    data: {!! json_encode($jobsStatusValues) !!},
                    backgroundColor: [
                        '#06c732', 
                        '#ffab00',
                        '#ffe2e5' 
                    ],
                    borderRadius: 5,
                    borderSkipped: false,
                    barPercentage: 0.9,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                indexAxis: 'y', // Makes it horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Total Jobs: ${context.parsed.x}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'right',
                        color: '#333',
                        backgroundColor: 'rgba(255, 255, 255, 0.8)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value) => value
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                size: 11,
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            }
                        },
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 8
                        },
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 8,
                        borderSkipped: false
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 10,
                        right: 20
                    }
                }
            },
            plugins: [showDataLabels] // ✅ custom labels plugin
        });

        const oprational_plan_chart = document.getElementById('oprational_plan_chart').getContext('2d');
        new Chart(oprational_plan_chart, {
            type: 'bar',
            data: {
                labels: {!! json_encode($oprationalPlanLabels) !!},
                datasets: [{
                    label: 'Total Subscription',
                    data: {!! json_encode($oprationalPlanValues) !!},
                    backgroundColor: [
                        '#2196f3', // Blue
                        '#2196f3', // Blue
                    ],
                    borderRadius: 5,
                    borderSkipped: false,
                    barPercentage: 0.9,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                indexAxis: 'y', // Makes it horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Total Subscription: ${context.parsed.x}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'right',
                        color: '#333',
                        backgroundColor: 'rgba(255, 255, 255, 0.8)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value) => value
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                size: 11,
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            }
                        },
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 8
                        },
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 8,
                        borderSkipped: false
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 10,
                        right: 20
                    }
                }
            },
            plugins: [showDataLabels] // ✅ custom labels plugin
        });

        const sales_plan_chart = document.getElementById('sales_plan_chart').getContext('2d');
        new Chart(sales_plan_chart, {
            type: 'bar',
            data: {
                labels: {!! json_encode($salesPlanLabels) !!},
                datasets: [{
                    label: 'Total Subscription',
                    data: {!! json_encode($salesPlanValues) !!},
                    backgroundColor: [
                        '#2196f3', // Blue
                        '#2196f3', // Blue
                    ],
                    borderRadius: 5,
                    borderSkipped: false,
                    barPercentage: 0.9,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                indexAxis: 'y', // Makes it horizontal
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Total Subscription: ${context.parsed.x}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'right',
                        color: '#333',
                        backgroundColor: 'rgba(255, 255, 255, 0.8)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value) => value
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                size: 11,
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            }
                        },
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 8
                        },
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 8,
                        borderSkipped: false
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 10,
                        right: 20
                    }
                }
            },
            plugins: [showDataLabels] // ✅ custom labels plugin
        });

        const active_recruiters_chart = document.getElementById('active_recruiters_chart').getContext('2d');
        new Chart(active_recruiters_chart, {
            type: 'pie',
            data: {
                labels: {!! json_encode($recruiterStatsLabels) !!},
                datasets: [{
                    label: 'Total Active Recruiters',
                    data: {!! json_encode($recruiterStatsValues) !!},
                    backgroundColor: [
                        '#565656',  // Level 1 - 
                        '#2896ff',  // Level 2 - 
                        '#06c732',  // Level 3 -  
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 15,
                    offset: 5,
                    cutout: '0%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        align: 'center',
                        labels: {
                            font: {
                                size: 12,
                                weight: 'bold',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 12,
                            boxHeight: 12,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label} (${value} - ${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: '#fff',
                                            lineWidth: 2,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} Recruiters (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        color: '#fff',
                        backgroundColor: 'rgba(0, 0, 0, 0.6)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value, context) => {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${value}\n(${percentage}%)`;
                        },
                        align: 'center',
                        offset: 8
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 5,
                        bottom: 5,
                        left: 5,
                        right: 5
                    }
                }
            }
        });

        const candidate_uploads_chart = document.getElementById('candidate_uploads_chart').getContext('2d');
        new Chart(candidate_uploads_chart, {
            type: 'bar',
            data: {
                labels: {!! json_encode($candidateUploadLabels) !!},
                datasets: [{
                    label: 'Total Candidates',
                    data: {!! json_encode($candidateUploadValues) !!},
                    backgroundColor: [
                        '#2196f3', // Blue
                    ],
                    borderRadius: 5,
                    borderSkipped: false,
                    barPercentage: 0.9,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                indexAxis: 'x', // Makes it vartical
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Total Candidates: ${context.parsed.y}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'right',
                        color: '#333',
                        backgroundColor: 'rgba(255, 255, 255, 0.8)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value) => value
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                size: 11,
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            }
                        },
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 8
                        },
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 8,
                        borderSkipped: false
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 10,
                        right: 20
                    }
                }
            },
            plugins: [showDataLabels] // ✅ custom labels plugin
        });

        const total_candidate_uploads_chart = document.getElementById('total_candidate_uploads_chart').getContext('2d');
        new Chart(total_candidate_uploads_chart, {
            type: 'bar',
            data: {
                labels: {!! json_encode($allcandidateslabels) !!},
                datasets: [{
                    label: 'Total Candidates',
                    data: {!! json_encode($allcandidatesValues) !!},
                    backgroundColor: [
                        '#8b6727',
                        '#000000',
                        '#c000ff',
                        '#2896ff',
                        '#ff4098',
                        '#ffab00',
                        '#06c732',
                        '#ff0202',
                    ],
                    borderRadius: 5,
                    borderSkipped: false,
                    barPercentage: 0.9,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                indexAxis: 'y', // Makes it vartical
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Total Candidates: ${context.parsed.x}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'right',
                        color: '#333',
                        backgroundColor: 'rgba(255, 255, 255, 0.8)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value) => value
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                size: 11,
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            }
                        },
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 8
                        },
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 8,
                        borderSkipped: false
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 10,
                        right: 20
                    }
                }
            },
            plugins: [showDataLabels] // ✅ custom labels plugin
        });

        const total_interview_uploads_chart = document.getElementById('total_interview_uploads_chart').getContext('2d');
        new Chart(total_interview_uploads_chart, {
            type: 'bar',
            data: {
                labels: {!! json_encode($interviewDatalabels) !!},
                datasets: [{
                    label: 'Total Interview',
                    data: {!! json_encode($interviewDataValues) !!},
                    backgroundColor: [
                        '#06c732',
                        '#ff4098',
                        '#ffab00',
                        '#ff0202',
                        '#000000',
                    ],
                    borderRadius: 5,
                    borderSkipped: false,
                    barPercentage: 0.9,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                indexAxis: 'y', // Makes it vartical
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Total Interview: ${context.parsed.x}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'right',
                        color: '#333',
                        backgroundColor: 'rgba(255, 255, 255, 0.8)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value) => value
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                size: 11,
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            }
                        },
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 8
                        },
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 8,
                        borderSkipped: false
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 10,
                        right: 20
                    }
                }
            },
            plugins: [showDataLabels] // ✅ custom labels plugin
        });

        const total_email_chart = document.getElementById('total_email_chart').getContext('2d');
        new Chart(total_email_chart, {
            type: 'bar',
            data: {
                labels: {!! json_encode($emailSummaryDatalabels) !!},
                datasets: [{
                    label: 'Total Email',
                    data: {!! json_encode($emailSummaryDataValues) !!},
                    backgroundColor: [
                        '#8b6727',
                        '#000000',
                        '#c000ff',
                        '#2896ff',
                        '#ff4098',
                    ],
                    borderRadius: 5,
                    borderSkipped: false,
                    barPercentage: 0.9,
                    categoryPercentage: 0.8
                }]
            },
            options: {
                indexAxis: 'y', // Makes it vartical
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return `Total Email: ${context.parsed.x}`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        anchor: 'end',
                        align: 'right',
                        color: '#333',
                        backgroundColor: 'rgba(255, 255, 255, 0.8)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value) => value
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                size: 11,
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            }
                        },
                    },
                    y: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 8
                        },
                    }
                },
                elements: {
                    bar: {
                        borderRadius: 8,
                        borderSkipped: false
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 20,
                        bottom: 20,
                        left: 10,
                        right: 20
                    }
                }
            },
            plugins: [showDataLabels] // ✅ custom labels plugin
        });

        const total_whatsapp_chart = document.getElementById('total_whatsapp_chart').getContext('2d');
        new Chart(total_whatsapp_chart, {
            type: 'pie',
            data: {
                labels: {!! json_encode($whatsappSummaryDatalabels) !!},
                datasets: [{
                    label: 'Total Whatsapp Message',
                    data: {!! json_encode($whatsappSummaryDataValues) !!},
                    backgroundColor: [
                        '#565656',  // Level 1 - 
                        '#2896ff',  // Level 2 -  
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 15,
                    offset: 5,
                    cutout: '0%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'right',
                        align: 'center',
                        labels: {
                            font: {
                                size: 12,
                                weight: 'bold',
                                family: "'Segoe UI', 'Roboto', sans-serif"
                            },
                            padding: 12,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 12,
                            boxHeight: 12,
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return {
                                            text: `${label} (${value} - ${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            strokeStyle: '#fff',
                                            lineWidth: 2,
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                                return [];
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        titleFont: {
                            size: 13,
                            weight: 'bold',
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        bodyFont: {
                            size: 12,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return `${label}: ${value} Whatsapp Message (${percentage}%)`;
                            }
                        }
                    },
                    datalabels: {
                        display: true,
                        color: '#fff',
                        backgroundColor: 'rgba(0, 0, 0, 0.6)',
                        borderRadius: 4,
                        padding: {
                            left: 6,
                            right: 6,
                            top: 4,
                            bottom: 4
                        },
                        font: {
                            weight: 'bold',
                            size: 11,
                            family: "'Segoe UI', 'Roboto', sans-serif"
                        },
                        formatter: (value, context) => {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${value}\n(${percentage}%)`;
                        },
                        align: 'center',
                        offset: 8
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                layout: {
                    padding: {
                        top: 5,
                        bottom: 5,
                        left: 5,
                        right: 5
                    }
                }
            }
        });


        
        

        

    </script>
@endpush
