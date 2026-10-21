@extends('layouts.service_company_layout')
@push('head-script')
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/clientList.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
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
    $activeJobsCount = (int)($clientDataCnt['activeJobs'] ?? ($clientDataCnt['clientHiring'] ?? 0));
    $pendingRequestsCount = (int)($clientDataCnt['clientRequest'] ?? 0);
    $hiringClientsCount = (int)($clientDataCnt['clientHiring'] ?? 0);

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
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
                <div>
                    <div class="cl-header-title-row">
                        <h1 class="cl-header-title">Clients</h1>
                        <span class="cl-verified-badge" title="Verified ATS Module">
                            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                <path d="m9 12 2 2 4-4" stroke="#16a34a" stroke-width="2.5"/>
                            </svg>
                        </span>
                    </div>
                    <div class="cl-header-subtitle">Manage client relationships, hiring activity and recruitment performance.</div>
                </div>
            </div>

            <div class="cl-header-actions">
                <button type="button" class="cl-export-btn" onclick="exportClientsToCsv()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/>
                        <line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    <span>Export</span>
                </button>

                @if(in_array("add_clients", $userPermissions))
                <a href="{{ url('service_company/clients/create') }}" class="cl-add-btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    <span>Add Internal Client</span>
                </a>
                @endif
            </div>
        </div>

        <!-- ── 5 STAT CARDS ── -->
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

            {{-- Card 4: Active Jobs --}}
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
                        <div class="cl-card-num" id="span_clientHiring">{{ $activeJobsCount }}</div>
                        <div class="cl-card-lbl">Active Jobs</div>
                    </div>
                </div>
                <div class="cl-card-sub orange">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="9" x2="20" y2="9"/>
                        <line x1="4" y1="15" x2="20" y2="15"/>
                        <line x1="10" y1="3" x2="8" y2="21"/>
                        <line x1="16" y1="3" x2="14" y2="21"/>
                    </svg>
                    <span>Across {{ max($hiringClientsCount, ($totalClientsCount > 0 ? 1 : 0)) }} client{{ max($hiringClientsCount, ($totalClientsCount > 0 ? 1 : 0)) > 1 ? 's' : '' }}</span>
                </div>
            </a>

            {{-- Card 5: Pending Requests --}}
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

            {{-- Filter 1: Client Type --}}
            <div class="cl-dropdown-pill">
                <div class="cl-pill-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="2" width="16" height="20" rx="2" ry="2"/>
                        <path d="M9 22v-4h6v4"/>
                        <path d="M8 6h.01"/>
                        <path d="M16 6h.01"/>
                        <path d="M8 10h.01"/>
                        <path d="M16 10h.01"/>
                    </svg>
                </div>
                <div class="cl-pill-texts">
                    <span class="cl-pill-lbl">Client Type</span>
                    <span class="cl-pill-val" id="clientTypeDisplay">All Types</span>
                </div>
                <div class="cl-pill-chevron">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <select class="cl-pill-select-native" id="clientTypeFilter" onchange="handleClientTypeChange(this)">
                    <option value="all" selected>All Types</option>
                    <option value="internal">Internal Clients</option>
                    <option value="direct">Direct Clients</option>
                    <option value="hiring">Active Hiring</option>
                    <option value="request">Pending Requests</option>
                </select>
            </div>

            {{-- Filter 2: Location --}}
            <div class="cl-dropdown-pill">
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
                <select class="cl-pill-select-native" id="country" onchange="handleLocationChange(this)">
                    <option value="" selected>All Locations</option>
                    @if (!empty($countries))
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            {{-- Filter 3: Sort --}}
            <div class="cl-dropdown-pill">
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
                    <option value="Show All" selected>Recently Added</option>
                    <option value="1">Newest to Oldest</option>
                    <option value="2">Oldest to Newest</option>
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
                <span>Active Hiring</span>
                <span class="cl-tab-badge" id="tabBadgeHiring">{{ $hiringClientsCount }}</span>
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
                        <div class="tbl-th-subcard" title="Sort by Company Name">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                <span>Company Name</span>
                            </div>
                            <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div class="tbl-th-subcard" title="Sort by Location">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>Location</span>
                            </div>
                            <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div class="tbl-th-subcard tbl-th-jobs" title="Jobs Status: Hiring, Hold, Closed">
                            <span class="tbl-th-title">Jobs</span>
                            <div class="tbl-th-job-chips">
                                <span class="th-chip chip-hiring" title="Hiring"><span class="chip-dot dot-green"></span>Hiring</span>
                                <span class="th-chip chip-hold" title="Hold"><span class="chip-dot dot-orange"></span>Hold</span>
                                <span class="th-chip chip-closed" title="Closed"><span class="chip-dot dot-red"></span>Closed</span>
                            </div>
                        </div>
                        <div class="tbl-th-subcard" title="Hiring Pipeline">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                                <span>Hiring Pipeline</span>
                            </div>
                            <svg class="tbl-th-info" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-toggle="tooltip" title="Funnel: Sourced → Evaluated → Interviews → Offers → Joined"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                        </div>
                        <div class="tbl-th-subcard" title="Sort by Date">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <span>Date</span>
                            </div>
                            <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div class="tbl-th-subcard" title="Sort by Last Activity">
                            <div class="tbl-th-label">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <span>Last Activity</span>
                            </div>
                            <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
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
<div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Internal Client</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesClient_close">
          <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
                <rect x="0" y="7" width="16" height="2" rx="1" />
                <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
              </g>
            </svg>
          </span>
        </button>
      </div>
    </div>
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
  </div>
