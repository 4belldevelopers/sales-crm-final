@extends('layouts.service_company_layout')
@php
    // Display helpers (mirror the Inhouse page formatting).
    $curSym = ['INR' => '₹', 'USD' => '$', 'EUR' => '€', 'GBP' => '£', 'JPY' => '¥'][$currencyCode ?? 'INR'] ?? '₹';
    $fmtMoney = function ($n) {
        $n = (float) $n;
        $neg = $n < 0 ? '-' : '';
        $a = abs($n);
        if ($a >= 10000000) return $neg . round($a / 10000000, 2) . 'Cr';
        if ($a >= 100000) return $neg . round($a / 100000, 2) . 'L';
        if ($a >= 1000) return $neg . round($a / 1000, 1) . 'K';
        return $neg . round($a);
    };
    $ag = $benchAging ?? (object) ['b1' => 0, 'b2' => 0, 'b3' => 0, 'b4' => 0];
    $agTotal = $ag->b1 + $ag->b2 + $ag->b3 + $ag->b4;
    $pct = fn ($v) => $agTotal > 0 ? round(($v / $agTotal) * 100) : 0;

    $dl = $deltas ?? (object) ['cost' => 0, 'profit' => 0];
    $deltaChip = function ($v, $invert = false) {
        $up = $v >= 0;
        $good = $invert ? !$up : $up;
        return '<span class="kpi-delta ' . ($good ? 'up' : 'down') . '"><i class="fa fa-arrow-' . ($up ? 'up' : 'down') . '"></i> ' .
            abs($v) . '% <span class="kpi-vs">vs last month</span></span>';
    };
@endphp

