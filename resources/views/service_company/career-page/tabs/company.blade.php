{{-- Company Info Tab Panel --}}
<div class="cps-panel" id="panel-company">
<div class="row g-4">

    {{-- Left: Controls --}}
    <div class="col-lg-7">

        {{-- Company Information --}}
        <div class="cps-section">
            <div class="cps-section-title">Company Information</div>
            <div class="cps-section-sub">Add your company details that will be displayed on the career page.</div>

            {{-- Logo + Industry row --}}
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="cps-label">Company Logo <i class="ti ti-info-circle" style="font-size:11px;color:#94A3B8;"></i></label>
                    <div class="ci-logo-wrap">
                        <div class="ci-logo-placeholder" id="ciLogoBox">
                            <i class="ti ti-building" style="font-size:24px;color:#3B82F6;"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:12px;font-weight:600;color:#374151;" id="ciLogoName">No logo uploaded</div>
                            <div class="ci-logo-meta">PNG · 300 × 300px</div>
                            <div class="ci-logo-actions">
                                <label class="ci-change-btn" style="cursor:pointer;">
                                    Change
                                    <input type="file" name="ci_logo" accept="image/*" style="display:none;" onchange="ciPreviewLogo(this)">
                                </label>
                                <button type="button" class="ci-del-btn" onclick="ciDelLogo()">
                                    <i class="ti ti-trash" style="font-size:13px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <label class="cps-label">Industry</label>
                    <select class="ci-select" name="ci_industry">
                        <option value="">Select Industry</option>
                        @foreach(['Information Technology & Services','Software Development','Staffing & Recruiting','Human Resources','Financial Services','Healthcare','Education','E-Commerce','Manufacturing','Consulting'] as $ind)
                        <option value="{{ $ind }}">{{ $ind }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Company Name --}}
            <div class="mb-3">
                <label class="cps-label">Company Name <span style="color:#EF4444;">*</span></label>
                <input type="text" class="ci-input" name="ci_company_name"
                       value="{{ $companyName }}" placeholder="Enter company name">
            </div>

            {{-- Headquarters --}}
            <div class="mb-3">
                <label class="cps-label">Headquarters</label>
                <input type="text" class="ci-input" name="ci_headquarters"
                       placeholder="e.g. Gurugram, Haryana, India">
            </div>

            {{-- Tagline --}}
            <div class="mb-3">
                <label class="cps-label">Tagline / Short Description <span style="color:#EF4444;">*</span></label>
                <textarea class="ci-textarea" name="ci_tagline" maxlength="100" rows="2"
                          id="ciTagline" placeholder="e.g. Building the future of recruitment with technology and trust."
                          oninput="document.getElementById('ciTaglineCount').textContent=this.value.length+'/100';"></textarea>
                <div style="display:flex;justify-content:flex-end;">
                    <span class="ci-char-count" id="ciTaglineCount">0/100</span>
                </div>
            </div>

            {{-- Website + LinkedIn --}}
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="cps-label">Company Website</label>
                    <div class="ci-input-icon">
                        <i class="ti ti-link ci-icon"></i>
                        <input type="url" class="ci-input" name="ci_website" placeholder="https://www.yoursite.com">
                        <i class="ti ti-external-link ci-icon-right" style="font-size:13px;"></i>
                    </div>
                </div>
                <div class="col-6">
                    <label class="cps-label">LinkedIn Page</label>
                    <div class="ci-input-icon">
                        <i class="ti ti-brand-linkedin ci-icon" style="color:#0077B5;"></i>
                        <input type="url" class="ci-input" name="ci_linkedin" placeholder="https://linkedin.com/company/...">
                        <i class="ti ti-brand-linkedin ci-icon-right" style="color:#0077B5;font-size:13px;"></i>
                    </div>
                </div>
            </div>

            {{-- Founded Year + Company Size --}}
            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="cps-label">Founded Year</label>
                    <input type="number" class="ci-input" name="ci_founded_year"
                           min="1900" max="2099" placeholder="e.g. 2016">
                </div>
                <div class="col-6">
                    <label class="cps-label">Company Size</label>
                    <select class="ci-select" name="ci_company_size">
                        @foreach(['1 - 10 Employees','11 - 50 Employees','51 - 200 Employees','201 - 500 Employees','501 - 1000 Employees','1001 - 5000 Employees','5001+ Employees'] as $sz)
                        <option value="{{ $sz }}">{{ $sz }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- X (Twitter) --}}
            <div>
                <label class="cps-label">X (Twitter) Handle <span style="font-size:10px;color:#94A3B8;font-weight:400;">(Optional)</span></label>
                <div class="ci-input-icon">
                    <i class="ti ti-brand-x ci-icon"></i>
                    <input type="url" class="ci-input" name="ci_twitter" placeholder="https://x.com/yourhandle">
                    <i class="ti ti-brand-x ci-icon-right" style="font-size:13px;"></i>
                </div>
            </div>
        </div>

        {{-- Company Description --}}
        <div class="cps-section">
            <div class="cps-section-title">Company Description</div>
            <div class="cps-section-sub">Tell candidates about your company, mission, vision and what makes you unique.</div>

            {{-- About Us --}}
            <div class="mb-3">
                <label class="cps-label">About Us <span style="color:#EF4444;">*</span></label>
                <div class="ci-rich-toolbar">
                    <select style="height:26px;border:1px solid #E2E8F0;border-radius:5px;font-size:11px;color:#374151;background:#fff;padding:0 6px;cursor:pointer;">
                        <option>Paragraph</option><option>Heading 1</option><option>Heading 2</option>
                    </select>
                    <div class="ci-rich-sep"></div>
                    <button type="button" class="ci-rich-btn" title="Bold"><b>B</b></button>
                    <button type="button" class="ci-rich-btn" title="Italic"><i>I</i></button>
                    <button type="button" class="ci-rich-btn" title="Underline"><u>U</u></button>
                    <div class="ci-rich-sep"></div>
                    <button type="button" class="ci-rich-btn" title="Bullet list"><i class="ti ti-list" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Ordered list"><i class="ti ti-list-numbers" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Outdent"><i class="ti ti-indent-decrease" style="font-size:13px;"></i></button>
                    <div class="ci-rich-sep"></div>
                    <button type="button" class="ci-rich-btn" title="Align left"><i class="ti ti-align-left" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Align center"><i class="ti ti-align-center" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Align right"><i class="ti ti-align-right" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Justify"><i class="ti ti-align-justified" style="font-size:13px;"></i></button>
                    <div class="ci-rich-sep"></div>
                    <button type="button" class="ci-rich-btn" title="Link"><i class="ti ti-link" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Image"><i class="ti ti-photo" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Table"><i class="ti ti-table" style="font-size:13px;"></i></button>
                    <div class="ci-rich-sep"></div>
                    <button type="button" class="ci-rich-btn" title="Undo"><i class="ti ti-arrow-back-up" style="font-size:13px;"></i></button>
                    <button type="button" class="ci-rich-btn" title="Redo"><i class="ti ti-arrow-forward-up" style="font-size:13px;"></i></button>
                </div>
                <textarea class="ci-rich-textarea" name="ci_about" maxlength="2000" id="ciAbout"
                          oninput="document.getElementById('ciAboutCount').textContent=this.value.length+'/2000';"
                          placeholder="Describe your company..."></textarea>
                <div style="display:flex;justify-content:flex-end;">
                    <span class="ci-char-count" id="ciAboutCount">0/2000</span>
                </div>
            </div>

            {{-- Mission + Vision --}}
            <div class="row g-3">
                <div class="col-6">
                    <label class="cps-label">Mission</label>
                    <textarea class="ci-textarea" name="ci_mission" maxlength="200" id="ciMission" rows="3"
                              placeholder="e.g. To simplify recruitment and empower organizations to build great teams."
                              oninput="document.getElementById('ciMissionCount').textContent=this.value.length+'/200';"></textarea>
                    <div style="display:flex;justify-content:flex-end;">
                        <span class="ci-char-count" id="ciMissionCount">0/200</span>
                    </div>
                </div>
                <div class="col-6">
                    <label class="cps-label">Vision</label>
                    <textarea class="ci-textarea" name="ci_vision" maxlength="200" id="ciVision" rows="3"
                              placeholder="e.g. To be the world's most trusted recruitment technology platform."
                              oninput="document.getElementById('ciVisionCount').textContent=this.value.length+'/200';"></textarea>
                    <div style="display:flex;justify-content:flex-end;">
                        <span class="ci-char-count" id="ciVisionCount">0/200</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- Right: Preview + Highlights --}}
    <div class="col-lg-5">

        {{-- Company Preview --}}
        <div class="cps-sidebar-card" style="padding:0;overflow:hidden;">
            <div style="padding:14px 18px 12px;border-bottom:1px solid #F1F5F9;">
                <h4 style="margin:0 0 2px;">Company Preview</h4>
                <p class="sub" style="margin:0;">See how your company information will appear on the career page.</p>
            </div>
            <div class="ci-prev-card" style="border:none;border-radius:0;">
                <div class="ci-prev-banner">
                    <div class="ci-prev-logo-circle">
                        <i class="ti ti-building" style="font-size:22px;color:#3B82F6;" id="ciPrevLogoIcon"></i>
                    </div>
                </div>
                <div class="ci-prev-body">
                    <div class="ci-prev-name">
                        {{ $companyName }}
                        <i class="ti ti-circle-check-filled" style="font-size:14px;color:#2563EB;"></i>
                    </div>
                    <div class="ci-prev-tagline">Building the future of recruitment with technology and trust.</div>
                    <div class="ci-prev-meta">
                        <div class="ci-prev-meta-item"><i class="ti ti-map-pin" style="font-size:11px;"></i> Gurugram, Haryana, India</div>
                        <div class="ci-prev-meta-item"><i class="ti ti-users" style="font-size:11px;"></i> 201 – 500 Employees</div>
                        <div class="ci-prev-meta-item"><i class="ti ti-calendar" style="font-size:11px;"></i> Founded 2016</div>
                    </div>
                    <div class="ci-prev-social">
                        <span class="ci-prev-meta-item" style="font-size:9px;color:#2563EB;">
                            <i class="ti ti-world" style="font-size:10px;"></i> www.4bell.com
                        </span>
                        <i class="ti ti-brand-linkedin" style="font-size:14px;color:#0077B5;"></i>
                        <i class="ti ti-brand-x" style="font-size:14px;color:#0F172A;"></i>
                    </div>
                    <div class="ci-prev-about-title">About Us</div>
                    <div class="ci-prev-about-text">
                        {{ $companyName }} is a recruitment technology company on a mission to simplify hiring for businesses and create meaningful career opportunities for talent across the globe.<br><br>
                        Our AI-powered platform connects the right people with the right opportunities – faster, smarter and more efficiently.
                    </div>
                    <div class="ci-prev-stats">
                        <div>
                            <div class="ci-prev-stat-n">2016</div>
                            <div class="ci-prev-stat-l">Founded</div>
                        </div>
                        <div>
                            <div class="ci-prev-stat-n">5+</div>
                            <div class="ci-prev-stat-l">Countries</div>
                        </div>
                        <div>
                            <div class="ci-prev-stat-n">500+</div>
                            <div class="ci-prev-stat-l">Employees</div>
                        </div>
                        <div>
                            <div class="ci-prev-stat-n">1000+</div>
                            <div class="ci-prev-stat-l">Clients</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Company Details (Highlights) --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <h4 style="margin:0 0 3px;">Company Details (Highlights)</h4>
            <p class="sub">Add key highlights to build trust and credibility.</p>
            <div id="ciHighlightsList">
                @php
                $ciHls = [
                    ['icon'=>'ti-users',   'bg'=>'#EFF6FF','ic'=>'#2563EB','label'=>'Employees',        'val'=>'500+'],
                    ['icon'=>'ti-world',   'bg'=>'#F0FDF4','ic'=>'#16A34A','label'=>'Countries',         'val'=>'5+'],
                    ['icon'=>'ti-users-group','bg'=>'#FFF7ED','ic'=>'#F97316','label'=>'Clients',       'val'=>'1000+'],
                    ['icon'=>'ti-calendar','bg'=>'#FAF5FF','ic'=>'#7C3AED','label'=>'Years in Business','val'=>'8+'],
                ];
                @endphp
                @foreach($ciHls as $hl)
                <div class="ci-hl-row">
                    <div class="ci-hl-left">
                        <div class="ci-hl-icon" style="background:{{ $hl['bg'] }};">
                            <i class="ti {{ $hl['icon'] }}" style="font-size:15px;color:{{ $hl['ic'] }};"></i>
                        </div>
                        <div>
                            <div class="ci-hl-label">{{ $hl['label'] }}</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div class="ci-hl-val">{{ $hl['val'] }}</div>
                        <button type="button" class="ci-hl-del" onclick="this.closest('.ci-hl-row').remove()">
                            <i class="ti ti-trash" style="font-size:12px;"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="ci-add-hl" onclick="ciAddHighlight()">
                <i class="ti ti-plus" style="font-size:13px;"></i> Add Highlight
            </button>
        </div>

    </div>
</div>

{{-- Bottom action bar --}}
<div class="hs-action-bar">
    <button type="button" class="hs-reset-btn">
        <i class="ti ti-refresh" style="font-size:14px;margin-right:4px;"></i> Reset to Default
    </button>
    <button type="submit" form="careerSettingsForm" class="hs-save-btn">
        <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save Changes
    </button>
</div>
</div>
