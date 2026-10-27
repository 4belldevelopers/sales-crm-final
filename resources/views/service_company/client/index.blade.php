@extends('layouts.service_company_layout')
@push('head-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
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
  width: 80% !important;
  max-width: 80vw !important;
  min-width: 0 !important;
}
@media (max-width: 1400px) {
  .cd-activities-drawer {
    width: 80% !important;
    max-width: 80vw !important;
  }
}
@media (max-width: 992px) {
  .cd-activities-drawer {
    width: 85% !important;
    max-width: 85vw !important;
  }
}
@media (max-width: 576px) {
  .cd-activities-drawer {
    width: 90% !important;
    max-width: 90vw !important;
  }
}
</style>
@endpush
@section('content')
@php
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
                    <div class="cl-card-icon green">
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
                <div class="cl-card-sub green">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="9" x2="20" y2="9"/>
                        <line x1="4" y1="15" x2="20" y2="15"/>
                        <line x1="10" y1="3" x2="8" y2="21"/>
                        <line x1="16" y1="3" x2="14" y2="21"/>
                    </svg>
                    <span>{{ $activeJobsCount > 0 ? $activeJobsCount . ' active position' . ($activeJobsCount > 1 ? 's' : '') : 'No active jobs' }}</span>
                </div>
            </a>

            {{-- Card 5: Inactive Companies --}}
            <a href="#" onclick="setClientTab('inactive', this); return false;" class="cl-card cl-stat-filter" data-tab="inactive">
                <div class="cl-card-top">
                    <div class="cl-card-icon pink">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="10" y1="15" x2="10" y2="9"/>
                            <line x1="14" y1="15" x2="14" y2="9"/>
                        </svg>
                    </div>
                    <div class="cl-card-meta">
                        <div class="cl-card-num" id="span_clientInactive">{{ $inactiveClientsCount }}</div>
                        <div class="cl-card-lbl">Inactive Companies</div>
                    </div>
                </div>
                <div class="cl-card-sub pink">
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
                <span>Inactive Companies</span>
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
                            <svg class="tbl-th-info" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-toggle="tooltip" title="Pipeline: Sourcing → Screening → Evaluation → Negotiations → Offer → Joined | Interview (Total Applications on hover)"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
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
<div id="kt_activitiesClient" class="bg-body cd-activities-drawer" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close" data-kt-drawer-width="{default:'90%', 'sm': '85%', 'md': '80%', 'lg': '80%', 'xl': '80%'}">
  <div class="card shadow-none rounded-0 w-100">
    <button type="button" id="kt_activitiesClient_close" class="d-none" aria-hidden="true"></button>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
  </div>
</div>

<!-- ── DRAWER FOR DIRECT CLIENT ── -->
<div id="kt_activitiesDirectClient_toggle"></div>
<div id="kt_activitiesDirectClient" class="bg-body cd-activities-drawer" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesDirectClient_toggle" data-kt-drawer-close="#kt_activitiesDirectClient_close" data-kt-drawer-width="{default:'90%', 'sm': '85%', 'md': '80%', 'lg': '80%', 'xl': '80%'}">
  <div class="card shadow-none rounded-0 w-100">
    <button type="button" id="kt_activitiesDirectClient_close" class="d-none" aria-hidden="true"></button>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesDirectClient_body"> &nbsp; </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}{{ TM_VERSION }}"></script>
<!-- JERO Distributed Client Runtime (JDCR V5.2) Local Persistent Engine & Incremental Renderer -->
<script src="{{ asset('assets/service_company/client/cl-entity-db.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/cl-drawer-view.js') }}{{ TM_VERSION }}"></script>
<!-- JERO Service Company Drawer - Scoped Instance Architecture V3.0 (Enterprise Master Standard) -->
<script src="{{ asset('assets/service_company/client/drawer/request-manager.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/ui-state-renderer.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tab-coordinator.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/overview.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/activity.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/jobs.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/candidates.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/contacts.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/interviews.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/tabs/offers.js') }}{{ TM_VERSION }}"></script>
<script src="{{ asset('assets/service_company/client/drawer/drawer-instance.js') }}{{ TM_VERSION }}"></script>
<script>
$(document).ready(function() {
    var searchTimer = null;
    $('#kt_tagify_2').on('keyup input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(function() {
            if (typeof loadClientList === 'function') {
                loadClientList(1);
            } else if (typeof fetchCandidates === 'function') {
                fetchCandidates(1);
            }
        }, 300);
    });
});

