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
        @include('sections.service_company.clientHero', ['activeTab' => 'direct'])

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

    </div>
  </div>
</div>
<!--end::Content-->
<div id="kt_activitiesDirectClient_toggle"></div>
<div id="kt_activitiesDirectClient" class="bg-body" data-kt-drawer="true" data-kt-drawer-name="activities" data-kt-drawer-activate="true" data-kt-drawer-overlay="true" data-kt-drawer-direction="end" data-kt-drawer-toggle="#kt_activitiesDirectClient_toggle" data-kt-drawer-close="#kt_activitiesDirectClient_close">
  <div class="card shadow-none rounded-0 w-100">
    <div class="card-header px-5 py-0" id="kt_activities_header">
      <div class="d-flex align-items-center py-1">
        <h3 class="card-title fw-bolder text-dark">Direct Client</h3>
        <span class="h-20px border-gray-200 border-start mx-4 d-none"></span> </div>
      <div class="card-toolbar">
        <button type="button" class="btn btn-sm btn-icon btn-active-light-danger me-n5" id="kt_activitiesDirectClient_close"> <span class="svg-icon svg-icon-1">
        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
          <g transform="translate(12.000000, 12.000000) rotate(-45.000000) translate(-12.000000, -12.000000) translate(4.000000, 4.000000)" fill="currentColor">
            <rect x="0" y="7" width="16" height="2" rx="1" />
            <rect opacity="0.5" transform="translate(8.000000, 8.000000) rotate(-270.000000) translate(-8.000000, -8.000000)" x="0" y="7" width="16" height="2" rx="1" />
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

  $(document).on('click', '.show-detail-direct', function ()
	{
		$('#kt_activitiesDirectClient_toggle').trigger('click');
		var id    		=	$(this).data('row-id');
		var url   		=	"{{ route('service_company.clients.dviewmore',':id') }}";
		url     		=	url.replace(':id', id);
		var baseurl    	=	"{{ asset('assets/images/Loading2.gif') }}";
		$.ajax({
						type  		: 'GET',
						url   		: url,
						 beforeSend	: function() {
                                  					$('#kt_activitiesDirectClient_body').html('<img src="'+baseurl+'" height="35px" width="100%">');
                    							},
						success 	: function (response) {
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

                $(this).addClass('disabled');

                fetchCandidates(page, $('#search').val()).then(() => {
                    $('#pagination .page-link').removeClass('disabled');
                }).catch(() => {
                    $('#pagination .page-link').removeClass('disabled');
                });
            });

      });


      function fetchCandidates(page = 1, search = '') {
            var clientid = '1';
            if($('#kt_tagify_2').val()!=''){
                $('.loader_search').css({ display: 'block' });
            }else{
                $('.page-loader').css('display', 'flex');
            }
            $.ajax({
                url: `{!! route('service_company.clients.client_direct_data_ajax') !!}?page=${page}`,
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
                    $('#allClients').html(response.html);
                    $('#direct_client_count').html(response.total);
                    $('#pagination').html(response.pagination);
                }
            });
        }

</script>
@endpush
