@extends('layouts.superadmin_layout')
@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
@endpush
@section('content')

<!--begin::Content-->
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <!--begin::Post-->
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <!--begin::Container-->
    <div id="kt_content_container" class="container-fluid px-3">
      <!--begin::Card-->
      <div class="card">
        <!--begin::Card body-->
        <div class="card-body pt-3 px-5">
            <div class="container mt-5">
                <h1 class="text-primary mb-4">Create Ticket</h1>
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('superadmin.tickets.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="title" class="font-weight-bold">Ticket No.</label>
                        <input type="text" id="ticketIdCheck" name="ticketIdCheck" value="{{ $formattedNumber }}" class="form-control" required readonly style="cursor: not-allowed;">
                        <input type="hidden" id="ticketId" name="ticketId" value="{{ encrypt($number_token) }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="title" class="font-weight-bold">Title.</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description" class="font-weight-bold">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="5" required>{{ old('description') }}</textarea>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        <button type="submit" class="btn btn-success mt-3">
                            <i class="fas fa-paper-plane"></i> Submit Ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!--end::Card body-->
      </div>
      <!--end::Card-->
    </div>
    <!--end::Container-->
  </div>
  <!--end::Post-->
</div>
<!--end::Content-->


@endsection

@push('footer-script') 
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>

@endpush

