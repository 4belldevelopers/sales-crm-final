<!--begin::Client Description (God-Level Design Aligned with Reference Design System)-->
@php
    $initialDesc = (($page_event ?? '') === 'ADD') ? '' : (!empty($description) ? $description : '');
    $initialDescCount = mb_strlen(strip_tags($initialDesc));

    $parsedTags = [];
    if (($page_event ?? '') !== 'ADD' && !empty($tags)) {
        $decoded = json_decode($tags, true);
        if (is_array($decoded)) {
            foreach ($decoded as $item) {
                $parsedTags[] = is_array($item) ? ($item['value'] ?? '') : $item;
            }
        } else {
            $parsedTags = array_map('trim', explode(',', $tags));
        }
    }
    $parsedTags = array_values(array_filter($parsedTags));
    $encodedTags = !empty($parsedTags) ? json_encode(array_map(fn($t) => ['value' => $t], $parsedTags)) : '';

    $defaultSuggestedServices = [
        'Custom Software Development',
        'Cloud Architecture & DevOps',
        'AI & Machine Learning',
        'Mobile App Development',
        'Cybersecurity & Compliance',
        'Data Engineering & Analytics',
        'QA & Automation',
        'API & Microservices',
        'UI/UX Product Design',
        'SaaS Platform Engineering',
        'Enterprise Cloud Migration',
        'Embedded Systems & IoT',
        'Blockchain & Smart Contracts'
    ];
@endphp

