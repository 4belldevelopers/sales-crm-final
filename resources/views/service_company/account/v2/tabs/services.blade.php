<!--begin::Services V2 Tab Content-->
<div class="card mb-5 mb-xl-10">
    <div class="card-header cursor-pointer">
        <div class="card-title m-0">
            <h3 class="fw-bolder m-0">Commercial & Services Settings</h3>
        </div>
    </div>
    <div class="card-body p-9">
        <div class="row g-6 g-xl-9">

            <!-- Fulltime Hiring Services Card -->
            <div class="col-md-6 col-xl-6">
                <div class="card card-dashed h-xl-100 p-6">
                    <div class="d-flex flex-stack mb-5">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-primary text-primary fw-bolder fs-4">FT</span>
                            </div>
                            <div class="me-2">
                                <h3 class="text-gray-800 text-hover-primary fs-4 fw-bolder mb-0">Fulltime Hiring Services</h3>
                                <span class="text-gray-400 fw-bold fs-7">One Time Placement Fee</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input update_main_status_fulltime" data-row_id="{{ encrypt((@$fullTimeStatus->id)) }}" type="checkbox" value="{{ @$fullTimeStatus->status }}" {{ (@$fullTimeStatus->status) ? 'checked' : '' }} />
                            </label>
                            <span id="f_text" class="ms-2 fw-bolder text-gray-700 fs-7">{{ (@$fullTimeStatus->status) ? 'ON' : 'OFF' }}</span>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-3"></div>

                    <div class="py-2">
                        <div class="d-flex align-items-center py-2 fs-6 class_change_fulltime" style="{{ @$fullTimeStatus->status ? '' : 'opacity: 0.3 !important' }}">
                            <span class="bullet bg-primary me-3"></span>
                            <span class="text-gray-700 fw-bold">One Time Payment for Service</span>
                        </div>
                        <div class="d-flex align-items-center py-2 fs-6 class_change_fulltime" style="{{ @$fullTimeStatus->status ? '' : 'opacity: 0.3 !important' }}">
                            <span class="bullet bg-primary me-3"></span>
                            <span class="text-gray-700 fw-bold">Commercials Configured</span>
                        </div>
                        <div class="d-flex align-items-center py-2 fs-6 class_change_fulltime" style="{{ @$fullTimeStatus->status ? '' : 'opacity: 0.3 !important' }}">
                            <span class="bullet bg-primary me-3"></span>
                            <span class="text-gray-700 fw-bold"><span id="fullTimeCount">{{ @$fullTimeCount }}</span> Countries Selected</span>
                        </div>
                    </div>

                    <div class="d-flex flex-stack pt-4 mt-auto">
                        <span class="badge badge-light-{{ (@$fullTimeStatus->status) ? 'success' : 'danger' }} fw-bolder py-2 px-3">
                            {{ (@$fullTimeStatus->status) ? 'Active' : 'Inactive' }}
                        </span>
                        <a href="javascript:;" class="btn btn-sm btn-light-primary fw-bolder ser_show-detail" data-type="2">
                            <i class="bi bi-pencil-square me-1"></i> Configure Commercials
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contract Hiring Services Card -->
            <div class="col-md-6 col-xl-6">
                <div class="card card-dashed h-xl-100 p-6">
                    <div class="d-flex flex-stack mb-5">
                        <div class="d-flex align-items-center">
                            <div class="symbol symbol-40px me-4">
                                <span class="symbol-label bg-light-info text-info fw-bolder fs-4">CT</span>
                            </div>
                            <div class="me-2">
                                <h3 class="text-gray-800 text-hover-primary fs-4 fw-bolder mb-0">Contract Hiring Services</h3>
                                <span class="text-gray-400 fw-bold fs-7">Hourly / Monthly Staffing</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="form-check form-switch form-check-custom form-check-solid">
                                <input class="form-check-input contract_main_status_fulltime" data-row_id="{{ encrypt((@$contractStatus->id)) }}" type="checkbox" value="{{ @$contractStatus->status }}" {{ (@$contractStatus->status) ? 'checked' : '' }} />
                            </label>
                            <span id="c_text" class="ms-2 fw-bolder text-gray-700 fs-7">{{ (@$contractStatus->status) ? 'ON' : 'OFF' }}</span>
                        </div>
                    </div>

                    <div class="separator separator-dashed my-3"></div>

                    <div class="py-2">
                        <div class="d-flex align-items-center py-2 fs-6 class_change_contract" style="{{ @$contractStatus->status ? '' : 'opacity: 0.3 !important' }}">
                            <span class="bullet bg-info me-3"></span>
                            <span class="text-gray-700 fw-bold">Hourly / Monthly Payment for Services</span>
                        </div>
                        <div class="d-flex align-items-center py-2 fs-6 class_change_contract" style="{{ @$contractStatus->status ? '' : 'opacity: 0.3 !important' }}">
                            <span class="bullet bg-info me-3"></span>
                            <span class="text-gray-700 fw-bold">Commercials Configured</span>
                        </div>
                        <div class="d-flex align-items-center py-2 fs-6 class_change_contract" style="{{ @$contractStatus->status ? '' : 'opacity: 0.3 !important' }}">
                            <span class="bullet bg-info me-3"></span>
                            <span class="text-gray-700 fw-bold"><span id="contractCount">{{ @$contractCount }}</span> Countries Selected</span>
                        </div>
                    </div>

                    <div class="d-flex flex-stack pt-4 mt-auto">
                        <span class="badge badge-light-{{ (@$contractStatus->status) ? 'success' : 'danger' }} fw-bolder py-2 px-3">
                            {{ (@$contractStatus->status) ? 'Active' : 'Inactive' }}
                        </span>
                        <a href="javascript:;" class="btn btn-sm btn-light-info fw-bolder ser_show-detail" data-type="1">
                            <i class="bi bi-pencil-square me-1"></i> Configure Commercials
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!--end::Services V2 Tab Content-->