function filtersdataget() {
    if (typeof loadClientList === 'function') {
        loadClientList(1, true);
    } else if (typeof fetchCandidates === 'function') {
        fetchCandidates(1, true);
    }
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
    if (e.key === 'Escape') {
        if ($('#clLocDropdownPanel').hasClass('show')) {
            $('#clLocDropdownPanel').removeClass('show');
            $('#clLocBtn').removeClass('active');
        }
        if ($('#kt_activitiesClient').hasClass('drawer-on') || $('#kt_activitiesDirectClient').hasClass('drawer-on') || document.querySelector('.drawer-overlay')) {
            if (typeof ClientStateManager !== 'undefined' && typeof ClientStateManager.clearDrawerState === 'function') {
                ClientStateManager.clearDrawerState();
            }
        }
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

// ── Session & Inactivity Recovery Engine (10-minute Idle & Sleep Recovery) ──
window.ClientSessionRecoveryManager = {
    heartbeatRoute: "{{ route('service_company.clients.sessionHeartbeat') }}",
    lastActivity: Date.now(),
    lastHeartbeat: Date.now(),
    heartbeatIntervalMs: 300000, // 5 minutes
    idleThresholdMs: 60000,      // 60 seconds
    isReconnecting: false,

    init: function() {
        var self = this;
        var updateActivity = function() {
            self.lastActivity = Date.now();
        };
        ['mousedown', 'keydown', 'scroll', 'touchstart'].forEach(function(evt) {
            window.addEventListener(evt, updateActivity, { passive: true });
        });

        // Tab Visibility & Focus Recovery (Triggered on wake from 10-minute sleep or background)
        document.addEventListener('visibilitychange', function() {
            if (document.visibilityState === 'visible') {
                self.onWakeUp('visibilitychange');
            }
        });
        window.addEventListener('focus', function() {
            self.onWakeUp('focus');
        });

        // Browser Online recovery
        window.addEventListener('online', function() {
            console.log('⚡ [Session Recovery] Network back online. Refreshing session...');
            self.pingHeartbeat(function() {
                if (typeof loadClientList === 'function') {
                    loadClientList(window.page_no || 1, false);
                }
            });
        });

        // Periodic Background Heartbeat (every 5 min while page active)
        setInterval(function() {
            if (document.visibilityState === 'visible' && (Date.now() - self.lastActivity < 600000)) {
                self.pingHeartbeat();
            }
        }, this.heartbeatIntervalMs);
    },

    onWakeUp: function(source) {
        var elapsed = Date.now() - this.lastActivity;
        this.lastActivity = Date.now();

        // If page was idle for > 60 seconds, ping session heartbeat to revive TCP keepalive and refresh CSRF
        if (elapsed > this.idleThresholdMs) {
            var self = this;
            this.pingHeartbeat(function(authOk) {
                if (!authOk) {
                    console.warn('[Session Recovery] Session expired during idle period.');
                    return;
                }
                // Revive Web Worker if suspended
                if (typeof clWorkerReady !== 'undefined' && !clWorkerReady && typeof initClientWorker === 'function') {
                    initClientWorker();
                } else if (typeof fetchActiveProjection === 'function') {
                    fetchActiveProjection();
                }

                // If active drawer was in error state, auto-recover
                if (window.ClientStateManager && window.ClientStateManager.state.drawerStatus === 'error') {
                    var selId = window.ClientStateManager.getSelectedClientId();
                    var selType = window.ClientStateManager.state.selectedClientType;
                    if (selId && selType) {
                        console.log('[Session Recovery] Auto-recovering failed drawer for client', selId);
                        window.ClientStateManager.openClientDrawer(selId, selType);
                    }
                } else if (window.ClientStateManager && window.ClientStateManager.state.drawerStatus === 'loaded') {
                    // Auto-heal idle/wake-up empty tab panes if drawer was left open
                    var curSelId = window.ClientStateManager.getSelectedClientId();
                    var curType = window.ClientStateManager.state.selectedClientType;
                    if (curSelId) {
                        if (curType === 'direct' && typeof window.loadDirectTabContent === 'function') {
                            var curDirectTab = $('#kt_activitiesDirectClient .tab-pane.active').attr('id');
                            if (curDirectTab && curDirectTab !== 'overview' && curDirectTab !== 'recent_activity') {
                                window.loadDirectTabContent(curDirectTab, false);
                            }
                        } else if (typeof window.loadInternalTabContent === 'function') {
                            var curInternalTab = $('#kt_activitiesClient .tab-pane.active').attr('id');
                            if (curInternalTab && curInternalTab !== 'overview' && curInternalTab !== 'recent_activity') {
                                window.loadInternalTabContent(curInternalTab, false);
                            }
                        }
                    }
                }
            });
        }
    },

    pingHeartbeat: function(callback) {
        var self = this;
        $.ajax({
            url: self.heartbeatRoute,
            type: 'GET',
            cache: false,
            timeout: 8000,
            success: function(resp) {
                self.lastHeartbeat = Date.now();
                if (resp && resp.csrf_token) {
                    $('meta[name="csrf-token"]').attr('content', resp.csrf_token);
                    $.ajaxSetup({
                        headers: { 'X-CSRF-TOKEN': resp.csrf_token }
                    });
                }
                if (typeof callback === 'function') callback(true, resp);
            },
            error: function(xhr) {
                console.warn('[Session Recovery] Heartbeat check error:', xhr.status);
                if (typeof callback === 'function') callback(false, xhr);
            }
        });
    },

    executeWithRetry: function(requestFn, retryCount = 1, onFinalError) {
        var self = this;
        requestFn(function(success, data, xhr) {
            if (success) return;
            // Transient connection reset (0) or CSRF token mismatch (419)
            if (xhr && (xhr.status === 0 || xhr.status === 419) && retryCount > 0) {
                console.log(`[Session Recovery] Transient HTTP ${xhr.status} caught. Reviving session & retrying...`);
                self.pingHeartbeat(function(authOk) {
                    if (authOk) {
                        setTimeout(function() {
                            self.executeWithRetry(requestFn, retryCount - 1, onFinalError);
                        }, 250);
                    } else if (typeof onFinalError === 'function') {
                        onFinalError(xhr);
                    }
                });
            } else if (typeof onFinalError === 'function') {
                onFinalError(xhr);
            }
        });
    }
};

// ── Canonical Architecture & Deterministic State Machine for Client Management ──
// ─── JDCR V5.2: L1 In-Memory Working RAM Store (Bounded to 25 active graphs) ───
window.ClientMemoryStore = {
    cache: new Map(),
    maxSize: 25,
    get: function(id) {
        return this.cache.get(Number(id)) || null;
    },
    set: function(id, data) {
        if (!data) return;
        if (this.cache.size >= this.maxSize) {
            var firstKey = this.cache.keys().next().value;
            this.cache.delete(firstKey);
        }
        this.cache.set(Number(id), data);
    },
    delete: function(id) {
        this.cache.delete(Number(id));
    },
    clear: function() {
        this.cache.clear();
    }
};

// ─── JDCR V5.2: Single Synchronization Authority (SyncSupervisor) ───
window.SyncSupervisor = {
    tenantId: {{ (int) (auth()->user()->company_id ?? 0) }},
    inFlightRequests: new Map(),
    dirtyEntities: new Set(),
    coalesceTimer: null,

    notifyHint: function(clientId) {
        if (!clientId) return;
        this.dirtyEntities.add(Number(clientId));
        if (this.coalesceTimer) return;
        var self = this;
        this.coalesceTimer = setTimeout(function() {
            self.flush();
            self.coalesceTimer = null;
        }, 150);
    },

    flush: function() {
        var self = this;
        this.dirtyEntities.forEach(function(cid) {
            self.reconcile(cid);
        });
        this.dirtyEntities.clear();
    },

    reconcile: function(clientId) {
        var self = this;
        var cid = Number(clientId);
        var currentSelected = Number(window.ClientStateManager ? window.ClientStateManager.getSelectedClientId() : 0);
        if (currentSelected === cid && window.ClientIncrementalRenderer) {
            ClientIncrementalRenderer.patchSyncStatus(cid, 'syncing', 'Syncing...');
        }

        var dedupeKey = `${this.tenantId}:client:${cid}:graph`;
        if (this.inFlightRequests.has(dedupeKey)) {
            return this.inFlightRequests.get(dedupeKey);
        }

        var endpoint = "{{ route('service_company.clients.api.v2.graph', ':id') }}".replace(':id', cid);
        var fetchPromise = $.ajax({
            type: 'GET',
            url: endpoint,
            cache: false,
            success: function(res) {
                if (res && res.data) {
                    window.ClientMemoryStore.set(cid, res.data);
                    if (window.ClientEntityDB) {
                        window.ClientEntityDB.put(self.tenantId, cid, res);
                    }
                    if (Number(window.ClientStateManager.getSelectedClientId()) === cid) {
                        if (window.ClientIncrementalRenderer) {
                            ClientIncrementalRenderer.patchSyncStatus(cid, 'uptodate', 'Up to Date');
                        }
                    }
                }
            },
            error: function(xhr) {
                if (Number(window.ClientStateManager.getSelectedClientId()) === cid) {
                    if (window.ClientIncrementalRenderer) {
                        ClientIncrementalRenderer.patchSyncStatus(cid, 'stale', 'Offline / Stale');
                    }
                }
            },
            complete: function() {
                self.inFlightRequests.delete(dedupeKey);
            }
        });

        this.inFlightRequests.set(dedupeKey, fetchPromise);
        return fetchPromise;
    }
};

// ─── JDCR V5.2: Multi-Tab BroadcastChannel Coordination ───
if (typeof window.BroadcastChannel !== 'undefined') {
    try {
        window.jdcrTabChannel = new BroadcastChannel('JERO_TAB_SYNC');
        window.jdcrTabChannel.onmessage = function(ev) {
            if (ev.data && ev.data.type === 'CLIENT_MUTATED' && ev.data.client_id) {
                window.SyncSupervisor.notifyHint(ev.data.client_id);
            }
        };
    } catch (e) {}
}

// ─── JDCR V5.2: Centralized Event Delegation (Zero Inline Handlers) ───
$(document).on('click', '[data-action="switch-tab"]', function(e) {
    e.preventDefault();
    var tabName = $(this).data('tab');
    var cid = $(this).data('client-id');
    if (window.ClientStateManager && typeof window.ClientStateManager.switchDrawerTab === 'function') {
        window.ClientStateManager.switchDrawerTab(cid, tabName);
    }
});

$(document).on('click', '[data-action="close-drawer"]', function(e) {
    e.preventDefault();
    if (window.ClientStateManager && typeof window.ClientStateManager.clearDrawerState === 'function') {
        window.ClientStateManager.clearDrawerState();
    }
});

// ─── JDCR V5.2: Formal Idle-Tab Lifecycle State Machine ───
document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        var selId = window.ClientStateManager ? window.ClientStateManager.getSelectedClientId() : null;
        if (selId && window.SyncSupervisor) {
            window.SyncSupervisor.notifyHint(selId);
        }
    }
});