@push('head-script')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
    {{-- ApexCharts (not bundled in Metronic global plugins) --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.min.js"></script>
    <style>
        /* ===== Staffing Dashboard (white / Poppins / max 12px) ===== */
        .staff-dash, .staff-dash * { font-family: 'Poppins', sans-serif !important; font-size: 12px; }
        .staff-dash { color: #3f4254; }
        .staff-dash .tiny { font-size: 10px !important; }
        .staff-dash .money { font-size: 20px !important; font-weight: 700; color: #181c32; }
        .staff-dash .jw-card { background: #fff; border: 1px solid #eef0f4; border-radius: 12px; }
        .staff-dash .jw-sub { background: #fff; border: 1px solid #f0f1f5; border-radius: 8px; }

        .staff-dash .chart-title { font-size: 13px !important; font-weight: 600; color: #181c32; }
        .staff-dash .aging-legend li { display: flex; align-items: center; justify-content: space-between; padding: 4px 0; font-size: 12px !important; }
        .staff-dash .dot { width: 9px; height: 9px; border-radius: 2px; display: inline-block; margin-right: 7px; }
        .staff-dash .chart-range { border: 1px solid #eef0f4; background: #fff; border-radius: 7px; font-size: 10px !important; padding: 4px 9px; color: #7e8299; height: 27px; }

        .staff-dash .kpi-delta { display: inline-flex; align-items: center; gap: 3px; font-size: 10px !important; font-weight: 600; }
        .staff-dash .kpi-delta i { font-size: 8px !important; }
        .staff-dash .up { color: #17c653; } .staff-dash .down { color: #f8285a; }
        .staff-dash .kpi-vs { color: #a1a5b7; font-weight: 500; }

        .staff-dash .stat-sub { display: flex; align-items: center; gap: 9px; padding: 10px 12px; }
        .staff-dash .stat-sub .ico { width: 30px; height: 30px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
        .staff-dash .stat-sub .ico i { font-size: 12px !important; }
        .staff-dash .stat-sub .lbl { font-size: 10.5px !important; color: #7e8299; }
        .staff-dash .stat-sub .val { font-size: 15px !important; font-weight: 700; color: #181c32; }

        .sc-primary { background: #eef3ff; color: #3e97ff; }
        .sc-success { background: #e8fff3; color: #17c653; }
        .sc-danger  { background: #ffeef1; color: #f8285a; }

        @media only screen and (max-width: 1440px) {
            .staff-dash .money { font-size: 17px !important; }
        }
    </style>
@endpush

@section('content')
    <div class="staff-dash content d-flex flex-column flex-column-fluid pt-3" id="kt_content">
        <div class="post d-flex flex-column-fluid" id="kt_post">
            <div id="kt_content_container" class="container-fluid px-3">

                <!-- Header -->
                <div class="d-flex flex-column mb-3">
                    <h5 class="text-dark fw-bolder my-0" style="font-size:16px !important;">Staffing Dashboard</h5>
                    <span class="tiny text-muted">Bench aging, cost and profitability at a glance.</span>
                </div>

                @if (in_array('view_bench', $userPermissions))
                    <div class="row g-3">
                        <!-- Bench Aging donut -->
                        <div class="col-lg-4">
                            <div class="jw-card p-3 h-100 d-flex flex-column">
                                <div class="chart-title mb-2">Bench Aging Overview</div>
                                <div class="d-flex align-items-center">
                                    <div id="agingChart" style="width:160px;"></div>
                                    <ul class="aging-legend list-unstyled mb-0 flex-grow-1 ms-2">
                                        <li><span><span class="dot" style="background:#17c653"></span>0 – 15 Days</span><span class="tiny fw-bold">{{ $ag->b1 }} ({{ $pct($ag->b1) }}%)</span></li>
                                        <li><span><span class="dot" style="background:#f6a609"></span>15 – 30 Days</span><span class="tiny fw-bold">{{ $ag->b2 }} ({{ $pct($ag->b2) }}%)</span></li>
                                        <li><span><span class="dot" style="background:#f6903f"></span>30 – 45 Days</span><span class="tiny fw-bold">{{ $ag->b3 }} ({{ $pct($ag->b3) }}%)</span></li>
                                        <li><span><span class="dot" style="background:#f8285a"></span>45+ Days</span><span class="tiny fw-bold">{{ $ag->b4 }} ({{ $pct($ag->b4) }}%)</span></li>
                                    </ul>
                                </div>
                                <div class="row g-2 mt-auto pt-3">
                                    <div class="col-6">
                                        <div class="jw-sub stat-sub">
                                            <span class="ico sc-primary"><i class="fa fa-clock"></i></span>
                                            <div><div class="lbl">Avg Bench Age</div><div class="val">{{ $avgBenchAge ?? 0 }} Days</div></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="jw-sub stat-sub">
                                            <span class="ico sc-danger"><i class="fa fa-triangle-exclamation"></i></span>
                                            <div><div class="lbl">Longest Bench Age</div><div class="val">{{ $longestBenchAge ?? 0 }} Days</div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bench Cost / Loss chart -->
                        <div class="col-lg-4">
                            <div class="jw-card p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="chart-title">Bench Cost This Month (Loss)</div>
                                    <select class="chart-range"><option>Last 30 Days</option></select>
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <div class="money text-danger">{{ $curSym }}{{ $fmtMoney($benchCost ?? 0) }}</div>
                                    {!! $deltaChip($dl->cost, true) !!}
                                </div>
                                <div id="lossChart"></div>
                            </div>
                        </div>

                        <!-- Gross Profit chart -->
                        <div class="col-lg-4">
                            <div class="jw-card p-3 h-100">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="chart-title">Gross Profit This Month</div>
                                    <select class="chart-range"><option>Last 30 Days</option></select>
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <div class="money {{ ($grossProfit ?? 0) < 0 ? 'text-danger' : 'text-success' }}">{{ $curSym }}{{ $fmtMoney($grossProfit ?? 0) }}</div>
                                    {!! $deltaChip($dl->profit) !!}
                                </div>
                                <div id="profitChart"></div>
                                <div class="row g-2 mt-1">
                                    <div class="col-6">
                                        <div class="jw-sub stat-sub">
                                            <span class="ico sc-success"><i class="fa fa-percent"></i></span>
                                            <div><div class="lbl">Profit Margin</div><div class="val text-success">{{ $profitMargin ?? 0 }}%</div></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="jw-sub stat-sub">
                                            <span class="ico sc-primary"><i class="fa fa-arrow-trend-up"></i></span>
                                            <div><div class="lbl">Bench ROI</div><div class="val">{{ $benchROI ?? 0 }}x</div></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('footer-script')
    <script>
        (function initStaffingCharts() {
            if (typeof ApexCharts === 'undefined') { return setTimeout(initStaffingCharts, 300); }

            var labels = @json($chartLabels ?? []);
            var lossSeries = @json($lossSeries ?? []);
            var profitSeries = @json($profitSeries ?? []);
            var aging = [{{ $ag->b1 }}, {{ $ag->b2 }}, {{ $ag->b3 }}, {{ $ag->b4 }}];

            // Donut — Bench Aging
            if (document.querySelector('#agingChart')) {
                new ApexCharts(document.querySelector('#agingChart'), {
                    chart: { type: 'donut', height: 160 },
                    series: aging.some(v => v > 0) ? aging : [1, 0, 0, 0],
                    labels: ['0-15 Days', '15-30 Days', '30-45 Days', '45+ Days'],
                    colors: ['#17c653', '#f6a609', '#f6903f', '#f8285a'],
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    plotOptions: { pie: { donut: { size: '68%', labels: {
                        show: true,
                        total: { show: true, label: 'On Bench', fontSize: '11px', fontFamily: 'Poppins',
                                 formatter: function() { return {{ (int) ($benchCount ?? 0) }}; } },
                        value: { fontSize: '17px', fontFamily: 'Poppins', fontWeight: 700 },
                        name: { fontSize: '10px', fontFamily: 'Poppins' }
                    } } } },
                    tooltip: { style: { fontSize: '11px', fontFamily: 'Poppins' } },
                    stroke: { width: 2 }
                }).render();
            }

            // Line — Bench Cost (Loss)
            if (document.querySelector('#lossChart')) {
                new ApexCharts(document.querySelector('#lossChart'), {
                    chart: { type: 'line', height: 160, toolbar: { show: false }, fontFamily: 'Poppins' },
                    series: [{ name: 'Loss', data: lossSeries }],
                    colors: ['#f8285a'],
                    stroke: { curve: 'smooth', width: 2 },
                    xaxis: { categories: labels, labels: { rotate: 0, hideOverlappingLabels: true, style: { fontSize: '9px', fontFamily: 'Poppins' } }, tickAmount: 6 },
                    yaxis: { labels: { style: { fontSize: '9px', fontFamily: 'Poppins' }, formatter: fmtAxis } },
                    grid: { borderColor: '#f4f5f8' },
                    dataLabels: { enabled: false },
                    tooltip: { style: { fontSize: '11px', fontFamily: 'Poppins' }, y: { formatter: fmtAxis } }
                }).render();
            }

            // Area — Gross Profit
            if (document.querySelector('#profitChart')) {
                new ApexCharts(document.querySelector('#profitChart'), {
                    chart: { type: 'area', height: 160, toolbar: { show: false }, fontFamily: 'Poppins' },
                    series: [{ name: 'Profit', data: profitSeries }],
                    colors: ['#17c653'],
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.02 } },
                    stroke: { curve: 'smooth', width: 2 },
                    xaxis: { categories: labels, labels: { rotate: 0, hideOverlappingLabels: true, style: { fontSize: '9px', fontFamily: 'Poppins' } }, tickAmount: 6 },
                    yaxis: { labels: { style: { fontSize: '9px', fontFamily: 'Poppins' }, formatter: fmtAxis } },
                    grid: { borderColor: '#f4f5f8' },
                    dataLabels: { enabled: false },
                    tooltip: { style: { fontSize: '11px', fontFamily: 'Poppins' }, y: { formatter: fmtAxis } }
                }).render();
            }

            function fmtAxis(v) {
                v = Number(v) || 0; var a = Math.abs(v);
                if (a >= 10000000) return (v / 10000000).toFixed(1) + 'Cr';
                if (a >= 100000) return (v / 100000).toFixed(1) + 'L';
                if (a >= 1000) return (v / 1000).toFixed(0) + 'K';
                return Math.round(v);
            }
        })();
    </script>
@endpush
