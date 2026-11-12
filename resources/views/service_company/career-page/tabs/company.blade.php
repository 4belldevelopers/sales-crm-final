{{-- Company Info Tab Panel (Rule 20 Card + Sub-Card System) --}}
<div class="cps-panel" id="panel-company">
    <div class="cps-workspace-grid">

        {{-- Left Main Column --}}
        <div class="cps-col-main">

            {{-- Primary Card 1: Organization Identity & Metadata --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-building-skyscraper"></i></div>
                        <div>
                            <div class="cps-card-title">Organization Identity</div>
                            <div class="cps-card-sub">Core profile details displayed on your public career portal.</div>
                        </div>
                    </div>
                    <span class="cps-card-pill blue">{{ $careerSettings->ci_industry ?? 'Industry Unset' }}</span>
                </div>

                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        {{-- Logo Sub-Card --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Brand Mark</span><span>300×300</span></div>
                            <label class="cps-label">Company Logo</label>
                            <div class="ci-logo-wrap">
                                <div class="ci-logo-placeholder" id="ciLogoBox">
                                    @if(!empty($careerSettings->td_logo_path))
                                        <img src="{{ asset($careerSettings->td_logo_path) }}" class="ci-logo-img" style="width:54px;height:54px;">
                                    @else
                                        <i class="ti ti-building" style="font-size:22px;color:#009ef7;"></i>
                                    @endif
                                </div>
                                <div style="flex:1;min-width:0;">
                                    <div style="font-size:11.5px;font-weight:600;color:#334155;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" id="ciLogoName">
                                        {{ !empty($careerSettings->td_logo_path) ? basename($careerSettings->td_logo_path) : 'No logo uploaded' }}
                                    </div>
                                    <div class="ci-logo-meta">PNG, SVG · Max 2MB</div>
                                    <div class="ci-logo-actions">
                                        <label class="ci-change-btn" style="cursor:pointer;">
                                            Change
                                            <input type="file" name="ci_logo" accept="image/*" style="display:none;" onchange="ciPreviewLogo(this)">
                                        </label>
                                        <button type="button" class="ci-del-btn" onclick="ciDelLogo()" title="Reset logo">
                                            <i class="ti ti-trash" style="font-size:12px;"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Industry + Name --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Classification</span></div>
                            <label class="cps-label">Industry</label>
                            <select class="ci-select" name="ci_industry" style="margin-bottom:8px;">
                                <option value="">Select Industry</option>
                                @foreach(['Information Technology & Services','Software Development','Staffing & Recruiting','Human Resources','Financial Services','Healthcare','Education','E-Commerce','Manufacturing','Consulting'] as $ind)
                                    <option value="{{ $ind }}" {{ ($careerSettings->ci_industry ?? '') === $ind ? 'selected' : '' }}>{{ $ind }}</option>
                                @endforeach
                            </select>
                            <label class="cps-label">Company Name <span style="color:#ef4444;">*</span></label>
                            <input type="text" class="ci-input" name="ci_company_name"
                                   value="{{ $careerSettings->ci_company_name ?? $companyName }}" placeholder="Enter company name">
                        </div>

                        {{-- Headquarters --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Location</span></div>
                            <label class="cps-label">Headquarters</label>
                            <input type="text" class="ci-input" name="ci_headquarters"
                                   value="{{ $careerSettings->ci_headquarters ?? '' }}"
                                   placeholder="e.g. Bengaluru, Karnataka, India">
                        </div>

                        {{-- Founded Year + Size --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Scale &amp; Heritage</span></div>
                            <div class="cps-subcard-grid-2">
                                <div>
                                    <label class="cps-label">Founded Year</label>
                                    <input type="number" class="ci-input cps-num" name="ci_founded_year"
                                           value="{{ $careerSettings->ci_founded_year ?? '' }}"
                                           min="1900" max="2099" placeholder="2018">
                                </div>
                                <div>
                                    <label class="cps-label">Company Size</label>
                                    <select class="ci-select" name="ci_company_size">
                                        @foreach(['1 - 10 Employees','11 - 50 Employees','51 - 200 Employees','201 - 500 Employees','501 - 1000 Employees','1001 - 5000 Employees','5001+ Employees'] as $sz)
                                            <option value="{{ $sz }}" {{ ($careerSettings->ci_company_size ?? '') === $sz ? 'selected' : '' }}>{{ $sz }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Tagline --}}
                        <div class="cps-subcard span-2">
                            <div class="cps-subcard-kicker">
                                <span>Brand Tagline</span>
                                <span class="ci-char-count" id="ciTaglineCount">{{ strlen($careerSettings->ci_tagline ?? '') }}/100</span>
                            </div>
                            <label class="cps-label">Tagline / Short Description <span style="color:#ef4444;">*</span></label>
                            <textarea class="ci-textarea" name="ci_tagline" maxlength="100" rows="2" id="ciTagline"
                                      placeholder="e.g. Building the future of talent and technology."
                                      oninput="document.getElementById('ciTaglineCount').textContent=this.value.length+'/100';">{{ $careerSettings->ci_tagline ?? '' }}</textarea>
                        </div>

                        {{-- Website, LinkedIn, X --}}
                        <div class="cps-subcard span-2">
                            <div class="cps-subcard-kicker"><span>Digital Presence</span></div>
                            <div class="cps-subcard-grid-3">
                                <div>
                                    <label class="cps-label">Website</label>
                                    <div class="ci-input-icon">
                                        <i class="ti ti-link ci-icon"></i>
                                        <input type="url" class="ci-input" name="ci_website" value="{{ $careerSettings->ci_website ?? '' }}" placeholder="https://www.company.com">
                                    </div>
                                </div>
                                <div>
                                    <label class="cps-label">LinkedIn Page</label>
                                    <div class="ci-input-icon">
                                        <i class="ti ti-brand-linkedin ci-icon" style="color:#0077b5;"></i>
                                        <input type="url" class="ci-input" name="ci_linkedin" value="{{ $careerSettings->ci_linkedin ?? '' }}" placeholder="https://linkedin.com/company/...">
                                    </div>
                                </div>
                                <div>
                                    <label class="cps-label">X (Twitter)</label>
                                    <div class="ci-input-icon">
                                        <i class="ti ti-brand-x ci-icon"></i>
                                        <input type="url" class="ci-input" name="ci_twitter" value="{{ $careerSettings->ci_twitter ?? '' }}" placeholder="https://x.com/handle">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 2: Narrative, Mission & Vision --}}
            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-file-description"></i></div>
                        <div>
                            <div class="cps-card-title">Company Narrative, Mission &amp; Vision</div>
                            <div class="cps-card-sub">Tell candidates who you are and the impact they will create.</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="cps-subcard" style="margin-bottom:10px;">
                        <div class="cps-subcard-kicker">
                            <span>About Us</span>
                            <span class="ci-char-count" id="ciAboutCount">{{ strlen($careerSettings->ci_about ?? '') }}/2000</span>
                        </div>
                        <div class="ci-rich-toolbar">
                            <button type="button" class="ci-rich-btn" title="Bold"><b>B</b></button>
                            <button type="button" class="ci-rich-btn" title="Italic"><i>I</i></button>
                            <button type="button" class="ci-rich-btn" title="Underline"><u>U</u></button>
                            <div class="ci-rich-sep"></div>
                            <button type="button" class="ci-rich-btn" title="Bullet list"><i class="ti ti-list"></i></button>
                            <button type="button" class="ci-rich-btn" title="Ordered list"><i class="ti ti-list-numbers"></i></button>
                            <div class="ci-rich-sep"></div>
                            <button type="button" class="ci-rich-btn" title="Link"><i class="ti ti-link"></i></button>
                        </div>
                        <textarea class="ci-rich-textarea" name="ci_about" maxlength="2000" id="ciAbout"
                                  oninput="document.getElementById('ciAboutCount').textContent=this.value.length+'/2000';"
                                  placeholder="Describe your company culture, product mission, and engineering standards...">{{ $careerSettings->ci_about ?? '' }}</textarea>
                    </div>

                    <div class="cps-subcard-grid-2">
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker">
                                <span>Mission</span>
                                <span class="ci-char-count" id="ciMissionCount">{{ strlen($careerSettings->ci_mission ?? '') }}/200</span>
                            </div>
                            <textarea class="ci-textarea" name="ci_mission" maxlength="200" id="ciMission" rows="3"
                                      placeholder="e.g. Empower organizations to build high-trust teams."
                                      oninput="document.getElementById('ciMissionCount').textContent=this.value.length+'/200';">{{ $careerSettings->ci_mission ?? '' }}</textarea>
                        </div>
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker">
                                <span>Vision</span>
                                <span class="ci-char-count" id="ciVisionCount">{{ strlen($careerSettings->ci_vision ?? '') }}/200</span>
                            </div>
                            <textarea class="ci-textarea" name="ci_vision" maxlength="200" id="ciVision" rows="3"
                                      placeholder="e.g. Set the global benchmark for candidate experience."
                                      oninput="document.getElementById('ciVisionCount').textContent=this.value.length+'/200';">{{ $careerSettings->ci_vision ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Sticky Preview & Highlights Column --}}
        <div class="cps-col-side sticky-side">

            {{-- Company Profile Preview --}}
            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-id-badge-2"></i></div>
                        <div>
                            <div class="cps-card-title">Company Card Preview</div>
                            <div class="cps-card-sub">Live candidate-facing employer profile</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="ci-prev-card">
                        <div class="ci-prev-banner">
                            <div class="ci-prev-logo-circle">
                                @if(!empty($careerSettings->td_logo_path))
                                    <img src="{{ asset($careerSettings->td_logo_path) }}" style="width:100%;height:100%;object-fit:contain;">
                                @else
                                    <i class="ti ti-building" style="font-size:20px;color:#009ef7;" id="ciPrevLogoIcon"></i>
                                @endif
                            </div>
                        </div>
                        <div class="ci-prev-body">
                            <div class="ci-prev-name">
                                <span id="ciPrevName">{{ $careerSettings->ci_company_name ?? $companyName }}</span>
                                <i class="ti ti-circle-check-filled" style="font-size:14px;color:#009ef7;"></i>
                            </div>
                            <div class="ci-prev-tagline" id="ciPrevTagline">
                                {{ $careerSettings->ci_tagline ?? 'Building high-performing teams with technology and trust.' }}
                            </div>
                            <div class="ci-prev-meta">
                                <div class="ci-prev-meta-item"><i class="ti ti-map-pin"></i> <span id="ciPrevHq">{{ $careerSettings->ci_headquarters ?? 'Global Headquarters' }}</span></div>
                                <div class="ci-prev-meta-item"><i class="ti ti-users"></i> <span id="ciPrevSize">{{ $careerSettings->ci_company_size ?? '201 - 500 Employees' }}</span></div>
                                <div class="ci-prev-meta-item"><i class="ti ti-calendar"></i> <span id="ciPrevYear">Founded {{ $careerSettings->ci_founded_year ?? '2020' }}</span></div>
                            </div>
                            <div class="ci-prev-social">
                                <span class="ci-prev-meta-item" style="font-size:10px;color:#009ef7;">
                                    <i class="ti ti-world"></i> <span id="ciPrevWeb">{{ $companyHost }}</span>
                                </span>
                                <i class="ti ti-brand-linkedin" style="font-size:14px;color:#0077b5;"></i>
                                <i class="ti ti-brand-x" style="font-size:14px;color:#0f172a;"></i>
                            </div>
                            <div class="ci-prev-about-title">About Us</div>
                            <div class="ci-prev-about-text" id="ciPrevAbout">
                                {{ !empty($careerSettings->ci_about) ? \Illuminate\Support\Str::limit($careerSettings->ci_about, 180) : $companyName . ' connects exceptional talent with high-impact opportunities through transparent hiring and strong engineering culture.' }}
                            </div>
                            <div class="ci-prev-stats">
                                <div><div class="ci-prev-stat-n" id="ciPrevStatYear">{{ $careerSettings->ci_founded_year ?? '2020' }}</div><div class="ci-prev-stat-l">Founded</div></div>
                                <div><div class="ci-prev-stat-n">{{ $careerSettings->stat_countries ?? 5 }}+</div><div class="ci-prev-stat-l">Countries</div></div>
                                <div><div class="ci-prev-stat-n">{{ $careerSettings->stat_employees ?? '220+' }}</div><div class="ci-prev-stat-l">Employees</div></div>
                                <div><div class="ci-prev-stat-n">{{ $careerSettings->stat_departments ?? 15 }}</div><div class="ci-prev-stat-l">Teams</div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Highlights Sub-Cards --}}
            <div class="cps-card accent-emerald">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon emerald"><i class="ti ti-chart-bar"></i></div>
                        <div>
                            <div class="cps-card-title">Credibility Highlights</div>
                            <div class="cps-card-sub">Key proof points shown to candidates</div>
                        </div>
                    </div>
                    <button type="button" class="cps-btn-secondary" style="height:28px;font-size:11px;" onclick="ciAddHighlight()">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <div class="cps-card-body">
                    <div id="ciHighlightsList">
                        @php
                        $ciHls = [
                            ['icon'=>'ti-users',      'bg'=>'#eff6ff','ic'=>'#009ef7','label'=>'Employees',        'val'=> $careerSettings->stat_employees ?? '500+'],
                            ['icon'=>'ti-world',      'bg'=>'#ecfdf5','ic'=>'#059669','label'=>'Countries',        'val'=> ($careerSettings->stat_countries ?? 5) . '+'],
                            ['icon'=>'ti-building',   'bg'=>'#fff7ed','ic'=>'#f97316','label'=>'Departments',      'val'=> $careerSettings->stat_departments ?? 15],
                            ['icon'=>'ti-award',      'bg'=>'#f5f3ff','ic'=>'#7c3aed','label'=>'Retention Rate',   'val'=> $careerSettings->stat_retention ?? '94%'],
                        ];
                        @endphp
                        @foreach($ciHls as $hl)
                            <div class="ci-hl-row">
                                <div class="ci-hl-left">
                                    <div class="ci-hl-icon" style="background:{{ $hl['bg'] }};">
                                        <i class="ti {{ $hl['icon'] }}" style="font-size:15px;color:{{ $hl['ic'] }};"></i>
                                    </div>
                                    <div class="ci-hl-label">{{ $hl['label'] }}</div>
                                </div>
                                <div style="display:flex;align-items:center;gap:8px;">
                                    <div class="ci-hl-val">{{ $hl['val'] }}</div>
                                    <button type="button" class="ci-hl-del" onclick="this.closest('.ci-hl-row').remove()">
                                        <i class="ti ti-trash" style="font-size:12px;"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