window.ClientStateManager = {
    state: {
        activeTab: {!! json_encode($initialTab ?? 'all') !!},
        selectedClientId: null,
        selectedClientType: null, // 'internal' | 'direct' | 'request' | null
        selectedClientTab: 'overview',
        drawerStatus: 'idle', // 'idle' | 'loading' | 'loaded' | 'error'
        drawerError: null,
        drawerRequestToken: 0,
        drawerLoadedTabs: {}, // keyed by `${clientId}_${tabName}`
        currentDrawerContext: null
    },

    activeDrawerXhr: null,

    getActiveTab: function() {
        return this.state.activeTab;
    },

    getSelectedClientId: function() {
        return this.state.selectedClientId ? parseInt(this.state.selectedClientId, 10) : null;
    },

    isTabLoaded: function(clientId, tabName) {
        var cid = parseInt(clientId, 10) || clientId;
        return !!this.state.drawerLoadedTabs[`${cid}_${tabName}`];
    },

    markTabLoaded: function(clientId, tabName) {
        var cid = parseInt(clientId, 10) || clientId;
        this.state.drawerLoadedTabs[`${cid}_${tabName}`] = true;
    },

    // ── Teardown Active Drawer (Atomic Clean State Transition) ──
    teardownDrawer: function(fullReset = true) {
        // 1. Abort in-flight drawer network request
        if (this.activeDrawerXhr && this.activeDrawerXhr.readyState !== 4) {
            this.activeDrawerXhr.abort();
            this.activeDrawerXhr = null;
        }

        // 2. Detach namespaced drawer DOM event listeners to prevent duplicate triggers
        $(document).off('.clientDrawer .internalDrawer .directDrawer .drawerTabs');

        // 3. Destroy Chart.js instances cleanly
        if (window.perfChartInstances) {
            Object.values(window.perfChartInstances).forEach(function(c) {
                try { if (c && typeof c.destroy === 'function') c.destroy(); } catch (e) {}
            });
            window.perfChartInstances = {};
        }
        if (window.dcChartInstances) {
            Object.values(window.dcChartInstances).forEach(function(c) {
                try { if (c && typeof c.destroy === 'function') c.destroy(); } catch (e) {}
            });
            window.dcChartInstances = {};
        }

        // 4. Clear performance analytics RAM cache
        window.perfClientCache = {};

        // 5. Teardown sub-card state machines
        if (window.__activeClientDrawer && typeof window.__activeClientDrawer.destroy === 'function') {
            try { window.__activeClientDrawer.destroy(); } catch (e) {}
            window.__activeClientDrawer = null;
        }
        if (window.ClientCandidateStore && typeof window.ClientCandidateStore.destroy === 'function') {
            try { window.ClientCandidateStore.destroy(); } catch (e) {}
        }
        if (window.ClientCandidateStoreDirect && typeof window.ClientCandidateStoreDirect.destroy === 'function') {
            try { window.ClientCandidateStoreDirect.destroy(); } catch (e) {}
        }

        // 6. Empty drawer container DOM elements completely
        $('#kt_activitiesClient_body').empty();
        $('#kt_activitiesDirectClient_body').empty();

        // 7. Reset tokens, context, and active drawer loaded tabs
        this.state.drawerRequestToken++;
        this.state.currentDrawerContext = null;
        this.state.drawerLoadedTabs = {};

        if (fullReset) {
            this.state.selectedClientId = null;
            this.state.selectedClientType = null;
            this.state.selectedClientTab = null;
            this.state.drawerStatus = 'idle';
            this.state.drawerError = null;
            window.activeSelectedClientId = null;

            closeClientDrawers();

            // Clear row highlights
            $('.tbl-r.client-selected').removeClass('client-selected');
            $('[data-selected-client]').removeAttr('data-selected-client');

            try {
                localStorage.removeItem('selected_client_id');
                localStorage.removeItem('active_client_id');
                sessionStorage.removeItem('selected_client_id');
                sessionStorage.removeItem('active_client_id');
            } catch (e) {}
        }
    },

    clearDrawerState: function() {
        this.teardownDrawer(true);
    },

    // ── Deterministic Tab Change Lifecycle (Requirement 4) ──
    onTabChange: function(newTab, updateUrl = true) {
        if (newTab === 'requests') newTab = 'request';
        if (newTab === 'custom') newTab = 'internal';

        // 1. Update activeTab
        this.state.activeTab = newTab;
        activeClientTab = newTab;
        if (window.clientListState) {
            window.clientListState.tab = newTab;
        }
        hiringFilter = (newTab === 'hiring') ? 1 : 0;
        page_no = 1;
        try { localStorage.setItem('currentPageClients', 1); } catch (e) {}

        // 2. Invalidate previous tab/client context
        this.invalidateTabContext();

        // 3. Clear selectedClient & full drawer teardown
        this.clearDrawerState();

        // 4. Load
        this.renderTabHeaders(newTab);
        if (typeof loadClientList === 'function') {
            loadClientList(1, updateUrl);
        } else if (typeof fetchCandidates === 'function') {
            fetchCandidates(1, updateUrl);
        }
    },

    invalidateTabContext: function() {
        // Abort in-flight drawer request
        if (this.activeDrawerXhr && this.activeDrawerXhr.readyState !== 4) {
            this.activeDrawerXhr.abort();
            this.activeDrawerXhr = null;
        }

        // Abort in-flight listing request
        if (typeof ClientStore !== 'undefined' && ClientStore.currentXhr && ClientStore.currentXhr.readyState !== 4) {
            ClientStore.currentXhr.abort();
            ClientStore.currentXhr = null;
        }

        // Clean any leftover URL client params so back/forward or reload starts clean
        try {
            const url = new URL(window.location);
            if (url.searchParams.has('open_client')) {
                url.searchParams.delete('open_client');
                window.history.replaceState({ tab: this.state.activeTab, page: 1 }, '', url);
            }
        } catch (e) {}
    },

    clearSelectedClient: function() {
        this.clearDrawerState();
    },

    openClientDrawer: function(clientId, type) {
        if (!clientId) return;
        var self = this;
        var currentTab = this.state.activeTab;
        var numericClientId = parseInt(clientId, 10) || clientId;

        // Atomic Teardown of previous client drawer without closing container
        this.teardownDrawer(false);

        var isDirect = (type === 'direct' || type === 'request');
        var targetDrawerId = isDirect ? 'kt_activitiesDirectClient' : 'kt_activitiesClient';
        var toggleId = isDirect ? 'kt_activitiesDirectClient_toggle' : 'kt_activitiesClient_toggle';
        var bodyId = isDirect ? '#kt_activitiesDirectClient_body' : '#kt_activitiesClient_body';
        var otherBodyId = isDirect ? '#kt_activitiesClient_body' : '#kt_activitiesDirectClient_body';

        // Deterministic Context Shield & Monotonic Token
        var requestToken = ++this.state.drawerRequestToken;
        var contextToken = 'ctx_' + requestToken + '_' + Date.now();
        var drawerContext = {
            contextToken: contextToken,
            requestToken: requestToken,
            clientId: numericClientId,
            clientType: type,
            tab: currentTab,
            openedAt: Date.now()
        };
        this.state.currentDrawerContext = drawerContext;

        // Set state - Single Source of Truth
        this.state.selectedClientId = numericClientId;
        this.state.selectedClientType = type;
        this.state.selectedClientTab = 'overview';
        this.state.drawerStatus = 'loading';
        this.state.drawerError = null;
        window.activeSelectedClientId = numericClientId;

        // Highlight selected row in current tab listing
        $('[data-row-id]').closest('.tbl-r').removeClass('client-selected');
        $(`[data-row-id="${clientId}"]`).closest('.tbl-r').addClass('client-selected');

        // Open target Metronic drawer container
        openClientDrawer(targetDrawerId, toggleId);
        $(otherBodyId).empty();

        var tenantId = {{ (int) (auth()->user()->company_id ?? 0) }};

        // Loading skeleton
        var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
        var loadingText = (type === 'request') ? 'Loading Request & Commercial Terms...' : (isDirect ? 'Loading Direct Client Details...' : 'Loading Client Details...');
        $(bodyId).html(`
            <div class="d-flex flex-column align-items-center justify-content-center p-10" style="min-height:380px;">
                <img src="${baseurl}" height="38px" width="38px" style="object-fit:contain;" alt="Loading...">
                <span class="mt-4 text-gray-700 font-poppins fs-7 fw-600">${loadingText}</span>
                <span class="text-gray-400 font-poppins fs-8 mt-1">Context: ${currentTab.toUpperCase()} tab</span>
            </div>
        `);

        var endpoint;
        if (type === 'request') {
            endpoint = "{{ route('service_company.clients.requestviewmore', ':id') }}".replace(':id', clientId);
        } else if (isDirect) {
            endpoint = "{{ route('service_company.clients.dviewmore', ':id') }}".replace(':id', clientId);
        } else {
            endpoint = "{{ route('service_company.clients.viewmore', ':id') }}".replace(':id', clientId);
        }

        var fetchDrawerData = function(doneCallback) {
            self.activeDrawerXhr = $.ajax({
                type: 'GET',
                url: endpoint,
                cache: false,
                success: function(response) {
                    var activeCtx = self.state.currentDrawerContext;
                    if (!activeCtx || activeCtx.contextToken !== contextToken || self.state.drawerRequestToken !== requestToken || parseInt(self.state.selectedClientId, 10) !== parseInt(clientId, 10)) {
                        console.log(`[Drawer Shield] Discarded stale response for client ${clientId}. Current token: ${activeCtx ? activeCtx.contextToken : 'null'}`);
                        return;
                    }

                    if (response && response.view) {
                        $(bodyId).html(response.view);
                        self.state.drawerStatus = 'loaded';
                        self.markTabLoaded(numericClientId, 'overview');

                        // Initialize Scoped ClientDrawerInstance V3.0 (Enterprise Master Standard)
                        if (typeof window.ClientDrawerInstance === 'function') {
                            if (window.__activeClientDrawer && typeof window.__activeClientDrawer.destroy === 'function') {
                                try { window.__activeClientDrawer.destroy(); } catch(e){}
                            }
                            new window.ClientDrawerInstance({
                                canonicalClientId: numericClientId,
                                clientType: isDirect ? 'direct' : 'internal',
                                companyId: tenantId,
                                container: bodyId
                            });
                        }

                        if (window.SyncSupervisor && typeof window.SyncSupervisor.notifyHint === 'function') {
                            window.SyncSupervisor.notifyHint(numericClientId);
                        }
                        if (typeof doneCallback === 'function') doneCallback(true, response);
                    } else {
                        $(bodyId).html('<div class="p-8 text-center text-danger font-poppins fs-7">No client details available for the selected record.</div>');
                        self.state.drawerStatus = 'error';
                        if (typeof doneCallback === 'function') doneCallback(false, null);
                    }
                },
                error: function(xhr, status) {
                    if (status === 'abort') return;
                    if (typeof doneCallback === 'function') doneCallback(false, null, xhr);
                }
            });
        };

        var executeLegacyDrawerFetch = function() {
            ClientSessionRecoveryManager.executeWithRetry(fetchDrawerData, 1, function(xhr) {
                var activeCtx = self.state.currentDrawerContext;
                if (!activeCtx || activeCtx.contextToken !== contextToken || self.state.drawerRequestToken !== requestToken) return;

                var msg = (!xhr || xhr.status === 0 || !navigator.onLine)
                    ? 'Network connection was interrupted. Please check your connectivity.'
                    : 'Could not load client details (HTTP ' + (xhr.status || 500) + '). Please try again.';

                self.state.drawerStatus = 'error';
                self.state.drawerError = msg;

                $(bodyId).html(`
                    <div class="d-flex flex-column align-items-center justify-content-center p-10 text-center" style="min-height:380px;">
                        <div class="mb-3 text-danger">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div class="fw-bold fs-6 text-gray-800 mb-1">Failed to Load Client Details</div>
                        <div class="text-muted fs-7 mb-4 font-poppins">${msg}</div>
                        <button type="button" class="btn btn-sm btn-primary px-4 py-2" onclick="ClientStateManager.openClientDrawer('${clientId}', '${type}')">
                            <i class="fas fa-redo me-1"></i> Retry Loading
                        </button>
                    </div>
                `);
            });
        };

        // Authoritative Workflow (Option A Master Standard):
        // Always render through authoritative Blade view with Scoped ClientDrawerInstance
        executeLegacyDrawerFetch();
    },

    // ─── Scoped Drawer Tab Switcher V3.0 (< 5ms) ───
    switchDrawerTab: function(clientId, tabName) {
        this.state.selectedClientTab = tabName;
        if (window.__activeClientDrawer && typeof window.__activeClientDrawer.coordinator.activateTab === 'function') {
            window.__activeClientDrawer.coordinator.activateTab(tabName);
            return;
        }
    },
            return;
        }

        if (window.ClientEntityDB) {
            var self = this;
            window.ClientEntityDB.get(tenantId, cid).then(function(rec) {
                if (rec && rec.data) {
                    if (window.ClientMemoryStore) window.ClientMemoryStore.set(cid, rec.data);
                    renderTab(rec.data);
                    self.markTabLoaded(cid, tabName);
                }
            }).catch(function() {});
        }
    },

    renderTabHeaders: function(tabName) {
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
            if (typeof updateHeaderSortIndicators === 'function' && window.clientListState) {
                updateHeaderSortIndicators(window.clientListState.sort);
            }
        }
    }
};

