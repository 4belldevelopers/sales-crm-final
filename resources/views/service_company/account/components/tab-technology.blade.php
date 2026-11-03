{{--
  God-Level Company Profile — Step 3: Technologies & Expertise
  Dual-Engine: Core Technologies / Tech Stack + Service Offerings (Tagify)
  100% Replicates Add Client Design System & Interaction Model (Rule 20 Design Standard)
  Optimized for 15.6" Screens (1366x768 & 1920x1080)
--}}
@php
    // 1. Technologies extraction from company_tags or JSON
    $existingTech = [];
    if (!empty($company_tags)) {
        foreach ($company_tags as $tRow) {
            $val = is_object($tRow) ? ($tRow->tag_name ?? '') : (is_array($tRow) ? ($tRow['tag_name'] ?? '') : (string)$tRow);
            if (trim($val) !== '') {
                $existingTech[] = trim($val);
            }
        }
    }
    $existingTech = array_values(array_unique(array_filter($existingTech)));
    $encodedTech = json_encode(array_map(fn($t) => ['value' => $t], $existingTech));

    // 2. Service Offerings extraction from client->tags
    $rawTags = $client->tags ?? '';
    $parsedServices = [];
    if (!empty($rawTags)) {
        $decoded = json_decode($rawTags, true);
        if (is_array($decoded)) {
            foreach ($decoded as $item) {
                $parsedServices[] = is_array($item) ? ($item['value'] ?? '') : $item;
            }
        } else {
            $parsedServices = array_map('trim', explode(',', $rawTags));
        }
    }
    $parsedServices = array_values(array_unique(array_filter($parsedServices)));
    $encodedServices = json_encode(array_map(fn($t) => ['value' => $t], $parsedServices));

    // 3. Preset Curated Technologies for 0ms Client-Side Discovery
    $presetTechnologies = [
        'Laravel', 'PHP', 'MySQL', 'AWS', 'Docker', 'Kubernetes',
        'React', 'Node.js', 'Python', 'Java', 'TypeScript', 'GCP',
        'PostgreSQL', 'Redis', 'GraphQL', 'Next.js', 'Vue.js',
        'Go', 'C#', '.NET', 'Flutter', 'Terraform', 'Azure', 'MongoDB'
    ];

    // 4. Default Suggested Services
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
        'Staff Augmentation & Dedicated Teams'
    ];
@endphp

