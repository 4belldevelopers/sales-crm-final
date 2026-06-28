@extends('layouts.service_company_layout')

@php
    use App\Helper\GeneralMethod;

    // Palette per KPI / funnel stage (soft Metronic pastels)
    $kpiCards = [
        ['key' => 'total',      'label' => 'Total Offers',     'value' => $stats['total'],      'delta' => $deltas['total'],      'suffix' => '',  'tone' => 'violet', 'icon' => 'M3 7h18M3 12h18M3 17h12'],
        ['key' => 'pending',    'label' => 'Pending Approval', 'value' => $stats['pending'],    'delta' => $deltas['pending'],    'suffix' => '',  'tone' => 'blue',   'icon' => 'M12 8v4l3 2M12 21a9 9 0 100-18 9 9 0 000 18z'],
        ['key' => 'accepted',   'label' => 'Accepted',         'value' => $stats['accepted'],   'delta' => $deltas['accepted'],   'suffix' => '',  'tone' => 'green',  'icon' => 'M20 6L9 17l-5-5'],
        ['key' => 'declined',   'label' => 'Declined',         'value' => $stats['declined'],   'delta' => $deltas['declined'],   'suffix' => '',  'tone' => 'rose',   'icon' => 'M18 6L6 18M6 6l12 12'],
        ['key' => 'joined',     'label' => 'Joined',           'value' => $stats['joined'],     'delta' => $deltas['joined'],     'suffix' => '',  'tone' => 'teal',   'icon' => 'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75'],
        ['key' => 'conversion', 'label' => 'Offer Conversion', 'value' => $stats['conversion'], 'delta' => $deltas['conversion'], 'suffix' => '%', 'tone' => 'amber',  'icon' => 'M23 6l-9.5 9.5-5-5L1 18'],
    ];

    $funnelTones = ['interview' => 'violet', 'drafted' => 'blue', 'released' => 'amber', 'accepted' => 'green', 'joined' => 'teal'];
    $recruiterTones = ['violet', 'green', 'amber', 'blue', 'teal'];
@endphp

