<form id="show-billing-form">
    @csrf
    <input class="form-control form-control-solid" value="{{ $company_id }}" type="hidden"
                id="company_id" name="company_id">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="country">Country / Region</label>
                    <select class="form-control form-control-solid" id="country" name="country">

                        @if ($countries)
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->country_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input class="form-control form-control-solid" value="{{ $firstName }}"
                        type="text" id="first_name" name="first_name" placeholder="First Name">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input class="form-control form-control-solid" value="{{ $lastName }}" type="text"
                        id="last_name" name="last_name" placeholder="Last Name">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input class="form-control form-control-solid" value="{{ $phone }}" type="tel" id="phone"
                        name="phone" placeholder="Phone Number">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="building">Building / Society</label>
                    <input class="form-control form-control-solid" value="{{ $building }}" type="text" id="building"
                        name="building" placeholder="Building / Society">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="street">Street Name / Landmark</label>
                    <input class="form-control form-control-solid" value="{{ @$locArr }}"
                        type="text" id="street" name="street"
                        placeholder="Street Name / Landmark">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="state">State</label>
                    <select class="form-control form-control-solid" id="state" name="state_id">
                    </select>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="city">City</label>
                    <select class="form-control form-control-solid" id="city" name="city">
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="postal_code">Postal Code</label>
            <input class="form-control form-control-solid" value="{{ $postal_code }}" type="text" id="postal_code"
                name="postal_code" placeholder="Postal Code">
        </div>
        <div class="form-group">
            <label for="organization">Organization</label>
            <input class="form-control form-control-solid" type="text" value="{{ @$company_name }}"
                id="organization" name="organization" placeholder="Organization" readonly>
        </div>
        <div class="form-group">
            <label for="gstin" id="gstin_text">GSTIN</label>
            <input class="form-control form-control-solid" value="{{ $gstin }}" type="text" id="gstin" name="gstin"
                placeholder="">
        </div>
        <div class="form-group">
            <label for="pan_no" id="pan_no_text">PAN No.</label>
            <input class="form-control form-control-solid" value="{{ $pan_no }}" type="text" id="pan_no" name="pan_no"
                placeholder="">
        </div>
<!-- Buttons -->
    <div class="text-center mt-4">
        <button type="submit" id="submitbtn" class="btn btn-light-primary">
            <span class="indicator-label">Save</span>
            <span class="indicator-progress">
                Please wait...
                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
        </button>
    </div>
</form>