<form id="ftDrawerForm" class="d-flex flex-column justify-content-between h-100">
    @csrf
    <input type="hidden" name="tokenId" id="ftDrawerTokenId" value="{{ $th_features->id ?? 0 }}">

    <!-- Drawer Body with Scrollable Area -->
    <div class="drawer-body-content p-6 flex-grow-1" style="overflow-y: auto;">
        
        <!-- Header Info Card -->
        <div class="p-4 mb-5 rounded-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
            <div class="d-flex align-items-center gap-3">
                <div class="ft-drawer-icon-box" style="width: 40px; height: 40px; border-radius: 9px; background: #eff6ff; border: 1px solid #bfdbfe; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                    <i class="fa fa-sliders fa-solid fa-sliders"></i>
                </div>
                <div>
                    <h5 class="fw-bold text-dark mb-1 fs-6">
                        {{ ($th_features->id ?? 0) > 0 ? 'Edit Feature Policy #' . $th_features->id : 'Create New Feature Policy' }}
                    </h5>
                    <span class="text-muted fs-8">Configure automated timeout thresholds and platform gating rules.</span>
                </div>
            </div>
        </div>

        <!-- Form Group: Policy Title -->
        <div class="mb-5">
            <label class="form-label fw-bold text-dark fs-7 required">Policy Name / Feature Title</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-gray-300 text-muted">
                    <i class="fa fa-pencil fa-solid fa-pen-nib"></i>
                </span>
                <input type="text"
                       name="title"
                       id="ftDrawerTitle"
                       class="form-control form-control-solid border-gray-300"
                       placeholder="e.g. Job Request expiration auto close"
                       value="{{ $th_features->title ?? '' }}"
                       required />
            </div>
            <div class="form-text text-muted fs-8 mt-1">Specify an unambiguous name describing the rule or operational workflow.</div>

            <!-- Suggestion Pills for New Rule -->
            @if (($th_features->id ?? 0) == 0)
                <div class="mt-2 d-flex flex-wrap gap-1">
                    <span class="badge badge-light-primary text-hover-primary cursor-pointer ft-suggest-pill" data-title="Interview Link expiration auto close" data-days="3">
                        + Interview Link Expire (3d)
                    </span>
                    <span class="badge badge-light-primary text-hover-primary cursor-pointer ft-suggest-pill" data-title="Vendor Requisition acknowledgment timeout" data-days="5">
                        + Vendor Ack Timeout (5d)
                    </span>
                    <span class="badge badge-light-primary text-hover-primary cursor-pointer ft-suggest-pill" data-title="Candidate Offer acceptance validity" data-days="14">
                        + Offer Acceptance (14d)
                    </span>
                </div>
            @endif
        </div>

        <!-- Form Group: Validity Duration (Days) -->
        <div class="mb-5">
            <label class="form-label fw-bold text-dark fs-7 required">Validity Window / Expiration Duration (Days)</label>
            <div class="input-group mb-2">
                <span class="input-group-text bg-light border-gray-300 text-muted">
                    <i class="fa fa-history fa-solid fa-clock-rotate-left"></i>
                </span>
                <input type="number"
                       name="days"
                       id="ftDrawerDays"
                       class="form-control form-control-solid border-gray-300 fw-bold fs-6"
                       placeholder="0"
                       min="0"
                       max="3650"
                       value="{{ $th_features->days ?? 0 }}"
                       required />
                <span class="input-group-text bg-light border-gray-300 text-muted fw-semibold fs-7">Days</span>
            </div>

            <!-- Quick Steppers -->
            <div class="d-flex align-items-center gap-1 mb-2 flex-wrap">
                <span class="text-muted fs-8 me-1">Quick Adjust:</span>
                <button type="button" class="btn btn-xs btn-light border py-1 px-2 ft-stepper-btn" data-step="-1">-1 Day</button>
                <button type="button" class="btn btn-xs btn-light border py-1 px-2 ft-stepper-btn" data-step="1">+1 Day</button>
                <button type="button" class="btn btn-xs btn-light border py-1 px-2 ft-stepper-btn" data-step="7">+7 Days</button>
                <button type="button" class="btn btn-xs btn-light border py-1 px-2 ft-stepper-btn" data-step="30">+30 Days</button>
                <button type="button" class="btn btn-xs btn-light-warning border py-1 px-2 ft-stepper-btn" data-set="0">Immediate (0d)</button>
            </div>
            <div class="form-text text-muted fs-8">Set to <code>0</code> for immediate event-driven gating (e.g. coin deductions) with no expiration delay.</div>
        </div>

        <!-- Form Group: Operational Status Switch -->
        <div class="mb-5 p-4 rounded-3" style="background: #ffffff; border: 1px solid #e2e8f0;">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <label class="form-label fw-bold text-dark fs-7 mb-0">Policy Operational Status</label>
                    <div class="text-muted fs-8">Controls whether this rule is actively enforced by platform background workers.</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold fs-7 {{ ($th_features->status ?? 1) == 1 ? 'text-success' : 'text-muted' }}" id="ftDrawerStatusText">
                        {{ ($th_features->status ?? 1) == 1 ? 'Active (ON)' : 'Inactive (OFF)' }}
                    </span>
                    <div class="form-check form-switch form-check-custom form-check-solid">
                        <input class="form-check-input cursor-pointer"
                               type="checkbox"
                               role="switch"
                               id="ftDrawerStatusSwitch"
                               name="status_switch"
                               {{ ($th_features->status ?? 1) == 1 ? 'checked' : '' }} />
                    </div>
                    <input type="hidden" name="status_is" id="ftDrawerStatusIs" value="{{ $th_features->status ?? 1 }}" />
                </div>
            </div>
        </div>

        <!-- Policy Impact Explanation Card -->
        <div class="p-4 rounded-3" id="ftPolicyImpactCard" style="background: #eff6ff; border: 1px solid #bfdbfe;">
            <div class="d-flex align-items-start gap-2">
                <i class="fa fa-info-circle fa-solid fa-circle-info text-primary mt-1 fs-6"></i>
                <div>
                    <span class="fw-bold text-dark fs-7 d-block mb-1">Policy Governance Notice</span>
                    <span class="text-muted fs-8 line-height-sm" id="ftPolicyImpactText">
                        {{ ($th_features->status ?? 1) == 1
                            ? 'When Active: This policy is enforced system-wide across all tenant workspaces. Inactive requests exceeding the duration window will be automatically processed by background crons.'
                            : 'When Inactive: Automated expiration for this rule is paused. Pending workflows remain open indefinitely until manually resolved or re-enabled.'
                        }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Drawer Footer Actions -->
    <div class="drawer-footer-actions p-6 bg-light border-top border-gray-200 d-flex align-items-center justify-content-between">
        <button type="button" class="btn btn-sm btn-light-secondary" id="ftDrawerCancelBtn">
            Cancel
        </button>
        <button type="button" class="btn btn-sm btn-primary fw-bold" id="ftDrawerSaveBtn">
            <span class="indicator-label"><i class="fa fa-check fa-solid fa-check me-1"></i> Save & Apply Policy</span>
            <span class="indicator-progress d-none">
                Saving... <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
            </span>
        </button>
    </div>
</form>
