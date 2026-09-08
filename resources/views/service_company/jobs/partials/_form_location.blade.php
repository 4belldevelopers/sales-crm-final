{{-- _form_location.blade.php — Location & Work City Component --}}
{{-- Intent: Modular partial for client address, work city tagify selection, and country restrictions. --}}
<div class="card card-custom mb-4" id="section-location-details">
    <div class="card-header">
        <h3 class="card-title">Work Location & Country Settings</h3>
    </div>
    <div class="card-body">
        <div class="form-group row">
            <div class="col-md-6 mb-3">
                <label for="kt_tagify_work_location" class="form-label">Work City Locations</label>
                <input type="text" name="kt_tagify_work_location" id="kt_tagify_work_location" class="form-control tagify-input" value="{{ old('kt_tagify_work_location', @$default_loc) }}" placeholder="Search and add cities...">
            </div>
            <div class="col-md-6 mb-3">
                <label for="address_id" class="form-label">Client Office Address</label>
                <select name="address_id" id="address_id" class="form-control select2">
                    <option value="">Select Address</option>
                    @if(isset($client_locations))
                        @foreach($client_locations as $loc)
                            <option value="{{ $loc->id }}">{{ $loc->address }} - {{ $loc->city }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
        </div>
    </div>
</div>
