@extends('layouts.service_company_layout')
@push('head-script')
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/clientList.css') }}?v={{ time() }}" rel="stylesheet" type="text/css" />
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

/* ── Modern Metronic Location Combobox (15.6-inch & Desktop Responsive) ── */
.cl-loc-dropdown-wrapper {
  position: relative;
  display: inline-block;
}
.cl-loc-combobox-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  height: 42px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 0 12px;
  min-width: 155px;
  max-width: 210px;
  cursor: pointer;
  user-select: none;
  transition: all 0.15s ease;
}
.cl-loc-combobox-btn:hover, .cl-loc-combobox-btn.active {
  border-color: #cbd5e1;
  background: #f8fafc;
}
.cl-loc-combobox-btn.has-filter {
  border-color: #f59e0b;
  background: #fffbeb;
}
.cl-loc-combobox-btn.has-filter .cl-pill-val {
  color: #b45309;
}
.cl-loc-dropdown-panel {
  position: absolute;
  top: calc(100% + 6px);
  left: 0;
  width: 310px;
  background: #ffffff;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.12), 0 8px 10px -6px rgba(15, 23, 42, 0.06);
  z-index: 1050;
  display: none;
  flex-direction: column;
  overflow: hidden;
  animation: clLocFadeIn 0.15s ease-out;
}
@keyframes clLocFadeIn {
  from { opacity: 0; transform: translateY(-4px); }
  to { opacity: 1; transform: translateY(0); }
}
.cl-loc-dropdown-panel.show {
  display: flex;
}
.cl-loc-search-box {
  padding: 10px 12px;
  border-bottom: 1px solid #f1f5f9;
  background: #fafafa;
  position: relative;
}
.cl-loc-search-inp {
  width: 100%;
  height: 34px;
  border: 1px solid #e2e8f0;
  border-radius: 7px;
  padding: 0 28px 0 30px;
  font-size: 12px;
  font-family: inherit;
  color: #0f172a;
  background: #ffffff;
  outline: none;
  transition: border-color 0.15s ease, box-shadow 0.15s ease;
}
.cl-loc-search-inp:focus {
  border-color: #f59e0b;
  box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.15);
}
.cl-loc-search-icon {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  pointer-events: none;
}
.cl-loc-search-clear {
  position: absolute;
  right: 20px;
  top: 50%;
  transform: translateY(-50%);
  color: #94a3b8;
  font-size: 11px;
  cursor: pointer;
  padding: 2px 4px;
  border-radius: 50%;
  display: none;
}
.cl-loc-search-clear:hover {
  color: #0f172a;
  background: #e2e8f0;
}
.cl-loc-dropdown-body {
  max-height: 290px;
  overflow-y: auto;
  padding: 6px 0;
  scrollbar-width: thin;
  scrollbar-color: #cbd5e1 transparent;
}
.cl-loc-dropdown-body::-webkit-scrollbar {
  width: 5px;
}
.cl-loc-dropdown-body::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}
.cl-loc-section-hdr {
  font-size: 10px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #94a3b8;
  padding: 6px 14px 4px 14px;
}
.cl-loc-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 7px 14px;
  font-size: 12px;
  font-weight: 500;
  color: #1e293b;
  cursor: pointer;
  transition: background 0.12s ease;
}
.cl-loc-item:hover {
  background: #f1f5f9;
}
.cl-loc-item.selected {
  background: #fffbeb;
  color: #b45309;
  font-weight: 600;
}
.cl-loc-item-left {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
  flex: 1;
}
.cl-loc-item-title {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.cl-loc-badge {
  font-size: 10.5px;
  font-weight: 600;
  padding: 1px 7px;
  border-radius: 12px;
  background: #e2e8f0;
  color: #475569;
  margin-left: 6px;
  flex-shrink: 0;
}
.cl-loc-item.selected .cl-loc-badge {
  background: #fde68a;
  color: #92400e;
}
.cl-loc-expand-btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 20px;
  height: 20px;
  border-radius: 4px;
  color: #94a3b8;
  cursor: pointer;
  margin-left: 4px;
  transition: transform 0.15s ease, background 0.15s ease;
}
.cl-loc-expand-btn:hover {
  background: #e2e8f0;
  color: #334155;
}
.cl-loc-expand-btn.expanded svg {
  transform: rotate(90deg);
}
.cl-loc-city-list {
  display: none;
  background: #f8fafc;
  border-left: 2px solid #e2e8f0;
  margin-left: 24px;
  padding: 2px 0;
}
.cl-loc-city-list.show {
  display: block;
}
.cl-loc-city-item {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 5px 14px 5px 12px;
  font-size: 11.5px;
  font-weight: 500;
  color: #334155;
  cursor: pointer;
  transition: background 0.12s ease;
}
.cl-loc-city-item:hover {
  background: #f1f5f9;
}
.cl-loc-city-item.selected {
  background: #fef3c7;
  color: #b45309;
  font-weight: 600;
}
.cl-loc-divider {
  height: 1px;
  background: #f1f5f9;
  margin: 4px 0;
}
.cl-loc-empty {
  padding: 18px 14px;
  text-align: center;
  font-size: 11.5px;
  color: #94a3b8;
}

/* ── Sortable Table Headers ── */
.cl-sortable-th {
  cursor: pointer !important;
  user-select: none;
  transition: background-color 0.15s ease;
}
.cl-sortable-th:hover {
  background-color: #f8fafc !important;
}
.cl-sortable-th.active-sort {
  color: #2563eb !important;
}
.cl-sortable-th.active-sort svg {
  color: #2563eb !important;
  stroke: #2563eb !important;
}
.cl-sortable-th.active-sort-asc .tbl-th-sort {
  transform: rotate(180deg);
}

/* ── 15.6-inch Screen Optimization (1366x768 & 1920x1080) ── */
@media (max-width: 1400px) {
  .cl-filter-bar {
    gap: 8px !important;
  }
  .cl-dropdown-pill, .cl-loc-combobox-btn {
    min-width: 135px !important;
    padding: 0 10px !important;
  }
}
.cd-activities-drawer {
  width: 90% !important;
  max-width: 1220px !important;
}
@media (max-width: 1400px) {
  .cd-activities-drawer {
    width: 95% !important;
    max-width: 1120px !important;
  }
}
@media (max-width: 768px) {
  .cd-activities-drawer {
    width: 100% !important;
  }
}
</style>
@endpush
@section('content')
@php
    $tabactive = [
         'allClient_active'=>'tabactive',
         'allClient_url'=> route('service_company.clients.index'),
         'span_clientAll'=>'span_clientAll',
         'clientDirect_active'=>'',
         'clientDirect_url'=> url('service_company/clients/direct_client/0'),
         'span_clientDirect'=>'span_clientDirect',
         'customClient_active'=>'',
         'customClient_url'=> url('service_company/clients/custom_client/0'),
         'span_clientcustom'=>'span_clientCustom',
         'clientRequest_active'=>'',
         'clientRequest_url'=> url('service_company/clients/client_request/0'),
         'span_clientRequest'=>'span_clientRequest',
         'clientAll' => @$clientDataCnt['clientAll'],
         'clientDirect' => @$clientDataCnt['clientDirect'],
         'clientCustom' => @$clientDataCnt['clientCustom'],
         'clientRequest' => @$clientDataCnt['clientRequest'],
         'clientHiring' => @$clientDataCnt['clientHiring'],
         'activeJobs' => @$clientDataCnt['activeJobs'],
    ];

    $totalClientsCount = (int)($clientDataCnt['clientAll'] ?? 0);
    $internalClientsCount = (int)($clientDataCnt['clientCustom'] ?? 0);
    $directClientsCount = (int)($clientDataCnt['clientDirect'] ?? 0);
    $hiringClientsCount = (int)($clientDataCnt['clientHiring'] ?? 0);
    $inactiveClientsCount = (int)($clientDataCnt['clientInactive'] ?? max(0, $totalClientsCount - $hiringClientsCount));
    $activeJobsCount = (int)($clientDataCnt['activeJobs'] ?? 0);
    $pendingRequestsCount = (int)($clientDataCnt['clientRequest'] ?? 0);

    $directPercent = ($totalClientsCount > 0) ? round(($directClientsCount / $totalClientsCount) * 100) : 100;