</div>

<!-- ── DRAWER FOR DIRECT CLIENT ── -->
<div id="kt_activitiesDirectClient_toggle"></div>
<div id="kt_activitiesDirectClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesDirectClient_toggle" data-kt-drawer-close="#kt_activitiesDirectClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Direct Client</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
      </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesDirectClient_close">
          <span class="svg-icon svg-icon-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
              <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
                <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
                <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
              </g>
            </svg>
          </span>
        </button>
      </div>
    </div>
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

// ── Dropdown label sync handlers ──
function handleClientTypeChange(sel) {
    var text = sel.options[sel.selectedIndex].text;
    $('#clientTypeDisplay').text(text);
    var val = sel.value;
    setClientTab(val);
}

function handleLocationChange(sel) {
    var text = sel.options[sel.selectedIndex].text;
    $('#locationDisplay').text(text);
    filtersdataget();
}

function handleSortChange(sel) {
    var text = sel.options[sel.selectedIndex].text;
    $('#sortDisplay').text(text);
    filtersdataget();
}

// ── Select all checkbox ──
function toggleSelectAllClients(master) {
    var checked = $(master).is(':checked');
    $('#allClients .tbl-cb').prop('checked', checked);
}

// ── Client-side CSV Exporter ──
function exportClientsToCsv() {
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

$(document).on('click', '#kt_activitiesClient_close', function(e) {
    e.preventDefault();
    var drawerEl = document.getElementById('kt_activitiesClient');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerEl);
        if (drawer && typeof drawer.hide === 'function') {
            drawer.hide();
        }
    }
});

$(document).on('click', '#kt_activitiesDirectClient_close', function(e) {
    e.preventDefault();
    var drawerEl = document.getElementById('kt_activitiesDirectClient');
    if (drawerEl && typeof KTDrawer !== 'undefined') {
        var drawer = KTDrawer.getInstance(drawerEl);
        if (drawer && typeof drawer.hide === 'function') {
            drawer.hide();
        }
    }
});