function openClientDrawer(drawerId, toggleId) {
    // 1. Hide opposite drawer if active to prevent multi-drawer overlay conflict
    var otherDrawerId = (drawerId === 'kt_activitiesClient') ? 'kt_activitiesDirectClient' : 'kt_activitiesClient';
    var otherDrawerEl = document.getElementById(otherDrawerId);
    if (otherDrawerEl) {
        if (typeof KTDrawer !== 'undefined' && typeof KTDrawer.getInstance === 'function') {
            var otherInst = KTDrawer.getInstance(otherDrawerEl);
            if (otherInst && typeof otherInst.hide === 'function') {
                otherInst.hide();
            }
        }
        otherDrawerEl.classList.remove('drawer-on');
        otherDrawerEl.style.removeProperty('transform');
        otherDrawerEl.style.removeProperty('z-index');
    }

    var drawerEl = document.getElementById(drawerId);
    if (!drawerEl) return;

    // 2. Official Metronic KTDrawer instance show()
    var opened = false;
    if (typeof KTDrawer !== 'undefined') {
        var drawer = (typeof KTDrawer.getInstance === 'function') ? KTDrawer.getInstance(drawerEl) : null;
        if (!drawer && typeof KTDrawer === 'function') {
            try {
                drawer = new KTDrawer(drawerEl);
            } catch (e) {}
        }
        if (!drawer && typeof KTDrawer.createInstances === 'function') {
            try {
                KTDrawer.createInstances();
                drawer = KTDrawer.getInstance(drawerEl);
            } catch (e) {}
        }
        if (drawer && typeof drawer.show === 'function') {
            drawer.show();
            opened = true;
        }
    }

    // 3. Toggle button trigger fallback
    if (!opened) {
        var toggleEl = document.getElementById(toggleId);
        if (toggleEl) {
            if (typeof toggleEl.click === 'function') {
                toggleEl.click();
            } else {
                $(toggleEl).trigger('click');
            }
        }
    }

    // 4. Guaranteed DOM fallback: ensures drawer is visibly sliding in and on screen
    setTimeout(function() {
        if (drawerEl && !drawerEl.classList.contains('drawer-on')) {
            drawerEl.classList.add('drawer', 'drawer-end', 'drawer-on');
            drawerEl.style.transform = 'none';
            drawerEl.style.zIndex = '999999';
            if (!document.querySelector('.drawer-overlay')) {
                var overlay = document.createElement('div');
                overlay.className = 'drawer-overlay custom-cand-drawer-overlay';
                overlay.style.zIndex = '999998';
                overlay.onclick = function() {
                    ClientStateManager.clearDrawerState();
                };
                document.body.appendChild(overlay);
            }
        }
    }, 40);
}

