<!--begin::Services Content-->
<div class="card-header cursor-pointer px-0 border-0 mb-3">
  <div class="card-title m-0">
    <h3 class="fw-bolder m-0 text-dark fs-3">Services</h3>
  </div>
</div>
<div class="card-body px-0 py-0"> 
  <div class="row g-6">

    <!-- Fulltime Hiring Services Card -->
    <div class="col-md-6 col-xl-6 mb-4">
      <div class="card card-dashed h-100 p-6" style="border: 1px dashed #E2E8F0; border-radius: 12px; background: #ffffff;">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div class="d-flex align-items-center">
            <div class="symbol me-4" style="width: 44px; height: 44px; border-radius: 10px; background: #E8F3FF; color: #0085FF; font-weight: 700; font-size: 15px; display: flex; align-items: center; justify-content: center;">
              FT
            </div>
            <div>
              <h4 class="text-dark fw-bolder fs-5 mb-1" style="color: #1E293B !important;">Fulltime Hiring Services</h4>
              <span class="text-muted fw-normal fs-7" style="color: #94A3B8 !important;">One Time Placement Fee</span>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <label class="form-check form-switch form-check-custom form-check-solid me-2">
              <input class="form-check-input update_main_status_fulltime" data-row_id="{{ encrypt((@$fullTimeStatus->id))}}" type="checkbox" value="{{ @$fullTimeStatus->status }}" {{ (@$fullTimeStatus->status) ? 'checked' : '' }} style="width: 40px; height: 22px; cursor: pointer;" />
            </label>
            <span id="f_text" class="fw-bold fs-7 ms-1" style="color: #1E293B;">{{ (@$fullTimeStatus->status) ? 'ON' : 'OFF' }}</span>
          </div>
        </div>

        <!-- Divider -->
        <div class="separator separator-dashed my-4" style="border-top: 1px dashed #E2E8F0;"></div>

        <!-- Details List -->
        <div class="py-2 class_change_fulltime" style="{{ @$fullTimeStatus->status ? '' : 'opacity: 0.4 !important;' }}">
          <div class="d-flex align-items-center mb-3">
            <span style="width: 12px; height: 4px; border-radius: 2px; background: #0085FF; display: inline-block; margin-right: 10px;"></span>
            <span class="fw-semibold fs-6" style="color: #475569;">One Time Payment for Service</span>
          </div>
          <div class="d-flex align-items-center mb-3">
            <span style="width: 12px; height: 4px; border-radius: 2px; background: #0085FF; display: inline-block; margin-right: 10px;"></span>
            <span class="fw-semibold fs-6" style="color: #475569;">Commercials Configured</span>
          </div>
          <div class="d-flex align-items-center mb-2">
            <span style="width: 12px; height: 4px; border-radius: 2px; background: #0085FF; display: inline-block; margin-right: 10px;"></span>
            <span class="fw-semibold fs-6" style="color: #475569;"><span id="fullTimeCount">{{ @$fullTimeCount }}</span> Countries Selected</span>
          </div>
        </div>

        <!-- Footer -->
        <div class="d-flex align-items-center justify-content-between pt-4 mt-auto">
          <span class="badge py-2 px-3 fw-bold fs-7" style="background: #ECFDF5; color: #10B981; border-radius: 6px;">
            {{ (@$fullTimeStatus->status) ? 'Active' : 'Inactive' }}
          </span>
          <a href="javascript:;" class="btn btn-sm fw-bold ser_show-detail" data-type="2" style="background: #EBF5FF; color: #0085FF; border: none; padding: 7px 14px; border-radius: 8px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            View/Edit
          </a>
        </div>
      </div>
    </div>

    <!-- Contract Hiring Services Card -->
    <div class="col-md-6 col-xl-6 mb-4">
      <div class="card card-dashed h-100 p-6" style="border: 1px dashed #E2E8F0; border-radius: 12px; background: #ffffff;">
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div class="d-flex align-items-center">
            <div class="symbol me-4" style="width: 44px; height: 44px; border-radius: 10px; background: #F5EEFF; color: #A855F7; font-weight: 700; font-size: 15px; display: flex; align-items: center; justify-content: center;">
              CT
            </div>
            <div>
              <h4 class="text-dark fw-bolder fs-5 mb-1" style="color: #1E293B !important;">Contract Hiring Services</h4>
              <span class="text-muted fw-normal fs-7" style="color: #94A3B8 !important;">Hourly / Monthly Staffing</span>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <label class="form-check form-switch form-check-custom form-check-solid me-2">
              <input class="form-check-input contract_main_status_fulltime" data-row_id="{{ encrypt((@$contractStatus->id))}}" type="checkbox" value="{{ @$contractStatus->status }}" {{ (@$contractStatus->status) ? 'checked' : '' }} style="width: 40px; height: 22px; cursor: pointer;" />
            </label>
            <span id="c_text" class="fw-bold fs-7 ms-1" style="color: #94A3B8;">{{ (@$contractStatus->status) ? 'ON' : 'OFF' }}</span>
          </div>
        </div>

        <!-- Divider -->
        <div class="separator separator-dashed my-4" style="border-top: 1px dashed #E2E8F0;"></div>

        <!-- Details List -->
        <div class="py-2 class_change_contract" style="{{ @$contractStatus->status ? '' : 'opacity: 0.4 !important;' }}">
          <div class="d-flex align-items-center mb-3">
            <span style="width: 12px; height: 4px; border-radius: 2px; background: {{ @$contractStatus->status ? '#A855F7' : '#CBD5E1' }}; display: inline-block; margin-right: 10px;"></span>
            <span class="fw-semibold fs-6" style="color: {{ @$contractStatus->status ? '#475569' : '#94A3B8' }};">Hourly / Monthly Payment for Services</span>
          </div>
          <div class="d-flex align-items-center mb-3">
            <span style="width: 12px; height: 4px; border-radius: 2px; background: {{ @$contractStatus->status ? '#A855F7' : '#CBD5E1' }}; display: inline-block; margin-right: 10px;"></span>
            <span class="fw-semibold fs-6" style="color: {{ @$contractStatus->status ? '#475569' : '#94A3B8' }};">Commercials Configured</span>
          </div>
          <div class="d-flex align-items-center mb-2">
            <span style="width: 12px; height: 4px; border-radius: 2px; background: {{ @$contractStatus->status ? '#A855F7' : '#CBD5E1' }}; display: inline-block; margin-right: 10px;"></span>
            <span class="fw-semibold fs-6" style="color: {{ @$contractStatus->status ? '#475569' : '#94A3B8' }};"><span id="contractCount">{{ @$contractCount }}</span> Countries Selected</span>
          </div>
        </div>

        <!-- Footer -->
        <div class="d-flex align-items-center justify-content-between pt-4 mt-auto">
          <span class="badge py-2 px-3 fw-bold fs-7" style="background: {{ (@$contractStatus->status) ? '#ECFDF5' : '#FEF2F2' }}; color: {{ (@$contractStatus->status) ? '#10B981' : '#EF4444' }}; border-radius: 6px;">
            {{ (@$contractStatus->status) ? 'Active' : 'Inactive' }}
          </span>
          <a href="javascript:;" class="btn btn-sm fw-bold ser_show-detail" data-type="1" style="background: #FAF5FF; color: #A855F7; border: none; padding: 7px 14px; border-radius: 8px; font-size: 13px; display: inline-flex; align-items: center; gap: 6px;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
            View/Edit
          </a>
        </div>
      </div>
    </div>

  </div>
</div>
<!--end::Services Content-->
