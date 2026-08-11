@extends('layouts.service_company_layout')
@push('head-script')
<link href="{{ asset('demo/css/filterStyle.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/topCard.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('demo/css/clientList.css') }}{{ TM_VERSION }}" rel="stylesheet" type="text/css" />
@endpush
@section('content')

<div class="content d-flex flex-column flex-column-fluid pt-4 kt_content_client" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">

        <!-- HERO -->
        @include('sections.service_company.clientHero', ['activeTab' => 'request'])

        <div class="d-flex align-items-center flex-wrap custm-bg mb-0 d-none">
                  <div class="col-lg-4 d-flex align-items-center px-2" style="width:55%">
                     <input type="text" class="form-control form-control-solid ps-3" name="inputJobSearch" value="" placeholder="Search..." id="kt_tagify_2" onchange="filtersdataget()" />
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="sortingfilter" data-dropdown-css-class="w-150px" data-placeholder="Select an option" style="border: 1px solid #ffffff;" onchange="filtersdataget()">
                        <option value="Show All" selected="">Sort</option>
                        <option value="1">Newest to Oldest (Upload Date)</option>
                        <option value="2">Oldest to Newest (Upload Date)</option>
                     </select>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                     <select class="form-select form-select-lg form-select-solid" data-hide-search="true" id="country" data-dropdown-css-class="w-150px" data-placeholder="Select an option" data-kt-table-widget-3="filter_status" style="border: 1px solid #ffffff;" onchange="filtersdata()">
                        <option value="110" selected="selected">Location</option>
                     </select>
                  </div>
                  <div class="col-lg-2 d-flex align-items-center px-2" style="width: 15%;">
                     <button type="button" class="btn btn-sm btn-flex btn-light btn-active-primary fw-bolder w-100 py-4" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                        <span class="svg-icon svg-icon-2">
                           <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                              <path d="M19.0759 3H4.72777C3.95892 3 3.47768 3.83148 3.86067 4.49814L8.56967 12.6949C9.17923 13.7559 9.5 14.9582 9.5 16.1819V19.5072C9.5 20.2189 10.2223 20.7028 10.8805 20.432L13.8805 19.1977C14.2553 19.0435 14.5 18.6783 14.5 18.273V13.8372C14.5 12.8089 14.8171 11.8056 15.408 10.964L19.8943 4.57465C20.3596 3.912 19.8856 3 19.0759 3Z" fill="black"></path>
                           </svg>
                        </span>
                        Filter
                     </button>
                  </div>
        </div>
        
        <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
      <table id="myTabled" class="table align-middle table-row-dashed fs-6 gy-5">
       <thead>
              <tr class="fw-bolder text-muted">
                <th class="">Name</th>
                <th class="">Location</th>
                <th class="">Request Date</th>
                <th class="">Action</th>
              </tr>
        </thead>

            <tbody class="text-gray-600 fw-bold" id="requestJobLists">
            </tbody>

      </table>
	   <div id="pagination"></div>
    </div>
  </div>
   </div>
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
<script>
let searchTimer = null;

$(document).ready(function() {
    fetchCandidates(1);

    $('#inputSearch, #kt_tagify_2').on('keyup input', function() {
        clearTimeout(searchTimer);
        const searchVal = $(this).val();
        searchTimer = setTimeout(function() {
            fetchCandidates(1, searchVal);
        }, 300);
    });

    $(document).on('click', '#pagination .page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (!page || $(this).hasClass('disabled')) return;
        $(this).addClass('disabled');
        fetchCandidates(page, $('#inputSearch').val());
    });
});

function fetchCandidates(page = 1, search = '') {
    search = search || $('#inputSearch').val() || $('#kt_tagify_2').val() || '';
    $('.loadingMessage').css({ display: 'block' });
    return $.ajax({
        url: `{!! route('service_company.clients.client_request_data') !!}`,
        type: 'GET',
        data: {
            page: page,
            q: search,
            search: search
        },
        success: function(response) {
            $('.loadingMessage').css({ display: 'none' });
            $('#requestJobLists').html(response.html);
            $('#pagination').html(response.pagination);
        },
        error: function() {
            $('.loadingMessage').css({ display: 'none' });
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

$(document).on('click', '.show-detail-direct', function () {
    $('#kt_activitiesDirectClient_toggle').trigger('click');
    var id = $(this).data('row-id');
    var url = "{{ route('service_company.clients.requestviewmore', ':id') }}".replace(':id', id);
    var baseurl = "{{ asset('assets/images/Loading2.gif') }}";
    $.ajax({
        type: 'GET',
        url: url,
        beforeSend: function() {
            $('#kt_activitiesDirectClient_body').html('<div class="p-5 text-center"><img src="' + baseurl + '" height="35px"></div>');
        },
        success: function (response) {
            $('#kt_activitiesDirectClient_body').html(response.view);
        }
    });
});
</script>
@endpush 