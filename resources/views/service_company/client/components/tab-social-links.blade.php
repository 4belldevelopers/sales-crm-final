<!--begin::Step 5: Social Links (God-Level Design Aligned with Reference media_1792566743106_f52e3a53.png)-->
<div class="pb-5 client-social-step" data-wizard-type="step-content">
  <div class="client-step-layout client-social-layout">

    <!-- ==============================================================
         LEFT COLUMN: Official Social Media & Professional Profiles
         ============================================================== -->
    <div class="client-main-column">
      <div class="card client-social-links-card" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); overflow:hidden;">
        
        <!-- Header -->
        <div class="card-header border-0 py-4 px-6 d-flex align-items-center justify-content-between" style="background:#ffffff; border-bottom:1px solid #f1f5f9 !important;">
          <div class="d-flex align-items-center">
            <div style="width:38px; height:38px; border-radius:10px; background:#eff6ff; display:inline-flex; align-items:center; justify-content:center; color:#2563eb; margin-right:12px; flex-shrink:0;">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
              </svg>
            </div>
            <div>
              <h3 style="font-size:15px; font-weight:700; color:#101828; margin:0 0 2px 0;">Social Links</h3>
              <p style="font-size:12px; color:#64748b; margin:0;">Add the client’s official social media and professional profiles.</p>
            </div>
          </div>
        </div>

        <!-- Inputs Grid (Focus on the 6 platforms only) -->
        <div class="card-body px-6 py-5">
          <div style="display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:18px;" class="client-social-inputs-grid">
            
            <!-- 1. LinkedIn -->
            <div class="client-field">
              <label class="form-label d-flex align-items-center gap-2" for="linkedin_url" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">
                <span>LinkedIn Company Page</span>
              </label>
              <div class="input-group input-group-solid client-social-input-wrap" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
                <span class="input-group-text" style="background:#ffffff; border:none; color:#0a66c2; padding-left:12px; padding-right:8px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="#0a66c2">
                    <path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.2V10.9H6.46M7.83 6.2a1.66 1.66 0 0 0-1.66 1.66c0 .92.75 1.67 1.66 1.67.92 0 1.67-.75 1.67-1.67 0-.92-.75-1.66-1.67-1.66Z"/>
                  </svg>
                </span>
                <input type="url" class="form-control form-control-solid client-social-url-input" name="linkedin_url" id="linkedin_url" value="{{ $linkedin_url ?? '' }}" placeholder="https://linkedin.com/company/4bell-technology" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
              </div>
            </div>

            <!-- 2. X / Twitter -->
            <div class="client-field">
              <label class="form-label d-flex align-items-center gap-2" for="twitter_url" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">
                <span>X / Twitter Profile</span>
              </label>
              <div class="input-group input-group-solid client-social-input-wrap" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
                <span class="input-group-text" style="background:#ffffff; border:none; color:#0f172a; padding-left:12px; padding-right:8px;">
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="#0f172a">
                    <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                  </svg>
                </span>
                <input type="text" class="form-control form-control-solid client-social-url-input" name="twitter_url" id="twitter_url" value="{{ $twitter_url ?? '' }}" placeholder="https://x.com/4belltechnology" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
              </div>
            </div>

            <!-- 3. YouTube -->
            <div class="client-field">
              <label class="form-label d-flex align-items-center gap-2" for="youtube_url" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">
                <span>YouTube Channel</span>
              </label>
              <div class="input-group input-group-solid client-social-input-wrap" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
                <span class="input-group-text" style="background:#ffffff; border:none; color:#ef4444; padding-left:12px; padding-right:8px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="#ef4444">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                  </svg>
                </span>
                <input type="url" class="form-control form-control-solid client-social-url-input" name="youtube_url" id="youtube_url" value="{{ $youtube_url ?? '' }}" placeholder="https://youtube.com/@4belltechnology" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
              </div>
            </div>

            <!-- 4. Facebook -->
            <div class="client-field">
              <label class="form-label d-flex align-items-center gap-2" for="facebook_url" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">
                <span>Facebook Page</span>
              </label>
              <div class="input-group input-group-solid client-social-input-wrap" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
                <span class="input-group-text" style="background:#ffffff; border:none; color:#1877f2; padding-left:12px; padding-right:8px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="#1877f2">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                  </svg>
                </span>
                <input type="url" class="form-control form-control-solid client-social-url-input" name="facebook_url" id="facebook_url" value="{{ $facebook_url ?? '' }}" placeholder="https://facebook.com/4belltechnology" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
              </div>
            </div>

            <!-- 5. Instagram -->
            <div class="client-field">
              <label class="form-label d-flex align-items-center gap-2" for="instagram_url" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">
                <span>Instagram Profile</span>
              </label>
              <div class="input-group input-group-solid client-social-input-wrap" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
                <span class="input-group-text" style="background:#ffffff; border:none; color:#e1306c; padding-left:12px; padding-right:8px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="#e1306c">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                  </svg>
                </span>
                <input type="url" class="form-control form-control-solid client-social-url-input" name="instagram_url" id="instagram_url" value="{{ $instagram_url ?? '' }}" placeholder="https://instagram.com/4belltechnology" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
              </div>
            </div>

            <!-- 6. GitHub -->
            <div class="client-field">
              <label class="form-label d-flex align-items-center gap-2" for="github_url" style="font-size:12.5px; font-weight:600; color:#1e293b; margin-bottom:6px;">
                <span>GitHub Organization</span>
              </label>
              <div class="input-group input-group-solid client-social-input-wrap" style="border:1px solid #cbd5e1; border-radius:8px; overflow:hidden; background:#ffffff; height:42px;">
                <span class="input-group-text" style="background:#ffffff; border:none; color:#24292f; padding-left:12px; padding-right:8px;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="#24292f">
                    <path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/>
                  </svg>
                </span>
                <input type="url" class="form-control form-control-solid client-social-url-input" name="github_url" id="github_url" value="{{ $github_url ?? '' }}" placeholder="https://github.com/4belltechnology" style="border:none; height:100%; font-size:13px; color:#1e293b; background-color:#ffffff;">
              </div>
            </div>

          </div>
        </div>

        <!-- Bottom Banner -->
        <div class="px-6 py-4" style="background:#ffffff; border-top:1px solid #f1f5f9;">
          <div class="client-social-info-banner" style="background:#eff6ff; border:1px solid #dbeafe; border-radius:8px; padding:10px 14px; display:flex; align-items:center; gap:10px; color:#1e40af; font-size:12.5px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="12" y1="16" x2="12" y2="12"></line>
              <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
            <span>Social profiles help verify client authenticity and improve company branding across talent workflows.</span>
          </div>
        </div>
      </div>
    </div>

    <!-- ==============================================================
         RIGHT COLUMN: AI Profile Discovery (AI discovers -> User verifies -> User adds)
         ============================================================== -->
    <aside class="client-side-column">
      <div class="card client-ai-social-card" style="background:#ffffff; border:1px solid #ede9fe; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,0.03); padding:18px 20px;">
        
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="d-flex align-items-center gap-2">
            <div style="width:34px; height:34px; border-radius:10px; background:#f3e8ff; display:inline-flex; align-items:center; justify-content:center; color:#7e22ce; flex-shrink:0;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/>
              </svg>
            </div>
            <div class="d-flex align-items-center gap-2">
              <h4 style="font-size:15px; font-weight:700; color:#1e293b; margin:0;">AI Profile Discovery</h4>
              <span class="badge" style="background:#ede9fe; color:#7c3aed; font-weight:600; font-size:10.5px; padding:2px 8px; border-radius:6px;">Beta</span>
            </div>
          </div>
          <button type="button" class="btn btn-sm" id="btn_add_all_social" onclick="window.clientSocialManager ? window.clientSocialManager.addAllSuggested() : false;" style="background:#eff6ff; border:1px solid #bfdbfe; color:#2563eb; font-weight:600; font-size:11.5px; padding:5px 12px; border-radius:6px; cursor:pointer; display:inline-flex; align-items:center; gap:4px; transition:all 0.15s ease;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            <span>Add All Suggested</span>
          </button>
        </div>
        
        <p style="font-size:12px; color:#64748b; line-height:1.45; margin-bottom:14px;">
          We discovered official profiles from public professional sources. Review and add them to your client profile.
        </p>

        <!-- AI Discovered Profiles List -->
        <div class="client-ai-social-list" id="client_ai_social_list">
          
          <!-- LinkedIn Card -->
          <div class="client-ai-social-item mb-2" data-platform="linkedin" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; transition:all 0.15s ease;">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2 min-w-0" style="flex:1;">
                <div style="width:28px; height:28px; border-radius:6px; background:#eff6ff; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="#0a66c2"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.46 10.9v8.37H9.2V10.9H6.46M7.83 6.2a1.66 1.66 0 0 0-1.66 1.66c0 .92.75 1.67 1.66 1.67.92 0 1.67-.75 1.67-1.67 0-.92-.75-1.66-1.67-1.66Z"/></svg>
                </div>
                <div class="min-w-0" style="flex:1;">
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:12.5px; font-weight:700; color:#101828;">LinkedIn</span>
                    <span class="badge" style="background:#dcfce7; color:#15803d; font-size:10px; font-weight:600; padding:1px 6px; border-radius:4px;">Verified</span>
                  </div>
                  <div class="text-truncate ai-profile-url-display" style="font-size:11px; color:#64748b; max-width:210px;" id="ai_url_linkedin">https://linkedin.com/company/4bell-technology</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm client-ai-social-btn" id="btn_ai_add_linkedin" onclick="window.clientSocialManager ? window.clientSocialManager.addPlatform('linkedin') : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11.5px; padding:4px 12px; border-radius:6px; cursor:pointer; white-space:nowrap; flex-shrink:0;">
                Add to Profile
              </button>
            </div>
          </div>

          <!-- X / Twitter Card -->
          <div class="client-ai-social-item mb-2" data-platform="twitter" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; transition:all 0.15s ease;">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2 min-w-0" style="flex:1;">
                <div style="width:28px; height:28px; border-radius:6px; background:#f8fafc; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="#0f172a"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                </div>
                <div class="min-w-0" style="flex:1;">
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:12.5px; font-weight:700; color:#101828;">X / Twitter</span>
                    <span class="badge" style="background:#dcfce7; color:#15803d; font-size:10px; font-weight:600; padding:1px 6px; border-radius:4px;">Verified</span>
                  </div>
                  <div class="text-truncate ai-profile-url-display" style="font-size:11px; color:#64748b; max-width:210px;" id="ai_url_twitter">https://x.com/4belltechnology</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm client-ai-social-btn" id="btn_ai_add_twitter" onclick="window.clientSocialManager ? window.clientSocialManager.addPlatform('twitter') : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11.5px; padding:4px 12px; border-radius:6px; cursor:pointer; white-space:nowrap; flex-shrink:0;">
                Add to Profile
              </button>
            </div>
          </div>

          <!-- YouTube Card -->
          <div class="client-ai-social-item mb-2" data-platform="youtube" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; transition:all 0.15s ease;">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2 min-w-0" style="flex:1;">
                <div style="width:28px; height:28px; border-radius:6px; background:#fef2f2; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="#ef4444"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                </div>
                <div class="min-w-0" style="flex:1;">
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:12.5px; font-weight:700; color:#101828;">YouTube</span>
                    <span class="badge" style="background:#dbeafe; color:#1d4ed8; font-size:10px; font-weight:600; padding:1px 6px; border-radius:4px;">Likely Match</span>
                  </div>
                  <div class="text-truncate ai-profile-url-display" style="font-size:11px; color:#64748b; max-width:210px;" id="ai_url_youtube">https://youtube.com/@4belltechnology</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm client-ai-social-btn" id="btn_ai_add_youtube" onclick="window.clientSocialManager ? window.clientSocialManager.addPlatform('youtube') : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11.5px; padding:4px 12px; border-radius:6px; cursor:pointer; white-space:nowrap; flex-shrink:0;">
                Add to Profile
              </button>
            </div>
          </div>

          <!-- Facebook Card -->
          <div class="client-ai-social-item mb-2" data-platform="facebook" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; transition:all 0.15s ease;">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2 min-w-0" style="flex:1;">
                <div style="width:28px; height:28px; border-radius:6px; background:#eff6ff; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="#1877f2"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                </div>
                <div class="min-w-0" style="flex:1;">
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:12.5px; font-weight:700; color:#101828;">Facebook</span>
                    <span class="badge" style="background:#dbeafe; color:#1d4ed8; font-size:10px; font-weight:600; padding:1px 6px; border-radius:4px;">Likely Match</span>
                  </div>
                  <div class="text-truncate ai-profile-url-display" style="font-size:11px; color:#64748b; max-width:210px;" id="ai_url_facebook">https://facebook.com/4belltechnology</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm client-ai-social-btn" id="btn_ai_add_facebook" onclick="window.clientSocialManager ? window.clientSocialManager.addPlatform('facebook') : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11.5px; padding:4px 12px; border-radius:6px; cursor:pointer; white-space:nowrap; flex-shrink:0;">
                Add to Profile
              </button>
            </div>
          </div>

          <!-- Instagram Card -->
          <div class="client-ai-social-item mb-2" data-platform="instagram" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; transition:all 0.15s ease;">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2 min-w-0" style="flex:1;">
                <div style="width:28px; height:28px; border-radius:6px; background:#fdf2f8; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="#e1306c"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </div>
                <div class="min-w-0" style="flex:1;">
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:12.5px; font-weight:700; color:#101828;">Instagram</span>
                    <span class="badge" style="background:#fef3c7; color:#b45309; font-size:10px; font-weight:600; padding:1px 6px; border-radius:4px;">Possible Match</span>
                  </div>
                  <div class="text-truncate ai-profile-url-display" style="font-size:11px; color:#64748b; max-width:210px;" id="ai_url_instagram">https://instagram.com/4belltechnology</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm client-ai-social-btn" id="btn_ai_add_instagram" onclick="window.clientSocialManager ? window.clientSocialManager.addPlatform('instagram') : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11.5px; padding:4px 12px; border-radius:6px; cursor:pointer; white-space:nowrap; flex-shrink:0;">
                Add to Profile
              </button>
            </div>
          </div>

          <!-- GitHub Card -->
          <div class="client-ai-social-item" data-platform="github" style="background:#ffffff; border:1px solid #e2e8f0; border-radius:10px; padding:10px 14px; transition:all 0.15s ease;">
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center gap-2 min-w-0" style="flex:1;">
                <div style="width:28px; height:28px; border-radius:6px; background:#f8fafc; display:inline-flex; align-items:center; justify-content:center; flex-shrink:0;">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="#24292f"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>
                </div>
                <div class="min-w-0" style="flex:1;">
                  <div class="d-flex align-items-center gap-2">
                    <span style="font-size:12.5px; font-weight:700; color:#101828;">GitHub</span>
                    <span class="badge" style="background:#dcfce7; color:#15803d; font-size:10px; font-weight:600; padding:1px 6px; border-radius:4px;">Verified</span>
                  </div>
                  <div class="text-truncate ai-profile-url-display" style="font-size:11px; color:#64748b; max-width:210px;" id="ai_url_github">https://github.com/4belltechnology</div>
                </div>
              </div>
              <button type="button" class="btn btn-sm client-ai-social-btn" id="btn_ai_add_github" onclick="window.clientSocialManager ? window.clientSocialManager.addPlatform('github') : false;" style="background:#ffffff; color:#2563eb; border:1px solid #2563eb; font-weight:600; font-size:11.5px; padding:4px 12px; border-radius:6px; cursor:pointer; white-space:nowrap; flex-shrink:0;">
                Add to Profile
              </button>
            </div>
          </div>

        </div>
      </div>
    </aside>

  </div>
