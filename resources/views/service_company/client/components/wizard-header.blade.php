{{-- 
  Unified Header & 5-Step Stepper Navigation for Add / Edit Client Workflow
  Pixel-matched to user specification (Reference: media_1792403786709_d614e0b3.png)
  Strictly isolated to Client Wizard (Service Company & Admin)
  Optimized for 15.6" Laptops (1366x768 & 1920x1080) and Smaller Screens
--}}
<div class="card client-top-nav-card" id="client_top_nav_card">
  <div class="client-header-grid">
    
    <!-- LEFT: Title Block -->
    <div class="client-header-identity">
      <div class="client-header-titles">
        <h2 class="client-header-title" id="client_header_title">{{ $page_event === 'EDIT' ? 'Edit Client' : 'Add Internal Client' }}</h2>
        <p class="client-header-subtitle" id="client_header_subtitle" title="Create a client profile for recruitment operations">Create a client profile for recruitment operations</p>
      </div>
    </div>

    <!-- CENTER: Stepper (1 Company → 2 Description → 3 Address → 4 Contacts → 5 Review) -->
    <div class="client-header-stepper-col">
      <div class="client-stepper-wrap" id="client_wizard_stepper" role="tablist">
        
        <!-- Step 1: Company -->
        <div class="client-step-item is-active" data-step="1" role="tab" aria-current="step" aria-label="Step 1: Company">
          <div class="client-step-circle client-step-circle-1 is-active">1</div>
          <span class="client-step-name">Company</span>
        </div>

        <div class="client-step-arrow" data-arrow-after="1" aria-hidden="true">
          <svg class="client-step-arrow-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"/>
            <path d="m12 5 7 7-7 7"/>
          </svg>
        </div>

        <!-- Step 2: Description -->
        <div class="client-step-item" data-step="2" role="tab" aria-label="Step 2: Description">
          <div class="client-step-circle client-step-circle-2 is-upcoming">2</div>
          <span class="client-step-name">Description</span>
        </div>

        <div class="client-step-arrow" data-arrow-after="2" aria-hidden="true">
          <svg class="client-step-arrow-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"/>
            <path d="m12 5 7 7-7 7"/>
          </svg>
        </div>

        <!-- Step 3: Address -->
        <div class="client-step-item" data-step="3" role="tab" aria-label="Step 3: Address">
          <div class="client-step-circle client-step-circle-3 is-upcoming">3</div>
          <span class="client-step-name">Address</span>
        </div>

        <div class="client-step-arrow" data-arrow-after="3" aria-hidden="true">
          <svg class="client-step-arrow-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"/>
            <path d="m12 5 7 7-7 7"/>
          </svg>
        </div>

        <!-- Step 4: Contacts -->
        <div class="client-step-item" data-step="4" role="tab" aria-label="Step 4: Contacts">
          <div class="client-step-circle client-step-circle-4 is-upcoming">4</div>
          <span class="client-step-name">Contacts</span>
        </div>

        <div class="client-step-arrow" data-arrow-after="4" aria-hidden="true">
          <svg class="client-step-arrow-svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M5 12h14"/>
            <path d="m12 5 7 7-7 7"/>
          </svg>
        </div>

        <!-- Step 5: Review -->
        <div class="client-step-item" data-step="5" role="tab" aria-label="Step 5: Review">
          <div class="client-step-circle client-step-circle-5 is-upcoming">5</div>
          <span class="client-step-name">Review</span>
        </div>

      </div>
    </div>

    <!-- RIGHT: Unified Contextual Action Navigation Area -->
    <div class="client-header-actions" id="client_header_actions">
      <!-- Step 1: Close (✕) Button (Pixel-perfect match to screenshot) -->
      <button type="button" class="btn client-top-close-btn client-nav-close" id="client_top_close_btn" title="Close and exit">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <line x1="18" y1="6" x2="6" y2="18"></line>
          <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
        <span>Close</span>
      </button>

      <!-- Steps 2-5: Back Button (Returns to previous step) -->
      <button type="button" class="btn client-top-back-btn client-nav-back" id="client_top_back_btn" title="Back to previous step" style="display:none !important;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M19 12H5"/>
          <path d="m12 19-7-7 7-7"/>
        </svg>
        <span>Back</span>
      </button>

      <!-- Steps 1-4: Next Page Button -->
      <button type="button" class="btn client-top-next-btn client-nav-next" id="client_top_next_btn">
        <span>Next Page</span>
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M5 12h14"/>
          <path d="m12 5 7 7-7 7"/>
        </svg>
      </button>

      <!-- Step 5: Final Submit / Update Button -->
      @if ($page_event == 'EDIT')
        <button type="button" class="btn client-top-submit-btn updatefrm" id="submitBtn" style="display:none !important;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <span>Update Client</span>
        </button>
      @else
        <button type="button" class="btn client-top-submit-btn submitfrm" id="submitBtn" style="display:none !important;">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
          <span>Create Client</span>
        </button>
      @endif
    </div>

  </div>
</div>
