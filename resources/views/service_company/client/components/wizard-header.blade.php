{{-- 
  Unified Header & Wizard Stepper for Add / Edit Client Wizard
  Strictly isolated to Client Wizard (Service Company & Admin)
  Pixel-consistent baseline: Step 2 Reference (media_1792065531568.png / media_1792068852883.png)
--}}
<div class="card client-top-nav-card" id="client_top_nav_card">
  <div class="client-header-grid">
    
    <!-- LEFT: Back Button + Title Block -->
    <div class="client-header-identity">
      <button type="button" class="btn client-header-back-btn client-nav-back" id="client_header_back_btn" aria-label="Go Back">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0a1647" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 12H5"/>
          <path d="m12 19-7-7 7-7"/>
        </svg>
      </button>
      <div class="client-header-titles">
        <h2 class="client-header-title" id="client_header_title">{{ $page_event === 'EDIT' ? 'Edit Client' : 'Add Internal Client' }}</h2>
        <p class="client-header-subtitle" id="client_header_subtitle" title="Create and configure a client profile for jobs, contacts and recruitment operations.">Create and configure a client profile for jobs, contacts and recruitment operations.</p>
      </div>
    </div>

    <!-- CENTER: 5-Step Horizontal Stepper -->
    <div class="client-header-stepper-col">
      <div class="client-stepper-wrap" id="client_wizard_stepper" role="tablist">
        
        <!-- Step 1 -->
        <div class="client-step-item" data-step="1" role="tab" aria-current="step" aria-label="Step 1: Client Information">
          <div class="client-step-circle client-step-circle-1 is-active">1</div>
          <div class="client-step-text">
            <div class="client-step-name">Client Information</div>
            <div class="client-step-sub">Basic details</div>
          </div>
        </div>

        <div class="client-step-line" aria-hidden="true"></div>

        <!-- Step 2 -->
        <div class="client-step-item" data-step="2" role="tab" aria-label="Step 2: Client Description">
          <div class="client-step-circle client-step-circle-2 is-upcoming">2</div>
          <div class="client-step-text">
            <div class="client-step-name">Client Description</div>
            <div class="client-step-sub">About the client</div>
          </div>
        </div>

        <div class="client-step-line" aria-hidden="true"></div>

        <!-- Step 3 -->
        <div class="client-step-item" data-step="3" role="tab" aria-label="Step 3: Address Details">
          <div class="client-step-circle client-step-circle-3 is-upcoming">3</div>
          <div class="client-step-text">
            <div class="client-step-name">Address Details</div>
            <div class="client-step-sub">Location & address</div>
          </div>
        </div>

        <div class="client-step-line" aria-hidden="true"></div>

        <!-- Step 4 -->
        <div class="client-step-item" data-step="4" role="tab" aria-label="Step 4: Contact Person">
          <div class="client-step-circle client-step-circle-4 is-upcoming">4</div>
          <div class="client-step-text">
            <div class="client-step-name">Contact Person</div>
            <div class="client-step-sub">Key contacts</div>
          </div>
        </div>

        <div class="client-step-line" aria-hidden="true"></div>

        <!-- Step 5 -->
        <div class="client-step-item" data-step="5" role="tab" aria-label="Step 5: Review & Submit">
          <div class="client-step-circle client-step-circle-5 is-upcoming">5</div>
          <div class="client-step-text">
            <div class="client-step-name">Review & Submit</div>
            <div class="client-step-sub">Confirm & save</div>
          </div>
        </div>

      </div>
    </div>

    <!-- RIGHT: Contextual Action Buttons -->
    <div class="client-header-actions" id="client_header_actions">
      <!-- Steps 1-4: Next Page Button -->
      <button type="button" class="btn client-top-next-btn client-nav-next" id="client_top_next_btn">
        <span>Next Page</span>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#0a1647" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 12h14"/>
          <path d="m12 5 7 7-7 7"/>
        </svg>
      </button>


      <!-- Step 5: Submit / Update Button -->
      @if ($page_event == 'EDIT')
        <button type="button" class="btn client-top-submit-btn updatefrm" id="submitBtn" style="display:none !important;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <span>Update Client</span>
        </button>
      @else
        <button type="button" class="btn client-top-submit-btn submitfrm" id="submitBtn" style="display:none !important;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <span>Submit Client</span>
        </button>
      @endif
    </div>

  </div>
</div>