@endphp

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_client" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

        <!-- ── HEADER CARD ── -->
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
                <button type="button" class="cl-export-btn" onclick="exportClientsToCsv()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span>Export</span>
                </button>

                @if(in_array("add_clients", $userPermissions))
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

        <!-- ── 6 STAT CARDS ── -->
        <div class="cl-stat-grid">
            {{-- Card 1: Total Clients --}}
            <a href="#" onclick="setClientTab('all', this); return false;" class="cl-card cl-stat-filter active" data-tab="all">
                <div class="cl-card-top">
                    <div class="cl-card-icon blue">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientAll">{{ $totalClientsCount }}</div>
                        <div class="cl-card-lbl">Total Clients</div>
                    </div>
                </div>
                <div class="cl-card-sub green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
                    <span>+{{ max($totalClientsCount, 1) }} this month</span>
                </div>
            </a>

            {{-- Card 2: Internal Clients --}}
            <a href="#" onclick="setClientTab('internal', this); return false;" class="cl-card cl-stat-filter" data-tab="internal">
                <div class="cl-card-top">
                    <div class="cl-card-icon indigo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                            <polyline points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientCustom">{{ $internalClientsCount }}</div>
                        <div class="cl-card-lbl">Internal Clients</div>
                    </div>
                </div>
                <div class="cl-card-sub blue">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="m18 15-6-6-6 6"/></svg>
                    <span>{{ $internalClientsCount > 0 ? $internalClientsCount . ' active internal' : 'No internal clients yet' }}</span>
                </div>
            </a>

            {{-- Card 3: Direct Clients --}}
            <a href="#" onclick="setClientTab('direct', this); return false;" class="cl-card cl-stat-filter" data-tab="direct">
                <div class="cl-card-top">
                    <div class="cl-card-icon green">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                            <path d="M9 22v-4h6v4"/>
                            <path d="M8 6h.01"/>
                            <path d="M16 6h.01"/>
                            <path d="M8 10h.01"/>
                            <path d="M16 10h.01"/>
                            <path d="M8 14h.01"/>
                            <path d="M16 14h.01"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientDirect">{{ $directClientsCount }}</div>
                        <div class="cl-card-lbl">Direct Clients</div>
                    </div>
                </div>
                <div class="cl-card-sub green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <polyline points="17 11 19 13 23 9"/>
                    </svg>
                    <span>{{ $directPercent }}% of total clients</span>
                </div>
            </a>

            {{-- Card 4: Active Companies --}}
            <a href="#" onclick="setClientTab('hiring', this); return false;" class="cl-card cl-stat-filter" data-tab="hiring">
                <div class="cl-card-top">
                    <div class="cl-card-icon orange">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2"/>
                            <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                            <line x1="12" y1="12" x2="12" y2="12.01"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientHiring">{{ $hiringClientsCount }}</div>
                        <div class="cl-card-lbl">Active Companies</div>
                    </div>
                </div>
                <div class="cl-card-sub orange">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="9" x2="20" y2="9"/>
                        <line x1="4" y1="15" x2="20" y2="15"/>
                        <line x1="10" y1="3" x2="8" y2="21"/>
                        <line x1="16" y1="3" x2="14" y2="21"/>
                    </svg>
                    <span>{{ $activeJobsCount > 0 ? $activeJobsCount . ' active position' . ($activeJobsCount > 1 ? 's' : '') : 'No active jobs' }}</span>
                </div>
            </a>

            {{-- Card 5: Inactive Clients --}}
            <a href="#" onclick="setClientTab('inactive', this); return false;" class="cl-card cl-stat-filter" data-tab="inactive">
                <div class="cl-card-top">
                    <div class="cl-card-icon gray">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="10" y1="15" x2="10" y2="9"/>
                            <line x1="14" y1="15" x2="14" y2="9"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientInactive">{{ $inactiveClientsCount }}</div>
                        <div class="cl-card-lbl">Inactive Clients</div>
                    </div>
                </div>
                <div class="cl-card-sub gray">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="8" y1="12" x2="16" y2="12"/>
                    </svg>
                    <span>0 active positions</span>
                </div>
            </a>

            {{-- Card 6: Pending Requests --}}
            <a href="#" onclick="setClientTab('request', this); return false;" class="cl-card cl-stat-filter" data-tab="request">
                <div class="cl-card-top">
                    <div class="cl-card-icon purple">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <line x1="16" y1="13" x2="8" y2="13"/>
                            <line x1="16" y1="17" x2="8" y2="17"/>
                            <polyline points="10 9 9 9 8 9"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientRequest">{{ $pendingRequestsCount }}</div>
                        <div class="cl-card-lbl">Pending Requests</div>
                    </div>
                </div>
                <div class="cl-card-sub purple">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <polyline points="12 6 12 12 14 14"/>
                    </svg>
                    <span>{{ $pendingRequestsCount > 0 ? $pendingRequestsCount . ' action needed' : "You're all caught up" }}</span>
                </div>
            </a>
        </div>

        <!-- ── SEARCH & FILTER BAR ── -->
        <div class="cl-filter-bar">
            {{-- Search Input --}}
            <div class="cl-search-box">
                <svg class="cl-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" class="cl-search-inp" name="inputJobSearch" value="" placeholder="Search clients by name, industry, location..." id="kt_tagify_2" onchange="filtersdataget()" />
                <div id="loader_search" class="loader_search hidden_search" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);"></div>
            </div>

            {{-- Filter 1: Location Combobox --}}
            <div class="cl-loc-dropdown-wrapper" id="clLocDropdownWrapper">
                <div class="cl-loc-combobox-btn" id="clLocBtn" onclick="toggleLocationDropdown(event)">
                    <div class="cl-pill-icon">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div class="cl-pill-texts">
                        <span class="cl-pill-lbl">Location</span>
                        <span class="cl-pill-val" id="locationDisplay">All Locations</span>
                    </div>
                    <div class="cl-pill-chevron">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>

                {{-- Dropdown popover panel --}}
                <div class="cl-loc-dropdown-panel" id="clLocDropdownPanel">
                    <div class="cl-loc-search-box">
                        <svg class="cl-loc-search-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                            <circle cx="11" cy="11" r="8"/>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" class="cl-loc-search-inp" id="clLocSearchInp" placeholder="Search countries or cities..." autocomplete="off" oninput="handleLocationSearchInput(this.value)" />
                        <span class="cl-loc-search-clear" id="clLocSearchClear" onclick="clearLocationSearch()">✕</span>
                    </div>
                    <div class="cl-loc-dropdown-body" id="clLocDropdownBody">
                        {{-- Rendered dynamically from window.initialLocationFacets & window.allCountriesList --}}
                    </div>
                </div>

                {{-- Hidden inputs to preserve backward compatibility --}}
                <input type="hidden" id="country" name="country" value="" />
                <input type="hidden" id="country_name" name="country_name" value="" />
                <input type="hidden" id="city_id" name="city_id" value="" />
                <input type="hidden" id="city_name" name="city_name" value="" />
                <input type="hidden" id="unspecified_location" name="unspecified_location" value="0" />
            </div>

            {{-- Filter 2: Industry Filter --}}
            <div class="cl-dropdown-pill" id="industryPill">
                <div class="cl-pill-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                    </svg>
                </div>
                <div class="cl-pill-texts">
                    <span class="cl-pill-lbl">Industry</span>
                    <span class="cl-pill-val" id="industryDisplay">All Industries</span>
                </div>
                <div class="cl-pill-chevron">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <select class="cl-pill-select-native" id="industryFilter" onchange="handleIndustryChange(this)">
                    <option value="" selected>All Industries</option>
                    @if (!empty($industries))
                        @foreach ($industries as $ind)
                            <option value="{{ $ind->ind_id }}">{{ $ind->ind_name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- Filter 3: Activity Filter --}}
            <div class="cl-dropdown-pill" id="activityPill">
                <div class="cl-pill-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                </div>
                <div class="cl-pill-texts">
                    <span class="cl-pill-lbl">Activity</span>
                    <span class="cl-pill-val" id="activityDisplay">All Activity</span>
                </div>
                <div class="cl-pill-chevron">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <select class="cl-pill-select-native" id="activityFilter" onchange="handleActivityChange(this)">
                    <option value="" selected>All Activity</option>
                    <option value="hiring">Hiring Now (Active Jobs)</option>
                    <option value="pipeline">Pipeline Active</option>
                    <option value="dormant">Dormant / Inactive</option>
                </select>
            </div>

            {{-- Filter 4: Sort --}}
            <div class="cl-dropdown-pill" id="sortPill">
                <div class="cl-pill-icon">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="7" y1="4" x2="7" y2="20"/>
                        <polyline points="3 8 7 4 11 8"/>
                        <line x1="17" y1="4" x2="17" y2="20"/>
                        <polyline points="13 16 17 20 21 16"/>
                    </svg>
                </div>
                <div class="cl-pill-texts">
                    <span class="cl-pill-lbl">Sort</span>
                    <span class="cl-pill-val" id="sortDisplay">Recently Added</span>
                </div>
                <div class="cl-pill-chevron">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <select class="cl-pill-select-native" id="sortingfilter" onchange="handleSortChange(this)">
                    <option value="recent" selected>Recently Added</option>
                    <option value="oldest">Oldest First</option>
                    <option value="name_asc">Client Name (A → Z)</option>
                    <option value="name_desc">Client Name (Z → A)</option>
                    <option value="jobs_desc">Most Active Jobs</option>
                </select>
            </div>
        </div>

        <!-- ── TABS BAR ── -->
        <div class="cl-nav-tabs">
            <a href="#" onclick="setClientTab('all', this); return false;" class="cl-tab-item cl-tab-filter active" data-tab="all">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span>All Clients</span>
                <span class="cl-tab-badge" id="tabBadgeAll">{{ $totalClientsCount }}</span>
            </a>

            <a href="#" onclick="setClientTab('internal', this); return false;" class="cl-tab-item cl-tab-filter" data-tab="internal">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                <span>Internal Clients</span>
                <span class="cl-tab-badge" id="tabBadgeInternal">{{ $internalClientsCount }}</span>
            </a>

            <a href="#" onclick="setClientTab('direct', this); return false;" class="cl-tab-item cl-tab-filter" data-tab="direct">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>Direct Clients</span>
                <span class="cl-tab-badge" id="tabBadgeDirect">{{ $directClientsCount }}</span>
            </a>

            <a href="#" onclick="setClientTab('hiring', this); return false;" class="cl-tab-item cl-tab-filter" id="tab-hiring" data-tab="hiring">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="20" height="14" rx="2"/>
                    <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
                </svg>
                <span>Active Companies</span>
                <span class="cl-tab-badge" id="tabBadgeHiring">{{ $hiringClientsCount }}</span>
            </a>

            <a href="#" onclick="setClientTab('inactive', this); return false;" class="cl-tab-item cl-tab-filter" id="tab-inactive" data-tab="inactive">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="10" y1="15" x2="10" y2="9"/>
                    <line x1="14" y1="15" x2="14" y2="9"/>
                </svg>
                <span>Inactive Clients</span>
                <span class="cl-tab-badge" id="tabBadgeInactive">{{ $inactiveClientsCount }}</span>
            </a>

            <a href="#" onclick="setClientTab('request', this); return false;" class="cl-tab-item cl-tab-filter" data-tab="request">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                </svg>
                <span>Pending Requests</span>
                <span class="cl-tab-badge" id="tabBadgeRequest">{{ $pendingRequestsCount }}</span>
            </a>
        </div>

        @if(in_array("view_clients", $userPermissions))
        <!-- ── TABLE ── -->
        <div class="tbl">
            <div class="tbl-w">
                <div class="tbl-scroll-container">
                    <div class="tbl-h" id="clientTableHead">
                        <div class="tbl-th-subcard tbl-th-check">
                            <input class="tbl-cb" type="checkbox" id="clientCheckAll" onclick="toggleSelectAllClients(this)" />
                        </div>
                        <div class="tbl-th-subcard cl-sortable-th" data-sort-field="name" title="Click to sort by Company Name (A-Z / Z-A)" onclick="handleHeaderSort('name')">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                <span>Company Name</span>
                            </div>
                            <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div class="tbl-th-subcard" title="Location">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>Location</span>
                            </div>
                        </div>
                        <div class="tbl-th-subcard cl-sortable-th" data-sort-field="date" title="Click to sort by Date Added (Newest / Oldest)" onclick="handleHeaderSort('date')">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <span>Date</span>
                            </div>
                            <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div class="tbl-th-subcard" title="Last Activity">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>Last Activity</span>
                            </div>
                        </div>
                        <div class="tbl-th-subcard cl-sortable-th" data-sort-field="jobs" title="Click to sort by Most Active Jobs" onclick="handleHeaderSort('jobs')">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                <span>Jobs</span>
                            </div>
                            <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div class="tbl-th-subcard" title="Hiring Pipeline">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                                <span>Hiring Pipeline</span>
                            </div>
                            <svg class="tbl-th-info" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-toggle="tooltip" title="Funnel: Sourced → Evaluated → Interviews → Offers → Joined"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        </div>
                    </div>

                    <div class="loadingMessage" style="display:none; position:absolute; left:50%; top:50%; transform:translate(-50%, -50%); z-index:10; background:rgba(255,255,255,0.9); padding:10px 20px; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.1);">Processing...</div>
                    <div id="cl_search_sync_banner" class="cl-sync-banner" style="display:none; margin: 8px 12px 4px 12px;">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>Showing cached clients. Live search projection is syncing in the background...</span>
                    </div>
                    <div id="allClients">{!! $initialHtml ?? '' !!}</div>
                </div>

                <!-- ── PAGINATION BAR ── -->
                <div id="pagination">{!! $initialPagination ?? '' !!}</div>
            </div>
        </div>
        @endif

    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->

