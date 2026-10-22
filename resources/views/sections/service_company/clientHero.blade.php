{{-- Shared Clients hero header.
     $activeTab: 'all' | 'direct' | 'internal' | 'request' (defaults to 'all'). --}}
@php
    $activeTab = $activeTab ?? 'all';
    $heroTitles = [
        'all'      => 'Clients Tracker',
        'direct'   => 'Direct Clients',
        'internal' => 'Internal Clients',
        'request'  => 'Client Requests',
    ];
    $heroTitle = $heroTitles[$activeTab] ?? 'Clients Tracker';
@endphp

<style>
/* ── Guaranteed Card-Based Header Design ── */
.cl-header, .cl-header-card {
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 12px !important;
  padding: 14px 20px !important;
  margin-bottom: 14px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: space-between !important;
  gap: 16px !important;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03) !important;
}
.cl-header-left {
  display: flex !important;
  align-items: center !important;
  gap: 12px !important;
  min-width: 0 !important;
}
.cl-header-icon-box {
  width: 44px !important;
  height: 44px !important;
  border-radius: 10px !important;
  background: #fef3c7 !important;
  border: 1px solid #fde68a !important;
  display: flex !important;
  align-items: center !important;
  justify-content: center !important;
  color: #d97706 !important;
  flex-shrink: 0 !important;
  box-shadow: 0 1px 2px rgba(217, 119, 6, 0.06) !important;
}
.cl-header-icon-box svg {
  color: #d97706 !important;
  stroke: #d97706 !important;
}
.cl-header-title-row {
  display: flex !important;
  align-items: center !important;
  gap: 8px !important;
}
.cl-header-title {
  font-size: 20px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  letter-spacing: -0.015em !important;
  margin: 0 !important;
  line-height: 1.2 !important;
}
.cl-verified-badge {
  display: inline-flex !important;
  align-items: center !important;
  justify-content: center !important;
}
.cl-verified-badge svg {
  color: #16a34a !important;
  stroke: #16a34a !important;
}
.cl-header-subtitle {
  font-size: 13px !important;
  font-weight: 400 !important;
  color: #64748b !important;
  margin-top: 3px !important;
  line-height: 1.35 !important;
}
.cl-header-actions {
  display: flex !important;
  align-items: center !important;
  gap: 10px !important;
  flex-shrink: 0 !important;
}
.cl-export-btn {
  display: inline-flex !important;
  align-items: center !important;
  gap: 7px !important;
  background: #ffffff !important;
  border: 1px solid #e2e8f0 !important;
  border-radius: 8px !important;
  padding: 8px 18px !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  cursor: pointer !important;
  text-decoration: none !important;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
  transition: all 0.15s ease !important;
  white-space: nowrap !important;
}
.cl-export-btn:hover {
  background: #f8fafc !important;
  border-color: #cbd5e1 !important;
  color: #0f172a !important;
}
.cl-export-btn svg {
  color: #0f172a !important;
  stroke: #0f172a !important;
}
.cl-add-btn-primary {
  display: inline-flex !important;
  align-items: center !important;
  gap: 6px !important;
  background: #f59e0b !important;
  border: none !important;
  border-radius: 8px !important;
  padding: 8px 20px !important;
  font-size: 13px !important;
  font-weight: 700 !important;
  color: #0f172a !important;
  cursor: pointer !important;
  text-decoration: none !important;
  box-shadow: 0 1px 3px rgba(245, 158, 11, 0.25) !important;
  transition: background 0.15s ease, box-shadow 0.15s ease, transform 0.1s ease !important;
  white-space: nowrap !important;
}
.cl-add-btn-primary:hover {
  background: #d97706 !important;
  color: #0f172a !important;
  box-shadow: 0 2px 6px rgba(217, 119, 6, 0.35) !important;
  transform: translateY(-0.5px) !important;
}
.cl-add-btn-primary svg {
  color: #0f172a !important;
  stroke: #0f172a !important;
}

/* ── Client Hero (shared fallback) ── */
.cl-hero { display:none; }

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
.cl-stat-icon.gray   { background:#f1f5f9; color:#64748b; }
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

<!-- ── HEADER CARD (Card-based design) ── -->
<div class="cl-header cl-header-card">
    <div class="cl-header-left">
        <div class="cl-header-icon-box">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div>
            <div class="cl-header-title-row">
                <h1 class="cl-header-title">Clients Tracker</h1>
                <span class="cl-verified-badge" title="Verified ATS Module">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4" stroke-width="2.5"/>
                    </svg>
                </span>
            </div>
            <div class="cl-header-subtitle">Manage client relationships, hiring activity and recruitment performance.</div>
        </div>
    </div>

    <div class="cl-header-actions">
        <button type="button" class="cl-export-btn" onclick="if(typeof exportClientsToCsv==='function'){exportClientsToCsv();}else{window.location.reload();}">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/>
                <line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            <span>Export</span>
        </button>

        @if(!empty($userPermissions) && in_array("add_clients", $userPermissions))
        <a href="{{ url('service_company/clients/create') }}" class="cl-add-btn-primary">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Add Internal Client</span>
        </a>
        @endif
    </div>
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
            <div class="cl-stat-label">Active Companies</div>
        </div>
    </a>
    <a href="#" onclick="if(typeof setClientTab==='function'){setClientTab('inactive', this); return false;}" class="cl-stat cl-stat-filter {{ ($activeTab??'')=='inactive' ? 'active' : '' }}" data-tab="inactive">
        <div class="cl-stat-icon gray">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg>
        </div>
        <div>
            <div class="cl-stat-num" id="span_clientInactive">{{ $clientDataCnt['clientInactive'] ?? max(0, ($clientDataCnt['clientAll'] ?? 0) - ($clientDataCnt['clientHiring'] ?? 0)) }}</div>
            <div class="cl-stat-label">Inactive Clients</div>
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
        Active Companies
    </a>
    <a href="#" onclick="if(typeof setClientTab==='function'){setClientTab('inactive', this); return false;}" class="cl-tab cl-tab-filter {{ ($activeTab??'')=='inactive' ? 'active' : '' }}" id="tab-inactive" data-tab="inactive">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="10" y1="15" x2="10" y2="9"/><line x1="14" y1="15" x2="14" y2="9"/></svg>
        Inactive Clients
    </a>
    <a href="{{ url('service_company/clients/client_request/0') }}" onclick="if(typeof setClientTab==='function'){setClientTab('request', this); return false;}" class="cl-tab cl-tab-filter {{ ($activeTab??'')=='request' ? 'active' : '' }}" data-tab="request">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        Pending Job Requests
    </a>
</div>