@push('head-script')
<style>
    /* ===== Offer Tracker — scoped, Poppins, all text <= 14px ===== */
    .offer-tracker { font-family: 'Poppins', sans-serif; color: #1f2937; }
    .offer-tracker * { font-family: 'Poppins', sans-serif; box-sizing: border-box; }

    .ot-tone-violet { --tone:#7c5cff; --tone-soft:#efeaff; }
    .ot-tone-blue   { --tone:#3e8bff; --tone-soft:#e7f0ff; }
    .ot-tone-green  { --tone:#1fb673; --tone-soft:#e3f7ee; }
    .ot-tone-rose   { --tone:#f1556c; --tone-soft:#fdeaed; }
    .ot-tone-teal   { --tone:#13b5b1; --tone-soft:#e0f6f5; }
    .ot-tone-amber  { --tone:#f5a623; --tone-soft:#fff2dc; }

    .offer-tracker .ot-shell { max-width: 1320px; margin: 0 auto; padding: 4px 2px 20px; }

    /* Page heading */
    .offer-tracker .ot-head { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; margin-bottom:14px; }
    .offer-tracker .ot-title { display:flex; align-items:center; gap:8px; font-size:14px; font-weight:600; }
    .offer-tracker .ot-title b { font-size:14px; font-weight:700; }
    .offer-tracker .ot-chip { display:inline-flex; align-items:center; gap:6px; background:#eef0ff; color:#5b54e6; font-size:11px; font-weight:500; padding:3px 9px; border-radius:20px; }
    .offer-tracker .ot-daterange { display:inline-flex; align-items:center; gap:8px; background:#fff; border:1px solid #e7eaf0; border-radius:8px; padding:6px 10px; font-size:11.5px; color:#475467; cursor:pointer; position:relative; }
    .offer-tracker .ot-daterange svg { width:14px; height:14px; stroke:#9aa3b2; }
    .offer-tracker .ot-date-pop { position:absolute; top:calc(100% + 6px); right:0; background:#fff; border:1px solid #e7eaf0; border-radius:10px; box-shadow:0 12px 30px rgba(16,24,40,.12); padding:12px; z-index:30; display:none; }
    .offer-tracker .ot-date-pop.show { display:block; }
    .offer-tracker .ot-date-pop label { display:block; font-size:10.5px; color:#98a2b3; margin:6px 0 3px; }
    .offer-tracker .ot-date-pop input { font-size:12px; padding:6px 8px; border:1px solid #e7eaf0; border-radius:7px; width:170px; }
    .offer-tracker .ot-date-pop .ot-apply { margin-top:10px; width:100%; background:#5b54e6; color:#fff; border:none; border-radius:7px; padding:7px; font-size:12px; font-weight:500; cursor:pointer; }

    /* Card shell */
    .offer-tracker .ot-card { background:#fff; border:1px solid #eef0f4; border-radius:14px; box-shadow:0 1px 2px rgba(16,24,40,.04); }

    /* KPI grid */
    .offer-tracker .ot-kpis { display:grid; grid-template-columns:repeat(6,1fr); gap:12px; margin-bottom:14px; }
    .offer-tracker .ot-kpi { padding:14px; display:flex; flex-direction:column; gap:10px; }
    .offer-tracker .ot-kpi-top { display:flex; align-items:center; gap:9px; }
    .offer-tracker .ot-kpi-ico { width:34px; height:34px; border-radius:9px; background:var(--tone-soft); display:flex; align-items:center; justify-content:center; flex:0 0 auto; }
    .offer-tracker .ot-kpi-ico svg { width:17px; height:17px; stroke:var(--tone); fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .offer-tracker .ot-kpi-label { font-size:11.5px; color:#667085; font-weight:500; line-height:1.2; }
    .offer-tracker .ot-kpi-value { font-size:14px; font-weight:700; color:#101828; letter-spacing:-.2px; }
    .offer-tracker .ot-kpi-value span { font-size:14px; }
    .offer-tracker .ot-delta { display:inline-flex; align-items:center; gap:4px; font-size:11px; font-weight:500; }
    .offer-tracker .ot-delta.up { color:#1fb673; }
    .offer-tracker .ot-delta.down { color:#f1556c; }
    .offer-tracker .ot-delta .muted { color:#98a2b3; font-weight:400; }
    .offer-tracker .ot-delta svg { width:12px; height:12px; }

    /* Main split row */
    .offer-tracker .ot-row { display:grid; grid-template-columns: 1fr 320px; gap:14px; margin-bottom:14px; }
    .offer-tracker .ot-panel-head { display:flex; align-items:center; justify-content:space-between; padding:14px 16px 4px; }
    .offer-tracker .ot-panel-title { font-size:13px; font-weight:600; color:#101828; display:flex; align-items:center; gap:6px; }
    .offer-tracker .ot-panel-title .i { width:14px; height:14px; border-radius:50%; border:1.5px solid #cbd2dc; color:#98a2b3; font-size:9px; display:inline-flex; align-items:center; justify-content:center; }
    .offer-tracker .ot-link { font-size:11.5px; color:#5b54e6; font-weight:500; text-decoration:none; }

    /* Funnel */
    .offer-tracker .ot-funnel { display:flex; align-items:stretch; gap:6px; padding:14px 16px 18px; flex-wrap:nowrap; }
    .offer-tracker .ot-stage-wrap { display:flex; align-items:center; gap:6px; flex:1 1 0; min-width:0; }
    .offer-tracker .ot-stage { flex:1 1 0; min-width:0; background:var(--tone-soft); border-radius:12px; padding:14px 8px; text-align:center; }
    .offer-tracker .ot-stage-pill { display:inline-block; font-size:10px; font-weight:600; color:var(--tone); background:#fff; padding:2px 8px; border-radius:20px; margin-bottom:8px; white-space:nowrap; }
    .offer-tracker .ot-stage-num { font-size:14px; font-weight:700; color:#101828; }
    .offer-tracker .ot-stage-rate { font-size:11px; color:#667085; margin-top:2px; }
    .offer-tracker .ot-arrow { color:#cbd2dc; flex:0 0 auto; }
    .offer-tracker .ot-arrow svg { width:14px; height:14px; }

    /* Conversion mini card */
    .offer-tracker .ot-conv { flex:0 0 150px; border:1px dashed #e3e6ec; border-radius:12px; padding:12px; text-align:center; display:flex; flex-direction:column; justify-content:center; }
    .offer-tracker .ot-conv-num { font-size:14px; font-weight:700; color:#101828; }
    .offer-tracker .ot-conv-label { font-size:10.5px; color:#98a2b3; margin-bottom:4px; }
    .offer-tracker .ot-conv-delta { font-size:11px; color:#1fb673; margin-bottom:6px; }
    .offer-tracker .ot-spark { width:100%; height:34px; }

    /* Recruiters */
    .offer-tracker .ot-recruiters { padding:6px 14px 12px; }
    .offer-tracker .ot-rec-row { display:flex; align-items:center; gap:10px; padding:9px 2px; border-bottom:1px solid #f3f4f7; }
    .offer-tracker .ot-rec-row:last-child { border-bottom:none; }
    .offer-tracker .ot-rec-rank { width:16px; font-size:11px; color:#98a2b3; font-weight:600; text-align:center; }
    .offer-tracker .ot-rec-av { width:30px; height:30px; border-radius:50%; background:var(--tone-soft); color:var(--tone); font-size:11px; font-weight:600; display:flex; align-items:center; justify-content:center; flex:0 0 auto; }
    .offer-tracker .ot-rec-meta { flex:1 1 auto; min-width:0; }
    .offer-tracker .ot-rec-name { font-size:12.5px; font-weight:500; color:#1f2937; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .offer-tracker .ot-rec-sub { font-size:10.5px; color:#98a2b3; }
    .offer-tracker .ot-rec-rate { display:inline-flex; align-items:center; gap:5px; font-size:11.5px; font-weight:600; color:#1fb673; }
    .offer-tracker .ot-rec-rate .dot { width:18px; height:18px; border-radius:50%; background:#e3f7ee; color:#1fb673; font-size:9.5px; font-weight:700; display:flex; align-items:center; justify-content:center; }
    .offer-tracker .ot-empty { padding:26px 10px; text-align:center; color:#98a2b3; font-size:12px; }

    /* Filter / action toolbar */
    .offer-tracker .ot-toolbar { display:flex; align-items:center; gap:8px; flex-wrap:wrap; padding:12px 14px; }
    .offer-tracker .ot-search { flex:1 1 220px; min-width:180px; display:flex; align-items:center; gap:7px; background:#f7f8fa; border:1px solid #eef0f4; border-radius:8px; padding:7px 10px; }
    .offer-tracker .ot-search svg { width:14px; height:14px; stroke:#9aa3b2; flex:0 0 auto; }
    .offer-tracker .ot-search input { border:none; background:transparent; outline:none; font-size:12px; width:100%; color:#475467; }
    .offer-tracker .ot-btn { display:inline-flex; align-items:center; gap:6px; font-size:11.5px; font-weight:500; color:#475467; background:#fff; border:1px solid #e7eaf0; border-radius:8px; padding:7px 11px; cursor:pointer; white-space:nowrap; text-decoration:none; }
    .offer-tracker .ot-btn:hover { background:#f7f8fa; }
    .offer-tracker .ot-btn svg { width:13px; height:13px; stroke:#9aa3b2; }
    .offer-tracker .ot-btn.is-accent { color:#5b54e6; border-color:#d8d5fb; background:#f3f2ff; }
    .offer-tracker .ot-btn.is-primary { color:#fff; background:#5b54e6; border-color:#5b54e6; }
    .offer-tracker .ot-btn.is-primary svg { stroke:#fff; }

    /* Responsive — fit 15.6" laptops and down */
    @media (max-width: 1400px) { .offer-tracker .ot-kpis { gap:10px; } .offer-tracker .ot-kpi { padding:12px; } }
    @media (max-width: 1200px) {
        .offer-tracker .ot-kpis { grid-template-columns:repeat(3,1fr); }
        .offer-tracker .ot-row { grid-template-columns:1fr; }
        .offer-tracker .ot-funnel { flex-wrap:wrap; }
        .offer-tracker .ot-stage-wrap { flex:1 1 30%; }
        .offer-tracker .ot-conv { flex:1 1 100%; }
    }
    @media (max-width: 680px) {
        .offer-tracker .ot-kpis { grid-template-columns:repeat(2,1fr); }
        .offer-tracker .ot-stage-wrap { flex:1 1 100%; }
        .offer-tracker .ot-arrow { display:none; }
    }
</style>
@endpush

@section('content')
<div class="offer-tracker">
  <div class="ot-shell">

    {{-- Heading --}}
    <div class="ot-head">
      <div class="ot-title">
        <b>Offer Tracker</b>
        <span class="ot-chip">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#5b54e6" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
          {{ $stats['total'] }} Total Offers
        </span>
      </div>

      <div class="ot-daterange" id="otDateBtn">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        <span>{{ $rangeStart->format('d M Y') }} – {{ $rangeEnd->format('d M Y') }}</span>
        <form class="ot-date-pop" id="otDatePop" method="GET" action="{{ route('service_company.applicationoffer.tracker') }}">
          <label>From</label>
          <input type="date" name="start" value="{{ $rangeStart->format('Y-m-d') }}">
          <label>To</label>
          <input type="date" name="end" value="{{ $rangeEnd->format('Y-m-d') }}">
          <button type="submit" class="ot-apply">Apply</button>
        </form>
      </div>
    </div>

    {{-- KPI cards --}}
    <div class="ot-kpis">
      @foreach ($kpiCards as $c)
        @php $up = $c['delta'] >= 0; @endphp
        <div class="ot-card ot-kpi ot-tone-{{ $c['tone'] }}">
          <div class="ot-kpi-top">
            <div class="ot-kpi-ico">
              <svg viewBox="0 0 24 24"><path d="{{ $c['icon'] }}"/></svg>
            </div>
            <div class="ot-kpi-label">{{ $c['label'] }}</div>
          </div>
          <div class="ot-kpi-value">{{ number_format($c['value']) }}<span>{{ $c['suffix'] }}</span></div>
          <div class="ot-delta {{ $up ? 'up' : 'down' }}">
            @if ($up)
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 14l5-5 5 5"/></svg>
            @else
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M7 10l5 5 5-5"/></svg>
            @endif
            {{ $up ? '+' : '' }}{{ $c['delta'] }}% <span class="muted">vs last period</span>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Funnel + Recruiters --}}
    <div class="ot-row">

      {{-- Pipeline funnel --}}
      <div class="ot-card">
        <div class="ot-panel-head">
          <div class="ot-panel-title">Offer Pipeline Funnel <span class="i">i</span></div>
        </div>
        <div class="ot-funnel">
          @foreach ($funnel as $i => $stage)
            <div class="ot-stage-wrap ot-tone-{{ $funnelTones[$stage['key']] }}">
              <div class="ot-stage">
                <span class="ot-stage-pill">{{ $stage['label'] }}</span>
                <div class="ot-stage-num">{{ number_format($stage['count']) }}</div>
                <div class="ot-stage-rate">{{ rtrim(rtrim(number_format($stage['rate'],1),'0'),'.') }}%</div>
              </div>
              @if ($i < count($funnel) - 1)
                <span class="ot-arrow"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
              @endif
            </div>
          @endforeach

          <div class="ot-conv">
            <div class="ot-conv-label">Conversion<br>(Accepted → Joined)</div>
            <div class="ot-conv-num">{{ rtrim(rtrim(number_format($pipelineConversion,1),'0'),'.') }}%</div>
            <div class="ot-conv-delta">↑ {{ $deltas['conversion'] >= 0 ? '+' : '' }}{{ $deltas['conversion'] }}%</div>
            <svg class="ot-spark" viewBox="0 0 120 34" preserveAspectRatio="none" fill="none">
              <polyline points="0,28 18,22 36,26 54,14 72,18 90,8 110,12 120,6" stroke="#7c5cff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              <polyline points="0,34 0,28 18,22 36,26 54,14 72,18 90,8 110,12 120,6 120,34" fill="#efeaff" stroke="none" opacity=".6"/>
            </svg>
          </div>
        </div>
      </div>

      {{-- Top recruiters --}}
      <div class="ot-card">
        <div class="ot-panel-head">
          <div class="ot-panel-title">Top Recruiters This Month</div>
          <a href="{{ route('service_company.applicationoffer.index') }}" class="ot-link">View All</a>
        </div>
        <div class="ot-recruiters">
          @forelse ($topRecruiters as $idx => $r)
            @php
              $tone = $recruiterTones[$idx % count($recruiterTones)];
              $initials = collect(explode(' ', trim($r->name)))->filter()->take(2)->map(fn($w) => strtoupper($w[0]))->implode('');
            @endphp
            <div class="ot-rec-row ot-tone-{{ $tone }}">
              <div class="ot-rec-rank">{{ $idx + 1 }}</div>
              <div class="ot-rec-av">{{ $initials ?: 'NA' }}</div>
              <div class="ot-rec-meta">
                <div class="ot-rec-name">{{ $r->name }}</div>
                <div class="ot-rec-sub">{{ $r->offers }} {{ $r->offers == 1 ? 'Offer' : 'Offers' }}</div>
              </div>
              <div class="ot-rec-rate"><span class="dot">R</span>{{ $r->rate }}%</div>
            </div>
          @empty
            <div class="ot-empty">No recruiter activity in this period.</div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Filter / action toolbar --}}
    <div class="ot-card ot-toolbar">
      <div class="ot-search">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4-4"/></svg>
        <input type="text" id="otQuickSearch" placeholder="Search candidate, job, recruiter…">
      </div>
      <button type="button" class="ot-btn is-accent">
        <svg viewBox="0 0 24 24" fill="none" stroke="#5b54e6" stroke-width="2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
        Advanced Filters
      </button>
      <button type="button" class="ot-btn">Offer Stage <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>
      <button type="button" class="ot-btn">Recruiter <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>
      <button type="button" class="ot-btn">Risk Level <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>
      <button type="button" class="ot-btn">Joining Date <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></button>
      <button type="button" class="ot-btn">More Filters</button>
      <button type="button" class="ot-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M19 21l-7-5-7 5V5a2 2 0 012-2h10a2 2 0 012 2z"/></svg>
        Saved Views
      </button>
      <button type="button" class="ot-btn" id="otExport">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3"/></svg>
        Export
      </button>
      <a href="{{ route('service_company.applicationoffer.index') }}" class="ot-btn is-primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Create Offer
      </a>
    </div>

  </div>
</div>
@endsection

@push('footer-script')
<script>
(function () {
    // Date range popover
    var btn = document.getElementById('otDateBtn');
    var pop = document.getElementById('otDatePop');
    if (btn && pop) {
        btn.addEventListener('click', function (e) {
            if (e.target.closest('.ot-date-pop')) return;
            pop.classList.toggle('show');
        });
        document.addEventListener('click', function (e) {
            if (!btn.contains(e.target)) pop.classList.remove('show');
        });
    }

    // Lightweight client-side export of the KPI summary (zero server load)
    var exportBtn = document.getElementById('otExport');
    if (exportBtn) {
        exportBtn.addEventListener('click', function () {
            var rows = [['Metric', 'Value']];
            document.querySelectorAll('.ot-kpi').forEach(function (k) {
                var label = k.querySelector('.ot-kpi-label').textContent.trim();
                var value = k.querySelector('.ot-kpi-value').textContent.trim();
                rows.push([label, value]);
            });
            var csv = rows.map(function (r) {
                return r.map(function (c) { return '"' + c.replace(/"/g, '""') + '"'; }).join(',');
            }).join('\n');
            var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            var a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'offer-tracker-summary.csv';
            a.click();
            URL.revokeObjectURL(a.href);
        });
    }
})();
</script>
@endpush
