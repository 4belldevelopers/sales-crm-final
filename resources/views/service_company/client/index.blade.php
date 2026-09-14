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
         'clientAll' =>@$clientDataCnt['clientAll'],
         'clientDirect' =>@$clientDataCnt['clientDirect'],
         'clientCustom' =>@$clientDataCnt['clientCustom'],
         'clientRequest' =>@$clientDataCnt['clientRequest'],
        ];
@endphp


<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_client" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">

        <style>
        /* ── Client Hero Redesign ── */
        .cl-hero { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:18px; gap:12px; }
        .cl-hero-left { display:flex; flex-direction:column; gap:3px; }
        .cl-hero-title { display:flex; align-items:center; gap:8px; font-size:20px; font-weight:700; color:#181c32; line-height:1.2; }
        .cl-hero-title svg { width:20px; height:20px; flex-shrink:0; }
        .cl-hero-sub { font-size:12px; color:#7e8299; margin-top:2px; }
        .cl-add-btn { display:inline-flex; align-items:center; gap:6px; background:#1b84ff; color:#fff; border:none; border-radius:7px; padding:9px 18px; font-size:12px; font-weight:600; cursor:pointer; text-decoration:none; white-space:nowrap; }
        .cl-add-btn:hover { background:#056ee9; color:#fff; }

        /* Stat cards */
        .cl-stats { display:flex; gap:12px; margin-bottom:16px; flex-wrap:wrap; }
        .cl-stat { flex:1; min-width:140px; background:#fff; border:1px solid #e4e6ef; border-radius:10px; padding:14px 16px; display:flex; align-items:center; gap:12px; cursor:pointer; transition:box-shadow .15s, border-color .15s; text-decoration:none; }
        .cl-stat:hover, .cl-stat.active { box-shadow:0 4px 14px rgba(27,132,255,.18); border-color:#1b84ff; text-decoration:none; }
        .cl-stat-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
        .cl-stat-icon svg { width:20px; height:20px; }
        .cl-stat-icon.blue { background:#eff6ff; color:#2563eb; }
        .cl-stat-icon.indigo { background:#eef2ff; color:#4f46e5; }
        .cl-stat-icon.green { background:#f0fdf4; color:#16a34a; }
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
        .cl-filter-right { display:flex; align-items:center; gap:6px; margin-left:auto; }
        .cl-filter-btn { display:inline-flex; align-items:center; gap:5px; border:1px solid #e4e6ef; border-radius:7px; background:#fff; padding:7px 12px; font-size:11px; font-weight:500; color:#5e6278; cursor:pointer; white-space:nowrap; text-decoration:none; }
        .cl-filter-btn:hover { border-color:#1b84ff; color:#1b84ff; }
        .cl-filter-btn svg { width:13px; height:13px; }
        .cl-icon-btn { width:32px; height:32px; border:1px solid #e4e6ef; border-radius:7px; background:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; color:#5e6278; }
        .cl-icon-btn:hover { border-color:#1b84ff; color:#1b84ff; }
        .cl-icon-btn svg { width:13px; height:13px; }

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
                    Clients
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4" stroke="#16A34A" stroke-width="2.5"/>
                    </svg>
                </div>
                <div class="cl-hero-sub">Manage and track your clients, relationships and business performance.</div>
            </div>
            @if(in_array("add_clients", $userPermissions))
            <a href="{{ url('service_company/clients/create') }}" class="cl-add-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="14" height="14"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Add Client
            </a>
            @endif
        </div>

        <!-- STAT CARDS -->
        <div class="cl-stats">
            <a href="#" onclick="setClientTab('all', this); return false;" class="cl-stat cl-stat-filter active" data-tab="all">
                <div class="cl-stat-icon blue">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div>
                    <div class="cl-stat-num" id="span_clientAll">{{ $clientDataCnt['clientAll'] ?? 0 }}</div>
                    <div class="cl-stat-label">Total Clients</div>
                </div>
            </a>
            <a href="#" onclick="setClientTab('internal', this); return false;" class="cl-stat cl-stat-filter" data-tab="internal">
                <div class="cl-stat-icon indigo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div>
                    <div class="cl-stat-num" id="span_clientCustom">{{ $clientDataCnt['clientCustom'] ?? 0 }}</div>
                    <div class="cl-stat-label">Internal Clients</div>
                </div>
            </a>
            <a href="#" onclick="setClientTab('direct', this); return false;" class="cl-stat cl-stat-filter" data-tab="direct">
                <div class="cl-stat-icon green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <div class="cl-stat-num" id="span_clientDirect">{{ $clientDataCnt['clientDirect'] ?? 0 }}</div>
                    <div class="cl-stat-label">Direct Clients</div>
                </div>
            </a>
            <a href="#" onclick="setClientTab('hiring', this); return false;" class="cl-stat cl-stat-filter" data-tab="hiring">
                <div class="cl-stat-icon orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
                </div>
                <div>
                    <div class="cl-stat-num" id="span_clientHiring">{{ $clientDataCnt['clientHiring'] ?? 0 }}</div>
                    <div class="cl-stat-label">Clients Actively Hiring</div>
                </div>
            </a>
            <a href="#" onclick="setClientTab('request', this); return false;" class="cl-stat cl-stat-filter" data-tab="request">
                <div class="cl-stat-icon purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="12" y2="17"/></svg>
                </div>
                <div>
                    <div class="cl-stat-num" id="span_clientRequest">{{ $clientDataCnt['clientRequest'] ?? 0 }}</div>
                    <div class="cl-stat-label">Pending Requests</div>
                </div>
            </a>
        </div>

        <!-- FILTER BAR -->
        <div class="cl-filter">
            <div class="cl-search-wrap" style="flex:1;">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" class="cl-search-input" name="inputJobSearch" value="" placeholder="Search....." id="kt_tagify_2" onchange="filtersdataget()" />
                <div id="loader_search" class="loader_search hidden_search" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);"></div>
            </div>
            <select class="cl-select" id="sortingfilter" onchange="filtersdataget()" style="min-width:160px;">
                <option value="Show All" selected>Sort</option>
                <option value="1">Newest to Oldest (Upload Date)</option>
                <option value="2">Oldest to Newest (Upload Date)</option>
            </select>
            <select class="cl-select" id="country" onchange="filtersdataget()" style="min-width:160px;">
                <option value="" selected>Location</option>
                @if (!empty($countries))
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                    @endforeach
                @endif
            </select>
        </div>

        <!-- TABS -->
        <div class="cl-tabs">
            <a href="#" onclick="setClientTab('all', this); return false;" class="cl-tab cl-tab-filter active" data-tab="all">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                All Clients
            </a>
            <a href="#" onclick="setClientTab('internal', this); return false;" class="cl-tab cl-tab-filter" data-tab="internal">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Internal Clients
            </a>
            <a href="#" onclick="setClientTab('direct', this); return false;" class="cl-tab cl-tab-filter" data-tab="direct">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Direct Clients
            </a>
            <a href="#" onclick="setClientTab('hiring', this); return false;" class="cl-tab cl-tab-filter" id="tab-hiring" data-tab="hiring">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/></svg>
                Hiring
            </a>
            <a href="#" onclick="setClientTab('request', this); return false;" class="cl-tab cl-tab-filter" data-tab="request">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Pending Job Requests
            </a>
        </div>

        @if(in_array("view_clients", $userPermissions))
        <!-- Table -->
        <div class="tbl">
            <div class="tbl-w">
                <div class="tbl-h" id="clientTableHead">
                    <span>
                        <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="tbl-cb" type="checkbox" data-kt-check="true" data-kt-check-target="#allClients .tbl-cb" value="1" />
                        </div>
                    </span>
                    <span>Name
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="6 9 12 15 18 9"/></svg>
                    </span>
                    <span>Location</span>
                    <span>Jobs</span>
                    <span>Performance
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    </span>
                    <span>Connected On</span>
                    <span>Client</span>
                </div>
                <div class="loadingMessage" style="display:none; position:absolute;">Processing...</div>
                <div id="allClients"></div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-3" id="pagination"></div>
        @endif

    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->

</div>
<!--end::Content-->
<div id="kt_activitiesClient_toggle"></div>
<div id="kt_activitiesClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesClient_toggle" data-kt-drawer-close="#kt_activitiesClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <!--begin::Header-->
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Internal Client</h3>
        <!--begin::seprator-->
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span>
        <!--end::seprator-->
      </div>
      <!--end::Header-->
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesClient_close">
        <!--begin::Svg Icon | path: icons/duotone/Navigation/Close.svg-->
        <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
            <rect x="0" y="7" width="16" height="2" rx="1" />
            <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span>
        <!--end::Svg Icon-->
        </button>
      </div>
    </div>
    <!--end::Header-->
    <div class="card-body position-relative px-0 py-0" id="kt_activitiesClient_body"> &nbsp; </div>
    <!--begin::Body-->
  </div>
</div>
<div id="kt_activitiesDirectClient_toggle"></div>
<div id="kt_activitiesDirectClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesDirectClient_toggle" data-kt-drawer-close="#kt_activitiesDirectClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Direct Client</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-primary me-n5" id="kt_activitiesDirectClient_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="#000000">
            <rect fill="#000000" x="0" y="7" width="16" height="2" rx="1" />
            <rect fill="#000000" opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
          </g>
        </svg>
        </span> </button>
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

 function filtersdataget(){
            fetchCandidates();
        	}

  $(document).on('click', '.show-detail', function ()
	{

		$('#kt_activitiesClient_toggle').trigger('click');
    var currentPageClients = $('.custom-pagination .page-link.active').data('page');
      localStorage.setItem('currentPageClients', currentPageClients);
		var id    		= $(this).data('row-id');
		var url   		= "{{ route('service_company.clients.viewmore',':id') }}";
		url     		= url.replace(':id', id);

		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}";
		$.ajax({
						type  : 'GET',
						url   : url,
						 beforeSend: function() {
                                  $('#kt_activitiesClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    	},
						success : function (response) {
                $('#kt_activitiesDirectClient_body').html('');
								$('#kt_activitiesClient_body').html(response.view);
						}
	  			});
	});

	$(document).on('click', '.show-detail-direct', function ()
	{
		$('#kt_activitiesDirectClient_toggle').trigger('click');
		var id    		= $(this).data('row-id');
		var url   		= "{{ route('service_company.clients.dviewmore',':id') }}";
		url     		= url.replace(':id', id);

		 var baseurl    = "{{ asset('assets/images/Loading2.gif') }}";
		$.ajax({
						type  : 'GET',
						url   : url,
						 beforeSend: function() {
                                  $('#kt_activitiesDirectClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    	},
						success : function (response) {
              $('#kt_activitiesClient_body').html('');
								$('#kt_activitiesDirectClient_body').html(response.view);
						}
	  			});
	});


    $(document).ready(function() {

            fetchCandidates();

            $(document).on('click', '#pagination .pagi-page, #pagination .pagi-arrow', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('pagi-disabled') || $(this).hasClass('pagi-active-disabled')) return;

                fetchCandidates(page, $('#search').val());
            });

            $(document).on('change', '#pagination .pagi-perpage', function() {
                var selectedPerPage = $(this).val();
                localStorage.setItem('candidatePerPage', selectedPerPage);
                localStorage.setItem('currentPageClients', 1);
                page_no = 1;
                fetchCandidates(1);
            });

      });

      var page_no = localStorage.getItem('currentPageClients') || 1;

      var activeClientTab = 'all';
      var hiringFilter = 0;

      function setClientTab(tabName, el) {
          activeClientTab = tabName;
          hiringFilter = (tabName === 'hiring') ? 1 : 0;

          $('.cl-stat-filter').removeClass('active');
          $('.cl-tab-filter').removeClass('active');

          $(`.cl-stat-filter[data-tab="${tabName}"]`).addClass('active');
          $(`.cl-tab-filter[data-tab="${tabName}"]`).addClass('active');

          if (tabName === 'request') {
              $('.tbl').addClass('tbl-req');
              $('#clientTableHead').html(`
                  <span>Name</span>
                  <span>Location</span>
                  <span>Request Date</span>
                  <span style="justify-content:flex-end;">Action</span>
              `);
          } else {
              $('.tbl').removeClass('tbl-req');
              $('#clientTableHead').html(`
                  <span>
                      <div class="form-check form-check-sm form-check-custom form-check-solid">
                          <input class="tbl-cb" type="checkbox" data-kt-check="true" data-kt-check-target="#allClients .tbl-cb" value="1" />
                      </div>
                  </span>
                  <span>Name
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="6 9 12 15 18 9"/></svg>
                  </span>
                  <span>Location</span>
                  <span>Jobs</span>
                  <span>Performance
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                  </span>
                  <span>Connected On</span>
                  <span>Client</span>
              `);
          }

          fetchCandidates(1);
      }

      function setHiringFilter(el) {
          setClientTab('hiring', el);
      }

      function fetchCandidates(page, search = '') {
            var targetPage = page || page_no || 1;
            page_no = targetPage;
            localStorage.setItem('currentPageClients', targetPage);

            var perPage = $('#pagination .pagi-perpage').val() || localStorage.getItem('candidatePerPage') || 10;

            if($('#kt_tagify_2').val()!=''){
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            $.ajax({
                url: `{!! route('service_company.clients.all_clients_data_ajax') !!}?page=${targetPage}`,
                type: 'GET',
                data: {
                  custom_status : $('#status').val(),
                  search : $('#inputSearch').val(),
                  sortingfilter : $('#sortingfilter').val(),
                  cus_search : $('#kt_tagify_2').val(),
                  country:$('#country').val(),
                  hiring: hiringFilter,
                  tab: activeClientTab,
                  size: perPage,
                  per_page: perPage
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    localStorage.setItem('currentPageClients', targetPage);
                    $('#allClients').html(response.html);
                    if (activeClientTab === 'all' && response.total !== undefined) {
                        $('#span_clientAll').html(response.total);
                    }
                    $('#pagination').html(response.pagination);
                }
            });
        }

$(document).on('click', '.request_event', function () {
    var id = $(this).data('row-id');
    var event_id = $(this).data('row-event');
    var from_company = $(this).data('row-from_company');
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
            var url = "{{ route('service_company.clients.request_update') }}";
            $.ajax({
                type: 'GET',
                url: url,
                data: {'invitation_id': id, 'event_id': event_id, 'from_company': from_company},
                success: function (response) {
                    if (response.clientDataCnt) {
                        if ($('#span_clientAll').length) $('#span_clientAll').html(response.clientDataCnt.clientAll);
                        if ($('#span_clientDirect').length) $('#span_clientDirect').html(response.clientDataCnt.clientDirect);
                        if ($('#span_clientCustom').length) $('#span_clientCustom').html(response.clientDataCnt.clientCustom);
                        if ($('#span_clientRequest').length) $('#span_clientRequest').html(response.clientDataCnt.clientRequest);
                    }
                    if (response.status == 'success') {
                        fetchCandidates(1);
                        swalWithBootstrapButtons.fire(
                            actionText + 'ed!',
                            response.message || ('Request has been ' + actionText.toLowerCase() + 'ed.'),
                            'success'
                        );
                    }
                }
            });
        }
    });
});

</script>
@endpush