</div>
<!--end::Content-->

<!-- ── DRAWER FOR INTERNAL CLIENT ── -->
<div id="kt_activitiesClient_toggle"></div>
<div id="kt_activitiesClient" class="bg-body cd-activities-drawer" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close" data-kt-drawer-width="{default:'100%', 'md': '92%', 'lg': '88%', 'xl': '1180px'}">
  <div class="card shadow-none rounded-0 w-100">
    <button type="button" id="kt_activitiesClient_close" class="d-none" aria-hidden="true"></button>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
  </div>
</div>

<!-- ── DRAWER FOR DIRECT CLIENT ── -->
<div id="kt_activitiesDirectClient_toggle"></div>
<div id="kt_activitiesDirectClient" class="bg-body cd-activities-drawer" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesDirectClient_toggle" data-kt-drawer-close="#kt_activitiesDirectClient_close" data-kt-drawer-width="{default:'100%', 'md': '92%', 'lg': '88%', 'xl': '1180px'}">
  <div class="card shadow-none rounded-0 w-100">
    <button type="button" id="kt_activitiesDirectClient_close" class="d-none" aria-hidden="true"></button>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesDirectClient_body"> &nbsp; </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<script>
$(document).ready(function() {
    var searchTimer = null;
    $('#kt_tagify_2').on('keyup input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            fetchCandidates(1);
        }, 300);
    });
});

function filtersdataget() {
    fetchCandidates(1, true);
}

// ── Location Combobox Controls ──
function toggleLocationDropdown(e) {
    if (e) {
        e.stopPropagation();
        e.preventDefault();
    }
    const $panel = $('#clLocDropdownPanel');
    const isVisible = $panel.hasClass('show');
    if (isVisible) {
        $panel.removeClass('show');
        $('#clLocBtn').removeClass('active');
    } else {
        $panel.addClass('show');
        $('#clLocBtn').addClass('active');
        $('#clLocSearchInp').focus();
        renderLocationDropdown($('#clLocSearchInp').val() || '');
    }
}

function handleLocationSearchInput(q) {
    if (q && q.trim()) {
        $('#clLocSearchClear').show();
    } else {
        $('#clLocSearchClear').hide();
    }
    renderLocationDropdown(q);
}

function clearLocationSearch() {
    $('#clLocSearchInp').val('');
    $('#clLocSearchClear').hide();
    renderLocationDropdown('');
    $('#clLocSearchInp').focus();
}

function escapeAttr(str) {
    if (!str) return '';
    return String(str).replace(/'/g, "\\'").replace(/"/g, '&quot;');
}

function highlightText(text, query) {
    if (!query || !text) return text || '';
    const index = text.toLowerCase().indexOf(query.toLowerCase());
    if (index === -1) return text;
    const before = text.substring(0, index);
    const match = text.substring(index, index + query.length);
    const after = text.substring(index + query.length);
    return `${before}<b style="color:#d97706; text-decoration:underline;">${match}</b>${after}`;
}

function renderLocationDropdown(filterQuery = '') {
    const q = (filterQuery || '').toLowerCase().trim();
    const $body = $('#clLocDropdownBody');
    const facets = window.initialLocationFacets || { total: 0, located_count: 0, unspecified_count: 0, hubs: [] };
    const hubs = facets.hubs || [];
    const unspecifiedCount = facets.unspecified_count || 0;
    const countries = window.allCountriesList || [];

    let html = '';

    // Option 1: All Locations (Reset)
    const isAllSelected = !clientListState.country_id && !clientListState.unspecified_location;
    if (!q || 'all locations'.includes(q) || 'clear'.includes(q)) {
        html += `
            <div class="cl-loc-item ${isAllSelected ? 'selected' : ''}" onclick="resetLocationFilter(true)">
                <div class="cl-loc-item-left">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m2 12 20 0"/></svg>
                    <span class="cl-loc-item-title">All Locations</span>
                </div>
                <span class="cl-loc-badge">${facets.total || 0}</span>
            </div>
        `;
    }

    let hubsRendered = 0;
    let hubHtml = '';

    // Active Hubs Section
    hubs.forEach(function(hub) {
        const countryMatch = hub.country_name.toLowerCase().includes(q);
        const matchingCities = (hub.cities || []).filter(c => c.city_name.toLowerCase().includes(q));

        if (!q || countryMatch || matchingCities.length > 0) {
            hubsRendered++;
            const isCountrySelected = (clientListState.country_id == hub.country_id && !clientListState.city_id && !clientListState.unspecified_location);
            const hasCities = (hub.cities && hub.cities.length > 0);
            const isExpanded = (q.length > 0) || (clientListState.country_id == hub.country_id);

            hubHtml += `
                <div class="cl-loc-item-container" data-country-id="${hub.country_id}">
                    <div class="cl-loc-item ${isCountrySelected ? 'selected' : ''}" onclick="selectLocationCountry(${hub.country_id}, '${escapeAttr(hub.country_name)}', true)">
                        <div class="cl-loc-item-left">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            <span class="cl-loc-item-title">${highlightText(hub.country_name, q)}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="cl-loc-badge">${hub.count}</span>
                            ${hasCities ? `
                                <span class="cl-loc-expand-btn ${isExpanded ? 'expanded' : ''}" onclick="toggleCityExpand(event, ${hub.country_id})">
                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                                </span>
                            ` : ''}
                        </div>
                    </div>
            `;

            // Cities list
            if (hasCities) {
                hubHtml += `<div class="cl-loc-city-list ${isExpanded ? 'show' : ''}" id="clLocCities_${hub.country_id}">`;
                hub.cities.forEach(function(city) {
                    if (!q || countryMatch || city.city_name.toLowerCase().includes(q)) {
                        const isCitySelected = (clientListState.country_id == hub.country_id && (clientListState.city_id == city.city_id || clientListState.city_name == city.city_name));
                        hubHtml += `
                            <div class="cl-loc-city-item ${isCitySelected ? 'selected' : ''}" onclick="selectLocationCity(${hub.country_id}, '${escapeAttr(hub.country_name)}', '${city.city_id}', '${escapeAttr(city.city_name)}', true)">
                                <span>${highlightText(city.city_name, q)}</span>
                                <span class="cl-loc-badge" style="font-size:9.5px; padding:0 5px;">${city.count}</span>
                            </div>
                        `;
                    }
                });
                hubHtml += `</div>`;
            }

            hubHtml += `</div>`;
        }
    });

    if (hubHtml) {
        html += `<div class="cl-loc-section-hdr">Your Client Hubs</div>${hubHtml}`;
    }

    // Unspecified Location option
    if (unspecifiedCount > 0 && (!q || 'location not specified'.includes(q) || 'unspecified'.includes(q) || 'unknown'.includes(q))) {
        const isUnspecifiedSelected = clientListState.unspecified_location === true;
        html += `
            <div class="cl-loc-item ${isUnspecifiedSelected ? 'selected' : ''}" onclick="selectLocationUnspecified(true)">
                <div class="cl-loc-item-left">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                    <span class="cl-loc-item-title" style="color:#64748b;">Location Not Specified</span>
                </div>
                <span class="cl-loc-badge" style="background:#f1f5f9; color:#64748b;">${unspecifiedCount}</span>
            </div>
        `;
    }

    // All 250 Countries Section
    let globalHtml = '';
    let globalCount = 0;
    const maxGlobalToShow = q ? 50 : 15;

    countries.forEach(function(c) {
        const inHubs = hubs.some(h => h.country_id == c.id || h.country_name.toLowerCase() === c.country_name.toLowerCase());
        if (inHubs && !q) return;

        if (!q || c.country_name.toLowerCase().includes(q)) {
            if (globalCount < maxGlobalToShow) {
                globalCount++;
                const isSelected = (clientListState.country_id == c.id && !clientListState.city_id && !clientListState.unspecified_location);
                globalHtml += `
                    <div class="cl-loc-item ${isSelected ? 'selected' : ''}" onclick="selectLocationCountry(${c.id}, '${escapeAttr(c.country_name)}', true)">
                        <div class="cl-loc-item-left">
                            <span style="font-size:13px; line-height:1;">${c.emoji || '🌐'}</span>
                            <span class="cl-loc-item-title">${highlightText(c.country_name, q)}</span>
                        </div>
                    </div>
                `;
            }
        }
    });

    if (globalHtml) {
        html += `
            <div class="cl-loc-divider"></div>
            <div class="cl-loc-section-hdr">${q ? 'Global Countries Match' : 'All Countries'}</div>
            ${globalHtml}
        `;
        if (!q && countries.length > maxGlobalToShow) {
            html += `<div style="padding:4px 14px 8px 14px; font-size:10.5px; color:#94a3b8; font-style:italic;">Type above to search all 250 countries...</div>`;
        }
    }

    if (!html) {
        html = `<div class="cl-loc-empty">No locations matching "${escapeAttr(q)}"</div>`;
    }

    $body.html(html);
}

function toggleCityExpand(e, countryId) {
    e.stopPropagation();
    e.preventDefault();
    const $cities = $(`#clLocCities_${countryId}`);
    const $btn = $(e.currentTarget);
    $cities.toggleClass('show');
    $btn.toggleClass('expanded');
}

function resetLocationFilter(triggerFetch = true) {
    clientListState.country_id = null;
    clientListState.country_name = '';
    clientListState.city_id = null;
    clientListState.city_name = '';
    clientListState.unspecified_location = false;

    $('#country').val('');
    $('#country_name').val('');
    $('#city_id').val('');
    $('#city_name').val('');
    $('#unspecified_location').val('0');

    $('#locationDisplay').text('All Locations');
    $('#clLocBtn').removeClass('has-filter active');
    $('#clLocDropdownPanel').removeClass('show');

    if (triggerFetch) {
        filtersdataget();
    }
}

