{{-- General Settings Tab Panel --}}
<div class="cps-panel active" id="panel-general">
<div class="row g-4">
    {{-- Left: Main content --}}
    <div class="col-lg-8">

        {{-- General Settings --}}
        <div class="cps-section" id="tab-general">
            <div class="cps-section-title">General Settings</div>
            <div class="cps-section-sub">Manage your career page basic information and preferences.</div>

            <div class="cps-grid2">

                {{-- Career Page URL --}}
                <div>
                    <label class="cps-label">
                        Career Page URL
                        <i class="ti ti-info-circle" style="font-size:11px;color:#94A3B8;margin-left:3px;"></i>
                    </label>
                    <div class="cps-url-wrap">
                        <input type="text" class="cps-input" id="careerPageUrl"
                               value="{{ $careerUrl }}" readonly>
                        <button type="button" class="cps-copy-btn" onclick="copyCareerUrl()"><i class="ti ti-copy" style="font-size:13px;margin-right:3px;"></i> Copy</button>
                    </div>
                    <div class="cps-url-ok">
                        <i class="ti ti-circle-check-filled" style="font-size:12px;"></i>
                        URL is available
                    </div>
                    <div class="cps-hint">Customize your career page URL</div>
                </div>

                {{-- Career Page Status --}}
                <div>
                    <label class="cps-label">Career Page Status</label>
                    <div class="cps-toggle-row" style="margin-top:4px;">
                        <div class="cps-toggle-desc">Your career page is live and visible to candidates.</div>
                        <label class="cps-switch">
                            <input type="checkbox" name="career_page_status" value="1"
                                   {{ $isActive ? 'checked' : '' }}>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                </div>

                {{-- Default Language --}}
                <div>
                    <label class="cps-label">Default Language</label>
                    <select class="cps-select" name="default_language">
                        <option value="en"  {{ ($careerSettings->default_language ?? 'en') == 'en'  ? 'selected':'' }}>English</option>
                        <option value="hi"  {{ ($careerSettings->default_language ?? '')   == 'hi'  ? 'selected':'' }}>Hindi</option>
                        <option value="ar"  {{ ($careerSettings->default_language ?? '')   == 'ar'  ? 'selected':'' }}>Arabic</option>
                        <option value="fr"  {{ ($careerSettings->default_language ?? '')   == 'fr'  ? 'selected':'' }}>French</option>
                    </select>
                    <div class="cps-hint">Select the default language for your career page</div>
                </div>

                {{-- Currency --}}
                <div>
                    <label class="cps-label">Currency</label>
                    <select class="cps-select" name="currency">
                        <option value="INR" {{ ($careerSettings->currency ?? 'INR') == 'INR' ? 'selected':'' }}>INR (₹)</option>
                        <option value="USD" {{ ($careerSettings->currency ?? '')    == 'USD' ? 'selected':'' }}>USD ($)</option>
                        <option value="EUR" {{ ($careerSettings->currency ?? '')    == 'EUR' ? 'selected':'' }}>EUR (€)</option>
                        <option value="GBP" {{ ($careerSettings->currency ?? '')    == 'GBP' ? 'selected':'' }}>GBP (£)</option>
                    </select>
                    <div class="cps-hint">Select currency to display salary information</div>
                </div>

            </div>
        </div>

        {{-- Branding & Appearance --}}
        <div class="cps-section">
            <div class="cps-section-title">Branding & Appearance</div>
            <div class="cps-section-sub">Customize how your career page looks and represents your brand.</div>

            <div class="cps-grid2">

                {{-- Primary Color --}}
                <div>
                    <label class="cps-label">Primary Color</label>
                    <div class="cps-color-row">
                        <input type="color" class="cps-color-swatch" id="primaryPicker"
                               value="{{ $primaryColor }}"
                               oninput="document.getElementById('primaryText').value=this.value;updatePreview();">
                        <input type="text" class="cps-input" id="primaryText"
                               name="primary_color" value="{{ $primaryColor }}"
                               oninput="document.getElementById('primaryPicker').value=this.value;updatePreview();">
                    </div>
                </div>

                {{-- Secondary Color --}}
                <div>
                    <label class="cps-label">Secondary Color</label>
                    <div class="cps-color-row">
                        <input type="color" class="cps-color-swatch" id="secondaryPicker"
                               value="{{ $secondaryColor }}"
                               oninput="document.getElementById('secondaryText').value=this.value;">
                        <input type="text" class="cps-input" id="secondaryText"
                               name="secondary_color" value="{{ $secondaryColor }}"
                               oninput="document.getElementById('secondaryPicker').value=this.value;">
                    </div>
                </div>

                {{-- Font Family --}}
                <div>
                    <label class="cps-label">Font Family</label>
                    <select class="cps-select" name="font_family">
                        @foreach(['Poppins','Inter','Roboto','Open Sans','Montserrat'] as $f)
                        <option value="{{ $f }}" {{ $fontFamily == $f ? 'selected':'' }}>{{ $f }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Career Page Logo --}}
                <div>
                    <label class="cps-label">Career Page Logo</label>
                    <div class="cps-logo-row">
                        @if(!empty($company->logo))
                            <img src="{{ $company->logo }}" alt="Logo" class="cps-logo-img" id="logoPreviewImg">
                        @else
                            <div class="cps-logo-placeholder" id="logoPreviewImg">No Logo</div>
                        @endif
                        <div style="flex:1;min-width:0;">
                            <div class="cps-logo-name">{{ $company->logo ? basename($company->logo) : 'No logo uploaded' }}</div>
                            <div class="cps-logo-meta">PNG · 120 x 40 px</div>
                        </div>
                        <label class="cps-change-btn" style="cursor:pointer;">
                            Change
                            <input type="file" name="career_logo" accept="image/*"
                                   style="display:none;" onchange="previewLogo(this)">
                        </label>
                    </div>
                    <div class="cps-hint">Recommended: 120x40px, PNG with transparent background</div>
                </div>

            </div>
        </div>

        {{-- Layout Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">Layout Settings</div>
            <div class="cps-section-sub">Choose layout and display preferences for your career page.</div>

            <div class="cps-grid2">

                {{-- Page Layout --}}
                <div>
                    <label class="cps-label">Page Layout</label>
                    <div class="cps-layout-grid">
                        @foreach(['modern'=>['Modern','Best for engagement'],'classic'=>['Classic','Clean and professional'],'minimal'=>['Minimal','Simple and focused']] as $k=>$v)
                        <div class="cps-layout-card {{ $pageLayout==$k?'selected':'' }}"
                             onclick="selectLayout('{{ $k }}',this)">
                            <div class="cps-lc-check">
                                <i class="ti ti-check" style="font-size:9px;color:#fff;"></i>
                            </div>
                            <div class="cps-lc-icon">
                                <i class="ti ti-layout-grid" style="font-size:15px;color:#64748B;"></i>
                            </div>
                            <div class="cps-lc-name">{{ $v[0] }}</div>
                            <div class="cps-lc-desc">{{ $v[1] }}</div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="page_layout" id="pageLayoutInput" value="{{ $pageLayout }}">
                </div>

                {{-- Jobs Display Style --}}
                <div>
                    <label class="cps-label">Jobs Display Style</label>
                    <div class="cps-display-grid">
                        <div class="cps-display-card {{ $jobsDisplay=='card'?'selected':'' }}"
                             onclick="selectDisplay('card',this)">
                            <i class="ti ti-layout-grid" style="font-size:22px;color:#2563EB;"></i>
                            <div>
                                <div class="cps-dc-name">Card View</div>
                                <div class="cps-dc-desc">Best visual experience</div>
                            </div>
                        </div>
                        <div class="cps-display-card {{ $jobsDisplay=='list'?'selected':'' }}"
                             onclick="selectDisplay('list',this)">
                            <i class="ti ti-list" style="font-size:22px;color:#64748B;"></i>
                            <div>
                                <div class="cps-dc-name">List View</div>
                                <div class="cps-dc-desc">Compact and detailed</div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="jobs_display" id="jobsDisplayInput" value="{{ $jobsDisplay }}">
                </div>

            </div>
        </div>

    </div>

    {{-- Right: Sidebar --}}
    <div class="col-lg-4">

        {{-- Live Preview --}}
        <div class="cps-sidebar-card">
            <h4>Live Preview</h4>
            <p class="sub">See how your career page looks to candidates</p>
            <div class="cps-preview-box">
                <div class="cps-prev-hdr" id="previewHeader"
                     style="background:linear-gradient(135deg,{{ $primaryColor }},#7C3AED);">
                    <div class="cps-prev-hdr-title">Build the Future with</div>
                    <div class="cps-prev-hdr-name">{{ $companyName }}</div>
                    <div class="cps-prev-search">🔍 Search by skills, position or titles...</div>
                </div>
                <div class="cps-prev-body">
                    <div class="cps-prev-stats">
                        <div><div class="cps-prev-stat-n">32</div><div class="cps-prev-stat-l">Open Positions</div></div>
                        <div><div class="cps-prev-stat-n">15</div><div class="cps-prev-stat-l">Departments</div></div>
                        <div><div class="cps-prev-stat-n">220+</div><div class="cps-prev-stat-l">Employees</div></div>
                    </div>
                    <div class="cps-prev-positions">Our Open Positions</div>
                    <div class="cps-prev-job">
                        <div class="cps-prev-job-t">Senior Developer</div>
                        <div class="cps-prev-job-s">Frontend</div>
                    </div>
                    <div class="cps-prev-job">
                        <div class="cps-prev-job-t">UI/UX Designer</div>
                        <div class="cps-prev-job-s">Design</div>
                    </div>
                    <div class="cps-prev-job">
                        <div class="cps-prev-job-t">Product Manager</div>
                        <div class="cps-prev-job-s">Product</div>
                    </div>
                    <div class="cps-prev-footer">
                        <a href="{{ $careerUrl }}" target="_blank">
                            Preview Full Page <i class="ti ti-external-link" style="font-size:10px;"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Actions --}}
        <div class="cps-sidebar-card">
            <h4>Quick Actions</h4>
            @php
            $qaItems = [
                ['icon'=>'ti-photo',     'bg'=>'#EFF6FF','ic'=>'#2563EB','title'=>'Upload Company Logo',   'desc'=>'Recommended size: 120x40px'],
                ['icon'=>'ti-camera',    'bg'=>'#FFF7ED','ic'=>'#F97316','title'=>'Update Company Images', 'desc'=>'Add photos for gallery section'],
                ['icon'=>'ti-file-text', 'bg'=>'#F0FDF4','ic'=>'#16A34A','title'=>'Manage Custom Pages',  'desc'=>'Add custom content pages'],
                ['icon'=>'ti-mail',      'bg'=>'#FFF1F2','ic'=>'#E11D48','title'=>'Email Templates',      'desc'=>'Customize career related emails'],
            ];
            @endphp
            @foreach($qaItems as $qa)
            <div class="cps-qa">
                <div class="cps-qa-left">
                    <div class="cps-qa-icon" style="background:{{ $qa['bg'] }};">
                        <i class="ti {{ $qa['icon'] }}" style="font-size:16px;color:{{ $qa['ic'] }};"></i>
                    </div>
                    <div>
                        <div class="cps-qa-title">{{ $qa['title'] }}</div>
                        <div class="cps-qa-desc">{{ $qa['desc'] }}</div>
                    </div>
                </div>
                <i class="ti ti-chevron-right" style="font-size:14px;color:#CBD5E1;"></i>
            </div>
            @endforeach
        </div>

        {{-- Save Changes --}}
        <div class="cps-sidebar-card">
            <h4>Save Changes</h4>
            <div style="font-size:11px;color:#94A3B8;margin-bottom:12px;">Don't forget to save your changes</div>
            <button type="submit" class="cps-save-btn">
                <i class="ti ti-device-floppy" style="font-size:16px;"></i> Save All Changes
            </button>
        </div>

    </div>
</div>
</div>
