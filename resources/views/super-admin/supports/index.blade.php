@extends('layouts.superadmin_layout')

@push('head-script')
<link rel="stylesheet" href="//cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/js/all.min.js" type="text/javascript"></script>
@endpush

@section('content')
<div class="content d-flex flex-column flex-column-fluid pt-4" id="kt_content">
  <div class="post d-flex flex-column-fluid" id="kt_post">
    <div id="kt_content_container" class="container-fluid px-3">
      <div class="card">
        <div class="container mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-primary">Tickets</h1>
                {{-- <a href="{{ route('superadmin.tickets.create') }}" class="btn btn-success">
                    <i class="fas fa-plus"></i> Create Ticket
                </a> --}}
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route('superadmin.tickets.ticketList') }}" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search by Title or Ticket No" value="{{ request('search') }}">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>

            @if($tickets->count())
                <div class="row">
                    @foreach($tickets as $ticket)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="card-title">
                                            <a href="{{ route('superadmin.tickets.show', encrypt($ticket->id)) }}" class="text-dark">
                                                {{ $ticket->title }} ({{ $ticket->ticket_no }})
                                            </a>
                                        </h5>
                                        <p class="card-text mb-0">Status:
                                            <span class="badge @if($ticket->ticketstatus->name === 'Open') badge-success 
                                                        @elseif($ticket->ticketstatus->name === 'In Progress') badge-warning 
                                                        @elseif($ticket->ticketstatus->name === 'Closed') badge-danger 
                                                        @else badge-light @endif">{{ $ticket->ticketstatus->name }}
                                            </span>
                                        </p>
                                    </div>
                                        <p class="card-text text-muted mb-0">Company: {{ $ticket->company->company_name }}</p>
                                       
                                    
                                    <a href="{{ route('superadmin.tickets.show', encrypt($ticket->id)) }}" class="btn btn-outline-primary btn-sm mt-2">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-center mt-4">
                    {{ $tickets->appends(['search' => request('search')])->links() }}
                </div>
            @else
                <div class="alert alert-warning text-center">
                    No tickets found.
                </div>
            @endif
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('footer-script')
<script src="{{ asset('demo/cdn/js/dataTables.fixedHeader.min.js') }}<?php echo TM_VERSION; ?>"></script>
@endpush