function selectLocationCountry(countryId, countryName, triggerFetch = true) {
    clientListState.country_id = countryId;
    clientListState.country_name = countryName;
    clientListState.city_id = null;
    clientListState.city_name = '';
    clientListState.unspecified_location = false;

    $('#country').val(countryId);
    $('#country_name').val(countryName);
    $('#city_id').val('');
    $('#city_name').val('');
    $('#unspecified_location').val('0');

    $('#locationDisplay').text(countryName);
    $('#clLocBtn').addClass('has-filter').removeClass('active');
    $('#clLocDropdownPanel').removeClass('show');

    if (triggerFetch) {
        filtersdataget();
    }
}

function selectLocationCity(countryId, countryName, cityId, cityName, triggerFetch = true) {
    clientListState.country_id = countryId;
    clientListState.country_name = countryName;
    clientListState.city_id = (cityId === 'unspecified') ? null : cityId;
    clientListState.city_name = cityName;
    clientListState.unspecified_location = false;

    $('#country').val(countryId);
    $('#country_name').val(countryName);
    $('#city_id').val(clientListState.city_id || '');
    $('#city_name').val(cityName);
    $('#unspecified_location').val('0');

    const displayLabel = `${countryName}: ${cityName}`;
    $('#locationDisplay').text(displayLabel);
    $('#clLocBtn').addClass('has-filter').removeClass('active');
    $('#clLocDropdownPanel').removeClass('show');

    if (triggerFetch) {
        filtersdataget();
    }
}

function selectLocationUnspecified(triggerFetch = true) {
    clientListState.country_id = null;
    clientListState.country_name = '';
    clientListState.city_id = null;
    clientListState.city_name = '';
    clientListState.unspecified_location = true;

    $('#country').val('unspecified');
    $('#country_name').val('');
    $('#city_id').val('');
    $('#city_name').val('');
    $('#unspecified_location').val('1');

    $('#locationDisplay').text('Location Not Specified');
    $('#clLocBtn').addClass('has-filter').removeClass('active');
    $('#clLocDropdownPanel').removeClass('show');

    if (triggerFetch) {
        filtersdataget();
    }
}

function selectLocationByCountryAndCity(countryVal, cityVal, triggerFetch = true) {
    if (!countryVal || countryVal === 'all') {
        resetLocationFilter(triggerFetch);
        return;
    }
    if (countryVal === 'unspecified') {
        selectLocationUnspecified(triggerFetch);
        return;
    }

    const countries = window.allCountriesList || [];
    let matchedCountry = countries.find(c => c.id == countryVal || c.country_name.toLowerCase() === countryVal.toLowerCase());
    let countryName = matchedCountry ? matchedCountry.country_name : countryVal;
    let countryId = matchedCountry ? matchedCountry.id : countryVal;

    if (cityVal) {
        selectLocationCity(countryId, countryName, cityVal, cityVal, triggerFetch);
    } else {
        selectLocationCountry(countryId, countryName, triggerFetch);
    }
}

// ── Dropdown filter sync handlers ──
function handleIndustryChange(sel) {
    const text = sel.options[sel.selectedIndex].text;
    const val = sel.value;
    $('#industryDisplay').text(text);
    clientListState.industry_id = val;
    filtersdataget();
}

function handleActivityChange(sel) {
    const text = sel.options[sel.selectedIndex].text;
    const val = sel.value;
    $('#activityDisplay').text(text);
    clientListState.activity = val;
    filtersdataget();
}

function handleSortChange(sel) {
    const text = sel.options[sel.selectedIndex].text;
    const val = sel.value;
    $('#sortDisplay').text(text);
    clientListState.sort = val;
    updateHeaderSortIndicators(val);
    filtersdataget();
}

function handleHeaderSort(field) {
    let currentSort = $('#sortingfilter').val() || 'recent';
    let nextSort = 'recent';

    if (field === 'name') {
        nextSort = (currentSort === 'name_asc') ? 'name_desc' : 'name_asc';
    } else if (field === 'date') {
        nextSort = (currentSort === 'recent') ? 'oldest' : 'recent';
    } else if (field === 'jobs') {
        nextSort = (currentSort === 'jobs_desc') ? 'recent' : 'jobs_desc';
    }

    $('#sortingfilter').val(nextSort);
    const sortText = $(`#sortingfilter option[value="${nextSort}"]`).text() || nextSort;
    $('#sortDisplay').text(sortText);
    clientListState.sort = nextSort;
    updateHeaderSortIndicators(nextSort);
    filtersdataget();
}

function updateHeaderSortIndicators(activeSort) {
    $('.cl-sortable-th').removeClass('active-sort active-sort-asc active-sort-desc');
    if (activeSort === 'name_asc') {
        $('.cl-sortable-th[data-sort-field="name"]').addClass('active-sort active-sort-asc');
    } else if (activeSort === 'name_desc') {
        $('.cl-sortable-th[data-sort-field="name"]').addClass('active-sort active-sort-desc');
    } else if (activeSort === 'recent') {
        $('.cl-sortable-th[data-sort-field="date"]').addClass('active-sort active-sort-desc');
    } else if (activeSort === 'oldest') {
        $('.cl-sortable-th[data-sort-field="date"]').addClass('active-sort active-sort-asc');
    } else if (activeSort === 'jobs_desc') {
        $('.cl-sortable-th[data-sort-field="jobs"]').addClass('active-sort active-sort-desc');
    }
}

// Close location dropdown on outside click or escape
$(document).on('click', function(e) {
    if (!$(e.target).closest('#clLocDropdownWrapper').length) {
        $('#clLocDropdownPanel').removeClass('show');
        $('#clLocBtn').removeClass('active');
    }
});

$(document).on('keydown', function(e) {
    if (e.key === 'Escape' && $('#clLocDropdownPanel').hasClass('show')) {
        $('#clLocDropdownPanel').removeClass('show');
        $('#clLocBtn').removeClass('active');
    }
});

// ── Select all checkbox ──
function toggleSelectAllClients(master) {
    var checked = $(master).is(':checked');
    $('#allClients .tbl-cb').prop('checked', checked);
}

