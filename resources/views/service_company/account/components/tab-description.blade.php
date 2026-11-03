{{--
  God-Level Company Profile — Step 2: Company Description & AI Assist
  100% Replicates Add Client Step 2 Description Design System (Rule 20 Design Standard)
  Quill Rich-Text Editor with 2,000 character limit, collapsible tips, and AI Company Assist
  Optimized for 15.6" Screens (1366x768 & 1920x1080)
--}}
@php
    $initialDesc = $account->description ?? ($client->description ?? '');
    $initialDescCount = mb_strlen(strip_tags((string)$initialDesc));

    $compName = $account->company_name ?? ($client->name ?? 'Your Organization');
    $compInd = ($selectedIndName ?? '') ?: 'Technology & Professional Services';
    $defaultAiDesc = $compName . ' is an innovative enterprise specializing in ' . $compInd . ', delivering high-value solutions, exceptional talent operations, and specialized industry services across global markets.';
@endphp

<div class="pb-5 client-desc-step profile-desc-step" data-wizard-type="step-content">
  <div class="client-step-layout client-description-layout">
    
    <!-- ==========================================
         MAIN COLUMN: About the Company Editor
         ========================================== -->
    <div class="client-main-column">
      <section class="card client-section-card client-desc-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        
        <!-- Header bar matching reference design -->
        <div class="card-header border-0 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:12px 18px !important; justify-content:flex-start !important;">
          <div class="d-flex align-items-center" style="gap:12px; text-align:left !important;">
            <div style="width:36px; height:36px; border-radius:9px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <line x1="10" y1="9" x2="8" y2="9"></line>
              </svg>
            </div>
            <div style="text-align:left !important;">
              <h3 class="client-section-heading" style="font-size:14px; font-weight:700; color:#101828; margin:0 0 2px 0; text-align:left !important;">About the Company</h3>
              <p class="client-section-copy" style="font-size:11px; color:#64748b; margin:0; text-align:left !important;">Give recruiters, candidates, and partners a clear understanding of what your company does.</p>
            </div>
          </div>
        </div>

        <div class="card-body" style="padding:14px 18px !important;">
          <div class="client-editor-wrap">
            <div class="d-flex align-items-center justify-content-between mb-1 client-desc-label-row">
              <label class="form-label mb-0" style="font-size:12px; font-weight:700; color:#0f172a;">Company Description <span class="text-danger">*</span></label>
            </div>
            <p style="font-size:11.5px; color:#64748b; margin:0 0 8px 0;">Briefly describe your company's core mission, business offerings, industry domain, and key organizational strengths.</p>

            <div class="client-editor-box" id="profile_editor_box" style="border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; background:#ffffff;">
              <div id="kt_company_description_quill" style="min-height:160px; font-size:12.5px; color:#1e293b;">{!! $initialDesc !!}</div>
            </div>
            <!-- Synchronized hidden textarea for standard form POST compatibility -->
            <textarea id="kt_docs_tinymce_hidden" name="description" style="display:none !important;" aria-hidden="true">{{ $initialDesc }}</textarea>
            
            <div class="d-flex align-items-center justify-content-between mt-1">
              <div class="text-danger" id="profile_desc_error" style="display:none; font-size:11.5px; font-weight:500;">
                <span>Please enter a description for your organization (minimum 20 characters).</span>
              </div>
              <span class="client-char-count ms-auto" style="font-size:11px; font-weight:500; color:#94a3b8;"><span id="profile_desc_count">{{ $initialDescCount }}</span> / 2,000</span>
            </div>
          </div>

          <!-- Tips banner at bottom matching reference design (Collapsible) -->
          <div class="client-info-banner client-tips-accordion" id="profile_tips_banner" style="background:#f0f7ff; border:1px solid #dbeafe; border-radius:10px; padding:12px 16px !important; margin-top:14px !important; margin-bottom:0 !important;">
            <div class="d-flex align-items-center justify-content-between cursor-pointer" id="profile_tips_toggle" style="cursor:pointer; user-select:none;" onclick="var c=document.getElementById('profile_tips_content'), ch=document.getElementById('profile_tips_chevron'); if(c.style.display==='none'){c.style.display='block'; ch.style.transform='rotate(0deg)';}else{c.style.display='none'; ch.style.transform='rotate(180deg)';}">
              <div class="d-flex align-items-center gap-2" style="font-size:12px; font-weight:600; color:#1d4ed8;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                  <path d="M9 18h6"></path>
                  <path d="M10 22h4"></path>
                  <path d="M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26c1.81-1.27 3-3.36 3-5.74a7 7 0 0 0-7-7z"></path>
                </svg>
                <span>Tips for an exceptional corporate profile</span>
              </div>
              <span id="profile_tips_chevron" style="color:#2563eb; font-size:12px; font-weight:700; transition:transform 0.2s ease;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
              </span>
            </div>
            <div id="profile_tips_content" style="margin-top:10px;">
              <ul style="list-style:none; padding-left:0; margin:0; font-size:11.5px; color:#334155; line-height:1.7;">
                <li class="d-flex align-items-center gap-2 mb-1">
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981" class="flex-shrink-0"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                  <span>Highlight your core business model, key products, and primary client markets.</span>
                </li>
                <li class="d-flex align-items-center gap-2 mb-1">
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981" class="flex-shrink-0"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                  <span>Mention your engineering culture, team scale, and major achievements.</span>
                </li>
                <li class="d-flex align-items-center gap-2">
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981" class="flex-shrink-0"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                  <span>This description is displayed on career portals and talent marketplace briefs.</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- ==========================================
         RIGHT SIDEBAR: AI Assist & Information
         ========================================== -->
    <aside class="client-side-column">
      
      <!-- Card: AI-Generated Company Description -->
      <div class="card client-helper-card client-ai-assist-card" style="background:#faf8ff; border:1px solid #ede9fe; border-radius:12px; padding:16px 18px !important; margin-bottom:12px !important;">
        <div class="d-flex align-items-center justify-content-between mb-1">
          <div class="d-flex align-items-center gap-2">
            <div style="width:28px; height:28px; border-radius:7px; background:#ede9fe; display:inline-flex; align-items:center; justify-content:center; color:#7040f5; flex-shrink:0;">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7040f5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
              </svg>
            </div>
            <h4 style="font-size:13.5px; font-weight:700; color:#101828; margin:0; letter-spacing:-0.2px;">AI Company Assist</h4>
          </div>
          <span class="badge" style="background:#ede9fe; color:#7040f5; font-weight:600; font-size:10px; padding:2px 7px; border-radius:6px;">Smart AI</span>
        </div>
        <p style="font-size:11.5px; color:#64748b; line-height:1.45; margin-bottom:10px;">AI-synthesized company summary based on your industry and domain intelligence.</p>

        <!-- Description Box -->
        <div id="profile_ai_suggested_desc" class="client-ai-suggested-desc" style="background:#ffffff; border:1px solid #f1f5f9; border-radius:8px; padding:12px 14px !important; font-size:12.5px; color:#334155; line-height:1.6; margin-bottom:12px !important; max-height:120px; overflow-y:auto;">{{ $defaultAiDesc }}</div>

        <!-- Footer Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2" style="font-size:10.5px; color:#64748b;">
            <span class="d-inline-flex align-items-center gap-1" style="color:#475569; font-weight:500;">
              <span style="width:6px; height:6px; border-radius:50%; background:#10b981; display:inline-block;"></span> Ready
            </span>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm" id="profile_review_edit_desc_btn" style="background:#ffffff; border:1px solid #c4b5fd; color:#6d28d9; border-radius:7px; padding:5px 12px; font-size:11.5px; font-weight:600; cursor:pointer;" onclick="if(window.quillCompanyEditor) window.quillCompanyEditor.focus();">
              Review & Edit
            </button>
            <button type="button" class="btn btn-sm" id="profile_apply_desc_suggestion" onclick="window.applyDescriptionSuggestion ? window.applyDescriptionSuggestion() : false;" style="background:#7040f5; border:none; color:#ffffff; font-weight:600; font-size:11.5px; border-radius:7px; padding:6px 14px; display:inline-flex; align-items:center; gap:5px; box-shadow:0 1px 3px rgba(112,64,245,0.3); cursor:pointer;">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
              <span>Apply Suggestion</span>
            </button>
          </div>
        </div>
      </div>

      <!-- Quick Specs Card -->
      <div class="card client-helper-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px 18px !important;">
        <h4 style="font-size:12.5px; font-weight:700; color:#0f172a; margin:0 0 10px; display:flex; align-items:center; gap:8px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          <span>Search & Marketplace Reach</span>
        </h4>
        <p style="font-size:11px; color:#64748b; line-height:1.55; margin:0;">
          A rich description enhances your company's visibility on candidate portals, talent trade exchanges, and automated agency matching algorithms.
        </p>
      </div>

    </aside>

  </div>
</div>
