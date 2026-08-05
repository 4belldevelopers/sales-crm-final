<div class="modal-header">
    <h5 class="modal-title" style="font-family:'Poppins',sans-serif;font-size:14px;">
        {{ __('Update Stage') }}
        @if(!empty($childStatus))
            &mdash; <span class="text-primary">{{ ucfirst($childStatus->name) }}</span>
        @endif
    </h5>
</div>
<div class="modal-body" style="font-family:'Poppins',sans-serif;font-size:13px;">
    @if(!empty($application))
    <div class="mb-3">
        <strong>{{ $application->full_name ?? '' }}</strong><br>
        <span class="text-muted">{{ $application->job_title ?? '' }}</span>
        @if(!empty($application->client_name))
            &mdash; {{ $application->client_name }}
        @endif
    </div>
    @endif

    <form id="rejectionFrmSubmit"
          action="{{ route('apptracker.sub_stage') }}"
          method="GET">
        @csrf
        <input type="hidden" name="application_id"  value="{{ $application_id }}">
        <input type="hidden" name="job_id"           value="{{ $job_id }}">
        <input type="hidden" name="status"           value="{{ $current_child_id }}">
        <input type="hidden" name="parent_status"    value="{{ $parent_status_id }}">

        @if(!empty($interview_node) && is_array($interview_node))
        <div class="form-group mb-3">
            <label style="font-size:13px;">{{ __('Interview Mode') }}</label>
            <select name="interview_mode" class="form-control form-control-sm" style="font-size:13px;">
                <option value="">{{ __('Select') }}</option>
                @foreach($interview_node as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="form-group mb-3">
            <label style="font-size:13px;">{{ __('Remarks / Reason') }}</label>
            <textarea name="reason" rows="3"
                      class="form-control form-control-sm"
                      style="font-size:13px;"
                      placeholder="{{ __('Optional — add a note about this stage change') }}"></textarea>
        </div>
    </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal"
            style="font-size:13px;">
        {{ __('Cancel') }}
    </button>
    <button type="button"
            class="btn btn-sm btn-primary"
            style="font-size:13px;"
            onclick="document.getElementById('rejectionFrmSubmit').submit();">
        {{ __('Save') }}
    </button>
</div>