// ── Client-side CSV Exporter ──
function exportClientsToCsv() {
    if (clWorker && clWorkerReady) {
        clWorker.postMessage({
            type: 'EXPORT_CSV'
        });
        return;
    }

    var rows = [];
    rows.push(['Company Name', 'Industry', 'Location', 'Jobs Hiring', 'Jobs Hold', 'Jobs Closed', 'Sourced', 'Evaluated', 'Interviews', 'Offers', 'Joined', 'Client Type', 'Date', 'Last Activity']);

    $('#allClients .tbl-r').each(function() {
        var $r = $(this);
        var name = $r.find('.cl-name').text().trim();
        var ind = $r.find('.cl-ind').text().trim();
        var locCity = $r.find('.cl-loc-city').text().trim();
        var locCountry = $r.find('.cl-loc-country').text().trim();
        var loc = locCity + (locCity && locCountry ? ', ' : '') + locCountry;
        var hiringJobs = $r.find('.tbl-cell-jobs .cl-job-line:eq(0) .cl-job-num').text().trim();
        var holdJobs = $r.find('.tbl-cell-jobs .cl-job-line:eq(1) .cl-job-num').text().trim();
        var closedJobs = $r.find('.tbl-cell-jobs .cl-job-line:eq(2) .cl-job-num').text().trim();
        var sourced = $r.find('.pill-sourced').text().trim();
        var evaluated = $r.find('.pill-evaluated').text().trim();
        var interviews = $r.find('.pill-interviews').text().trim();
        var offers = $r.find('.pill-offers').text().trim();
        var joined = $r.find('.pill-joined').text().trim();
        var type = $r.find('.cl-type-name').text().trim();
        var conn = $r.find('.cl-date-box span').text().trim();
        var act = $r.find('.cl-act-main').text().trim();

        if (name) {
            rows.push([name, ind, loc, hiringJobs, holdJobs, closedJobs, sourced, evaluated, interviews, offers, joined, type, conn, act]);
        }
    });

    if (rows.length <= 1) {
        Swal.fire({
            icon: 'info',
            title: 'No Data',
            text: 'There are no clients visible on the current page to export.'
        });
        return;
    }

    var csvContent = "data:text/csv;charset=utf-8," + rows.map(e => e.map(cell => '"' + ('' + cell).replace(/"/g, '""') + '"').join(",")).join("\n");
    var encodedUri = encodeURI(csvContent);
    var link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "clients_export_" + new Date().toISOString().slice(0, 10) + ".csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// ── Bounded Browser RAM LRU Cache for Client Drawers ──
const DrawerLRUCache = {
    cache: new Map(),
    maxEntries: 25,

    get: function(key) {
        return this.cache.get(key) || null;
    },

    set: function(key, html) {
        if (this.cache.size >= this.maxEntries) {
            const oldestKey = this.cache.keys().next().value;
            this.cache.delete(oldestKey);
        }
        this.cache.set(key, html);
    },

    invalidate: function(id) {
        this.cache.delete(`internal_${id}`);
        this.cache.delete(`direct_${id}`);
    }
};

function openClientDrawer(drawerId, toggleId) {
    var drawerEl = document.getElementById(drawerId);
    if (drawerEl) {
        if (typeof KTDrawer !== 'undefined') {
            var drawer = KTDrawer.getInstance(drawerEl);
            if (!drawer && typeof KTDrawer === 'function') {
                try {
                    drawer = new KTDrawer(drawerEl);
                } catch (e) {}
            }
            if (drawer && typeof drawer.show === 'function') {
                drawer.show();
                return;
            }
        }
    }
    if (typeof openMetronicDrawer === 'function') {
        openMetronicDrawer(drawerId, '#' + toggleId);
        return;
    }
    $('#' + toggleId).trigger('click');
}

$(document).on('click', '.show-detail', function (e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    openClientDrawer('kt_activitiesClient', 'kt_activitiesClient_toggle');
    var currentPageClients = $('.custom-pagination .page-link.active').data('page') || 1;
    localStorage.setItem('currentPageClients', currentPageClients);
    var id = $(this).data('row-id') || $(this).closest('[data-row-id]').data('row-id');
    if (!id) return;

    var cachedDrawer = DrawerLRUCache.get(`internal_${id}`);
    if (cachedDrawer) {
        $('#kt_activitiesDirectClient_body').html('');
        $('#kt_activitiesClient_body').html(cachedDrawer);
        return;
    }

    var url = "{{ route('service_company.clients.viewmore', ':id') }}".replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

    $.ajax({
        type: 'GET',
        url: url,
        beforeSend: function() {
            $('#kt_activitiesClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
        },
        success: function (response) {
            $('#kt_activitiesDirectClient_body').html('');
            $('#kt_activitiesClient_body').html(response.view);
            if (response.view) {
                DrawerLRUCache.set(`internal_${id}`, response.view);
            }
        }
    });
});

$(document).on('click', '.show-detail-direct', function (e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    openClientDrawer('kt_activitiesDirectClient', 'kt_activitiesDirectClient_toggle');
    var id = $(this).data('row-id') || $(this).closest('[data-row-id]').data('row-id');
    if (!id) return;

    var cachedDrawer = DrawerLRUCache.get(`direct_${id}`);
    if (cachedDrawer) {
        $('#kt_activitiesClient_body').html('');
        $('#kt_activitiesDirectClient_body').html(cachedDrawer);
        return;
    }

    var url = "{{ route('service_company.clients.dviewmore', ':id') }}".replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";

    $.ajax({
        type: 'GET',
        url: url,
        beforeSend: function() {
            $('#kt_activitiesDirectClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
        },
        success: function (response) {
            $('#kt_activitiesClient_body').html('');
            $('#kt_activitiesDirectClient_body').html(response.view);
            if (response.view) {
                DrawerLRUCache.set(`direct_${id}`, response.view);
            }
        }
    });
});

$(document).on('click', '#kt_activitiesClient_close, #kt_activitiesDirectClient_close, .cd-close-drawer', function(e) {
    e.preventDefault();
    var drawerEl = document.getElementById('kt_activitiesClient');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerEl);
        if (drawer && typeof drawer.hide === 'function') {
            drawer.hide();
        }
    }
    var drawerDirectEl = document.getElementById('kt_activitiesDirectClient');
    if (drawerDirectEl && typeof KTDrawer !== 'undefined') {
        var drawerD = KTDrawer.getInstance(drawerDirectEl);
        if (drawerD && typeof drawerD.hide === 'function') {
            drawerD.hide();
        }
    }
});

// ── Canonical Client Listing State (Zero tenant_id, strictly session-scoped) ──
window.clientListState = {
    tab: {!! json_encode($initialTab ?? 'all') !!},
    search: '',
    country_id: null,
    country_name: '',
    city_id: null,
    city_name: '',
    unspecified_location: false,
    industry_id: '',
    activity: '',
    sort: 'recent',
    page: 1,
    per_page: 10,
    cursor: null
};
if (window.clientListState.tab === 'requests') window.clientListState.tab = 'request';
if (window.clientListState.tab === 'custom') window.clientListState.tab = 'internal';

window.initialLocationFacets = {!! json_encode($locationFacets ?? ['total' => 0, 'located_count' => 0, 'unspecified_count' => 0, 'hubs' => []]) !!};
window.allCountriesList = {!! json_encode($countries ?? []) !!};

// ── Offline & Zero-Server IndexedDB Reference Cache ──
const ClientLocationDB = {
    dbName: 'jero_client_db',
    storeName: 'reference_data',
    version: 1,

    open: function() {
        return new Promise(function(resolve, reject) {
            if (!window.indexedDB) return reject(new Error('IndexedDB not supported'));
            const req = indexedDB.open(ClientLocationDB.dbName, ClientLocationDB.version);
            req.onupgradeneeded = function(e) {
                const db = e.target.result;
                if (!db.objectStoreNames.contains(ClientLocationDB.storeName)) {
                    db.createObjectStore(ClientLocationDB.storeName, { keyPath: 'key' });
                }
            };
            req.onsuccess = function(e) { resolve(e.target.result); };
            req.onerror = function(e) { reject(e.target.error); };
        });
    },

    get: function(key) {
        return new Promise(function(resolve) {
            ClientLocationDB.open().then(function(db) {
                const tx = db.transaction(ClientLocationDB.storeName, 'readonly');
                const store = tx.objectStore(ClientLocationDB.storeName);
                const req = store.get(key);
                req.onsuccess = function() { resolve(req.result ? req.result.value : null); };
                req.onerror = function() { resolve(null); };
            }).catch(function() { resolve(null); });
        });
    },

    set: function(key, value) {
        return new Promise(function(resolve) {
            ClientLocationDB.open().then(function(db) {
                const tx = db.transaction(ClientLocationDB.storeName, 'readwrite');
                const store = tx.objectStore(ClientLocationDB.storeName);
                store.put({ key: key, value: value });
                tx.oncomplete = function() { resolve(true); };
                tx.onerror = function() { resolve(false); };
            }).catch(function() { resolve(false); });
        });
    }
};

function initCountryBundle() {
    if (window.allCountriesList && window.allCountriesList.length > 0) {
        ClientLocationDB.set('countries_list', window.allCountriesList);
        return;
    }
    ClientLocationDB.get('countries_list').then(function(cached) {
        if (cached && cached.length > 0) {
            window.allCountriesList = cached;
            renderLocationDropdown($('#clLocSearchInp').val() || '');
            return;
        }
        fetch('{{ asset("assets/service_company/client/countries.json") }}')
            .then(function(res) { return res.ok ? res.json() : null; })
            .then(function(countries) {
                if (countries && countries.length > 0) {
                    window.allCountriesList = countries;
                    ClientLocationDB.set('countries_list', countries);
                    renderLocationDropdown($('#clLocSearchInp').val() || '');
                }
            }).catch(function(err) {
                console.warn('[Countries Bundle] Offline fetch fallback:', err);
            });
    });
}

// ── Web Worker In-Browser OLAP Engine (Zero Server Read Strain) ──
let clWorker = null;
let clWorkerReady = false;
let pendingWorkerQuery = null;

function renderClientRows(rows) {
    if (!rows || rows.length === 0) {
        return `
        <div class="cl-empty-state">
            <div class="cl-empty-icon">
                <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="10" r="10"/>
                    <line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
            </div>
            <div class="cl-empty-title">No clients found</div>
            <div class="cl-empty-desc">No clients match your selected filters or search query.</div>
        </div>`;
    }

    return rows.map(function(r) {
        const logoHtml = (r.logo_url && r.logo_url.length > 10)
            ? `<img src="${r.logo_url}" alt="${escapeAttr(r.initials)}" class="cl-logo-img" onerror="this.style.display='none'; if(this.nextElementSibling){this.nextElementSibling.style.display='flex';}">
               <span class="cl-initials-fallback ${r.bg_color || 'bg-light-primary text-primary'}" style="display:none;">${escapeAttr(r.initials)}</span>`
            : `<span class="cl-initials-fallback ${r.bg_color || 'bg-light-primary text-primary'}">${escapeAttr(r.initials)}</span>`;

        const indHtml = r.industry ? `<div class="cl-ind" title="${escapeAttr(r.industry)}">${escapeAttr(r.industry)}</div>` : '';
        const sizeHtml = r.company_size ? `<div class="cl-size">${escapeAttr(r.company_size)}</div>` : '';

        let locHtml = '';
        if (r.location_main) locHtml += `<div class="cl-loc-city" title="${escapeAttr(r.location_main)}">${escapeAttr(r.location_main)}</div>`;
        if (r.country) locHtml += `<div class="cl-loc-country">${escapeAttr(r.country)}</div>`;
        if (!locHtml) locHtml = `<div class="cl-loc-empty">-</div>`;

        const jobActive = r.jobs ? (r.jobs.hiring || r.jobs.active || 0) : 0;
        const jobHold = r.jobs ? (r.jobs.hold || 0) : 0;
        const jobClosed = r.jobs ? (r.jobs.closed || 0) : 0;

        const sourced = r.pipeline ? (r.pipeline.sourced || 0) : 0;
        const evaluated = r.pipeline ? (r.pipeline.evaluated || 0) : 0;
        const interviews = r.pipeline ? (r.pipeline.interviews || 0) : 0;
        const offers = r.pipeline ? (r.pipeline.offers || 0) : 0;
        const joined = r.pipeline ? (r.pipeline.joined || 0) : 0;

        const daysAgo = r.days_ago || '';
        const clientTypeClass = r.is_direct ? 'type-direct' : 'type-internal';
        const clientTypeCss = r.client_type_css || (r.is_direct ? 'show-detail-direct' : 'show-detail');
        const clientTypeLabel = r.client_type || (r.is_direct ? 'Direct Client' : 'Internal Client');

        return `
        <div class="tbl-r">
            <div class="tbl-cell-check">
                <input type="checkbox" class="tbl-cb" value="${r.id}" onclick="event.stopPropagation()">
            </div>
            <div class="tbl-cell-client ${clientTypeCss}" data-row-id="${r.id}" title="${escapeAttr(r.name)}">
                <div class="cl-company-wrapper">
                    <div class="cl-logo-box">${logoHtml}</div>
                    <div class="cl-details-box">
                        <div class="cl-name">${escapeAttr(r.name)}</div>
                        ${indHtml}
                        ${sizeHtml}
                    </div>
                </div>
            </div>
            <div class="tbl-cell-loc">${locHtml}</div>
            <div class="tbl-cell-type">
                <div class="cl-date-box">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                    <span>${escapeAttr(r.connected_on || '-')}</span>
                </div>
                <div class="cl-conn-caption">Connected on</div>
                <div class="cl-type-name ${clientTypeClass} ${clientTypeCss}" data-row-id="${r.id}">
                    ${escapeAttr(clientTypeLabel)}
                </div>
            </div>
            <div class="tbl-cell-activity">
                <div class="cl-act-main">${escapeAttr(daysAgo || 'No activity yet')}</div>
                <div class="cl-act-desc">${daysAgo ? 'Active in system' : 'Be the first to add a job or candidate.'}</div>
                <div class="cl-act-status ${jobActive > 0 ? 'status-active' : 'status-inactive'}">
                    <span class="cl-act-status-dot"></span>
                    <span>${jobActive > 0 ? 'Active' : 'Inactive'}</span>
                </div>
            </div>
            <div class="tbl-cell-jobs">
                <div class="cl-job-line ${jobActive > 0 ? 'has-count cl-job-hiring' : 'no-count'}">
                    <span class="cl-job-bullet ${jobActive > 0 ? 'bullet-hiring' : 'bullet-neutral'}"></span>
                    <span class="cl-job-num">${jobActive}</span>
                    <span class="cl-job-label">Hiring</span>
                </div>
                <div class="cl-job-line ${jobHold > 0 ? 'has-count cl-job-hold' : 'no-count'}">
                    <span class="cl-job-bullet ${jobHold > 0 ? 'bullet-hold' : 'bullet-neutral'}"></span>
                    <span class="cl-job-num">${jobHold}</span>
                    <span class="cl-job-label">Hold</span>
                </div>
                <div class="cl-job-line ${jobClosed > 0 ? 'has-count cl-job-closed' : 'no-count'}">
                    <span class="cl-job-bullet ${jobClosed > 0 ? 'bullet-closed' : 'bullet-neutral'}"></span>
                    <span class="cl-job-num">${jobClosed}</span>
                    <span class="cl-job-label">Closed</span>
                </div>
            </div>
            <div class="tbl-cell-pipeline">
                <div class="cl-pipeline-flow">
                    <div class="cl-pipe-step">
                        <span class="cl-pipe-pill pill-sourced">${sourced}</span>
                        <span class="cl-pipe-label">Sourced</span>
                    </div>
                    <div class="cl-pipe-step">
                        <span class="cl-pipe-pill pill-evaluated">${evaluated}</span>
                        <span class="cl-pipe-label">Evaluated</span>
                    </div>
                    <div class="cl-pipe-step">
                        <span class="cl-pipe-pill pill-interviews">${interviews}</span>
                        <span class="cl-pipe-label">Interviews</span>
                    </div>
                    <div class="cl-pipe-step">
                        <span class="cl-pipe-pill pill-offers">${offers}</span>
                        <span class="cl-pipe-label">Offers</span>
                    </div>
                    <div class="cl-pipe-step">
                        <span class="cl-pipe-pill pill-joined">${joined}</span>
                        <span class="cl-pipe-label">Joined</span>
                    </div>
                </div>
            </div>
        </div>`;
    }).join('');
}

function renderClientPagination(pagi) {
    if (!pagi || pagi.total <= 0) return '';
    const perPage = pagi.per_page || 10;
    const total = pagi.total;
    const totalPages = pagi.last_page || Math.ceil(total / perPage);
    const curPage = pagi.current_page || 1;
    const from = (curPage - 1) * perPage + 1;
    const to = Math.min(curPage * perPage, total);

    let pagesHtml = '';
    for (let i = 1; i <= totalPages; i++) {
        if (i === 1 || i === totalPages || (i >= curPage - 2 && i <= curPage + 2)) {
            const isActive = (i === curPage);
            pagesHtml += `<button class="cl-pagi-page pagi-page ${isActive ? 'pagi-active pagi-active-disabled' : ''}" data-page="${i}" data-listType="allcandidate">${i}</button>`;
        } else if (i === curPage - 3 || i === curPage + 3) {
            pagesHtml += `<span class="cl-pagi-dots">…</span>`;
        }
    }

    return `
    <div class="cl-pagi-bar">
        <div class="cl-pagi-info">
            Showing ${from} to ${to} of ${total} clients
        </div>
        <div class="cl-pagi-controls">
            <div class="cl-pagi-perpage-wrap">
                <span class="cl-pagi-perpage-lbl">Rows per page:</span>
                <select class="cl-pagi-perpage-select pagi-perpage">
                    <option value="10" ${perPage == 10 ? 'selected' : ''}>10</option>
                    <option value="25" ${perPage == 25 ? 'selected' : ''}>25</option>
                    <option value="50" ${perPage == 50 ? 'selected' : ''}>50</option>
                    <option value="100" ${perPage == 100 ? 'selected' : ''}>100</option>
                </select>
            </div>
            <button class="cl-pagi-btn pagi-arrow ${curPage <= 1 ? 'pagi-disabled' : ''}" data-page="${curPage - 1}" data-listType="allcandidate" aria-label="Previous page">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
            </button>
            ${pagesHtml}
            <button class="cl-pagi-btn pagi-arrow ${curPage >= totalPages ? 'pagi-disabled' : ''}" data-page="${curPage + 1}" data-listType="allcandidate" aria-label="Next page">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="m9 18 6-6-6-6"/></svg>
            </button>
        </div>
    </div>`;
}

function handleWorkerMessage(data) {
    if (!data || !data.type) return;

    if (data.type === 'INIT_COMPLETE') {
        clWorkerReady = true;
    } else if (data.type === 'QUERY_RESULT') {
        const q = pendingWorkerQuery;
        if (!q || q.id !== data.queryId) return;
        pendingWorkerQuery = null;

        const renderedHtml = renderClientRows(data.rows);
        const renderedPagi = renderClientPagination({
            total: data.total,
            per_page: data.per_page,
            current_page: data.page,
            last_page: Math.ceil(data.total / data.per_page)
        });

        if (data.counts) {
            $('#span_clientAll, #tabBadgeAll').text(data.counts.all);
            $('#span_clientCustom, #tabBadgeInternal').text(data.counts.internal);
            $('#span_clientDirect, #tabBadgeDirect').text(data.counts.direct);
            $('#span_clientHiring, #tabBadgeHiring').text(data.counts.hiring);
            if (data.counts.inactive !== undefined) {
                $('#span_clientInactive, #tabBadgeInactive').text(data.counts.inactive);
            }
        }

        const syntheticResp = {
            html: renderedHtml,
            total: data.total,
            pagination: renderedPagi,
            rows: data.rows,
            projection_status: 'synced'
        };

        if (q.cacheKey) {
            ClientStore.set(q.cacheKey, syntheticResp);
        }

        applyClientResponse(syntheticResp, data.page);
    } else if (data.type === 'CSV_READY') {
        const blob = new Blob([data.csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", "clients_export_" + new Date().toISOString().slice(0, 10) + ".csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    }
}

function initClientWorker() {
    if (!window.Worker) return;
    try {
        clWorker = new Worker('{{ asset("assets/service_company/client/cl-data-worker.js") }}');
        clWorker.onmessage = function(e) {
            handleWorkerMessage(e.data);
        };
        clWorker.onerror = function(err) {
            console.warn('[CL Worker] Worker runtime error:', err);
            clWorkerReady = false;
        };

        // Seed with initial rows if present
        @if(!empty($initialRows))
        const initialRows = {!! json_encode($initialRows) !!};
        if (initialRows && initialRows.length > 0) {
            clWorker.postMessage({
                type: 'INIT_DATASET',
                rows: initialRows,
                version: {{ (int)($initialDataVersion ?? 1) }}
            });
            clWorkerReady = true;
        }
        @endif

        // Hydrate from active projection in background
        fetchActiveProjection();
    } catch(e) {
        console.warn('[CL Worker] Web Worker init skipped:', e);
        clWorker = null;
    }
}

function fetchActiveProjection() {
    const companyId = {{ (int)auth()->user()->company_id }};
    const userId = {{ (int)auth()->id() }};
    const etagKey = 'jero_cl_etag_tenant_' + companyId + '_user_' + userId;
    const cachedEtag = localStorage.getItem(etagKey);
    const headers = {};
    if (cachedEtag) {
        headers['If-None-Match'] = cachedEtag;
    }

    const idbStoreKey = 'jero_client_projection:tenant=' + companyId + ':user=' + userId + ':projection=clients:schema=2';

    fetch('{{ route("service_company.clients.activeClientProjection") }}', {
        headers: headers
    }).then(async function(res) {
        if (res.status === 304) {
            const stored = await ClientLocationDB.get(idbStoreKey);
            if (stored && stored.rows && stored.rows.length > 0 && clWorker) {
                clWorker.postMessage({
                    type: 'INIT_DATASET',
                    rows: stored.rows,
                    version: stored.data_version || 1
                });
                clWorkerReady = true;
            }
            return;
        }
        if (res.ok) {
            const newEtag = res.headers.get('ETag');
            if (newEtag) localStorage.setItem(etagKey, newEtag);
            const data = await res.json();
            if (data && data.rows && clWorker) {
                if (ClientStore.latestMutationVersion && data.data_version && data.data_version < ClientStore.latestMutationVersion) {
                    console.warn('[Sync] Ignored stale projection version:', data.data_version, 'current mutation:', ClientStore.latestMutationVersion);
                    return;
                }
                clWorker.postMessage({
                    type: 'INIT_DATASET',
                    rows: data.rows,
                    version: data.data_version || 1
                });
                clWorkerReady = true;
                ClientLocationDB.set(idbStoreKey, data);
            }
        }
    }).catch(function(err) {
        console.warn('[CL Worker] Background activeClientProjection fetch skipped:', err);
    });
}

// ── Bounded Browser RAM Store (ClientStore) with SWR & Request Token Guard ──
const ClientStore = {
    cache: new Map(),
    maxEntries: 50,
    currentXhr: null,
    latestRequestId: 0,
    latestMutationVersion: 0,
    maxStaleAgeMs: 5000, // 5 seconds bounded staleness for SWR

    getKey: function(tab, page, search, sort, countryId, cityId, unspecified, industryId, activity, perPage) {
        return `${tab}:${page}:${search || ''}:${sort || 'recent'}:${countryId || ''}:${cityId || ''}:${unspecified ? 1 : 0}:${industryId || ''}:${activity || ''}:${perPage || 10}`;
    },

    get: function(key) {
        const entry = this.cache.get(key);
        if (!entry) return null;
        return {
            data: entry.data,
            age: Date.now() - entry.timestamp,
            isStale: (Date.now() - entry.timestamp) > this.maxStaleAgeMs
        };
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
    },

    invalidateTab: function(tab) {
        for (const k of this.cache.keys()) {
            if (k.startsWith(`${tab}:`)) {
                this.cache.delete(k);
            }
        }
    },

    clear: function() {
        this.cache.clear();
    }
};

@if(!empty($initialHtml))
(function() {
    var initTab = {!! json_encode($initialTab ?? 'all') !!};
    if (initTab === 'requests') initTab = 'request';
    if (initTab === 'custom') initTab = 'internal';
    var initKey = ClientStore.getKey(initTab, 1, '', 'recent', '', '', 0, '', '', 10);
    ClientStore.set(initKey, {
        html: {!! json_encode($initialHtml) !!},
        total: {{ (int)($initialTotal ?? 0) }},
        pagination: {!! json_encode($initialPagination ?? '') !!},
        rows: {!! json_encode($initialRows ?? []) !!},
        data_version: {{ (int)($initialDataVersion ?? 1) }}
    });
})();
@endif

var page_no = localStorage.getItem('currentPageClients') || 1;
var activeClientTab = {!! json_encode($initialTab ?? 'all') !!};
if (activeClientTab === 'requests') activeClientTab = 'request';
if (activeClientTab === 'custom') activeClientTab = 'internal';
var hiringFilter = (activeClientTab === 'hiring') ? 1 : 0;
var initialOpenClientId = {{ (int)($initialOpenClient ?? 0) }};

function syncUrlState(replace = false) {
    try {
        const url = new URL(window.location);
        if (activeClientTab && activeClientTab !== 'all') {
            url.searchParams.set('tab', activeClientTab);
        } else {
            url.searchParams.delete('tab');
        }

        const searchVal = $('#kt_tagify_2').val() || '';
        if (searchVal.trim()) {
            url.searchParams.set('search', searchVal.trim());
        } else {
            url.searchParams.delete('search');
        }

        if (clientListState.unspecified_location) {
            url.searchParams.set('country', 'unspecified');
            url.searchParams.delete('city');
        } else if (clientListState.country_id) {
            url.searchParams.set('country', clientListState.country_id);
            if (clientListState.city_id) {
                url.searchParams.set('city', clientListState.city_id);
            } else {
                url.searchParams.delete('city');
            }
        } else {
            url.searchParams.delete('country');
            url.searchParams.delete('city');
        }

        if (clientListState.industry_id) {
            url.searchParams.set('industry', clientListState.industry_id);
        } else {
            url.searchParams.delete('industry');
        }

        if (clientListState.activity) {
            url.searchParams.set('activity', clientListState.activity);
        } else {
            url.searchParams.delete('activity');
        }

        const sortVal = $('#sortingfilter').val() || 'recent';
        if (sortVal && sortVal !== 'recent' && sortVal !== 'Show All') {
            url.searchParams.set('sort', sortVal);
        } else {
            url.searchParams.delete('sort');
        }

        if (page_no > 1) {
            url.searchParams.set('page', page_no);
        } else {
            url.searchParams.delete('page');
        }

        // Drop open_client after initial consume
        url.searchParams.delete('open_client');

        if (replace) {
            window.history.replaceState({ tab: activeClientTab, page: page_no }, '', url);
        } else {
            window.history.pushState({ tab: activeClientTab, page: page_no }, '', url);
        }
    } catch (e) {
        // Fallback for sandboxed frames
    }
}

function hydrateStateFromUrl(shouldFetch = true) {
    try {
        const params = new URLSearchParams(window.location.search);
        let tab = params.get('tab') || activeClientTab || 'all';
        if (tab === 'requests') tab = 'request';
        if (tab === 'custom') tab = 'internal';

        const search = params.get('search') || params.get('q') || '';
        const country = params.get('country') || '';
        const city = params.get('city') || '';
        const industry = params.get('industry') || '';
        const activity = params.get('activity') || '';
        const sort = params.get('sort') || params.get('sortingfilter') || 'recent';
        const page = parseInt(params.get('page'), 10) || 1;
        const openClient = parseInt(params.get('open_client'), 10) || initialOpenClientId || 0;

        $('#kt_tagify_2').val(search);
        clientListState.search = search;

        // Hydrate location
        if (country === 'unspecified') {
            selectLocationUnspecified(false);
        } else if (country) {
            selectLocationByCountryAndCity(country, city, false);
        } else {
            resetLocationFilter(false);
        }

        // Hydrate industry
        $('#industryFilter').val(industry);
        clientListState.industry_id = industry;
        if (industry) {
            const indText = $(`#industryFilter option[value="${industry}"]`).text() || $('#industryFilter option:selected').text();
            if (indText) $('#industryDisplay').text(indText);
        } else {
            $('#industryDisplay').text('All Industries');
        }

        // Hydrate activity
        $('#activityFilter').val(activity);
        clientListState.activity = activity;
        if (activity) {
            const actText = $(`#activityFilter option[value="${activity}"]`).text() || $('#activityFilter option:selected').text();
            if (actText) $('#activityDisplay').text(actText);
        } else {
            $('#activityDisplay').text('All Activity');
        }

        // Hydrate sort
        $('#sortingfilter').val(sort);
        clientListState.sort = sort;
        if (sort && sort !== 'recent' && sort !== 'Show All') {
            const sortText = $(`#sortingfilter option[value="${sort}"]`).text() || $('#sortingfilter option:selected').text();
            if (sortText) $('#sortDisplay').text(sortText);
        } else {
            $('#sortDisplay').text('Recently Added');
        }
        updateHeaderSortIndicators(sort);

        setClientTab(tab, null, false);
        if (shouldFetch) {
            fetchCandidates(page, false);
        }

        if (openClient > 0) {
            setTimeout(function() {
                triggerOpenClientDrawer(openClient);
            }, 350);
        }
    } catch (e) {
        // Fallback
    }
}

window.addEventListener('popstate', function(event) {
    hydrateStateFromUrl(true);
});

function triggerOpenClientDrawer(clientId) {
    const $target = $(`.show-detail[data-row-id="${clientId}"], .show-detail-direct[data-row-id="${clientId}"], .cl-name[data-row-id="${clientId}"]`).first();
    if ($target.length) {
        $target.trigger('click');
    } else {
        openClientDrawer('kt_activitiesClient', 'kt_activitiesClient_toggle');
        const url = "{{ route('service_company.clients.viewmore', ':id') }}".replace(':id', clientId);
        const baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        $.ajax({
            type: 'GET',
            url: url,
            beforeSend: function() {
                $('#kt_activitiesClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
            },
            success: function(resp) {
                if (resp && resp.view) {
                    $('#kt_activitiesDirectClient_body').html('');
                    $('#kt_activitiesClient_body').html(resp.view);
                }
            }
        });
    }
}

function setClientTab(tabName, el, updateUrl = true) {
    if (tabName === 'requests') tabName = 'request';
    if (tabName === 'custom') tabName = 'internal';

    activeClientTab = tabName;
    clientListState.tab = tabName;
    hiringFilter = (tabName === 'hiring') ? 1 : 0;
    page_no = 1;

    $('.cl-stat-filter').removeClass('active');
    $('.cl-tab-filter').removeClass('active');

    $(`.cl-stat-filter[data-tab="${tabName}"]`).addClass('active');
    $(`.cl-tab-filter[data-tab="${tabName}"]`).addClass('active');

    if (tabName === 'request') {
        $('.tbl').addClass('tbl-req');
        $('#clientTableHead').html(`
            <div class="tbl-th-subcard tbl-th-check">
                <input class="tbl-cb" type="checkbox" id="clientCheckAll" onclick="toggleSelectAllClients(this)" />
            </div>
            <div class="tbl-th-subcard">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <span>Company Name</span>
                </div>
            </div>
            <div class="tbl-th-subcard">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Location</span>
                </div>
            </div>
            <div class="tbl-th-subcard">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>Request Date</span>
                </div>
            </div>
            <div class="tbl-th-subcard" style="justify-content:flex-end;">
                <span class="tbl-th-label">Action</span>
            </div>
        `);
    } else {
        $('.tbl').removeClass('tbl-req');
        $('#clientTableHead').html(`
            <div class="tbl-th-subcard tbl-th-check">
                <input class="tbl-cb" type="checkbox" id="clientCheckAll" onclick="toggleSelectAllClients(this)" />
            </div>
            <div class="tbl-th-subcard cl-sortable-th" data-sort-field="name" title="Click to sort by Company Name (A-Z / Z-A)" onclick="handleHeaderSort('name')">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <span>Company Name</span>
                </div>
                <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="tbl-th-subcard" title="Location">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Location</span>
                </div>
            </div>
            <div class="tbl-th-subcard cl-sortable-th" data-sort-field="date" title="Click to sort by Date Added (Newest / Oldest)" onclick="handleHeaderSort('date')">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>Date</span>
                </div>
                <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="tbl-th-subcard" title="Last Activity">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Last Activity</span>
                </div>
            </div>
            <div class="tbl-th-subcard cl-sortable-th" data-sort-field="jobs" title="Click to sort by Most Active Jobs" onclick="handleHeaderSort('jobs')">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    <span>Jobs</span>
                </div>
                <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="tbl-th-subcard" title="Hiring Pipeline">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                    <span>Hiring Pipeline</span>
                </div>
                <svg class="tbl-th-info" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-toggle="tooltip" title="Funnel: Sourced → Evaluated → Interviews → Offers → Joined"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
        `);
        updateHeaderSortIndicators(clientListState.sort);
    }

    fetchCandidates(1, updateUrl);
}

function applyClientResponse(response, targetPage) {
    if (!response || typeof response !== 'object') return;
    requestAnimationFrame(function() {
        $('.loadingMessage').css({ display: 'none' });
        $('.loader_search').css({ display: 'none' });
        $('.page-loader').css('display', 'none');
        localStorage.setItem('currentPageClients', targetPage);

        // Honest Search Degradation UX (Invariant I10)
        if (response.projection_status === 'syncing') {
            $('#cl_search_sync_banner').show();
        } else {
            $('#cl_search_sync_banner').hide();
        }

        $('#allClients').html(response.html);

        if (response.total !== undefined) {
            if (activeClientTab === 'all') {
                $('#span_clientAll').html(response.total);
                $('#tabBadgeAll').html(response.total);
            } else if (activeClientTab === 'internal' || activeClientTab === 'custom') {
                $('#span_clientCustom').html(response.total);
                $('#tabBadgeInternal').html(response.total);
            } else if (activeClientTab === 'direct') {
                $('#span_clientDirect').html(response.total);
                $('#tabBadgeDirect').html(response.total);
            } else if (activeClientTab === 'hiring') {
                $('#span_clientHiring').html(response.total);
                $('#tabBadgeHiring').html(response.total);
            } else if (activeClientTab === 'inactive') {
                $('#span_clientInactive').html(response.total);
                $('#tabBadgeInactive').html(response.total);
            } else if (activeClientTab === 'request') {
                $('#span_clientRequest').html(response.total);
                $('#tabBadgeRequest').html(response.total);
            }
        }
        $('#pagination').html(response.pagination);
    });
}

function fetchCandidates(page, updateUrl = true, cursor = null) {
    var targetPage = page || page_no || 1;
    page_no = targetPage;
    localStorage.setItem('currentPageClients', targetPage);

    if (cursor) {
        clientListState.cursor = cursor;
    } else if (targetPage <= 1) {
        clientListState.cursor = null;
    }

    if (updateUrl) {
        syncUrlState();
    }

    var perPage = $('#pagination .pagi-perpage').val() || localStorage.getItem('candidatePerPage') || 10;
    var searchVal = $('#kt_tagify_2').val() || '';
    var sortVal = $('#sortingfilter').val() || 'recent';
    var countryVal = clientListState.country_id || $('#country').val() || '';
    var cityVal = clientListState.city_id || $('#city_id').val() || '';
    var unspecifiedVal = clientListState.unspecified_location ? 1 : 0;
    var industryVal = clientListState.industry_id || $('#industryFilter').val() || '';
    var activityVal = clientListState.activity || $('#activityFilter').val() || '';

    var cacheKey = ClientStore.getKey(activeClientTab, targetPage, searchVal, sortVal, countryVal, cityVal, unspecifiedVal, industryVal, activityVal, perPage);
    var cachedEntry = ClientStore.get(cacheKey);

    // Stale-While-Revalidate (SWR): Immediate perceived instant render if cache entry exists
    if (cachedEntry) {
        applyClientResponse(cachedEntry.data, targetPage);
        if (!cachedEntry.isStale) {
            return; // Warm hit within 5s SLA - 0 network requests!
        }
        // Stale entry: UI already rendered instantly; background revalidation runs without blocking UI
    } else {
        // Cold fetch: clear container to prevent showing previous tab's stale HTML/empty state
        $('#allClients').html('<div class="d-flex align-items-center justify-content-center py-15"><div class="spinner-border text-warning" role="status" style="width: 2.2rem; height: 2.2rem;"><span class="visually-hidden">Loading...</span></div></div>');
        if (searchVal != '') {
            $('.loader_search').css({ display: 'block' });
        } else {
            $('.loadingMessage').css({ display: 'block' });
        }
    }

    // Abort previous in-flight request to prevent race conditions
    if (ClientStore.currentXhr && ClientStore.currentXhr.readyState !== 4) {
        ClientStore.currentXhr.abort();
    }

    // Request Generation Token Guard
    var requestId = ++ClientStore.latestRequestId;

    var reqHeaders = {};
    if (cachedEntry && cachedEntry.data && cachedEntry.data.etag) {
        reqHeaders['If-None-Match'] = cachedEntry.data.etag;
    }

    ClientStore.currentXhr = $.ajax({
        url: `{!! route('service_company.clients.all_clients_data_ajax') !!}?page=${targetPage}`,
        type: 'GET',
        headers: reqHeaders,
        data: {
            custom_status: $('#status').val(),
            search: searchVal,
            sortingfilter: sortVal,
            sort: sortVal,
            cus_search: searchVal,
            country: countryVal,
            country_id: countryVal,
            country_name: clientListState.country_name,
            city_id: cityVal,
            city_name: clientListState.city_name,
            unspecified_location: unspecifiedVal,
            industry_id: industryVal,
            industry: industryVal,
            activity: activityVal,
            cursor: clientListState.cursor,
            hiring: hiringFilter,
            tab: activeClientTab,
            size: perPage,
            per_page: perPage,
            min_version: ClientStore.latestMutationVersion || 0
        },
        statusCode: {
            304: function() {
                // ETag Match: Server confirmed cached data is authoritative (0 bytes transferred)
                if (cachedEntry) {
                    cachedEntry.timestamp = Date.now();
                    applyClientResponse(cachedEntry.data, targetPage);
                }
                $('.loadingMessage').css({ display: 'none' });
                $('.loader_search').css({ display: 'none' });
                $('.page-loader').css('display', 'none');
            }
        },
        success: function(response) {
            // Discard stale out-of-order response or empty 304 response bodies
            if (requestId !== ClientStore.latestRequestId) return;
            if (!response || typeof response !== 'object') return;

            if (response.cursor) {
                clientListState.cursor = response.cursor;
            }

            ClientStore.set(cacheKey, response);
            applyClientResponse(response, targetPage);
        },
        error: function(xhr, status, error) {
            if (status !== 'abort' && requestId === ClientStore.latestRequestId) {
                $('.loadingMessage').css({ display: 'none' });
                $('.loader_search').css({ display: 'none' });
                $('.page-loader').css('display', 'none');
            }
        }
    });
}

$(document).ready(function() {
    // Phase 1: Hydrate static country bundle from IndexedDB / JSON
    initCountryBundle();

    // Phase 2: Start background Web Worker OLAP engine
    initClientWorker();

    $(document).on('click', '#pagination .pagi-page, #pagination .pagi-arrow', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        const cursor = $(this).data('cursor') || null;
        if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
        fetchCandidates(page, true, cursor);
    });

    $(document).on('change', '#pagination .pagi-perpage', function() {
        var selectedPerPage = $(this).val();
        localStorage.setItem('candidatePerPage', selectedPerPage);
        localStorage.setItem('currentPageClients', 1);
        page_no = 1;
        fetchCandidates(1, true);
    });

    // Hydrate state from URL on page load
    hydrateStateFromUrl(false);

    const urlParams = new URLSearchParams(window.location.search);
    const hasFilterParams = urlParams.has('country') || urlParams.has('city') || urlParams.has('industry') || urlParams.has('activity') || urlParams.has('search') || urlParams.has('q') || urlParams.has('tab') || urlParams.has('sort');

    if ($('#allClients .tbl-r').length === 0 || hasFilterParams) {
        fetchCandidates(1, false);
    }
});

$(document).on('click', '.request_event', function () {
    var $btn = $(this);
    if ($btn.hasClass('disabled') || $btn.prop('disabled')) return;

    var id = $btn.data('row-id');
    var event_id = $btn.data('row-event');
    var from_company = $btn.data('row-from_company');
    var actionText = (event_id == -1) ? 'Ignore' : 'Accept';
    var confirmBtnClass = (event_id == -1) ? 'btn btn-danger' : 'btn btn-success';

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: confirmBtnClass,
            cancelButton: 'btn btn-light me-3'
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, ' + actionText + ' it!',
        cancelButtonText: 'No, cancel!',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $btn.closest('.tbl-r').find('.request_event').prop('disabled', true).addClass('disabled').css('pointer-events', 'none');

            var url = "{{ route('service_company.clients.request_update') }}";
            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': '{{ csrf_token() }}',
                    'invitation_id': id,
                    'event_id': event_id,
                    'from_company': from_company
                },
                success: function (response) {
                    ClientStore.invalidateTab('request');
                    ClientStore.invalidateTab('all');
                    ClientStore.invalidateTab('direct');

                    if (response.mutation_version) {
                        ClientStore.latestMutationVersion = response.mutation_version;
                    }

                    if (response.clientDataCnt) {
                        if ($('#span_clientAll').length) $('#span_clientAll').html(response.clientDataCnt.clientAll);
                        if ($('#span_clientDirect').length) $('#span_clientDirect').html(response.clientDataCnt.clientDirect);
                        if ($('#span_clientCustom').length) $('#span_clientCustom').html(response.clientDataCnt.clientCustom);
                        if ($('#span_clientHiring').length) $('#span_clientHiring').html(response.clientDataCnt.clientHiring);
                        if ($('#span_clientInactive').length && response.clientDataCnt.clientInactive !== undefined) $('#span_clientInactive').html(response.clientDataCnt.clientInactive);
                        if ($('#span_clientRequest').length) $('#span_clientRequest').html(response.clientDataCnt.clientRequest);
                    }
                    if (window.clientSyncChannel) {
                        window.clientSyncChannel.postMessage({
                            type: 'CLIENT_REQUEST_MUTATED',
                            invitationId: id,
                            eventId: event_id,
                            version: response.mutation_version || null,
                            clientDataCnt: response.clientDataCnt || null
                        });
                    }

                    if (response.status == 'success') {
                        fetchCandidates(1);
                        swalWithBootstrapButtons.fire(
                            actionText + 'ed!',
                            response.message || ('Request has been ' + actionText.toLowerCase() + 'ed.'),
                            'success'
                        );
                    }
                },
                error: function() {
                    $btn.closest('.tbl-r').find('.request_event').prop('disabled', false).removeClass('disabled').css('pointer-events', 'auto');
                }
            });
        }
    });
});