</div>

<style>
  /* --------------------------------------------------------------------------
     Step 5 (Social Links): 62:38 two-column layout & 15.6" screen optimization
     -------------------------------------------------------------------------- */
  .client-social-layout {
    display: grid !important;
    width: 100% !important;
    grid-template-columns: minmax(0, 62fr) minmax(0, 38fr) !important;
    gap: 20px !important;
    align-items: start !important;
  }

  .client-ai-social-list {
    max-height: 480px;
    overflow-y: auto;
    padding-right: 2px;
  }
  .client-ai-social-list::-webkit-scrollbar {
    width: 5px;
  }
  .client-ai-social-list::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
  }

  .client-ai-social-item:hover {
    border-color: #cbd5e1 !important;
    box-shadow: 0 3px 10px rgba(0,0,0,0.04);
  }

  /* Compact Viewports (1366x768 and height <= 850px) */
  @media (max-height: 850px) {
    .client-social-inputs-grid {
      gap: 12px !important;
    }
    .client-social-input-wrap {
      height: 38px !important;
    }
    .client-ai-social-list {
      max-height: 380px !important;
    }
    .client-ai-social-item {
      padding: 8px 12px !important;
      margin-bottom: 6px !important;
    }
  }

  @media (max-width: 992px) {
    .client-social-layout {
      grid-template-columns: 1fr !important;
    }
    .client-social-inputs-grid {
      grid-template-columns: 1fr !important;
    }
  }
</style>
<!--end::Step 5: Social Links-->
