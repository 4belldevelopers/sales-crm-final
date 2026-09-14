{{-- Shared Clients hero header.
     $activeTab: 'all' | 'direct' | 'internal' | 'request' (defaults to 'all'). --}}
@php
    $activeTab = $activeTab ?? 'all';
    $heroTitles = [
        'all'      => 'Clients',
        'direct'   => 'Direct Clients',
        'internal' => 'Internal Clients',
        'request'  => 'Client Requests',
    ];
    $heroTitle = $heroTitles[$activeTab] ?? 'Clients';
@endphp

<style>
/* ── Client Hero (shared) ── */
.cl-hero { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:18px; gap:12px; }
.cl-hero-left { display:flex; flex-direction:column; gap:3px; }
.cl-hero-title { display:flex; align-items:center; gap:8px; font-size:20px; font-weight:700; color:#181c32; line-height:1.2; }
.cl-hero-title svg { width:20px; height:20px; flex-shrink:0; }
.cl-hero-sub { font-size:12px; color:#7e8299; margin-top:2px; }
.cl-add-btn { display:inline-flex; align-items:center; gap:6px; background:#1b84ff; color:#fff; border:none; border-radius:7px; padding:9px 18px; font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; white-space:nowrap; }
.cl-add-btn:hover { background:#056ee9; color:#fff; }

/* Stat cards */
.cl-stats { display:flex; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
.cl-stat { flex:1; min-width:140px; background:#fff; border:1px solid #e4e6ef; border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:12px; cursor:pointer; transition:box-shadow .15s; text-decoration:none; }
.cl-stat:hover { box-shadow:0 4px 14px rgba(0,0,0,.08); text-decoration:none; }
.cl-stat-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.cl-stat-icon svg { width:20px; height:20px; }
.cl-stat-icon.blue   { background:#eff6ff; color:#2563eb; }
.cl-stat-icon.indigo { background:#eef2ff; color:#4f46e5; }
.cl-stat-icon.green  { background:#f0fdf4; color:#16a34a; }
.cl-stat-icon.orange { background:#fff7ed; color:#ea580c; }
.cl-stat-icon.purple { background:#faf5ff; color:#7c3aed; }
.cl-stat-num { font-size:20px; font-weight:700; color:#181c32; line-height:1.1; }
.cl-stat-label { font-size:11px; color:#7e8299; margin-top:2px; }

/* Filter bar */
.cl-filter { display:flex; align-items:center; gap:8px; margin-bottom:12px; flex-wrap:wrap; }
.cl-search-wrap { position:relative; flex:1; min-width:160px; }
.cl-search-wrap svg { position:absolute; left:10px; top:50%; transform:translateY(-50%); width:14px; height:14px; color:#a1a5b7; pointer-events:none; }
.cl-search-input { width:100%; border:1px solid #e4e6ef; border-radius:7px; padding:7px 10px 7px 32px; font-size:12px; font-family:'Poppins',sans-serif; color:#181c32; outline:none; background:#fff; }
.cl-search-input:focus { border-color:#1b84ff; }
.cl-select { border:1px solid #e4e6ef; border-radius:7px; padding:7px 28px 7px 10px; font-size:12px; font-family:'Poppins',sans-serif; color:#181c32; background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='7' viewBox='0 0 10 7'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%23a1a5b7' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E") no-repeat right 9px center; appearance:none; outline:none; cursor:pointer; }

/* Tabs */
.cl-tabs { display:flex; align-items:center; gap:0; border-bottom:2px solid #e4e6ef; margin-bottom:14px; overflow-x:auto; }
.cl-tab { display:inline-flex; align-items:center; gap:6px; padding:9px 16px; font-size:12px; font-weight:500; color:#7e8299; text-decoration:none; border-bottom:2px solid transparent; margin-bottom:-2px; white-space:nowrap; cursor:pointer; }
.cl-tab:hover { color:#181c32; text-decoration:none; }
.cl-tab.active { color:#1b84ff; border-bottom-color:#1b84ff; font-weight:600; }
.cl-tab svg { width:14px; height:14px; }
</style>

<!-- HERO -->
<div class="cl-hero">
    <div class="cl-hero-left">
        <div class="cl-hero-title">
            {{ $heroTitle }}
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                <path d="m9 12 2 2 4-4" stroke="#16A34A" stroke-width="2.5"/>
            </svg>
        </div>
        <div class="cl-hero-sub">Manage and track your clients, relationships and business performance.</div>
    </div>
    @if(!empty($userPermissions) && in_array("add_clients", $userPermissions) && !in_array($activeTab, ['request', 'direct']))
    <a href="{{ url('service_company/clients/create') }}" class="cl-add-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Client
    </a>
    @endif
</div>

<!-- STAT CARDS -->
<div class="cl-stats">
    <a href="{{ route('service_company.clients.index') }}" onclick="if(typeof setClientTab==='function'){setClientTab('all', this); return false;}" class="cl-stat cl-stat-filter {{ ($activeTab??'all')=='all' ? 'active' : '' }}" data-tab="all">
        <div class="cl-stat-icon blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
            <div class="cl-stat-num" id="span_clientAll">{{ $clientDataCnt['clientAll'] ?? 0 }}</div>
            <div class="cl-stat-label">Total Clients</div>
        </div>
    </a>
    <a href="{{ url('service_company/clients/custom_client/0') }}" onclick="if(typeof setClientTab==='function'){setClientTab('internal', this); return false;}" class="cl-stat cl-stat-filter {{ ($activeTab??'')=='internal' ? 'active' : '' }}" data-tab="internal">
        <div class="cl-stat-icon indigo">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        </div>
        <div>
            <div class="cl-stat-num" id="span_clientCustom">{{ $clientDataCnt['clientCustom'] ?? 0 }}</div>
            <div class="cl-stat-label">Internal Clients</div>
        </div>
    </a>
    <a href="{{ url('service_company/clients/direct_client/0') }}" onclick="if(typeof setClientTab==='function'){setClientTab('direct', this); return false;}" class="cl-stat cl-stat-filter {{ ($activeTab??'')=='direct' ? 'active' : '' }}" data-tab="direct">
        <div class="cl-stat-icon green">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div>
            <div class="cl-stat-num" id="span_clientDirect">{{ $clientDataCnt['clientDirect'] ?? 0 }}</div>
            <div class="cl-stat-label">Direct Clients</div>
        </div>
    </a>
    <a href="#" onclick="if(typeof setClientTab==='function'){setClientTab('hiring', this); return false;}" class="cl-stat cl-stat-filter {{ ($activeTab??'')=='hiring' ? 'active' : '' }}" data-tab="hiring">
        <div class="cl-stat-icon orange">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
        </div>
        <div>
            <div class="cl-stat-num" id="span_clientHiring">{{ $clientDataCnt['clientHiring'] ?? 0 }}</div>
            <div class="cl-stat-label">Clients Actively Hiring</div>
        </div>
    </a>
    <a href="{{ url('service_company/clients/client_request/0') }}" onclick="if(typeof setClientTab==='function'){setClientTab('request', this); return false;}" class="cl-stat cl-stat-filter {{ ($activeTab??'')=='request' ? 'active' : '' }}" data-tab="request">
        <div class="cl-stat-icon purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="12" y2="17"/></svg>
        </div>
        <div>
            <div class="cl-stat-num" id="span_clientRequest">{{ $clientDataCnt['clientRequest'] ?? 0 }}</div>
            <div class="cl-stat-label">Pending Requests</div>
        </div>
    </a>
</div>

<!-- TABS -->
<div class="cl-tabs">
    <a href="{{ route('service_company.clients.index') }}" onclick="if(typeof setClientTab==='function'){setClientTab('all', this); return false;}" class="cl-tab cl-tab-filter {{ ($activeTab??'all')=='all' ? 'active' : '' }}" data-tab="all">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        All Clients
    </a>
    <a href="{{ url('service_company/clients/custom_client/0') }}" onclick="if(typeof setClientTab==='function'){setClientTab('internal', this); return false;}" class="cl-tab cl-tab-filter {{ ($activeTab??'')=='internal' ? 'active' : '' }}" data-tab="internal">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Internal Clients
    </a>
    <a href="{{ url('service_company/clients/direct_client/0') }}" onclick="if(typeof setClientTab==='function'){setClientTab('direct', this); return false;}" class="cl-tab cl-tab-filter {{ ($activeTab??'')=='direct' ? 'active' : '' }}" data-tab="direct">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        Direct Clients
    </a>
    <a href="#" onclick="if(typeof setClientTab==='function'){setClientTab('hiring', this); return false;}" class="cl-tab cl-tab-filter {{ ($activeTab??'')=='hiring' ? 'active' : '' }}" id="tab-hiring" data-tab="hiring">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
        Hiring
    </a>

    <a href="{{ url('service_company/clients/client_request/0') }}" onclick="if(typeof setClientTab==='function'){setClientTab('request', this); return false;}" class="cl-tab cl-tab-filter {{ ($activeTab??'')=='request' ? 'active' : '' }}" data-tab="request">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Pending Job Requests
    </a>
</div>
