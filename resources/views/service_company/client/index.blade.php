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

        <!-- HERO -->
        <div class="hero-section">
            <div class="hero-left">
                <div class="hero-title">
                    Clients
                    <svg class="shield-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        <path d="m9 12 2 2 4-4" stroke="#16A34A"/>
                    </svg>
                </div>
                <div class="hero-sub">Manage and track your clients</div>
                <div class="stats-row">
                    <a href="{{ route('service_company.clients.index') }}" class="stat-card active">
                        <div class="stat-icon blue">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <div class="stat-num">{{ @$clientDataCnt['clientAll'] ?? 0 }}</div>
                            <div class="stat-label">All Clients</div>
                        </div>
                    </a>
                    <a href="{{ url('service_company/clients/direct_client/0') }}" class="stat-card">
                        <div class="stat-icon green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <div class="stat-num">{{ @$clientDataCnt['clientDirect'] ?? 0 }}</div>
                            <div class="stat-label">Direct Clients</div>
                        </div>
                    </a>
                    <a href="{{ url('service_company/clients/custom_client/0') }}" class="stat-card">
                        <div class="stat-icon yellow">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <div class="stat-num">{{ @$clientDataCnt['clientCustom'] ?? 0 }}</div>
                            <div class="stat-label">Internal Clients</div>
                        </div>
                    </a>
                    <a href="{{ url('service_company/clients/client_request/0') }}" class="stat-card with-badge">
                        <span class="new-badge">New</span>
                        <div class="stat-icon purple">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                        </div>
                        <div class="stat-info">
                            <div class="stat-num">{{ @$clientDataCnt['clientRequest'] ?? 0 }}</div>
                            <div class="stat-label">Client Requests</div>
                        </div>
                    </a>
                </div>
            </div>
            <!-- Illustration -->
            <div class="illus-svg-wrap">
                <svg viewBox="0 0 220 160" xmlns="http://www.w3.org/2000/svg" width="220" height="160">
                    <rect x="0" y="30" width="90" height="60" rx="10" fill="#EFF6FF" stroke="#BFDBFE" stroke-width="1"/>
                    <circle cx="16" cy="60" r="9" fill="#94A3B8" transform="translate(8,0)"/>
                    <rect x="34" y="52" width="46" height="6" rx="3" fill="#CBD5E1"/>
                    <rect x="34" y="62" width="32" height="4" rx="2" fill="#E2E8F0"/>
                    <rect x="34" y="70" width="22" height="4" rx="2" fill="#BFDBFE"/>
                    <text x="64" y="76" font-size="8" fill="#F59E0B">★★★★★</text>
                    <ellipse cx="120" cy="148" rx="22" ry="6" fill="#E2E8F0" opacity=".5"/>
                    <rect x="108" y="90" width="24" height="38" rx="4" fill="#2563EB"/>
                    <circle cx="120" cy="82" r="12" fill="#FBBF24"/>
                    <path d="M108 80 Q120 68 132 80" fill="#1E293B"/>
                    <rect x="108" y="78" width="4" height="14" rx="2" fill="#1E293B"/>
                    <path d="M113 90 L120 98 L127 90" fill="white" opacity=".6"/>
                    <rect x="100" y="92" width="10" height="5" rx="2.5" fill="#2563EB"/>
                    <rect x="130" y="92" width="10" height="5" rx="2.5" fill="#2563EB"/>
                    <rect x="112" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                    <rect x="121" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                    <ellipse cx="115" cy="143" rx="5" ry="3" fill="#111827"/>
                    <ellipse cx="124" cy="143" rx="5" ry="3" fill="#111827"/>
                    <ellipse cx="175" cy="148" rx="22" ry="6" fill="#E2E8F0" opacity=".5"/>
                    <rect x="163" y="90" width="24" height="38" rx="4" fill="#1E40AF"/>
                    <circle cx="175" cy="82" r="12" fill="#D97706"/>
                    <path d="M163 78 Q175 68 187 78 Q185 72 175 70 Q165 72 163 78Z" fill="#1E293B"/>
                    <path d="M168 88 Q175 94 182 88 Q179 92 175 93 Q171 92 168 88Z" fill="#92400E" opacity=".7"/>
                    <path d="M168 90 L175 98 L182 90" fill="white" opacity=".6"/>
                    <path d="M175 94 L173 108 L175 110 L177 108 Z" fill="#EF4444"/>
                    <rect x="155" y="92" width="10" height="5" rx="2.5" fill="#1E40AF"/>
                    <rect x="185" y="92" width="10" height="5" rx="2.5" fill="#1E40AF"/>
                    <rect x="167" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                    <rect x="176" y="126" width="7" height="16" rx="3" fill="#1E293B"/>
                    <ellipse cx="170" cy="143" rx="5" ry="3" fill="#111827"/>
                    <ellipse cx="179" cy="143" rx="5" ry="3" fill="#111827"/>
                    <path d="M131 108 Q148 114 164 108" stroke="#F59E0B" stroke-width="3" stroke-linecap="round" fill="none"/>
                    <circle cx="131" cy="108" r="4" fill="#FBBF24"/>
                    <circle cx="164" cy="108" r="4" fill="#FBBF24"/>
                    <rect x="150" y="10" width="68" height="38" rx="8" fill="white" stroke="#E5E7EB" stroke-width="1"/>
                    <circle cx="162" cy="22" r="5" fill="#94A3B8"/>
                    <rect x="171" y="16" width="36" height="5" rx="2.5" fill="#CBD5E1"/>
                    <text x="171" y="34" font-size="9" fill="#F59E0B">★★★★★</text>
                    <circle cx="205" cy="58" r="13" fill="#DCFCE7" stroke="#86EFAC" stroke-width="1.5"/>
                    <path d="M199 58l4 4 7-7" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                </svg>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="filter-bar">
            <div class="search-input-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" class="search-input" name="inputJobSearch" value="" placeholder="Search....." id="kt_tagify_2" onchange="filtersdataget()" />
                <div id="loader_search" class="loader_search hidden_search" style="position:absolute; right:12px; top:50%; transform:translateY(-50%);"></div>
            </div>

            <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                <select class="form-select form-select-lg" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" onchange="filtersdataget()">
                    <option value="Show All" selected="">Sort</option>
                    <option value="1">Newest to Oldest (Upload Date)</option>
                    <option value="2">Oldest to Newest (Upload Date)</option>
                </select>
            </div>

            <div class="col-lg-2 d-flex align-items-center px-2" style="width: 20%;">
                <select class="form-select form-select-lg" data-hide-search="true" id="country" data-dropdown-css-class="w-150px" data-placeholder="Select an option" onchange="filtersdataget()">
                    <option value="" selected="selected">Location</option>
                    @if (!empty($countries))
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            @if(in_array("add_clients", $userPermissions))
            <a href="{{ url('service_company/clients/create') }}">
                <button type="button" class="ai-match-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                    Add Client
                </button>
            </a>
            @endif
        </div>

        @if(in_array("view_clients", $userPermissions))
        <!-- Table -->
        <div class="tbl">
            <div class="tbl-w">
                <div class="tbl-h">
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
	$('#kt_tagify_2').keyup(function() {
    fetchCandidates();
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

            $(document).on('click', '#pagination .page-link', function(e) {
                e.preventDefault();

                const page = $(this).data('page');
                if (!page || $(this).hasClass('disabled')) return;

                // Disable the clicked pagination link
                $(this).addClass('disabled');

                // Fetch data
                fetchCandidates(page, $('#search').val()).then(() => {
                    // Re-enable the links after successful data fetch
                    $('#pagination .page-link').removeClass('disabled');
                }).catch(() => {
                    // Re-enable the links even if there's an error
                    $('#pagination .page-link').removeClass('disabled');
                });
            });

      });

      var page_no = localStorage.getItem('currentPageClients');
                if (!page_no) {
                    page_no = 1;
                }

      function fetchCandidates(page = page_no, search = '') {
            var clientid = '1';
            if($('#kt_tagify_2').val()!=''){
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            $.ajax({
                url: `{!! route('service_company.clients.all_clients_data_ajax') !!}?page=${page}`,
                type: 'GET',
                data: {
                  custom_status : $('#status').val(),
                  search : $('#inputSearch').val(),
                  sortingfilter : $('#sortingfilter').val(),
                  cus_search : $('#kt_tagify_2').val(),
                  country:$('#country').val()
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    localStorage.setItem('currentPageClients', 1);
                    $('#allClients').html(response.html);
                    $('#span_clientAll').html(response.total);
                    $('#pagination').html(response.pagination);
                }
            });
        }

</script>
@endpush
