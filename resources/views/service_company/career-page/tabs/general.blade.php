{{-- General Settings Tab Panel (Rule 20 Card + Sub-Card System) --}}
<div class="cps-panel active" id="panel-general">
    <div class="cps-workspace-grid">

        {{-- Left Main Column --}}
        <div class="cps-col-main">

            {{-- Primary Card 1: Portal Endpoint & Availability --}}
            <div class="cps-card accent-emerald" id="tab-general">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon emerald"><i class="ti ti-world-www"></i></div>
                        <div>
                            <div class="cps-card-title">Portal Endpoint &amp; Localization</div>
                            <div class="cps-card-sub">Manage your public career URL, visibility status, language, and salary currency.</div>
                        </div>
                    </div>
                    <span class="cps-card-pill {{ $isActive ? 'green' : '' }}">{{ $isActive ? 'Publicly Accessible' : 'Portal Offline' }}</span>
                </div>

                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        {{-- Sub-Card: Public Career URL --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker">
                                <span>Public Endpoint</span>
                                <span style="color:#059669;"><i class="ti ti-circle-check-filled"></i> Active</span>
                            </div>
                            <label class="cps-label">Career Page URL</label>
                            <div class="cps-url-wrap">
                                <input type="text" class="cps-input" id="careerPageUrl" value="{{ $careerUrl }}" readonly>
                                <button type="button" class="cps-copy-btn" onclick="copyCareerUrl()">
                                    <i class="ti ti-copy"></i> Copy
                                </button>
                            </div>
                            <div class="cps-url-ok">
                                <i class="ti ti-circle-check-filled" style="font-size:12px;"></i>
                                SSL Verified &amp; Available
                            </div>
                        </div>

                        {{-- Sub-Card: Portal Visibility --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker">
                                <span>Availability</span>
                                <span>Live Switch</span>
                            </div>
                            <label class="cps-label">Career Page Status</label>
                            <div class="cps-toggle-row" style="margin-top:4px;">
                                <div class="cps-toggle-desc">Make your career portal live and discoverable by candidates.</div>
                                <label class="cps-switch">
                                    <input type="checkbox" name="career_page_status" value="1" {{ $isActive ? 'checked' : '' }}>
                                    <span class="cps-slider"></span>
                                </label>
                            </div>
                        </div>

                        {{-- Sub-Card: Language --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Localization</span></div>
                            <label class="cps-label">Default Language</label>
                            <select class="cps-select" name="default_language">
                                <option value="en" {{ ($careerSettings->default_language ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="hi" {{ ($careerSettings->default_language ?? '') == 'hi' ? 'selected' : '' }}>Hindi</option>
                                <option value="ar" {{ ($careerSettings->default_language ?? '') == 'ar' ? 'selected' : '' }}>Arabic</option>
                                <option value="fr" {{ ($careerSettings->default_language ?? '') == 'fr' ? 'selected' : '' }}>French</option>
                            </select>
                            <div class="cps-hint">Primary language rendered on the candidate portal.</div>
                        </div>

                        {{-- Sub-Card: Currency --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Compensation Display</span></div>
                            <label class="cps-label">Default Currency</label>
                            <select class="cps-select" name="currency">
                                <option value="INR" {{ ($careerSettings->currency ?? 'INR') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                                <option value="USD" {{ ($careerSettings->currency ?? '') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="EUR" {{ ($careerSettings->currency ?? '') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                <option value="GBP" {{ ($careerSettings->currency ?? '') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                            </select>
                            <div class="cps-hint">Used to format salary ranges on public job cards.</div>
                        </div>
                    </div>
                </div>

                <div class="cps-card-footer">
                    <span><i class="ti ti-shield-lock" style="color:#10b981;"></i> Tenant-isolated route for Company #{{ $company_id }}</span>
                    <span>Changes invalidate 24h Redis cache automatically on save</span>
                </div>
            </div>

            {{-- Primary Card 2: Branding & Visual Identity --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-palette"></i></div>
                        <div>
                            <div class="cps-card-title">Branding &amp; Visual Identity</div>
                            <div class="cps-card-sub">Synchronize brand colors, typography, and portal logo across all views.</div>
                        </div>
                    </div>
                    <span class="cps-card-pill blue">{{ $fontFamily }}</span>
                </div>

                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        {{-- Primary Color --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Brand Accent</span><span>HEX</span></div>
                            <label class="cps-label">Primary Color</label>
                            <div class="cps-color-row">
                                <input type="color" class="cps-color-swatch" id="primaryPicker"
                                       value="{{ $primaryColor }}"
                                       oninput="document.getElementById('primaryText').value=this.value;updatePreview();">
                                <input type="text" class="cps-input cps-num" id="primaryText"
                                       name="primary_color" value="{{ $primaryColor }}"
                                       oninput="document.getElementById('primaryPicker').value=this.value;updatePreview();">
                            </div>
                        </div>

                        {{-- Secondary Color --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Supporting Tone</span><span>HEX</span></div>
                            <label class="cps-label">Secondary Color</label>
                            <div class="cps-color-row">
                                <input type="color" class="cps-color-swatch" id="secondaryPicker"
                                       value="{{ $secondaryColor }}"
                                       oninput="document.getElementById('secondaryText').value=this.value;">
                                <input type="text" class="cps-input cps-num" id="secondaryText"
                                       name="secondary_color" value="{{ $secondaryColor }}"
                                       oninput="document.getElementById('secondaryPicker').value=this.value;">
                            </div>
                        </div>

                        {{-- Font Family --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Typography</span></div>
                            <label class="cps-label">Font Family</label>
                            <select class="cps-select" name="font_family">
                                @foreach(['Poppins','Inter','Roboto','Open Sans','Montserrat'] as $f)
                                    <option value="{{ $f }}" {{ $fontFamily == $f ? 'selected' : '' }}>{{ $f }}</option>
                                @endforeach
                            </select>
                            <div class="cps-hint">Applied globally across headings and body text.</div>
                        </div>

                        {{-- Career Page Logo --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Header Asset</span><span>120×40</span></div>
                            <label class="cps-label">Career Page Logo</label>
                            <div class="cps-logo-row">
                                @if(!empty($careerSettings->td_logo_path))
                                    <img src="{{ asset($careerSettings->td_logo_path) }}" alt="Logo" class="cps-logo-img" id="logoPreviewImg">
                                @elseif(!empty($company->logo))
                                    <img src="{{ $company->logo }}" alt="Logo" class="cps-logo-img" id="logoPreviewImg">
                                @else
                                    <div class="cps-logo-placeholder" id="logoPreviewImg">No Logo</div>
                                @endif
                                <div style="flex:1;min-width:0;">
                                    <div class="cps-logo-name" id="generalLogoName">
                                        {{ !empty($careerSettings->td_logo_path) ? basename($careerSettings->td_logo_path) : (!empty($company->logo) ? basename($company->logo) : 'No logo uploaded') }}
                                    </div>
                                    <div class="cps-logo-meta">PNG, SVG · Transparent</div>
                                </div>
                                <label class="cps-change-btn" style="cursor:pointer;">
                                    <i class="ti ti-upload"></i> Change
                                    <input type="file" id="generalLogoInput" name="career_logo" accept="image/*" style="display:none;" onchange="previewLogo(this)">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 3: Layout & Jobs Presentation --}}
            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-layout-grid"></i></div>
                        <div>
                            <div class="cps-card-title">Layout &amp; Presentation Preferences</div>
                            <div class="cps-card-sub">Select page architecture and candidate job card display mode.</div>
                        </div>
                    </div>
                    <span class="cps-card-pill">{{ ucfirst($pageLayout) }} • {{ ucfirst($jobsDisplay) }}</span>
                </div>

                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        {{-- Page Layout --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Portal Shell</span></div>
                            <label class="cps-label">Page Layout</label>
                            <div class="cps-layout-grid">
                                @foreach(['modern'=>['Modern','Best engagement'],'classic'=>['Classic','Clean & formal'],'minimal'=>['Minimal','Simple focus']] as $k => $v)
                                    <div class="cps-layout-card {{ $pageLayout == $k ? 'selected' : '' }}" onclick="selectLayout('{{ $k }}',this)">
                                        <div class="cps-lc-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                        <div class="cps-lc-icon"><i class="ti ti-layout-grid" style="font-size:15px;color:#64748b;"></i></div>
                                        <div class="cps-lc-name">{{ $v[0] }}</div>
                                        <div class="cps-lc-desc">{{ $v[1] }}</div>
                                    </div>
                                @endforeach
                            </div>
                            <input type="hidden" name="page_layout" id="pageLayoutInput" value="{{ $pageLayout }}">
                        </div>

                        {{-- Jobs Display Style --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Role Stream</span></div>
                            <label class="cps-label">Jobs Display Style</label>
                            <div class="cps-display-grid">
                                <div class="cps-display-card {{ $jobsDisplay == 'card' ? 'selected' : '' }}" onclick="selectDisplay('card',this)">
                                    <i class="ti ti-layout-grid" style="font-size:21px;color:#009ef7;"></i>
                                    <div>
                                        <div class="cps-dc-name">Card View</div>
                                        <div class="cps-dc-desc">Rich visual cards</div>
                                    </div>
                                </div>
                                <div class="cps-display-card {{ $jobsDisplay == 'list' ? 'selected' : '' }}" onclick="selectDisplay('list',this)">
                                    <i class="ti ti-list" style="font-size:21px;color:#64748b;"></i>
                                    <div>
                                        <div class="cps-dc-name">List View</div>
                                        <div class="cps-dc-desc">Compact density</div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="jobs_display" id="jobsDisplayInput" value="{{ $jobsDisplay }}">
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Sticky Preview & Quick Actions Column --}}
        <div class="cps-col-side sticky-side">

            {{-- Live Preview Card --}}
            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-device-desktop"></i></div>
                        <div>
                            <div class="cps-card-title">Live Portal Preview</div>
                            <div class="cps-card-sub">Candidate-facing header &amp; job stream snapshot</div>
                        </div>
                    </div>
                    <a href="{{ $careerUrl }}" target="_blank" class="cps-card-pill blue" style="text-decoration:none;">
                        Open <i class="ti ti-external-link"></i>
                    </a>
                </div>
                <div class="cps-card-body">
                    <div class="cps-preview-box">
                        <div class="cps-prev-hdr" id="previewHeader"
                             style="background:linear-gradient(135deg,{{ $primaryColor }},#7C3AED);">
                            <div class="cps-prev-hdr-title">Build the Future with</div>
                            <div class="cps-prev-hdr-name">{{ $companyName }}</div>
                            <div class="cps-prev-search"><i class="ti ti-search"></i> Search by skills, position or location...</div>
                        </div>
                        <div class="cps-prev-body">
                            <div class="cps-prev-stats">
                                <div class="cps-subcard white" style="padding:7px 9px;">
                                    <div class="cps-prev-stat-n">{{ $careerSettings->stat_departments ?? 15 }}</div>
                                    <div class="cps-prev-stat-l">Departments</div>
                                </div>
                                <div class="cps-subcard white" style="padding:7px 9px;">
                                    <div class="cps-prev-stat-n">{{ $careerSettings->stat_countries ?? 5 }}+</div>
                                    <div class="cps-prev-stat-l">Locations</div>
                                </div>
                                <div class="cps-subcard white" style="padding:7px 9px;">
                                    <div class="cps-prev-stat-n">{{ $careerSettings->stat_employees ?? '220+' }}</div>
                                    <div class="cps-prev-stat-l">Team</div>
                                </div>
                            </div>
                            <div class="cps-prev-positions">Featured Open Roles</div>
                            <div class="cps-prev-job">
                                <div>
                                    <div class="cps-prev-job-t">Senior Backend Engineer</div>
                                    <div class="cps-prev-job-s">Engineering • Full-Time</div>
                                </div>
                                <span class="cps-card-pill blue" style="font-size:9px;padding:2px 7px;">Apply</span>
                            </div>
                            <div class="cps-prev-job">
                                <div>
                                    <div class="cps-prev-job-t">Product Designer</div>
                                    <div class="cps-prev-job-s">Design • Hybrid</div>
                                </div>
                                <span class="cps-card-pill blue" style="font-size:9px;padding:2px 7px;">Apply</span>
                            </div>
                            <div class="cps-prev-footer">
                                <a href="{{ $careerUrl }}" target="_blank">
                                    Preview Full Portal <i class="ti ti-external-link"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Navigation Sub-Cards --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-bolt"></i></div>
                        <div>
                            <div class="cps-card-title">Quick Studio Actions</div>
                            <div class="cps-card-sub">Jump directly to common configuration tasks</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="cps-qa" onclick="document.getElementById('generalLogoInput').click();">
                        <div class="cps-qa-left">
                            <div class="cps-qa-icon" style="background:#eff6ff;"><i class="ti ti-photo" style="font-size:16px;color:#009ef7;"></i></div>
                            <div>
                                <div class="cps-qa-title">Upload Career Logo</div>
                                <div class="cps-qa-desc">Recommended: 120×40px transparent PNG</div>
                            </div>
                        </div>
                        <i class="ti ti-chevron-right" style="color:#94a3b8;"></i>
                    </div>
                    <div class="cps-qa" onclick="cpsActivateTab('hero')">
                        <div class="cps-qa-left">
                            <div class="cps-qa-icon" style="background:#eef2ff;"><i class="ti ti-sparkles" style="font-size:16px;color:#4f46e5;"></i></div>
                            <div>
                                <div class="cps-qa-title">Customize Hero Banner</div>
                                <div class="cps-qa-desc">Edit headline, CTAs &amp; background style</div>
                            </div>
                        </div>
                        <i class="ti ti-chevron-right" style="color:#94a3b8;"></i>
                    </div>
                    <div class="cps-qa" onclick="cpsActivateTab('seo')">
                        <div class="cps-qa-left">
                            <div class="cps-qa-icon" style="background:#ecfdf5;"><i class="ti ti-world-search" style="font-size:16px;color:#059669;"></i></div>
                            <div>
                                <div class="cps-qa-title">Configure SEO &amp; OG Cards</div>
                                <div class="cps-qa-desc">Search snippet &amp; social sharing tags</div>
                            </div>
                        </div>
                        <i class="ti ti-chevron-right" style="color:#94a3b8;"></i>
                    </div>
                    <div class="cps-qa" onclick="cpsActivateTab('templates')">
                        <div class="cps-qa-left">
                            <div class="cps-qa-icon" style="background:#fffbeb;"><i class="ti ti-palette" style="font-size:16px;color:#d97706;"></i></div>
                            <div>
                                <div class="cps-qa-title">Open Template Studio</div>
                                <div class="cps-qa-desc">Switch themes, custom CSS &amp; hero assets</div>
                            </div>
                        </div>
                        <i class="ti ti-chevron-right" style="color:#94a3b8;"></i>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
