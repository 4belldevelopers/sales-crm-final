@extends('layouts.service_company_layout')
@push('head-script')
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/clientList.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
@endpush
@section('content')

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_client" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">

        <!-- HERO -->
        @include('sections.service_company.clientHero', ['activeTab' => 'internal'])

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

        @if(in_array("view_clients", $userPermissions))
        <!-- Table -->
        <div class="tbl">
            <div class="tbl-w">
                <div class="tbl-h">
                    <div class="tbl-th-subcard tbl-th-check">
                        <input class="tbl-cb" type="checkbox" data-kt-check="true" data-kt-check-target="#allClients .tbl-cb" value="1" />
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
                    <div class="tbl-th-subcard" title="Sort by Jobs">
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
                <div class="loadingMessage" style="display:none; position:absolute;">Processing...</div>
                <div id="allClients"></div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-3" id="pagination"></div>
        @endif

    </div>
  </div>
</div>
<!--end::Content-->
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
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
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
		var id    			=	$(this).data('row-id');
		var url   			=	"{{ route('service_company.clients.viewmore',':id') }}";
		url     			=	url.replace(':id', id);
		 var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";
		$.ajax({
						type  		: 'GET',
						url   		: url,
						 beforeSend	: function() {
                                  $('#kt_activitiesClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    	},
						success 	: function (response) {
								$('#kt_activitiesClient_body').html(response.view);
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
                localStorage.setItem('currentPageCustomClients', 1);
                page_no = 1;
                fetchCandidates(1);
            });

      });

      var page_no = localStorage.getItem('currentPageCustomClients') || 1;

      var hiringFilter = 0;

      function setHiringFilter(el) {
          hiringFilter = (hiringFilter === 1) ? 0 : 1;
          $('.cl-tab').removeClass('active');
          if (hiringFilter === 1) {
              $(el).addClass('active');
          } else {
              $(".cl-tab[href*='custom_client']").addClass('active');
          }
          fetchCandidates(1);
      }

      function fetchCandidates(page, search = '') {
            var targetPage = page || page_no || 1;
            page_no = targetPage;
            localStorage.setItem('currentPageCustomClients', targetPage);

            var perPage = $('#pagination .pagi-perpage').val() || localStorage.getItem('candidatePerPage') || 10;

            if($('#kt_tagify_2').val()!=''){
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            $.ajax({
                url: `{!! route('service_company.clients.custom_client_data_ajax') !!}?page=${targetPage}`,
                type: 'GET',
                data: {
                  custom_status : $('#status').val(),
                  search : $('#inputSearch').val(),
                  sortingfilter : $('#sortingfilter').val(),
                  cus_search : $('#kt_tagify_2').val(),
                  country:$('#country').val(),
                  hiring: hiringFilter,
                  size: perPage,
                  per_page: perPage
                },
                success: function(response) {
                    $('.loadingMessage').css({ display: 'none' });
                    $('.loader_search').css({ display: 'none' });
                    $('.page-loader').css('display', 'none');
                    localStorage.setItem('currentPageCustomClients', targetPage);
                    $('#allClients').html(response.html);
                    $('#custom_client_count').html(response.total);
                    $('#pagination').html(response.pagination);
                }
            });
        }

</script>
@endpush