function closeClientDrawers() {
    ['kt_activitiesClient', 'kt_activitiesDirectClient'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) {
            if (typeof KTDrawer !== 'undefined' && typeof KTDrawer.getInstance === 'function') {
                var inst = KTDrawer.getInstance(el);
                if (inst && typeof inst.hide === 'function') {
                    inst.hide();
                }
            }
            el.classList.remove('drawer-on');
            el.style.removeProperty('transform');
            el.style.removeProperty('z-index');
        }
    });
    document.querySelectorAll('.drawer-overlay, .custom-cand-drawer-overlay').forEach(function(ov) {
        ov.remove();
    });
    document.body.removeAttribute('data-kt-drawer-activities');
    document.body.removeAttribute('data-kt-drawer');
    document.body.style.overflow = '';
}

$(document).on('click', '.show-detail', function (e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    var id = $(this).data('row-id') || $(this).closest('[data-row-id]').data('row-id');
    if (!id) return;
    ClientStateManager.openClientDrawer(id, 'internal');
});

$(document).on('click', '.show-detail-direct', function (e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    var id = $(this).data('row-id') || $(this).closest('[data-row-id]').data('row-id');
    if (!id) return;
    ClientStateManager.openClientDrawer(id, 'direct');
});

$(document).on('click', '.view-request-terms', function (e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    var id = $(this).data('client-id') || $(this).data('row-id') || $(this).closest('[data-row-id]').data('row-id');
    if (!id) return;
    ClientStateManager.openClientDrawer(id, 'request');
});