// ── Universal Real-Time Multi-Account Synchronization (Phase 3) ──
function handleClientMutationEvent(data) {
    if (!data) return;

    if (data.version) {
        if (ClientStore.latestMutationVersion && data.version < ClientStore.latestMutationVersion) {
            console.warn('[Sync] Dropped stale out-of-order mutation version:', data.version, 'current:', ClientStore.latestMutationVersion);
            return;
        }
        ClientStore.latestMutationVersion = data.version;
    }

    const companyId = {{ (int)auth()->user()->company_id }};
    const userId = {{ (int)auth()->id() }};
    localStorage.removeItem('jero_cl_etag_tenant_' + companyId + '_user_' + userId);

    // Invalidate main browser RAM stores
    ClientStore.clear();

    // If a specific client record is attached, delta-upsert directly in Worker
    if (data.client && clWorker && clWorkerReady) {
        clWorker.postMessage({
            type: 'DELTA_UPSERT',
            client: data.client,
            version: data.version || null
        });
    } else {
        // Re-hydrate active projection in background
        fetchActiveProjection();
    }

    if (data.clientDataCnt) {
        if ($('#span_clientAll').length && data.clientDataCnt.clientAll !== undefined) $('#span_clientAll').html(data.clientDataCnt.clientAll);
        if ($('#span_clientDirect').length && data.clientDataCnt.clientDirect !== undefined) $('#span_clientDirect').html(data.clientDataCnt.clientDirect);
        if ($('#span_clientCustom').length && data.clientDataCnt.clientCustom !== undefined) $('#span_clientCustom').html(data.clientDataCnt.clientCustom);
        if ($('#span_clientHiring').length && data.clientDataCnt.clientHiring !== undefined) $('#span_clientHiring').html(data.clientDataCnt.clientHiring);
        if ($('#span_clientInactive').length && data.clientDataCnt.clientInactive !== undefined) $('#span_clientInactive').html(data.clientDataCnt.clientInactive);
        if ($('#span_clientRequest').length && data.clientDataCnt.clientRequest !== undefined) $('#span_clientRequest').html(data.clientDataCnt.clientRequest);
        if ($('#tabBadgeInactive').length && data.clientDataCnt.clientInactive !== undefined) $('#tabBadgeInactive').html(data.clientDataCnt.clientInactive);
    }

    // Refresh current view with zero page reload
    fetchCandidates(page_no, false);
}

