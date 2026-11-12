{{-- Templates Design Tab Panel (Rule 20 Card + Sub-Card System) --}}
<div class="cps-panel" id="panel-templates">
    <div class="cps-workspace-grid">

        {{-- Left Main Column --}}
        <div class="cps-col-main">

            {{-- Primary Card 1: Choose Template --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-layout-grid-add"></i></div>
                        <div>
                            <div class="cps-card-title">Template Gallery</div>
                            <div class="cps-card-sub">Select a curated enterprise theme preset; color palettes sync automatically.</div>
                        </div>
                    </div>
                    <span class="cps-card-pill blue">{{ ucfirst($careerSettings->td_template ?? 'modern') }} Active</span>
                </div>

                <div class="cps-card-body">
                    <div class="td-tpl-grid" id="tdTemplateGrid">
                        @php
                        $savedTpl = $careerSettings->td_template ?? 'modern';
                        $tdTemplates = [
                            ['key'=>'modern',    'name'=>'Modern',    'desc'=>'Clean & crisp',    'from'=>'#1e3a8a','to'=>'#009ef7'],
                            ['key'=>'corporate', 'name'=>'Corporate', 'desc'=>'Executive formal', 'from'=>'#1f2937','to'=>'#374151'],
                            ['key'=>'creative',  'name'=>'Creative',  'desc'=>'Bold & vibrant',   'from'=>'#7c3aed','to'=>'#ec4899'],
                            ['key'=>'minimal',   'name'=>'Minimal',   'desc'=>'Light editorial',  'from'=>'#cbd5e1','to'=>'#94a3b8'],
                            ['key'=>'tech',      'name'=>'Tech',      'desc'=>'Dark engineering', 'from'=>'#0f172a','to'=>'#1e293b'],
                        ];
                        @endphp
                        @foreach($tdTemplates as $tt)
                            <div class="td-tpl-card {{ $tt['key'] === $savedTpl ? 'selected' : '' }}" onclick="tdSelectTemplate('{{ $tt['key'] }}',this)">
                                <div class="td-tpl-check"><i class="ti ti-check" style="font-size:10px;color:#fff;"></i></div>
                                <div class="td-tpl-thumb" style="background:linear-gradient(135deg,{{ $tt['from'] }},{{ $tt['to'] }});">
                                    <div style="padding:9px 8px;">
                                        <div style="height:4px;background:rgba(255,255,255,0.85);border-radius:3px;width:62%;margin-bottom:4px;"></div>
                                        <div style="height:3px;background:rgba(255,255,255,0.45);border-radius:3px;width:82%;margin-bottom:3px;"></div>
                                        <div style="height:12px;background:rgba(255,255,255,0.92);border-radius:3px;width:48%;margin-top:6px;"></div>
                                    </div>
                                </div>
                                <div class="td-tpl-info">
                                    <div class="td-tpl-name">{{ $tt['name'] }}</div>
                                    <div class="td-tpl-desc">{{ $tt['desc'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="td_template" id="tdTemplateInput" value="{{ $savedTpl }}">
                </div>
            </div>

            {{-- Primary Card 2: Design System Customizer --}}
            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-palette"></i></div>
                        <div>
                            <div class="cps-card-title">Design Token Customizer</div>
                            <div class="cps-card-sub">Fine-tune palette tokens, theme mode, container width, and custom CSS.</div>
                        </div>
                    </div>
                </div>

                <div class="cps-card-body">
                    <div class="td-design-wrap">
                        <div class="td-design-nav">
                            @php
                            $tdDesignTabs = [
                                ['key'=>'colors',   'icon'=>'ti-palette',          'label'=>'Colors & Layout'],
                                ['key'=>'typo',     'icon'=>'ti-letter-t',         'label'=>'Typography'],
                                ['key'=>'header',   'icon'=>'ti-layout-navbar',    'label'=>'Navigation'],
                                ['key'=>'buttons',  'icon'=>'ti-click',            'label'=>'Buttons'],
                                ['key'=>'sections', 'icon'=>'ti-layout',           'label'=>'Sections'],
                                ['key'=>'css',      'icon'=>'ti-code',             'label'=>'Custom CSS'],
                            ];
                            @endphp
                            @foreach($tdDesignTabs as $dt)
                                <div class="td-design-nav-item {{ $dt['key'] === 'colors' ? 'active' : '' }}" onclick="tdSwitchDesignTab('{{ $dt['key'] }}',this)">
                                    <i class="ti {{ $dt['icon'] }}"></i> {{ $dt['label'] }}
                                </div>
                            @endforeach
                        </div>

                        <div class="td-design-content">
                            <div class="td-design-tab active" id="td-design-colors">
                                <div class="td-design-section-title">Color Palette</div>
                                <div class="td-design-section-sub">Tokenized brand colors reflected live in the preview.</div>
                                @php
                                    $tdCP = $careerSettings->td_color_primary   ?? '#2563EB';
                                    $tdCS = $careerSettings->td_color_secondary ?? '#1F2937';
                                    $tdCA = $careerSettings->td_color_accent    ?? '#10B981';
                                    $tdCB = $careerSettings->td_color_bg        ?? '#FFFFFF';
                                    $tdTM = $careerSettings->td_theme_mode      ?? 'light';
                                    $tdLY = $careerSettings->td_layout          ?? 'wide';
                                @endphp
                                <div class="td-color-grid">
                                    <div>
                                        <div class="td-color-label">Primary Color</div>
                                        <div class="td-color-row">
                                            <input type="color" class="td-color-swatch" value="{{ $tdCP }}" id="tdColorPrimary" oninput="document.getElementById('tdColorPrimaryText').value=this.value;tdUpdatePreview()">
                                            <input type="text" class="td-color-input cps-num" id="tdColorPrimaryText" name="td_color_primary" value="{{ $tdCP }}" maxlength="7" oninput="syncColorSwatch('tdColorPrimary',this);tdUpdatePreview()">
                                        </div>
                                    </div>
                                    <div>
                                        <div class="td-color-label">Secondary Color</div>
                                        <div class="td-color-row">
                                            <input type="color" class="td-color-swatch" value="{{ $tdCS }}" id="tdColorSecondary" oninput="document.getElementById('tdColorSecondaryText').value=this.value;tdUpdatePreview()">
                                            <input type="text" class="td-color-input cps-num" id="tdColorSecondaryText" name="td_color_secondary" value="{{ $tdCS }}" maxlength="7" oninput="syncColorSwatch('tdColorSecondary',this);tdUpdatePreview()">
                                        </div>
                                    </div>
                                    <div>
                                        <div class="td-color-label">Accent Color</div>
                                        <div class="td-color-row">
                                            <input type="color" class="td-color-swatch" value="{{ $tdCA }}" id="tdColorAccent" oninput="document.getElementById('tdColorAccentText').value=this.value;tdUpdatePreview()">
                                            <input type="text" class="td-color-input cps-num" id="tdColorAccentText" name="td_color_accent" value="{{ $tdCA }}" maxlength="7" oninput="syncColorSwatch('tdColorAccent',this);tdUpdatePreview()">
                                        </div>
                                    </div>
                                    <div>
                                        <div class="td-color-label">Background Color</div>
                                        <div class="td-color-row">
                                            <input type="color" class="td-color-swatch" value="{{ $tdCB }}" id="tdColorBg" oninput="document.getElementById('tdColorBgText').value=this.value;tdUpdatePreview()">
                                            <input type="text" class="td-color-input cps-num" id="tdColorBgText" name="td_color_bg" value="{{ $tdCB }}" maxlength="7" oninput="syncColorSwatch('tdColorBg',this);tdUpdatePreview()">
                                        </div>
                                    </div>
                                </div>

                                <div class="td-design-section-title">Theme Mode</div>
                                <div class="td-theme-opts">
                                    <label class="td-theme-opt"><input type="radio" name="td_theme_mode" value="light" {{ $tdTM === 'light' ? 'checked' : '' }}> Light Mode</label>
                                    <label class="td-theme-opt"><input type="radio" name="td_theme_mode" value="dark" {{ $tdTM === 'dark' ? 'checked' : '' }}> Dark Mode</label>
                                    <label class="td-theme-opt"><input type="radio" name="td_theme_mode" value="system" {{ $tdTM === 'system' ? 'checked' : '' }}> System Default</label>
                                </div>

                                <div class="td-design-section-title">Container Width</div>
                                <div class="td-layout-grid" id="tdLayoutGrid">
                                    <div class="td-layout-card {{ $tdLY === 'wide' ? 'selected' : '' }}" onclick="tdSelectLayout('wide',this)">
                                        <div class="td-lc-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                        <div class="td-lc-thumb"><div style="display:flex;gap:3px;width:80%;height:18px;"><div style="flex:1;background:rgba(0,158,247,0.35);border-radius:2px;"></div><div style="flex:3;background:rgba(0,158,247,0.18);border-radius:2px;"></div></div></div>
                                        <div class="td-lc-name">Wide</div>
                                        <div class="td-lc-desc">1440px grid</div>
                                    </div>
                                    <div class="td-layout-card {{ $tdLY === 'boxed' ? 'selected' : '' }}" onclick="tdSelectLayout('boxed',this)">
                                        <div class="td-lc-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                        <div class="td-lc-thumb"><div style="width:55%;height:18px;background:#cbd5e1;border-radius:3px;"></div></div>
                                        <div class="td-lc-name">Boxed</div>
                                        <div class="td-lc-desc">Centered 1140px</div>
                                    </div>
                                    <div class="td-layout-card {{ $tdLY === 'fullwidth' ? 'selected' : '' }}" onclick="tdSelectLayout('fullwidth',this)">
                                        <div class="td-lc-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                        <div class="td-lc-thumb"><div style="width:100%;height:18px;background:#cbd5e1;border-radius:3px;"></div></div>
                                        <div class="td-lc-name">Full Width</div>
                                        <div class="td-lc-desc">Edge to edge</div>
                                    </div>
                                </div>
                                <input type="hidden" name="td_layout" id="tdLayoutInput" value="{{ $tdLY }}">
                            </div>

                            <div class="td-design-tab" id="td-design-typo" style="display:none;">
                                <div class="td-design-section-title">Typography Inheritance</div>
                                <div class="td-design-section-sub">Primary font family ({{ $fontFamily }}) is managed in the General tab and inherited across all templates.</div>
                                <button type="button" class="cps-btn-secondary" onclick="cpsActivateTab('general')">Edit Font in General</button>
                            </div>
                            <div class="td-design-tab" id="td-design-header" style="display:none;">
                                <div class="td-design-section-title">Header &amp; Navigation</div>
                                <div class="td-design-section-sub">Displays company logo, section jump links, and View Openings CTA automatically.</div>
                            </div>
                            <div class="td-design-tab" id="td-design-buttons" style="display:none;">
                                <div class="td-design-section-title">Button Styling</div>
                                <div class="td-design-section-sub">Primary and secondary buttons inherit your Primary Color and Border Radius tokens.</div>
                            </div>
                            <div class="td-design-tab" id="td-design-sections" style="display:none;">
                                <div class="td-design-section-title">Section Toggles</div>
                                <div class="td-design-section-sub">Enable or disable Culture, Testimonials, and Motion below in Template Feature Flags.</div>
                            </div>
                            <div class="td-design-tab" id="td-design-css" style="display:none;">
                                <div class="td-design-section-title">Custom CSS Override</div>
                                <div class="td-design-section-sub">Add scoped CSS rules to customize your public career portal.</div>
                                <textarea name="td_custom_css" rows="8" style="width:100%;border:1px solid #e2e8f0;border-radius:7px;padding:10px;font-size:11.5px;font-family:'Courier New',monospace;color:#0f172a;outline:none;resize:vertical;" placeholder="/* Add custom CSS here */&#10;.career-page { }">{{ $careerSettings->td_custom_css ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 3: Hero Assets & Feature Flags --}}
            <div class="cps-card accent-emerald">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon emerald"><i class="ti ti-photo"></i></div>
                        <div>
                            <div class="cps-card-title">Custom Hero Assets &amp; Feature Flags</div>
                            <div class="cps-card-sub">Upload showcase imagery and toggle rich portal sections.</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Hero Showcase Media</span></div>
                            <div class="td-asset-row">
                                <div class="td-asset-thumb" id="tdHeroImg1Thumb">
                                    @if(!empty($careerSettings->td_hero_img1_path))
                                        <img src="{{ asset($careerSettings->td_hero_img1_path) }}" style="max-width:52px;max-height:34px;object-fit:cover;">
                                    @else
                                        <i class="ti ti-photo" style="font-size:17px;color:#94a3b8;"></i>
                                    @endif
                                </div>
                                <div class="td-asset-info">
                                    <div class="td-asset-name">Hero Image 1</div>
                                    <div class="td-asset-size">{{ !empty($careerSettings->td_hero_img1_path) ? 'Uploaded' : '400 × 220px' }}</div>
                                </div>
                                <div class="td-asset-edit" onclick="document.getElementById('tdHeroImg1Upload').click()" title="Upload hero image 1">
                                    <i class="ti ti-pencil"></i>
                                </div>
                                <input type="file" id="tdHeroImg1Upload" name="td_hero_img1" accept="image/*" style="display:none;" onchange="tdPreviewAsset(this,'tdHeroImg1Thumb')">
                            </div>

                            <div class="td-asset-row">
                                <div class="td-asset-thumb" id="tdHeroImg2Thumb">
                                    @if(!empty($careerSettings->td_hero_img2_path))
                                        <img src="{{ asset($careerSettings->td_hero_img2_path) }}" style="max-width:52px;max-height:34px;object-fit:cover;">
                                    @else
                                        <i class="ti ti-photo" style="font-size:17px;color:#94a3b8;"></i>
                                    @endif
                                </div>
                                <div class="td-asset-info">
                                    <div class="td-asset-name">Hero Image 2</div>
                                    <div class="td-asset-size">{{ !empty($careerSettings->td_hero_img2_path) ? 'Uploaded' : '400 × 220px' }}</div>
                                </div>
                                <div class="td-asset-edit" onclick="document.getElementById('tdHeroImg2Upload').click()" title="Upload hero image 2">
                                    <i class="ti ti-pencil"></i>
                                </div>
                                <input type="file" id="tdHeroImg2Upload" name="td_hero_img2" accept="image/*" style="display:none;" onchange="tdPreviewAsset(this,'tdHeroImg2Thumb')">
                            </div>
                        </div>

                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Template Feature Flags</span></div>
                            @php
                            $tdOptions = [
                                ['key'=>'life',      'icon'=>'ti-heart',    'icon_bg'=>'#fff1f2','icon_color'=>'#e11d48','name'=>"Show 'Life at Company'", 'desc'=>'Culture photos & stories',     'val'=> $careerSettings->td_opt_life      ?? 1],
                                ['key'=>'testi',     'icon'=>'ti-quote',    'icon_bg'=>'#eff6ff','icon_color'=>'#009ef7','name'=>'Show Testimonials',      'desc'=>'Employee voice highlights',    'val'=> $careerSettings->td_opt_testi     ?? 1],
                                ['key'=>'animation', 'icon'=>'ti-sparkles', 'icon_bg'=>'#ecfdf5','icon_color'=>'#059669','name'=>'Enable Motion Effects',  'desc'=>'Smooth entrance transitions',  'val'=> $careerSettings->td_opt_animation ?? 1],
                            ];
                            @endphp
                            @foreach($tdOptions as $opt)
                                <div class="td-opt-row">
                                    <div class="td-opt-left">
                                        <div class="td-opt-icon" style="background:{{ $opt['icon_bg'] }};">
                                            <i class="ti {{ $opt['icon'] }}" style="font-size:14px;color:{{ $opt['icon_color'] }};"></i>
                                        </div>
                                        <div>
                                            <div class="td-opt-title">{{ $opt['name'] }}</div>
                                            <div class="td-opt-desc">{{ $opt['desc'] }}</div>
                                        </div>
                                    </div>
                                    <label class="cps-switch" style="flex-shrink:0;">
                                        <input type="checkbox" name="td_opt_{{ $opt['key'] }}" value="1" {{ $opt['val'] ? 'checked' : '' }}>
                                        <span class="cps-slider"></span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Sticky Responsive Live Preview --}}
        <div class="cps-col-side sticky-side">

            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-device-laptop"></i></div>
                        <div>
                            <div class="cps-card-title">Responsive Template Preview</div>
                            <div class="cps-card-sub">Switch desktop, tablet &amp; mobile viewports</div>
                        </div>
                    </div>
                    <div class="td-prev-device-bar">
                        <div class="td-prev-device-btn active" onclick="tdSwitchDevice('desktop',this)" title="Desktop"><i class="ti ti-device-desktop"></i></div>
                        <div class="td-prev-device-btn" onclick="tdSwitchDevice('tablet',this)" title="Tablet"><i class="ti ti-device-tablet"></i></div>
                        <div class="td-prev-device-btn" onclick="tdSwitchDevice('mobile',this)" title="Mobile"><i class="ti ti-device-mobile"></i></div>
                    </div>
                </div>

                <div class="cps-card-body">
                    <div id="tdPreviewFrame" class="td-live-prev" style="transition:max-width .2s;max-width:100%;">
                        <div class="td-prev-nav">
                            <div class="td-prev-nav-logo">{{ $companyName }}</div>
                            <div class="td-prev-nav-links">
                                <span class="td-prev-nav-link">Jobs</span>
                                <span class="td-prev-nav-link">Culture</span>
                                <span class="td-prev-nav-link">About</span>
                            </div>
                            <div class="td-prev-nav-btn">View Openings</div>
                        </div>

                        <div class="td-prev-hero">
                            <div>
                                <div class="td-prev-hero-title">Build the Future<br>Together</div>
                                <div class="td-prev-hero-sub">Join {{ $companyName }} and create work that scales globally.</div>
                                <div class="td-prev-hero-search">
                                    <input type="text" placeholder="Search open roles..." readonly>
                                    <button type="button" class="td-prev-hero-search-btn">Search</button>
                                </div>
                            </div>
                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                                <div style="height:78px;border-radius:6px;background:rgba(255,255,255,0.16);display:flex;align-items:center;justify-content:center;"><i class="ti ti-users" style="color:rgba(255,255,255,0.8);font-size:22px;"></i></div>
                                <div style="height:78px;border-radius:6px;background:rgba(255,255,255,0.12);display:flex;align-items:center;justify-content:center;"><i class="ti ti-building" style="color:rgba(255,255,255,0.8);font-size:22px;"></i></div>
                            </div>
                        </div>

                        <div class="td-prev-why">
                            <div class="td-prev-why-title">Why Work at {{ $companyName }}?</div>
                            <div class="td-prev-why-sub">Discover what makes our teams thrive</div>
                            <div class="td-prev-why-grid">
                                @php
                                $whyBenefits = [
                                    ['icon'=>'ti-rocket',     'bg'=>'#eff6ff','color'=>'#009ef7','name'=>'Innovation',   'desc'=>'Modern stack'],
                                    ['icon'=>'ti-trending-up','bg'=>'#ecfdf5','color'=>'#059669','name'=>'Growth',       'desc'=>'Fast progression'],
                                    ['icon'=>'ti-home',       'bg'=>'#fff7ed','color'=>'#f97316','name'=>'Flexibility',  'desc'=>'Hybrid options'],
                                    ['icon'=>'ti-heart',      'bg'=>'#fff1f2','color'=>'#e11d48','name'=>'Wellness',     'desc'=>'Full coverage'],
                                ];
                                @endphp
                                @foreach($whyBenefits as $wb)
                                    <div class="td-prev-why-card">
                                        <div class="td-prev-why-icon" style="background:{{ $wb['bg'] }};">
                                            <i class="ti {{ $wb['icon'] }}" style="font-size:12px;color:{{ $wb['color'] }};"></i>
                                        </div>
                                        <div class="td-prev-why-name">{{ $wb['name'] }}</div>
                                        <div class="td-prev-why-desc">{{ $wb['desc'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="td-prev-cta">
                            <div>
                                <div class="td-prev-cta-title">Ready to make an impact?</div>
                                <div class="td-prev-cta-sub">Explore open roles at {{ $companyName }}</div>
                            </div>
                            <div class="td-prev-cta-btn">View Openings</div>
                        </div>
                    </div>

                    <div class="td-publish-card" style="margin-top:12px;">
                        <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;">
                            <div style="width:32px;height:32px;background:#ffffff;border:1px solid #bae6fd;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                <i class="ti ti-cloud-upload" style="font-size:16px;color:#009ef7;"></i>
                            </div>
                            <div>
                                <div class="td-publish-title">Publish Template Design</div>
                                <div class="td-publish-desc">Save your design tokens and invalidate tenant cache immediately.</div>
                            </div>
                        </div>
                        <button type="submit" form="careerSettingsForm" class="td-publish-btn js-cps-save-btn">
                            <i class="ti ti-device-floppy" style="font-size:14px;"></i> Save &amp; Publish Design
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