<div class="pb-5 client-tech-step profile-tech-step" data-wizard-type="step-content">
  <div class="client-step-layout client-technology-layout">
    
    <!-- ==========================================
         MAIN COLUMN: Dual Tagging Engines
         ========================================== -->
    <div class="client-main-column">
      
      <!-- CARD 1: Technologies & Tech Stack Engine -->
      <section class="card client-section-card mb-4" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <!-- Card Header -->
        <div class="card-header border-0 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:12px 18px !important; justify-content:flex-start !important;">
          <div class="d-flex align-items-center" style="gap:12px; text-align:left !important;">
            <div style="width:36px; height:36px; border-radius:9px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="16 18 22 12 16 6"></polyline>
                <polyline points="8 6 2 12 8 18"></polyline>
              </svg>
            </div>
            <div style="text-align:left !important;">
              <h3 class="client-section-heading" style="font-size:14px; font-weight:700; color:#101828; margin:0 0 2px 0; text-align:left !important;">Technologies & Tech Stack</h3>
              <p class="client-section-copy" style="font-size:11px; color:#64748b; margin:0; text-align:left !important;">Specify the programming languages, cloud frameworks, and tools your company builds or works with.</p>
            </div>
          </div>
        </div>

        <div class="card-body" style="padding:14px 18px !important;">
          <!-- Hidden input for Form POST serialization -->
          <input type="hidden" name="technologies" id="profile_technologies" value="<?php echo htmlspecialchars($encodedTech, ENT_QUOTES); ?>">

          <!-- Technology Search Input -->
          <div class="position-relative mb-3">
            <span class="position-absolute" style="top:50%; left:12px; transform:translateY(-50%); pointer-events:none; color:#94a3b8;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
            </span>
            <input type="text"
                   id="profile_tech_search"
                   class="form-control"
                   placeholder="Search or type technologies (e.g. Laravel, React, AWS, Python, Kubernetes...)"
                   style="height:38px; padding-left:34px; padding-right:12px; font-size:12px; border:1px solid #e2e8f0; border-radius:8px; background:#ffffff; color:#0f172a;"
                   autocomplete="off"
                   spellcheck="false"
                   onkeydown="if(event.key==='Enter'||event.keyCode===13){event.preventDefault();window.addCustomTechnology(this.value);this.value='';return false;}">
            
            <!-- Live Suggestions Dropdown (Elasticsearch skills search) -->
            <div class="profile-tech-dropdown" id="profile_tech_dropdown" style="display:none; position:absolute; top:calc(100% + 4px); left:0; right:0; background:#ffffff; border:1px solid #e2e8f0; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.08); z-index:1050; max-height:200px; overflow-y:auto;">
              <div id="profile_tech_dropdown_list"></div>
            </div>
          </div>

          <!-- Selected Technologies Section -->
          <div class="mb-3" id="profile_selected_tech_section">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span style="font-size:11.5px; font-weight:700; color:#1e293b;">Selected Technologies (<span id="profile_selected_tech_count">{{ count($existingTech) }}</span>)</span>
              <button type="button" class="btn btn-link p-0" id="profile_clear_all_tech_btn" onclick="window.clearAllTechnologies ? window.clearAllTechnologies() : false;" style="font-size:11px; font-weight:500; color:#2563eb; text-decoration:none; cursor:pointer; background:none; border:none;">Clear all</button>
            </div>
            <div id="profile_selected_tech_container" class="d-flex flex-wrap gap-2" style="min-height:28px;">
              @forelse($existingTech as $tech)
                <div class="client-selected-chip profile-tech-chip" data-tech="{{ $tech }}">
                  <span>{{ $tech }}</span>
                  <span class="client-chip-remove" onclick="window.removeTechnology('{{ addslashes($tech) }}');" title="Remove">✕</span>
                </div>
              @empty
                <span class="text-muted" id="profile_tech_empty_msg" style="font-size:11px; font-style:italic;">No technologies added yet. Select from presets below or search above.</span>
              @endforelse
            </div>
          </div>

          <!-- Suggested Preset Technologies -->
          <div>
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span style="font-size:11.5px; font-weight:700; color:#1e293b;">Popular Technologies & Frameworks</span>
              <span style="font-size:10.5px; color:#64748b;">Click to add</span>
            </div>
            
            <div class="client-suggested-list d-flex flex-wrap gap-2" id="profile_suggested_tech_list">
              @foreach($presetTechnologies as $pTech)
                @php
                  $isAdded = in_array(strtolower($pTech), array_map('strtolower', $existingTech));
                @endphp
                <button type="button" class="btn btn-sm client-suggested-pill {{ $isAdded ? 'is-selected' : '' }}" data-tech="{{ $pTech }}" onclick="window.togglePresetTechnology(this, '{{ addslashes($pTech) }}');">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                  <span>{{ $pTech }}</span>
                </button>
              @endforeach
            </div>
          </div>

        </div>
      </section>

      <!-- CARD 2: Service Offerings & Capabilities Engine -->
      <section class="card client-section-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        <!-- Card Header -->
        <div class="card-header border-0 d-flex align-items-center" style="background:#ffffff; border-bottom:1px solid #f8fafc !important; padding:12px 18px !important; justify-content:flex-start !important;">
          <div class="d-flex align-items-center" style="gap:12px; text-align:left !important;">
            <div style="width:36px; height:36px; border-radius:9px; background:#fef9c3; display:inline-flex; align-items:center; justify-content:center; color:#ca8a04; flex-shrink:0;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                <polyline points="2 17 12 22 22 17"></polyline>
                <polyline points="2 12 12 17 22 12"></polyline>
              </svg>
            </div>
            <div style="text-align:left !important;">
              <h3 class="client-section-heading" style="font-size:14px; font-weight:700; color:#101828; margin:0 0 2px 0; text-align:left !important;">Service Offerings & Capabilities</h3>
              <p class="client-section-copy" style="font-size:11px; color:#64748b; margin:0; text-align:left !important;">Select the specialized recruitment, technical, or consulting services your company provides.</p>
            </div>
          </div>
        </div>

        <div class="card-body" style="padding:14px 18px !important;">
          <!-- Hidden input for Tagify sync & Form POST -->
          <input type="hidden" name="tags" id="tags" value="<?php echo htmlspecialchars($encodedServices, ENT_QUOTES); ?>">

          <!-- Search services input bar -->
          <div class="position-relative mb-3">
            <span class="position-absolute" style="top:50%; left:12px; transform:translateY(-50%); pointer-events:none; color:#94a3b8;">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
              </svg>
            </span>
            <input type="text" id="client_services_search" class="form-control" placeholder="Search services (e.g. Software Development, Cloud Architecture, AI & ML...)" style="height:38px; padding-left:34px; font-size:12px; border:1px solid #e2e8f0; border-radius:8px; background:#ffffff; color:#0f172a;" autocomplete="off">
          </div>

          <!-- Section: Selected Services (count) + Clear all -->
          <div class="mb-3" id="client_selected_services_section">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span style="font-size:11.5px; font-weight:700; color:#1e293b;">Selected Services (<span id="client_selected_count">{{ count($parsedServices) }}</span>)</span>
              <button type="button" class="btn btn-link p-0" id="client_clear_all_tags_btn" style="font-size:11px; font-weight:500; color:#2563eb; text-decoration:none; cursor:pointer; background:none; border:none;">Clear all</button>
            </div>
            <div id="client_selected_chips_container" class="d-flex flex-wrap gap-2" style="min-height:28px;">
              @forelse($parsedServices as $tag)
                <div class="client-selected-chip">
                  <span>{{ $tag }}</span>
                  <span class="client-chip-remove" data-tag="{{ $tag }}" title="Remove">✕</span>
                </div>
              @empty
                <span class="text-muted" style="font-size:11px; font-style:italic;">No services selected yet. Choose from suggestions below or search above.</span>
              @endforelse
            </div>
          </div>

          <!-- Section: Suggested Services + Add All Recommended -->
          <div>
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span id="client_suggested_label" style="font-size:11.5px; font-weight:700; color:#1e293b;">Suggested Services ({{ ($selectedIndName ?? '') ?: 'Industry Recommendations' }})</span>
              <button type="button" class="btn btn-link p-0" id="client_add_all_suggested_btn" onclick="window.addAllSuggestedServices ? window.addAllSuggestedServices() : false;" style="font-size:11px; font-weight:500; color:#2563eb; text-decoration:none; display:inline-flex; align-items:center; gap:4px; cursor:pointer; background:none; border:none;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Add All Recommended</span>
              </button>
            </div>
            
            <div class="client-suggested-list d-flex flex-wrap gap-2" id="client_suggested_services">
              @foreach($defaultSuggestedServices as $idx => $service)
                @php
                  $isSel = in_array(strtolower($service), array_map('strtolower', $parsedServices));
                @endphp
                <button type="button" class="btn btn-sm client-suggested-pill {{ $isSel ? 'is-selected' : '' }}" data-value="{{ $service }}" style="{{ $isSel ? 'display:none;' : '' }}">
                  <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                  <span>{{ $service }}</span>
                </button>
              @endforeach
            </div>
          </div>

        </div>
      </section>

    </div>

    <!-- ==========================================
         RIGHT SIDEBAR: Tagging Guidance
         ========================================== -->
    <aside class="client-side-column">
      
      <div class="card client-helper-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; padding:16px 18px !important; margin-bottom:12px !important;">
        <h4 style="font-size:12.5px; font-weight:700; color:#0f172a; margin:0 0 10px; display:flex; align-items:center; gap:8px;">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m12 16 4-4-4-4"/><path d="M8 12h8"/></svg>
          <span>Why Tech & Services Matter</span>
        </h4>
        <p style="font-size:11px; color:#64748b; line-height:1.55; margin:0 0 10px;">
          Your selected technologies and service offerings directly calibrate JERO's:
        </p>
        <ul style="list-style:none; padding-left:0; margin:0; font-size:11px; color:#334155; line-height:1.65;">
          <li class="d-flex align-items-center gap-2 mb-1">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <span>Candidate match scoring</span>
          </li>
          <li class="d-flex align-items-center gap-2 mb-1">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <span>Partner agency recommendations</span>
          </li>
          <li class="d-flex align-items-center gap-2">
            <svg width="13" height="13" viewBox="0 0 20 20" fill="#10b981"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
            <span>Talent marketplace routing</span>
          </li>
        </ul>
      </div>

    </aside>

  </div>
</div>
