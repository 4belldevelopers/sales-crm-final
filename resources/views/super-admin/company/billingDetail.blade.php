<style>
    /* Scoped Super Admin Billing Drawer */
    .sa-billing-drawer {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        color: #181c32;
        padding-bottom: 20px;
    }

    .sa-billing-drawer .form-group {
        margin-bottom: 14px;
    }

    .sa-billing-drawer label {
        font-size: 12.5px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
        display: block;
    }

    .sa-billing-drawer .form-control {
        height: 42px;
        background: #f8fafc;
        border: 1px solid #dce3ee;
        border-radius: 8px;
        font-size: 13px;
        color: #1e293b;
        padding: 8px 12px;
        transition: all 0.15s ease;
    }

    .sa-billing-drawer .form-control:focus {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        outline: none;
    }

    .sa-billing-drawer select.form-control {
        height: 42px;
        line-height: normal;
    }

    /* Pinned Sticky Footer */
    .sa-billing-drawer .sa-drawer-footer {
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;
        background: #ffffff;
        border-top: 1px solid #edf2f7;
        padding: 14px 20px;
        margin-top: 24px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        z-index: 10;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.04);
    }
</style>

<div class="sa-billing-drawer">
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
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="postal_code">Postal Code</label>
                    <input class="form-control form-control-solid" value="{{ $postal_code }}" type="text" id="postal_code"
                        name="postal_code" placeholder="Postal Code">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label for="organization">Organization</label>
                    <input class="form-control form-control-solid" type="text" value="{{ @$company_name }}"
                        id="organization" name="organization" placeholder="Organization" readonly>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="gstin" id="gstin_text">GSTIN</label>
                    <input class="form-control form-control-solid" value="{{ $gstin }}" type="text" id="gstin" name="gstin"
                        placeholder="GSTIN">
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label for="pan_no" id="pan_no_text">PAN No.</label>
                    <input class="form-control form-control-solid" value="{{ $pan_no }}" type="text" id="pan_no" name="pan_no"
                        placeholder="PAN No.">
                </div>
            </div>
        </div>

        <!-- Sticky Drawer Footer -->
        <div class="sa-drawer-footer">
            <button type="button" class="btn btn-light-secondary me-3 px-5 py-2 fw-semibold" data-kt-drawer-dismiss="true">
                Cancel
            </button>
            <button type="submit" id="submitbtn" class="btn btn-primary px-6 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                <span class="indicator-label">
                    <i class="fa-solid fa-floppy-disk fs-6 me-1"></i> Save Address
                </span>
                <span class="indicator-progress">
                    Please wait...
                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                </span>
            </button>
        </div>
    </form>
</div>