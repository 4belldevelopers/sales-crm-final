{{-- _form_team.blade.php — Client & Team Assignment Component --}}
{{-- Intent: Modular partial for client company, recruiters, and hiring managers assignment. --}}
<div class="card card-custom mb-4" id="section-team-assignment">
    <div class="card-header">
        <h3 class="card-title">Client & Team Assignment</h3>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <div class="col-md-4 mb-3">
                <label for="client_id" class="form-label required">Client Company</label>
                <select name="client_id" id="client_id" class="form-control select2" required>
                    <option value="">Select Client</option>
                    @if(isset($clients))
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ (old('client_id', @$job->client_id) == $client->id) ? 'selected' : '' }}>{{ $client->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="recruiter" class="form-label">Assigned Recruiters</label>
                <input type="text" name="recruiter" id="recruiter" class="form-control" value="{{ old('recruiter', @$recruiterdata) }}" placeholder="Select recruiters...">
                <input type="hidden" name="recruiter_id" id="recruiter_id" value="{{ old('recruiter_id', @$recruiter_id) }}">
            </div>
            <div class="col-md-4 mb-3">
                <label for="hiring_namager" class="form-label">Hiring Managers</label>
                <input type="text" name="hiring_namager" id="hiring_namager" class="form-control" value="{{ old('hiring_namager', @$hiring_namagerdata) }}" placeholder="Select hiring managers...">
            </div>
        </div>
    </div>
</div>