$(document).on('click', '#kt_activitiesClient_close, #kt_activitiesDirectClient_close, .cd-close-drawer, #kt_activitiesClient_close_btn, #kt_activitiesDirectClient_close_btn', function(e) {
    if (e) {
        e.preventDefault();
        e.stopPropagation();
    }
    ClientStateManager.clearDrawerState();
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

        if (r.is_pending_request) {
            const invId = r.staffing_company_invitation_id || r.id;
            const fromComp = r.from_company_id || 0;
            const targetId = r.target_id || r.client_id || fromComp || r.id;
            const hiringCount = (r.jobs && (r.jobs.hiring || r.jobs.active)) ? (r.jobs.hiring || r.jobs.active) : 0;
            const expiresFormatted = r.expires_on || 'in 7 days';
            const daysAgo = r.days_ago || 'Just now';

            return `
            <div class="tbl-r is-pending-request" data-pending-id="${invId}">
                <div class="tbl-cell-check">
                    <input type="checkbox" class="tbl-cb" value="${targetId}" onclick="event.stopPropagation()">
                </div>
                <div class="tbl-cell-client show-detail-direct" data-row-id="${targetId}" title="${escapeAttr(r.name)}">
                    <div class="cl-company-wrapper">
                        <div class="cl-logo-box">${logoHtml}</div>
                        <div class="cl-details-box">
                            <div class="d-flex align-items-center gap-2">
                                <span class="cl-name">${escapeAttr(r.name)}</span>
                                <span class="badge" style="background:#fef3c7; color:#d97706; font-size:10px; font-weight:700; padding:2px 6px; border-radius:4px; border:1px solid #fde68a;">⚡ Request</span>
                            </div>
                            ${indHtml}
                        </div>
                    </div>
                </div>
                <div class="tbl-cell-loc">${locHtml}</div>
                <div class="tbl-cell-type">
                    <div class="cl-type-name type-direct fw-bold" style="color:#2563eb; font-size:11px;">Direct Client</div>
                    <div class="cl-date-box">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line x1="16" y1="2" x2="16" y2="6"/>
                            <line x1="8" y1="2" x2="8" y2="6"/>
                            <line x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                        <span class="fw-bold text-gray-800">${escapeAttr(r.connected_on || '-')}</span>
                    </div>
                    <div class="cl-conn-caption text-danger" style="font-size:11px; font-weight:500;">Expires ${escapeAttr(expiresFormatted)}</div>
                </div>
                <div class="tbl-cell-activity">
                    <div class="d-flex align-items-center gap-1">
                        <span class="bullet bullet-dot bg-warning animate-pulse" style="width:7px; height:7px;"></span>
                        <span class="text-warning fw-bold" style="font-size:11px;">Action Needed</span>
                    </div>
                    <div class="cl-act-main fw-bold text-gray-800">Received ${escapeAttr(daysAgo)}</div>
                    <div class="cl-act-desc text-muted">Active in system</div>
                </div>
                <div class="tbl-cell-jobs">
                    <div class="cl-job-line ${hiringCount > 0 ? 'has-count cl-job-hiring' : 'no-count'}">
                        <span class="cl-job-bullet ${hiringCount > 0 ? 'bullet-hiring' : 'bullet-neutral'}"></span>
                        <span class="cl-job-num">${hiringCount}</span>
                        <span class="cl-job-label">Hiring</span>
                    </div>
                    <div class="cl-job-line no-count">
                        <span class="cl-job-bullet bullet-neutral"></span>
                        <span class="cl-job-num">0</span>
                        <span class="cl-job-label">Hold</span>
                    </div>
                    <div class="cl-job-line no-count">
                        <span class="cl-job-bullet bullet-neutral"></span>
                        <span class="cl-job-num">0</span>
                        <span class="cl-job-label">Closed</span>
                    </div>
                </div>
                <div class="tbl-cell-req-actions">
                    <button type="button" class="btn btn-sm fw-bold px-3 py-1.5 request_event" data-row-id="${invId}" data-row-event="1" data-row-from_company="${fromComp}" style="background:#10b981; color:#fff; border:none;" title="Accept Direct Client Partnership">Accept</button>
                    <button type="button" class="btn btn-sm fw-bold px-3 py-1.5 request_event" data-row-id="${invId}" data-row-event="-1" data-row-from_company="${fromComp}" style="background:#ef4444; color:#fff; border:none;" title="Decline Partnership Request">Decline</button>
                    <button type="button" class="btn btn-sm btn-light fw-bold px-2 py-1.5 show-detail-direct" data-row-id="${targetId}" style="background:#f1f5f9; color:#2563eb; border:1px solid #e2e8f0;" title="View Client Details & Terms">Terms</button>
                </div>
            </div>`;
        }

        const jobActive = r.jobs ? (r.jobs.hiring || r.jobs.active || 0) : 0;
        const jobHold = r.jobs ? (r.jobs.hold || 0) : 0;
        const jobClosed = r.jobs ? (r.jobs.closed || 0) : 0;

        const applications = r.pipeline ? (r.pipeline.applications || 0) : 0;
        const sourced = r.pipeline ? (r.pipeline.sourced || 0) : 0;
        const screening = r.pipeline ? (r.pipeline.screening || 0) : 0;
        const evaluated = r.pipeline ? (r.pipeline.evaluated || 0) : 0;
        const negotiations = r.pipeline ? (r.pipeline.negotiations || 0) : 0;
        const offers = r.pipeline ? (r.pipeline.offers || 0) : 0;
        const joined = r.pipeline ? (r.pipeline.joined || 0) : 0;
        const interviews = r.pipeline ? (r.pipeline.interviews || 0) : 0;

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
                <div class="cl-type-name ${clientTypeClass} ${clientTypeCss}" data-row-id="${r.id}">
                    ${escapeAttr(clientTypeLabel)}
                </div>
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
            </div>
            <div class="tbl-cell-activity">
                <div class="cl-act-status ${jobActive > 0 ? 'status-active' : 'status-inactive'}">
                    <span class="cl-act-status-dot"></span>
                    <span>${jobActive > 0 ? 'Active' : 'Inactive'}</span>
                </div>
                <div class="cl-act-main">${escapeAttr(daysAgo || 'No activity yet')}</div>
                <div class="cl-act-desc">${daysAgo ? 'Active in system' : 'Be the first to add a job or candidate.'}</div>
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
            <div class="tbl-cell-pipeline" title="Total Applications: ${applications} | Interviews: ${interviews}">
                <div class="cl-pipeline-flow">
                    <div class="cl-pipe-step" title="Sourcing: ${sourced}">
                        <span class="cl-pipe-pill pill-sourcing">${sourced}</span>
                        <span class="cl-pipe-label">Sourcing</span>
                    </div>
                    <div class="cl-pipe-step" title="Screening: ${screening}">
                        <span class="cl-pipe-pill pill-screening">${screening}</span>
                        <span class="cl-pipe-label">Screening</span>
                    </div>
                    <div class="cl-pipe-step" title="Evaluation: ${evaluated}">
                        <span class="cl-pipe-pill pill-evaluation">${evaluated}</span>
                        <span class="cl-pipe-label">Evaluation</span>
                    </div>
                    <div class="cl-pipe-step" title="Negotiations: ${negotiations}">
                        <span class="cl-pipe-pill pill-negotiations">${negotiations}</span>
                        <span class="cl-pipe-label">Negotiations</span>
                    </div>
                    <div class="cl-pipe-step" title="Offer: ${offers}">
                        <span class="cl-pipe-pill pill-offer">${offers}</span>
                        <span class="cl-pipe-label">Offer</span>
                    </div>
                    <div class="cl-pipe-step" title="Joined: ${joined}">
                        <span class="cl-pipe-pill pill-joined">${joined}</span>
                        <span class="cl-pipe-label">Joined</span>
                    </div>
                    <div class="cl-pipe-divider" aria-hidden="true"></div>
                    <div class="cl-pipe-step step-interview" title="Interview: ${interviews}">
                        <span class="cl-pipe-pill pill-interview">${interviews}</span>
                        <span class="cl-pipe-label">Interview</span>
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
            loadClientList(page, false);
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
        var isDirect = (ClientStateManager.state.activeTab === 'direct');
        ClientStateManager.openClientDrawer(clientId, isDirect ? 'direct' : 'internal');
    }
    try {
        const url = new URL(window.location);
        url.searchParams.delete('open_client');
        window.history.replaceState({ tab: ClientStateManager.state.activeTab, page: page_no }, '', url);
    } catch(e) {}
}

