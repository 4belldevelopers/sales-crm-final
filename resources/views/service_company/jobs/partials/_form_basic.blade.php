{{-- _form_basic.blade.php — Basic Job Requisition Details Component --}}
{{-- Intent: Modular partial for core job requisition title, category, employment type, positions, salary, and description. --}}
<div class="card card-custom mb-4" id="section-basic-details">
    <div class="card-header">
        <h3 class="card-title">Basic Requisition Details</h3>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <div class="col-md-6 mb-3">
                <label for="title" class="form-label required">Job Title</label>
                <input type="text" name="title" id="title" class="form-control" value="{{ old('title', @$job->title) }}" placeholder="e.g. Senior Full Stack Developer" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="category_id" class="form-label">Job Category / Department</label>
                <select name="category_id" id="category_id" class="form-control select2">
                    <option value="">Select Category</option>
                    @if(isset($categories))
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('category_id', @$job->category_id) == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
        <div class="form-group row">
            <div class="col-md-4 mb-3">
                <label for="employment_type" class="form-label">Employment Type</label>
                <select name="employment_type" id="employment_type" class="form-control select2">
                    <option value="1" {{ (old('employment_type', @$job->employment_type) == 1) ? 'selected' : '' }}>Full-Time</option>
                    <option value="2" {{ (old('employment_type', @$job->employment_type) == 2) ? 'selected' : '' }}>Part-Time</option>
                    <option value="3" {{ (old('employment_type', @$job->employment_type) == 3) ? 'selected' : '' }}>Contract</option>
                    <option value="4" {{ (old('employment_type', @$job->employment_type) == 4) ? 'selected' : '' }}>Internship</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="total_positions" class="form-label">Total Positions / Openings</label>
                <input type="number" name="total_positions" id="total_positions" class="form-control" value="{{ old('total_positions', @$job->total_positions ?? 1) }}" min="1">
            </div>
            <div class="col-md-4 mb-3">
                <label for="work_from_home" class="form-label">Work Mode</label>
                <select name="work_from_home" id="work_from_home" class="form-control select2">
                    <option value="no" {{ (old('work_from_home', @$job->work_type) == 'no') ? 'selected' : '' }}>Work From Office</option>
                    <option value="yes" {{ (old('work_from_home', @$job->work_type) == 'yes') ? 'selected' : '' }}>Remote</option>
                    <option value="other" {{ (old('work_from_home', @$job->work_type) == 'other') ? 'selected' : '' }}>Hybrid</option>
                </select>
            </div>
        </div>
    </div>
</div>