// 1. Cross-Tab Browser Channel (Same Computer / Multiple Tabs)
(function() {
    if (typeof BroadcastChannel !== 'undefined') {
        window.clientSyncChannel = new BroadcastChannel('jero_clients_cross_tab_sync');

        window.clientSyncChannel.onmessage = function(event) {
            const data = event.data;
            if (!data) return;

            if (data.type === 'CLIENT_REQUEST_MUTATED' || data.type === 'CLIENT_MUTATED' || data.type === 'JOB_MUTATED' || data.type === 'JOB_STATUS_CHANGED') {
                handleClientMutationEvent(data);
            }
        };
    }
})();

// 2. Laravel Reverb WebSocket Push (Across All Team Recruiters in Organization)
(function() {
    if (typeof window.Echo !== 'undefined') {
        try {
            const companyId = {{ (int)auth()->user()->company_id }};
            window.Echo.private('company.' + companyId)
                .listen('.client.mutated', function(e) {
                    handleClientMutationEvent(e);
                })
                .listen('.ClientMutated', function(e) {
                    handleClientMutationEvent(e);
                })
                .listen('.ClientRequestMutated', function(e) {
                    handleClientMutationEvent(e);
                })
                .listen('.JobStatusChanged', function(e) {
                    handleClientMutationEvent(e);
                })
                .listen('.JobMutated', function(e) {
                    handleClientMutationEvent(e);
                });
        } catch(echoErr) {
            console.warn('[Echo] Reverb listener registration skipped:', echoErr);
        }
    }
})();

// 3. Tab Focus / Sleep Recovery Guard
window.addEventListener('focus', function() {
    fetchActiveProjection();
});
</script>
@endpush