function setClientTab(tabName, el, updateUrl = true) {
    ClientStateManager.onTabChange(tabName, updateUrl);
}

function applyClientResponse(response, targetPage) {
    if (!response || typeof response !== 'object') return;
    requestAnimationFrame(function() {
        $('.loadingMessage').css({ display: 'none' });
        $('.loader_search').css({ display: 'none' });
        $('.page-loader').css('display', 'none');
        try { localStorage.setItem('currentPageClients', targetPage); } catch (e) {}

        // Honest Search Degradation UX (Invariant I10)
        if (response.projection_status === 'syncing') {
            $('#cl_search_sync_banner').show();
        } else {
            $('#cl_search_sync_banner').hide();
        }

        $('#allClients').html(response.html);

        // Highlight selected client if one is active on current tab
        if (typeof ClientStateManager !== 'undefined') {
            var selectedId = ClientStateManager.getSelectedClientId();
            if (selectedId) {
                $(`[data-row-id="${selectedId}"]`).closest('.tbl-r').addClass('client-selected');
            }
        }

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

function loadClientList(page, updateUrl = true, cursor = null) {
    var targetPage = page || page_no || 1;
    page_no = targetPage;
    try { localStorage.setItem('currentPageClients', targetPage); } catch (e) {}

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

                if ((xhr.status === 0 || xhr.status === 419) && !ClientSessionRecoveryManager.isReconnecting) {
                    ClientSessionRecoveryManager.isReconnecting = true;
                    ClientSessionRecoveryManager.pingHeartbeat(function(authOk) {
                        ClientSessionRecoveryManager.isReconnecting = false;
                        if (authOk) {
                            loadClientList(targetPage, false);
                            return;
                        }
                    });
                }

                $('#allClients').html(`
                    <div class="cl-empty-state py-12 text-center">
                        <div class="cl-empty-icon mb-3">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        </div>
                        <div class="fw-bold fs-6 text-gray-800 mb-1">Unable to Load Client Data</div>
                        <div class="text-muted fs-7 mb-4">Connection was interrupted or timed out. Click below to reconnect.</div>
                        <button type="button" class="btn btn-sm btn-primary px-4 py-2" onclick="loadClientList(${targetPage}, false)">
                            <i class="fas fa-redo me-1"></i> Retry Connection
                        </button>
                    </div>
                `);
            }
        }
    });
}