<div class="pb-5 client-desc-step" data-wizard-type="step-content">
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
              <p class="client-section-copy" style="font-size:11px; color:#64748b; margin:0; text-align:left !important;">Give recruiters a clear understanding of what this company does.</p>
            </div>
          </div>
        </div>

        <div class="card-body" style="padding:14px 18px !important;">
          <div class="client-editor-wrap">
            <div class="d-flex align-items-center justify-content-between mb-1 client-desc-label-row">
              <label class="form-label mb-0" style="font-size:12px; font-weight:700; color:#0f172a;">Company Description <span class="text-danger">*</span></label>
            </div>
            <p style="font-size:11.5px; color:#64748b; margin:0 0 8px 0;">Briefly describe the company's business, products/services, industry and key strengths.</p>

            <div class="client-editor-box" id="client_editor_box" style="border:1px solid #e2e8f0; border-radius:8px; overflow:hidden; background:#ffffff;">
              <div id="kt_client_description_quill" style="min-height:120px; font-size:12.5px; color:#1e293b;">{!! $initialDesc !!}</div>
            </div>
            <textarea id="kt_docs_tinymce_hidden" name="kt_docs_tinymce_hidden" style="display:none !important;" aria-hidden="true">{{ $initialDesc }}</textarea>
            
            <div class="d-flex justify-content-end mt-1">
              <span class="client-char-count" style="font-size:11px; font-weight:500; color:#94a3b8;"><span id="client_desc_count">{{ $initialDescCount }}</span> / 2,000</span>
            </div>
          </div>

          <!-- Tips banner at bottom matching reference design (Collapsible) -->
          <div class="client-info-banner client-tips-accordion" id="client_tips_banner" style="background:#f0f7ff; border:1px solid #dbeafe; border-radius:10px; padding:12px 16px !important; margin-top:12px !important; margin-bottom:0 !important;">
            <div class="d-flex align-items-center justify-content-between cursor-pointer" id="client_tips_toggle" style="cursor:pointer; user-select:none;">
              <div class="d-flex align-items-center gap-2" style="font-size:12px; font-weight:600; color:#1d4ed8;">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                  <path d="M9 18h6"></path>
                  <path d="M10 22h4"></path>
                  <path d="M12 2a7 7 0 0 0-7 7c0 2.38 1.19 4.47 3 5.74V17a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-2.26c1.81-1.27 3-3.36 3-5.74a7 7 0 0 0-7-7z"></path>
                </svg>
                <span>Tips for a great company description</span>
              </div>
              <span id="client_tips_chevron" style="color:#2563eb; font-size:12px; font-weight:700; transition:transform 0.2s ease;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"></polyline></svg>
              </span>
            </div>
            <div id="client_tips_content" style="margin-top:10px;">
              <ul style="list-style:none; padding-left:0; margin:0; font-size:11.5px; color:#334155; line-height:1.7;">
                <li class="d-flex align-items-center gap-2 mb-1">
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981" class="flex-shrink-0"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                  <span>Include a brief overview of the company and what they do.</span>
                </li>
                <li class="d-flex align-items-center gap-2 mb-1">
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981" class="flex-shrink-0"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                  <span>Mention their mission, vision and key strengths.</span>
                </li>
                <li class="d-flex align-items-center gap-2">
                  <svg width="14" height="14" viewBox="0 0 20 20" fill="#10b981" class="flex-shrink-0"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                  <span>This information helps recruiters understand the client better.</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>
    </div>

    <!-- ==========================================
         RIGHT SIDEBAR: Service Offerings & AI Assist
         ========================================== -->
    <aside class="client-side-column">
      
      <!-- Card 1: Service Offerings -->
      <div class="card client-helper-card client-service-offerings-card mb-3" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden; margin-bottom:12px !important;">
        <div class="card-header border-0 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:12px 16px !important; justify-content:flex-start !important;">
          <div class="d-flex align-items-center" style="gap:10px; text-align:left !important;">
            <div style="width:34px; height:34px; border-radius:8px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                <polyline points="2 17 12 22 22 17"></polyline>
                <polyline points="2 12 12 17 22 12"></polyline>
              </svg>
            </div>
            <div style="text-align:left !important;">
              <h4 style="font-size:14px; font-weight:700; color:#101828; margin:0 0 1px 0; text-align:left !important;">Service Offerings</h4>
              <p style="font-size:11px; color:#64748b; margin:0; text-align:left !important;">Select the services that this client provides or is known for.</p>
            </div>
          </div>
        </div>

        <div class="card-body" style="padding:14px 16px !important;">
          <!-- Hidden inputs for form submit & Tagify sync -->
          <input type="hidden" name="tags" id="tags" value="<?php echo htmlspecialchars($encodedTags, ENT_QUOTES); ?>">

          <!-- Search services input bar -->
          <div class="position-relative mb-3">
            <span class="position-absolute" style="top:50%; left:11px; transform:translateY(-50%); pointer-events:none; color:#94a3b8;">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
            </span>
            <input type="text" id="client_services_search" class="form-control" placeholder="Search services (e.g. Software Development, Cloud, QA...)" style="height:35px; padding-left:32px; font-size:11.5px; border:1px solid #e2e8f0; border-radius:8px; background:#ffffff; color:#1e293b;" autocomplete="off">
          </div>

          <!-- Error message if empty -->
          <div class="text-danger mb-2" id="client_tags_error" style="display:none; font-size:11px; font-weight:600;">
            <i class="la la-exclamation-circle"></i> Please select or add at least 1 Service Offering.
          </div>

          <!-- Section: Selected Services (count) + Clear all -->
          <div class="mb-3" id="client_selected_services_section">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span style="font-size:11.5px; font-weight:700; color:#1e293b;">Selected Services (<span id="client_selected_count">{{ count($parsedTags) }}</span>)</span>
              <button type="button" class="btn btn-link p-0" id="client_clear_all_tags_btn" style="font-size:11px; font-weight:500; color:#2563eb; text-decoration:none; cursor:pointer; background:none; border:none;">Clear all</button>
            </div>
            <div id="client_selected_chips_container" class="d-flex flex-wrap gap-2" style="min-height:24px;">
              @forelse($parsedTags as $tag)
                <div class="client-selected-chip">
                  <span>{{ $tag }}</span>
                  <span class="client-chip-remove" data-tag="{{ $tag }}" title="Remove">✕</span>
                </div>
              @empty
                <span class="text-muted" style="font-size:11px; font-style:italic;">No services selected yet. Choose from suggestions below or search above.</span>
              @endforelse
            </div>
          </div>

          <!-- Section: Suggested Services (Industry) + Add All Recommended -->
          <div>
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span id="client_suggested_label" style="font-size:11.5px; font-weight:700; color:#1e293b;">Suggested Services (Information Technology & Services)</span>
              <button type="button" class="btn btn-link p-0" id="client_add_all_suggested_btn" onclick="window.addAllSuggestedServices ? window.addAllSuggestedServices() : false;" style="font-size:11px; font-weight:500; color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:4px; cursor:pointer; background:none; border:none;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Add All Recommended</span>
              </button>
            </div>
            
            <div class="client-suggested-list d-flex flex-wrap gap-2" id="client_suggested_services">
              @foreach($defaultSuggestedServices as $idx => $service)
                @php
                  $isSel = in_array(strtolower($service), array_map('strtolower', $parsedTags));
                  $isExtra = $idx >= 9;
                @endphp
                <button type="button" class="btn btn-sm client-suggested-pill {{ $isSel ? 'is-selected' : '' }}" data-value="{{ $service }}" style="{{ ($isSel || $isExtra) ? 'display:none;' : '' }}">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                  <span>{{ $service }}</span>
                </button>
              @endforeach
            </div>

            <!-- View more recommended services link -->
            <div class="mt-2" id="client_view_more_services_wrap" style="display:block;">
              <a href="javascript:void(0);" id="client_toggle_more_services" style="font-size:11px; font-weight:600; color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                <span>View <span id="client_more_services_count">4</span> more recommended services</span> &rarr;
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Card 2: AI-Generated Company Description -->
      <div class="card client-helper-card client-ai-assist-card" style="background:#faf8ff; border:1px solid #ede9fe; border-radius:12px; padding:14px 16px !important; margin-bottom:0 !important;">
        <div class="d-flex align-items-center justify-content-between mb-1">
          <div class="d-flex align-items-center gap-2">
            <div style="width:26px; height:26px; border-radius:7px; background:#ede9fe; display:inline-flex; align-items:center; justify-content:center; color:#7040f5; flex-shrink:0;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#7040f5" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
              </svg>
            </div>
            <h4 style="font-size:13.5px; font-weight:700; color:#101828; margin:0; letter-spacing:-0.2px;">AI-Generated Company Description</h4>
          </div>
          <span class="badge" style="background:#ede9fe; color:#7040f5; font-weight:600; font-size:10px; padding:2px 7px; border-radius:6px;">Beta</span>
        </div>
        <p style="font-size:11.5px; color:#64748b; line-height:1.45; margin-bottom:10px;">AI-powered suggestion based on publicly available company information.</p>

        <!-- Description Box with enhanced readability, typography, and line-height -->
        <div id="client_ai_suggested_desc" class="client-ai-suggested-desc" style="background:#ffffff; border:1px solid #f1f5f9; border-radius:8px; padding:12px 14px !important; font-size:12.5px; color:#334155; line-height:1.6; margin-bottom:12px !important; max-height:110px; overflow-y:auto;">4bell is an industry-leading Information Technology & Services organization providing end-to-end solutions, enterprise excellence, and specialized operations within the Technology, Software & Telecom sector. The company focuses on scalable innovation, operational agility, and high-quality client delivery.</div>

        <!-- Footer Row -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center gap-2" style="font-size:10.5px; color:#64748b;">
            <a href="javascript:void(0);" id="client_ai_source_link" style="color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:2px;">
              <span>Source: Company Website</span>
              <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
            </a>
            <span class="d-inline-flex align-items-center gap-1" style="color:#475569; font-weight:500;">
              <span style="width:6px; height:6px; border-radius:50%; background:#10b981; display:inline-block;"></span> Verified
            </span>
            <span>Updated recently</span>
          </div>

          <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm" id="client_review_edit_desc_btn" style="background:#ffffff; border:1px solid #c4b5fd; color:#6d28d9; border-radius:7px; padding:5px 12px; font-size:11.5px; font-weight:600; cursor:pointer; transition:all 0.15s ease;" onmouseover="this.style.background='#f5f3ff';" onmouseout="this.style.background='#ffffff';">
              Review & Edit
            </button>
            <button type="button" class="btn btn-sm" id="client_apply_desc_suggestion" onclick="window.applyDescriptionSuggestion ? window.applyDescriptionSuggestion() : false;" style="background:#7040f5; border:none; color:#ffffff; font-weight:600; font-size:11.5px; border-radius:7px; padding:6px 14px; display:inline-flex; align-items:center; gap:5px; box-shadow:0 1px 3px rgba(112,64,245,0.3); cursor:pointer; transition:all 0.15s ease;" onmouseover="this.style.background='#5b21b6';" onmouseout="this.style.background='#7040f5';">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
              <span>Apply Suggestion</span>
            </button>
          </div>
        </div>
      </div>

    </aside>

  </div>
</div>
<!--end::Client Description-->