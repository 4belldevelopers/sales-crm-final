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
<div class="container mt-5">
    <!-- Ticket Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary">{{ $ticket->title }} ({{ $ticket->ticket_no }})</h1>
        <p class="card-text mb-0">Status : <span class="badge @if($ticket->ticketstatus->name === 'Open') badge-success 
                            @elseif($ticket->ticketstatus->name === 'In Progress') badge-warning 
                            @elseif($ticket->ticketstatus->name === 'Closed') badge-danger 
                            @else badge-light 
                            @endif p-2">
                           {{ $ticket->ticketstatus->name }}
        </span></p>
    </div>

    <!-- Ticket Details -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Created by:</strong> {{ $ticket->user->name ?? 'Unknown' }}</p>
            <p><strong>Description:</strong></p>
            <p>{{ $ticket->description }}</p>
        </div>
    </div>

    <!-- Comments Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white" style="padding: 24px 0px 0px 25px;">
            <h3 class="mb-0">Comments</h3>
        </div>
        <div class="card-body">
            @if ($ticket->comments->isEmpty())
                <p class="text-muted">No comments yet.</p>
            @else
                @foreach ($ticket->comments as $comment)
                    <div class="media border-bottom pb-3 mb-3">
                        <div class="media-body">
                            <h5 class="mt-0 font-weight-bold">{{ $comment->user->name ?? 'Anonymous' }}
                                <small class="text-muted">on {{ $comment->created_at->format('F j, Y, g:i a') }}</small>
                            </h5>
                            <p>{{ $comment->content }}</p>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Add Comment Form -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white" style="padding: 24px 0px 0px 25px;">
            <h4 class="mb-0">Add a Comment</h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('superadmin.comments.store') }}">
                @csrf
                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                <div class="form-group">
                    <label class="form-label">Select Status:</label>
                  <select name="status_is" id="status_is" class="form-select form-select-solid" aria-label="Select status_is">
                    @if($ticket_status)
                    @foreach($ticket_status as $val)
                    <option value="{{ $val->id }}" {{ ($ticket->status_id==$val->id) ? 'selected' : '' }}>{{ $val->name }}</option>
                    @endforeach
                    @endif  				
                  </select>
                </div><br>
                <div class="form-group">
                    <label class="form-label required">Comments:</label>
                    <textarea name="content" class="form-control" rows="3" placeholder="Write your comment here..." required></textarea>
                </div>
                <div class="d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-comment-dots"></i> Submit Comment
                    </button>
                </div>
            </form>
        </div>
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