// Preserve backwards-compatible global alias for legacy callers while protecting main function
window.loadClientList = loadClientList;
window.fetchCandidates = loadClientList;

$(document).ready(function() {
    // Phase 0: Initialize Session & Idle Inactivity Recovery Engine
    if (typeof ClientSessionRecoveryManager !== 'undefined') {
        ClientSessionRecoveryManager.init();
    }

    // Phase 1: Hydrate static country bundle from IndexedDB / JSON
    initCountryBundle();

    // Phase 2: Start background Web Worker OLAP engine
    initClientWorker();

    $(document).on('click', '#pagination .pagi-page, #pagination .pagi-arrow', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        const cursor = $(this).data('cursor') || null;
        if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
        loadClientList(page, true, cursor);
    });

    $(document).on('change', '#pagination .pagi-perpage', function() {
        var selectedPerPage = $(this).val();
        localStorage.setItem('candidatePerPage', selectedPerPage);
        localStorage.setItem('currentPageClients', 1);
        page_no = 1;
        loadClientList(1, true);
    });

    // Hydrate state from URL on page load
    hydrateStateFromUrl(false);

    const urlParams = new URLSearchParams(window.location.search);
    const hasFilterParams = urlParams.has('country') || urlParams.has('city') || urlParams.has('industry') || urlParams.has('activity') || urlParams.has('search') || urlParams.has('q') || urlParams.has('tab') || urlParams.has('sort');

    if ($('#allClients .tbl-r').length === 0 || hasFilterParams) {
        loadClientList(1, false);
    }
});

$(document).on('click', '.request_event', function () {
    var $btn = $(this);
    if ($btn.hasClass('disabled') || $btn.prop('disabled')) return;

    var id = $btn.data('row-id');
    var event_id = $btn.data('row-event');
    var from_company = $btn.data('row-from_company');
    var actionText = (event_id == -1) ? 'Decline' : 'Accept';
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
            var $row = $btn.closest('.tbl-r');
            var originalRowHtml = $row.html();
            var originalClasses = $row.attr('class');
            var $actionContainer = $btn.parent();
            var originalActionsHtml = $actionContainer.html();

            // Optimistic Perceived 0ms UI Transition
            $actionContainer.html(`
                <span class="badge bg-light-${event_id == 1 ? 'success text-success' : 'danger text-danger'} fw-bold px-3 py-1.5 d-inline-flex align-items-center gap-1">
                    <span class="spinner-border spinner-border-sm me-1" role="status" style="width:10px;height:10px;"></span>
                    ${event_id == 1 ? 'Accepting...' : 'Declining...'}
                </span>
            `);

            // Optimistically decrement request counters
            var $reqBadges = $('#span_clientRequest, #tabBadgeRequest');
            var origReqCount = parseInt($reqBadges.first().text(), 10) || 0;
            if (origReqCount > 0) {
                $reqBadges.text(origReqCount - 1);
            }
            var $dirBadges = $('#span_clientDirect, #tabBadgeDirect');
            var origDirCount = parseInt($dirBadges.first().text(), 10) || 0;
            if (event_id == 1) {
                $dirBadges.text(origDirCount + 1);
            }

            var mutationId = 'mut_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            var url = "{{ route('service_company.clients.request_update') }}";

            $.ajax({
                type: 'POST',
                url: url,
                data: {
                    '_token': '{{ csrf_token() }}',
                    'invitation_id': id,
                    'event_id': event_id,
                    'from_company': from_company,
                    'mutation_id': mutationId
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
                            mutationId: mutationId,
                            invitationId: id,
                            eventId: event_id,
                            version: response.mutation_version || null,
                            clientDataCnt: response.clientDataCnt || null
                        });
                    }

                    $actionContainer.html(`
                        <span class="badge bg-light-${event_id == 1 ? 'success text-success' : 'danger text-danger'} fw-bold px-3 py-1.5 d-inline-flex align-items-center gap-1">
                            ${event_id == 1 ? '✓ Accepted' : '✕ Declined'}
                        </span>
                    `);

                    swalWithBootstrapButtons.fire(
                        actionText + 'ed!',
                        response.message || ('Request has been ' + actionText.toLowerCase() + 'ed.'),
                        'success'
                    );

                    if (ClientStateManager.state.activeTab === 'request') {
                        $row.slideUp(250, function() {
                            $(this).remove();
                            if ($('#allClients .tbl-r').length === 0) {
                                $('#allClients').html(`
                                    <div class="cl-empty-state">
                                        <div class="cl-empty-icon">
                                            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="17" y1="11" x2="23" y2="11"/></svg>
                                        </div>
                                        <div class="cl-empty-title">No Pending Client Requests</div>
                                        <div class="cl-empty-desc">You're all caught up. New client partnership requests will appear here when received.</div>
                                    </div>
                                `);
                            }
                        });
                    } else {
                        // Refresh after brief delay so ES projection index propagates and row becomes normal Direct Client
                        setTimeout(function() {
                            loadClientList(1, false);
                        }, 500);
                    }
                },
                error: function(xhr) {
                    // Deterministic Rollback
                    $actionContainer.html(originalActionsHtml);
                    $row.attr('class', originalClasses);
                    $reqBadges.text(origReqCount);
                    if (event_id == 1) {
                        $('#span_clientDirect, #tabBadgeDirect').text(origDirCount);
                    }

                    if (xhr.status === 409) {
                        var conflictMsg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'This request has already been processed by another recruiter.';
                        swalWithBootstrapButtons.fire('Already Handled', conflictMsg, 'warning');
                        loadClientList(1, false);
                    } else {
                        swalWithBootstrapButtons.fire('Error', 'Failed to update request status. Please check your connection and try again.', 'error');
                    }
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

    // Invalidate drawer cache and notify SyncSupervisor (JDCR V5.2)
    if (typeof DrawerLRUCache !== 'undefined' && typeof DrawerLRUCache.clear === 'function') {
        DrawerLRUCache.clear();
    }
    const targetCid = (data.client && data.client.id) ? data.client.id : (data.client_id || data.id);
    if (targetCid && window.SyncSupervisor) {
        window.SyncSupervisor.notifyHint(targetCid);
    }

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
    loadClientList(page_no, false);
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
    if (typeof ClientSessionRecoveryManager !== 'undefined') {
        ClientSessionRecoveryManager.onWakeUp('focus');
    } else {
        fetchActiveProjection();
    }
});
</script>
@endpush