// ── Bounded Browser RAM Store (ClientStore) with SWR & Request Token Guard ──
const ClientStore = {
    cache: new Map(),
    maxEntries: 50,
    currentXhr: null,
    latestRequestId: 0,
    latestMutationVersion: 0,
    maxStaleAgeMs: 5000, // 5 seconds bounded staleness for SWR

    getKey: function(tab, page, search, sort, country, hiring, perPage) {
        return `${tab}:${page}:${search || ''}:${sort || ''}:${country || ''}:${hiring || 0}:${perPage || 10}`;
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
    var initKey = ClientStore.getKey(initTab, 1, '', 'Show All', '', (initTab === 'hiring' ? 1 : 0), 10);
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

        const countryVal = $('#country').val() || '';
        if (countryVal) {
            url.searchParams.set('country', countryVal);
        } else {
            url.searchParams.delete('country');
        }

        const sortVal = $('#sortingfilter').val() || '';
        if (sortVal && sortVal !== 'Show All') {
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
        const sort = params.get('sort') || params.get('sortingfilter') || 'Show All';
        const page = parseInt(params.get('page'), 10) || 1;
        const openClient = parseInt(params.get('open_client'), 10) || initialOpenClientId || 0;

        $('#kt_tagify_2').val(search);
        $('#country').val(country);
        $('#sortingfilter').val(sort);

        if (country) {
            const countryText = $('#country option:selected').text();
            $('#locationDisplay').text(countryText);
        } else {
            $('#locationDisplay').text('All Locations');
        }

        if (sort && sort !== 'Show All') {
            const sortText = $('#sortingfilter option:selected').text();
            $('#sortDisplay').text(sortText);
        } else {
            $('#sortDisplay').text('Recently Added');
        }

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
    hiringFilter = (tabName === 'hiring') ? 1 : 0;
    page_no = 1;

    $('.cl-stat-filter').removeClass('active');
    $('.cl-tab-filter').removeClass('active');

    $(`.cl-stat-filter[data-tab="${tabName}"]`).addClass('active');
    $(`.cl-tab-filter[data-tab="${tabName}"]`).addClass('active');

    // Update Client Type dropdown label
    var tabLabels = {
        'all': 'All Types',
        'internal': 'Internal Clients',
        'direct': 'Direct Clients',
        'hiring': 'Active Hiring',
        'request': 'Pending Requests'
    };
    if (tabLabels[tabName]) {
        $('#clientTypeDisplay').text(tabLabels[tabName]);
        $('#clientTypeFilter').val(tabName);
    }

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
            <div class="tbl-th-subcard" title="Sort by Company Name">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <span>Company Name</span>
                </div>
                <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="tbl-th-subcard" title="Sort by Location">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2a8 8 0 0 0-8 8c0 5.25 8 12 8 12s8-6.75 8-12a8 8 0 0 0-8-8z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>Location</span>
                </div>
                <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="tbl-th-subcard tbl-th-jobs" title="Jobs Status: Hiring, Hold, Closed">
                <span class="tbl-th-title">Jobs</span>
                <div class="tbl-th-job-chips">
                    <span class="th-chip chip-hiring" title="Hiring"><span class="chip-dot dot-green"></span>Hiring</span>
                    <span class="th-chip chip-hold" title="Hold"><span class="chip-dot dot-orange"></span>Hold</span>
                    <span class="th-chip chip-closed" title="Closed"><span class="chip-dot dot-red"></span>Closed</span>
                </div>
            </div>
            <div class="tbl-th-subcard" title="Hiring Pipeline">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                    <span>Hiring Pipeline</span>
                </div>
                <svg class="tbl-th-info" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" data-toggle="tooltip" title="Funnel: Sourced → Evaluated → Interviews → Offers → Joined"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            </div>
            <div class="tbl-th-subcard" title="Sort by Date">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span>Date</span>
                </div>
                <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="tbl-th-subcard" title="Sort by Last Activity">
                <div class="tbl-th-label">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Last Activity</span>
                </div>
                <svg class="tbl-th-sort" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        `);
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
            } else if (activeClientTab === 'request') {
                $('#span_clientRequest').html(response.total);
                $('#tabBadgeRequest').html(response.total);
            }
        }
        $('#pagination').html(response.pagination);
    });
}

function fetchCandidates(page, updateUrl = true) {
    var targetPage = page || page_no || 1;
    page_no = targetPage;
    localStorage.setItem('currentPageClients', targetPage);

    if (updateUrl) {
        syncUrlState();
    }

    var perPage = $('#pagination .pagi-perpage').val() || localStorage.getItem('candidatePerPage') || 10;
    var searchVal = $('#kt_tagify_2').val() || '';
    var sortVal = $('#sortingfilter').val() || '';
    var countryVal = $('#country').val() || '';

    var cacheKey = ClientStore.getKey(activeClientTab, targetPage, searchVal, sortVal, countryVal, hiringFilter, perPage);
    var cachedEntry = ClientStore.get(cacheKey);

    // Stale-While-Revalidate (SWR): Immediate perceived instant render if cache entry exists
    if (cachedEntry) {
        applyClientResponse(cachedEntry.data, targetPage);
        if (!cachedEntry.isStale) {
            return; // Warm hit within 5s SLA - 0 network requests!
        }
        // Stale entry: UI already rendered instantly; background revalidation runs without blocking UI
    } else {
        // Cold fetch: show non-blocking progress indicator
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
            cus_search: searchVal,
            country: countryVal,
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
    $(document).on('click', '#pagination .pagi-page, #pagination .pagi-arrow', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;
        fetchCandidates(page, true);
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

    if ($('#allClients .tbl-r').length === 0) {
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

// ── Native Browser Cross-Tab Synchronization Channel ──
(function() {
    if (typeof BroadcastChannel !== 'undefined') {
        window.clientSyncChannel = new BroadcastChannel('jero_clients_cross_tab_sync');

        window.clientSyncChannel.onmessage = function(event) {
            const data = event.data;
            if (!data) return;

            if (data.type === 'CLIENT_REQUEST_MUTATED') {
                if (data.version) {
                    ClientStore.latestMutationVersion = data.version;
                }
                ClientStore.invalidateTab('request');
                ClientStore.invalidateTab('all');
                ClientStore.invalidateTab('direct');

                if (data.clientDataCnt) {
                    if ($('#span_clientAll').length) $('#span_clientAll').html(data.clientDataCnt.clientAll);
                    if ($('#span_clientDirect').length) $('#span_clientDirect').html(data.clientDataCnt.clientDirect);
                    if ($('#span_clientCustom').length) $('#span_clientCustom').html(data.clientDataCnt.clientCustom);
                    if ($('#span_clientHiring').length) $('#span_clientHiring').html(data.clientDataCnt.clientHiring);
                    if ($('#span_clientRequest').length) $('#span_clientRequest').html(data.clientDataCnt.clientRequest);
                }

                if (activeClientTab === 'request') {
                    fetchCandidates(1);
                }
            }
        };
    }
})();
</script>
@endpush
