{{-- Templates Tab Panel --}}
<div class="cps-panel" id="panel-templates">
<div class="row g-0" style="min-height:600px;">

    {{-- ── LEFT COLUMN ── --}}
    <div class="col-lg-7" style="padding:28px 24px 28px 28px;border-right:1.5px solid #F1F5F9;">

        {{-- 1. CHOOSE TEMPLATE --}}
        <div style="margin-bottom:28px;">
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div>
                    <div style="font-size:14px;font-weight:700;color:#0F172A;">Choose Template</div>
                    <div style="font-size:11px;color:#94A3B8;margin-top:1px;">Select a pre-designed template or start from scratch.</div>
                </div>
                <a href="#" style="font-size:11px;font-weight:600;color:#2563EB;text-decoration:none;white-space:nowrap;">Browse More Templates <i class="ti ti-arrow-right" style="font-size:11px;"></i></a>
            </div>

            <div class="td-tpl-grid mt-3" id="tdTemplateGrid">
                @php
                $savedTpl = $careerSettings->td_template ?? 'modern';
                $tdTemplates = [
                    ['key'=>'modern',    'name'=>'Modern',    'desc'=>'Clean & professional', 'from'=>'#1E3A8A','to'=>'#3B82F6'],
                    ['key'=>'corporate', 'name'=>'Corporate', 'desc'=>'Classic & formal',     'from'=>'#1F2937','to'=>'#374151'],
                    ['key'=>'creative',  'name'=>'Creative',  'desc'=>'Bold & vibrant',       'from'=>'#7C3AED','to'=>'#EC4899'],
                    ['key'=>'minimal',   'name'=>'Minimal',   'desc'=>'Simple & elegant',     'from'=>'#E2E8F0','to'=>'#CBD5E1','dark_text'=>true],
                    ['key'=>'tech',      'name'=>'Tech',      'desc'=>'Dark & modern',        'from'=>'#0F172A','to'=>'#1E293B'],
                ];
                @endphp
                @foreach($tdTemplates as $tt)
                <div class="td-tpl-card {{ $tt['key']===$savedTpl?'selected':'' }}" onclick="tdSelectTemplate('{{ $tt['key'] }}',this)">
                    <div class="td-tpl-check"><i class="ti ti-check" style="font-size:10px;color:#fff;"></i></div>
                    <div class="td-tpl-thumb" style="background:linear-gradient(135deg,{{ $tt['from'] }},{{ $tt['to'] }});">
                        <div style="padding:10px 8px;">
                            <div style="height:5px;background:rgba(255,255,255,.8);border-radius:3px;width:60%;margin-bottom:4px;"></div>
                            <div style="height:3px;background:rgba(255,255,255,.45);border-radius:3px;width:80%;margin-bottom:3px;"></div>
                            <div style="height:3px;background:rgba(255,255,255,.45);border-radius:3px;width:65%;margin-bottom:8px;"></div>
                            <div style="height:14px;background:rgba(255,255,255,.9);border-radius:3px;width:50%;"></div>
                        </div>
                    </div>
                    <div class="td-tpl-info">
                        <div class="td-tpl-name">{{ $tt['name'] }}</div>
                        <div class="td-tpl-desc">{{ $tt['desc'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <input type="hidden" name="td_template" id="tdTemplateInput" value="{{ $careerSettings->td_template ?? 'modern' }}">
        </div>

        {{-- 2. CUSTOMIZE DESIGN --}}
        <div style="margin-bottom:28px;">
            <div style="font-size:14px;font-weight:700;color:#0F172A;margin-bottom:2px;">Customize Design</div>
            <div style="font-size:11px;color:#94A3B8;margin-bottom:12px;">Personalize colors, fonts, and layout to match your brand.</div>

            <div class="td-design-wrap">
                {{-- Sidebar nav --}}
                <div class="td-design-nav">
                    @php
                    $tdDesignTabs = [
                        ['key'=>'colors',  'icon'=>'ti-palette',    'label'=>'Colors'],
                        ['key'=>'typo',    'icon'=>'ti-letter-t',   'label'=>'Typography'],
                        ['key'=>'header',  'icon'=>'ti-layout-navbar','label'=>'Header & Navigation'],
                        ['key'=>'buttons', 'icon'=>'ti-button',     'label'=>'Buttons'],
                        ['key'=>'sections','icon'=>'ti-layout',      'label'=>'Sections'],
                        ['key'=>'footer',  'icon'=>'ti-layout-bottombar','label'=>'Footer'],
                        ['key'=>'css',     'icon'=>'ti-code',        'label'=>'Custom CSS'],
                    ];
                    @endphp
                    @foreach($tdDesignTabs as $dt)
                    <div class="td-design-nav-item {{ $dt['key']==='colors'?'active':'' }}" onclick="tdSwitchDesignTab('{{ $dt['key'] }}',this)">
                        <i class="ti {{ $dt['icon'] }}"></i> {{ $dt['label'] }}
                    </div>
                    @endforeach
                </div>

                {{-- Content panels --}}
                <div class="td-design-content">
                    {{-- Colors panel --}}
                    <div class="td-design-tab active" id="td-design-colors">
                        <div class="td-design-section-title">Color Scheme</div>
                        <div class="td-design-section-sub">Choose colors for your career page.</div>
                        @php
                            $tdCP = $careerSettings->td_color_primary   ?? '#2563EB';
                            $tdCS = $careerSettings->td_color_secondary ?? '#1F2937';
                            $tdCA = $careerSettings->td_color_accent    ?? '#10B981';
                            $tdCB = $careerSettings->td_color_bg        ?? '#FFFFFF';
                            $tdTM = $careerSettings->td_theme_mode      ?? 'light';
                            $tdLY = $careerSettings->td_layout          ?? 'wide';
                            $tdTpl= $careerSettings->td_template        ?? 'modern';
                        @endphp
                        {{-- 2×2 color grid: Primary / Secondary / Accent / Background --}}
                        <div class="td-color-grid">
                            <div>
                                <div class="td-color-label">Primary Color</div>
                                <div class="td-color-row">
                                    <input type="color" class="td-color-swatch" value="{{ $tdCP }}" id="tdColorPrimary" oninput="document.getElementById('tdColorPrimaryText').value=this.value;tdUpdatePreview()">
                                    <input type="text" class="td-color-input" id="tdColorPrimaryText" name="td_color_primary" value="{{ $tdCP }}" maxlength="7" oninput="syncColorSwatch('tdColorPrimary',this);tdUpdatePreview()">
                                </div>
                            </div>
                            <div>
                                <div class="td-color-label">Secondary Color</div>
                                <div class="td-color-row">
                                    <input type="color" class="td-color-swatch" value="{{ $tdCS }}" id="tdColorSecondary" oninput="document.getElementById('tdColorSecondaryText').value=this.value;tdUpdatePreview()">
                                    <input type="text" class="td-color-input" id="tdColorSecondaryText" name="td_color_secondary" value="{{ $tdCS }}" maxlength="7" oninput="syncColorSwatch('tdColorSecondary',this);tdUpdatePreview()">
                                </div>
                            </div>
                            <div>
                                <div class="td-color-label">Accent Color</div>
                                <div class="td-color-row">
                                    <input type="color" class="td-color-swatch" value="{{ $tdCA }}" id="tdColorAccent" oninput="document.getElementById('tdColorAccentText').value=this.value;tdUpdatePreview()">
                                    <input type="text" class="td-color-input" id="tdColorAccentText" name="td_color_accent" value="{{ $tdCA }}" maxlength="7" oninput="syncColorSwatch('tdColorAccent',this);tdUpdatePreview()">
                                </div>
                            </div>
                            <div>
                                <div class="td-color-label">Background Color</div>
                                <div class="td-color-row">
                                    <input type="color" class="td-color-swatch" value="{{ $tdCB }}" id="tdColorBg" oninput="document.getElementById('tdColorBgText').value=this.value;tdUpdatePreview()">
                                    <input type="text" class="td-color-input" id="tdColorBgText" name="td_color_bg" value="{{ $tdCB }}" maxlength="7" oninput="syncColorSwatch('tdColorBg',this);tdUpdatePreview()">
                                </div>
                            </div>
                        </div>
                        {{-- Theme mode --}}
                        <div class="td-design-section-title" style="margin-top:4px;">Theme Mode</div>
                        <div class="td-theme-opts" style="margin-bottom:16px;">
                            <label class="td-theme-opt"><input type="radio" name="td_theme_mode" value="light" {{ $tdTM==='light'?'checked':'' }}> Light Mode</label>
                            <label class="td-theme-opt"><input type="radio" name="td_theme_mode" value="dark" {{ $tdTM==='dark'?'checked':'' }}> Dark Mode</label>
                            <label class="td-theme-opt"><input type="radio" name="td_theme_mode" value="system" {{ $tdTM==='system'?'checked':'' }}> System Default</label>
                        </div>

                        {{-- Layout Style --}}
                        <div class="td-design-section-title">Layout Style</div>
                        <div class="td-design-section-sub">Choose how content is displayed across the page.</div>
                        @php $savedLayout = $careerSettings->td_layout ?? 'wide'; @endphp
                        <div class="td-layout-grid" id="tdLayoutGrid">
                            <div class="td-layout-card {{ $savedLayout==='wide'?'selected':'' }}" onclick="tdSelectLayout('wide',this)">
                                <div class="td-lc-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                <div class="td-lc-thumb">
                                    <div style="display:flex;gap:3px;width:80%;height:20px;">
                                        <div style="flex:1;background:rgba(37,99,235,.3);border-radius:2px;"></div>
                                        <div style="flex:3;background:rgba(37,99,235,.15);border-radius:2px;"></div>
                                    </div>
                                </div>
                                <div class="td-lc-name">Wide</div>
                                <div class="td-lc-desc">Best for large screens</div>
                            </div>
                            <div class="td-layout-card {{ $savedLayout==='boxed'?'selected':'' }}" onclick="tdSelectLayout('boxed',this)">
                                <div class="td-lc-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                <div class="td-lc-thumb">
                                    <div style="width:55%;height:20px;background:#E2E8F0;border-radius:3px;margin:0 auto;"></div>
                                </div>
                                <div class="td-lc-name">Boxed</div>
                                <div class="td-lc-desc">Content in a centered container</div>
                            </div>
                            <div class="td-layout-card {{ $savedLayout==='fullwidth'?'selected':'' }}" onclick="tdSelectLayout('fullwidth',this)">
                                <div class="td-lc-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                <div class="td-lc-thumb">
                                    <div style="width:100%;height:20px;background:#E2E8F0;border-radius:3px;"></div>
                                </div>
                                <div class="td-lc-name">Full Width</div>
                                <div class="td-lc-desc">Edge to edge layout</div>
                            </div>
                        </div>
                        <input type="hidden" name="td_layout" id="tdLayoutInput" value="{{ $tdLY }}">
                    </div>

                    {{-- Other design tabs (hidden) --}}
                    <div class="td-design-tab" id="td-design-typo" style="display:none;">
                        <div class="td-design-section-title">Typography</div>
                        <div class="td-design-section-sub">Choose fonts for headings and body text.</div>
                        <div style="font-size:11px;color:#94A3B8;margin-top:20px;text-align:center;"><i class="ti ti-letter-t" style="font-size:24px;display:block;margin-bottom:6px;"></i>Font settings coming soon</div>
                    </div>
                    <div class="td-design-tab" id="td-design-header" style="display:none;">
                        <div class="td-design-section-title">Header & Navigation</div>
                        <div class="td-design-section-sub">Configure your career page header and navigation menu.</div>
                        <div style="font-size:11px;color:#94A3B8;margin-top:20px;text-align:center;"><i class="ti ti-layout-navbar" style="font-size:24px;display:block;margin-bottom:6px;"></i>Header settings coming soon</div>
                    </div>
                    <div class="td-design-tab" id="td-design-buttons" style="display:none;">
                        <div class="td-design-section-title">Buttons</div>
                        <div class="td-design-section-sub">Customize the style of buttons on your career page.</div>
                        <div style="font-size:11px;color:#94A3B8;margin-top:20px;text-align:center;"><i class="ti ti-button" style="font-size:24px;display:block;margin-bottom:6px;"></i>Button settings coming soon</div>
                    </div>
                    <div class="td-design-tab" id="td-design-sections" style="display:none;">
                        <div class="td-design-section-title">Sections</div>
                        <div class="td-design-section-sub">Control which sections appear on your career page.</div>
                        <div style="font-size:11px;color:#94A3B8;margin-top:20px;text-align:center;"><i class="ti ti-layout" style="font-size:24px;display:block;margin-bottom:6px;"></i>Section settings coming soon</div>
                    </div>
                    <div class="td-design-tab" id="td-design-footer" style="display:none;">
                        <div class="td-design-section-title">Footer</div>
                        <div class="td-design-section-sub">Customize the footer of your career page.</div>
                        <div style="font-size:11px;color:#94A3B8;margin-top:20px;text-align:center;"><i class="ti ti-layout-bottombar" style="font-size:24px;display:block;margin-bottom:6px;"></i>Footer settings coming soon</div>
                    </div>
                    <div class="td-design-tab" id="td-design-css" style="display:none;">
                        <div class="td-design-section-title">Custom CSS</div>
                        <div class="td-design-section-sub">Add your own CSS to further customize the look.</div>
                        <textarea name="td_custom_css" rows="8" style="width:100%;border:1.5px solid #E2E8F0;border-radius:7px;padding:10px;font-size:11px;font-family:'Courier New',monospace;color:#374151;outline:none;resize:vertical;" placeholder="/* Add your custom CSS here */&#10;.career-page { }">{{ $careerSettings->td_custom_css ?? '' }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. CUSTOM ASSETS --}}
        <div style="margin-bottom:28px;">
            <div style="font-size:14px;font-weight:700;color:#0F172A;margin-bottom:2px;">Custom Assets</div>
            <div style="font-size:11px;color:#94A3B8;margin-bottom:14px;">Upload your brand assets for a consistent look.</div>
            <div style="border:1.5px solid #E2E8F0;border-radius:10px;overflow:hidden;">
                {{-- Hero Image 1 --}}
                <div class="td-asset-row" style="padding:14px 16px;">
                    <div class="td-asset-thumb" id="tdHeroImg1Thumb">
                        @if(!empty($careerSettings->td_hero_img1_path))
                            <img src="{{ asset($careerSettings->td_hero_img1_path) }}" style="max-width:60px;max-height:36px;object-fit:cover;border-radius:4px;">
                        @else
                            <i class="ti ti-photo" style="font-size:18px;color:#CBD5E1;"></i>
                        @endif
                    </div>
                    <div class="td-asset-info">
                        <div class="td-asset-name">Hero Image 1</div>
                        <div class="td-asset-size">{{ !empty($careerSettings->td_hero_img1_path) ? 'Image uploaded' : 'Recommended size: 400 × 220px' }}</div>
                    </div>
                    <div class="td-asset-edit" onclick="document.getElementById('tdHeroImg1Upload').click()" title="Upload hero image 1">
                        <i class="ti ti-pencil" style="font-size:14px;"></i>
                    </div>
                    <input type="file" id="tdHeroImg1Upload" name="td_hero_img1" accept="image/*" style="display:none;" onchange="tdPreviewAsset(this,'tdHeroImg1Thumb')">
                </div>
                {{-- Hero Image 2 --}}
                <div class="td-asset-row" style="padding:14px 16px;border-top:1px solid #F1F5F9;">
                    <div class="td-asset-thumb" id="tdHeroImg2Thumb">
                        @if(!empty($careerSettings->td_hero_img2_path))
                            <img src="{{ asset($careerSettings->td_hero_img2_path) }}" style="max-width:60px;max-height:36px;object-fit:cover;border-radius:4px;">
                        @else
                            <i class="ti ti-photo" style="font-size:18px;color:#CBD5E1;"></i>
                        @endif
                    </div>
                    <div class="td-asset-info">
                        <div class="td-asset-name">Hero Image 2</div>
                        <div class="td-asset-size">{{ !empty($careerSettings->td_hero_img2_path) ? 'Image uploaded' : 'Recommended size: 400 × 220px' }}</div>
                    </div>
                    <div class="td-asset-edit" onclick="document.getElementById('tdHeroImg2Upload').click()" title="Upload hero image 2">
                        <i class="ti ti-pencil" style="font-size:14px;"></i>
                    </div>
                    <input type="file" id="tdHeroImg2Upload" name="td_hero_img2" accept="image/*" style="display:none;" onchange="tdPreviewAsset(this,'tdHeroImg2Thumb')">
                </div>
            </div>
        </div>

        {{-- 4. ADDITIONAL OPTIONS --}}
        <div>
            <div style="font-size:14px;font-weight:700;color:#0F172A;margin-bottom:2px;">Additional Options</div>
            <div style="font-size:11px;color:#94A3B8;margin-bottom:14px;">Enable or disable extra features on your career page.</div>
            <div style="border:1.5px solid #E2E8F0;border-radius:10px;overflow:hidden;">
                @php
                $tdOptions = [
                    ['key'=>'life',      'icon'=>'ti-heart',     'icon_bg'=>'#FFF1F2', 'icon_color'=>'#E11D48', 'name'=>"Show 'Life at Company' Section", 'desc'=>'Display photos and culture content', 'val'=> $careerSettings->td_opt_life      ?? 1],
                    ['key'=>'testi',     'icon'=>'ti-quote',     'icon_bg'=>'#EFF6FF', 'icon_color'=>'#2563EB', 'name'=>'Show Testimonials',               'desc'=>'Feature employee testimonials',      'val'=> $careerSettings->td_opt_testi     ?? 1],
                    ['key'=>'animation', 'icon'=>'ti-sparkles',  'icon_bg'=>'#F0FDF4', 'icon_color'=>'#16A34A', 'name'=>'Enable Animation',                'desc'=>'Smooth scroll and entrance effects', 'val'=> $careerSettings->td_opt_animation ?? 1],
                ];
                @endphp
                @foreach($tdOptions as $i => $opt)
                <div class="td-opt-row" style="padding:13px 16px;{{ $i>0?'border-top:1px solid #F1F5F9;':'' }}">
                    <div class="td-opt-left">
                        <div class="td-opt-icon" style="background:{{ $opt['icon_bg'] }};">
                            <i class="ti {{ $opt['icon'] }}" style="font-size:14px;color:{{ $opt['icon_color'] }};"></i>
                        </div>
                        <div>
                            <div class="td-opt-title">{{ $opt['name'] }}</div>
                            <div class="td-opt-desc">{{ $opt['desc'] }}</div>
                        </div>
                    </div>
                    <div class="form-check form-switch mb-0">
                        <input class="form-check-input" type="checkbox" name="td_opt_{{ $opt['key'] }}" value="1" {{ $opt['val']?'checked':'' }} style="width:40px;height:22px;cursor:pointer;">
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </div>

    {{-- ── RIGHT COLUMN ── --}}
    <div class="col-lg-5" style="padding:28px 24px;background:#F8FAFC;">

        {{-- Live Preview header --}}
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <div style="font-size:14px;font-weight:700;color:#0F172A;">Live Preview</div>
                <div style="font-size:11px;color:#94A3B8;margin-top:1px;">See how your career page looks with the selected design.</div>
            </div>
            <div class="td-prev-device-bar">
                <div class="td-prev-device-btn active" onclick="tdSwitchDevice('desktop',this)" title="Desktop"><i class="ti ti-device-desktop"></i></div>
                <div class="td-prev-device-btn" onclick="tdSwitchDevice('tablet',this)" title="Tablet"><i class="ti ti-device-tablet"></i></div>
                <div class="td-prev-device-btn" onclick="tdSwitchDevice('mobile',this)" title="Mobile"><i class="ti ti-device-mobile"></i></div>
            </div>
        </div>

        {{-- Preview frame --}}
        <div id="tdPreviewFrame" class="td-live-prev" style="transition:max-width .2s;max-width:100%;">
            {{-- Navbar --}}
            <div class="td-prev-nav">
                <div class="td-prev-nav-logo">
                    <span>{{ $companyName }}</span>
                </div>
                <div class="td-prev-nav-links">
                    <span class="td-prev-nav-link">Jobs</span>
                    <span class="td-prev-nav-link">Life at 4Bell</span>
                    <span class="td-prev-nav-link">Teams</span>
                    <span class="td-prev-nav-link">About Us</span>
                </div>
                <div class="td-prev-nav-btn">View Openings</div>
            </div>

            {{-- Hero --}}
            <div class="td-prev-hero">
                <div>
                    <div class="td-prev-hero-title">Build the Future<br>Together</div>
                    <div class="td-prev-hero-sub">Join our team of innovators and create<br>technology that shapes tomorrow.</div>
                    <div class="td-prev-hero-search">
                        <input type="text" placeholder="Search for roles..." readonly>
                        <button class="td-prev-hero-search-btn">Search Jobs</button>
                    </div>
                </div>
                <div class="td-prev-hero-img" style="display:grid;grid-template-columns:1fr 1fr;gap:6px;padding:0;background:transparent;height:auto;">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=200&h=100&fit=crop&auto=format" style="width:100%;height:100px;object-fit:cover;border-radius:6px;" alt="">
                    <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?w=200&h=100&fit=crop&auto=format" style="width:100%;height:100px;object-fit:cover;border-radius:6px;" alt="">
                </div>
            </div>

            {{-- Why Work Here --}}
            <div class="td-prev-why">
                <div class="td-prev-why-title">Why Work at 4Bell Technology?</div>
                <div class="td-prev-why-sub">Discover what makes us a great place to work</div>
                <div class="td-prev-why-grid">
                    @php
                    $whyBenefits = [
                        ['icon'=>'ti-rocket','bg'=>'#EFF6FF','color'=>'#2563EB','name'=>'Innovative Culture','desc'=>'Work on cutting-edge projects'],
                        ['icon'=>'ti-trending-up','bg'=>'#F0FDF4','color'=>'#16A34A','name'=>'Growth Opportunities','desc'=>'Level up your career fast'],
                        ['icon'=>'ti-home','bg'=>'#FFF7ED','color'=>'#F97316','name'=>'Work-Life Balance','desc'=>'Flexible hybrid work options'],
                        ['icon'=>'ti-heart','bg'=>'#FFF1F2','color'=>'#E11D48','name'=>'Great Benefits','desc'=>'Health, dental, and more'],
                    ];
                    @endphp
                    @foreach($whyBenefits as $wb)
                    <div class="td-prev-why-card">
                        <div class="td-prev-why-icon" style="background:{{ $wb['bg'] }};">
                            <i class="ti {{ $wb['icon'] }}" style="font-size:13px;color:{{ $wb['color'] }};"></i>
                        </div>
                        <div class="td-prev-why-name">{{ $wb['name'] }}</div>
                        <div class="td-prev-why-desc">{{ $wb['desc'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- CTA --}}
            <div class="td-prev-cta">
                <div>
                    <div class="td-prev-cta-title">Ready to make an impact?</div>
                    <div class="td-prev-cta-sub">Join thousands of professionals who chose 4Bell</div>
                </div>
                <div class="td-prev-cta-btn">View Openings</div>
            </div>
        </div>

        {{-- Publishing Changes --}}
        <div class="td-publish-card">
            <div style="display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;">
                <div style="width:34px;height:34px;background:#EFF6FF;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="ti ti-cloud-upload" style="font-size:17px;color:#2563EB;"></i>
                </div>
                <div>
                    <div class="td-publish-title">Publishing Changes</div>
                    <div class="td-publish-desc">Your changes are saved automatically. Click 'Publish' to make them live on your career page.</div>
                </div>
            </div>
            <button type="button" class="td-publish-btn">
                <i class="ti ti-external-link" style="font-size:14px;"></i> Preview &amp; Publish
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
