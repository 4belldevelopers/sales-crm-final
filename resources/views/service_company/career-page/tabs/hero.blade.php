{{-- Hero Section Tab Panel (Rule 20 Card + Sub-Card System) --}}
<div class="cps-panel" id="panel-hero">
    <div class="cps-workspace-grid">

        {{-- Left Main Column --}}
        <div class="cps-col-main">

            {{-- Primary Card 1: Hero Layout & Copy --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-layout-navbar"></i></div>
                        <div>
                            <div class="cps-card-title">Hero Architecture &amp; Messaging</div>
                            <div class="cps-card-sub">Define structural layout, eyebrow badge, primary headline, and value proposition.</div>
                        </div>
                    </div>
                    <span class="cps-card-pill blue">{{ ucfirst($careerSettings->hero_layout ?? 'left-right') }}</span>
                </div>

                <div class="cps-card-body">
                    {{-- Sub-Card: Layout Style --}}
                    <div class="cps-subcard" style="margin-bottom:10px;">
                        <div class="cps-subcard-kicker"><span>Hero Layout Style</span><span>4 Presets</span></div>
                        @php $currentLayout = $careerSettings->hero_layout ?? 'left-right'; @endphp
                        <div class="hs-layout-grid">
                            <div class="hs-layout-card {{ $currentLayout === 'left-right' ? 'selected' : '' }}" onclick="selectHeroLayout('left-right',this)">
                                <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                                <div class="hs-lc-thumb">
                                    <svg width="52" height="30" viewBox="0 0 52 30"><rect x="2" y="4" width="22" height="22" rx="2" fill="#93c5fd"/><rect x="28" y="4" width="22" height="22" rx="2" fill="#bfdbfe"/></svg>
                                </div>
                                <div class="hs-lc-name">Left Content<br>Right Image</div>
                            </div>
                            <div class="hs-layout-card {{ $currentLayout === 'center' ? 'selected' : '' }}" onclick="selectHeroLayout('center',this)">
                                <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                                <div class="hs-lc-thumb">
                                    <svg width="52" height="30" viewBox="0 0 52 30"><rect x="8" y="8" width="36" height="14" rx="2" fill="#93c5fd"/></svg>
                                </div>
                                <div class="hs-lc-name">Center<br>Content</div>
                            </div>
                            <div class="hs-layout-card {{ $currentLayout === 'fullwidth' ? 'selected' : '' }}" onclick="selectHeroLayout('fullwidth',this)">
                                <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                                <div class="hs-lc-thumb">
                                    <svg width="52" height="30" viewBox="0 0 52 30"><rect x="2" y="2" width="48" height="26" rx="2" fill="#93c5fd"/></svg>
                                </div>
                                <div class="hs-lc-name">Full Width<br>Background</div>
                            </div>
                            <div class="hs-layout-card {{ $currentLayout === 'video' ? 'selected' : '' }}" onclick="selectHeroLayout('video',this)">
                                <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                                <div class="hs-lc-thumb">
                                    <svg width="52" height="30" viewBox="0 0 52 30"><rect x="2" y="2" width="48" height="26" rx="2" fill="#bfdbfe"/><polygon points="20,10 20,20 34,15" fill="#2563eb"/></svg>
                                </div>
                                <div class="hs-lc-name">Video<br>Background</div>
                            </div>
                        </div>
                        <input type="hidden" name="hero_layout" id="heroLayoutInput" value="{{ $currentLayout }}">
                    </div>

                    <div class="cps-subcard-grid-2">
                        {{-- Sub-Card: Badge --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker">
                                <span>Eyebrow Badge</span>
                                <span class="hs-char-count" id="heroBadgeCount">{{ strlen($careerSettings->hero_badge ?? '') }}/80</span>
                            </div>
                            <label class="hs-label">Badge Text</label>
                            <input type="text" class="hs-input" name="hero_badge" id="heroBadge"
                                   maxlength="80" placeholder="e.g. 🚀 Now Hiring — Join Our Team"
                                   value="{{ $careerSettings->hero_badge ?? '' }}"
                                   oninput="document.getElementById('heroBadgeCount').textContent=this.value.length+'/80';">
                            <div class="hs-hint">Short pill displayed above the headline.</div>
                        </div>

                        {{-- Sub-Card: Main Heading --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker">
                                <span>Headline</span>
                                <span class="hs-char-count" id="heroHeadingCount">{{ strlen($careerSettings->hero_heading ?? '') }}/80</span>
                            </div>
                            <label class="hs-label">Main Heading</label>
                            <input type="text" class="hs-input" name="hero_heading" id="heroHeading"
                                   maxlength="80" placeholder="e.g. Build the Future with {{ $companyName }}"
                                   value="{{ $careerSettings->hero_heading ?? '' }}"
                                   oninput="document.getElementById('heroHeadingCount').textContent=this.value.length+'/80';">
                            <div class="hs-hint">Keep concise (&lt; 10 words) for crisp mobile wrapping.</div>
                        </div>

                        {{-- Sub-Card: Description --}}
                        <div class="cps-subcard span-2">
                            <div class="cps-subcard-kicker">
                                <span>Sub-Headline Copy</span>
                                <span class="hs-char-count" id="heroDescCount">{{ strlen($careerSettings->hero_description ?? '') }}/200</span>
                            </div>
                            <label class="hs-label">Hero Description</label>
                            <textarea class="hs-textarea" name="hero_description" id="heroDesc" maxlength="200" rows="2"
                                      placeholder="Join our team of innovators and help shape the future of technology."
                                      oninput="document.getElementById('heroDescCount').textContent=this.value.length+'/200';">{{ $careerSettings->hero_description ?? '' }}</textarea>
                        </div>

                        {{-- Sub-Card: Highlight Points --}}
                        <div class="cps-subcard span-2">
                            <div class="cps-subcard-kicker"><span>Culture Checkmarks</span></div>
                            <label class="hs-label">Highlight Points</label>
                            <div class="hs-tags-wrap" id="hsTagsWrap">
                                <div class="hs-tag">
                                    <span class="hs-tag-icon"><i class="ti ti-check" style="font-size:8px;color:#2563EB;"></i></span>
                                    Great Culture
                                    <span class="hs-tag-del" onclick="removeHsTag(this)">×</span>
                                </div>
                                <div class="hs-tag">
                                    <span class="hs-tag-icon"><i class="ti ti-check" style="font-size:8px;color:#2563EB;"></i></span>
                                    Remote Friendly
                                    <span class="hs-tag-del" onclick="removeHsTag(this)">×</span>
                                </div>
                                <div class="hs-tag">
                                    <span class="hs-tag-icon"><i class="ti ti-check" style="font-size:8px;color:#2563EB;"></i></span>
                                    Competitive Pay
                                    <span class="hs-tag-del" onclick="removeHsTag(this)">×</span>
                                </div>
                            </div>
                            <button type="button" class="hs-add-point" onclick="addHsTag()">
                                <i class="ti ti-plus" style="font-size:11px;"></i> Add Highlight Point
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 2: Call-to-Action Buttons --}}
            <div class="cps-card accent-emerald">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon emerald"><i class="ti ti-click"></i></div>
                        <div>
                            <div class="cps-card-title">Hero Call-to-Action Buttons</div>
                            <div class="cps-card-sub">Direct candidates to open roles or company culture pages.</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Primary CTA</span></div>
                            <div class="hs-cta-grid">
                                <div>
                                    <label class="hs-label">Button Text</label>
                                    <input type="text" class="hs-input" name="hero_cta1_text" value="{{ $careerSettings->hero_cta1_text ?? '' }}" placeholder="Explore Open Roles">
                                </div>
                                <div>
                                    <label class="hs-label">Target Link</label>
                                    <input type="text" class="hs-input" name="hero_cta1_link" value="{{ $careerSettings->hero_cta1_link ?? '' }}" placeholder="#jobs">
                                </div>
                            </div>
                            <label class="hs-cta-newtab">
                                <input type="checkbox" name="hero_cta1_newtab" value="1" {{ !empty($careerSettings->hero_cta1_newtab) ? 'checked' : '' }} style="accent-color:#009ef7;">
                                Open in new tab
                            </label>
                        </div>

                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Secondary CTA (Optional)</span></div>
                            <div class="hs-cta-grid">
                                <div>
                                    <label class="hs-label">Button Text</label>
                                    <input type="text" class="hs-input" name="hero_cta2_text" value="{{ $careerSettings->hero_cta2_text ?? '' }}" placeholder="Learn About Us">
                                </div>
                                <div>
                                    <label class="hs-label">Target Link</label>
                                    <input type="text" class="hs-input" name="hero_cta2_link" value="{{ $careerSettings->hero_cta2_link ?? '' }}" placeholder="#about">
                                </div>
                            </div>
                            <label class="hs-cta-newtab">
                                <input type="checkbox" name="hero_cta2_newtab" value="1" {{ !empty($careerSettings->hero_cta2_newtab) ? 'checked' : '' }} style="accent-color:#009ef7;">
                                Open in new tab
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 3: Background Style & Gradient --}}
            <div class="cps-card accent-purple">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon purple"><i class="ti ti-brush"></i></div>
                        <div>
                            <div class="cps-card-title">Hero Backdrop &amp; Gradients</div>
                            <div class="cps-card-sub">Select backdrop mode and brand-aligned gradient swatches.</div>
                        </div>
                    </div>
                    <button type="button" class="cps-btn-secondary" style="height:30px;font-size:11px;" onclick="resetHeroSection()">
                        <i class="ti ti-refresh"></i> Reset Hero
                    </button>
                </div>
                <div class="cps-card-body">
                    <div class="cps-subcard">
                        <div class="cps-subcard-kicker"><span>Backdrop Mode</span></div>
                        <div class="hs-bg-options">
                            @php $savedBgStyle = $careerSettings->hero_bg_style ?? 'gradient'; @endphp
                            <label class="hs-bg-opt"><input type="radio" name="hero_bg_style" value="gradient" {{ $savedBgStyle === 'gradient' ? 'checked' : '' }}> Gradient</label>
                            <label class="hs-bg-opt"><input type="radio" name="hero_bg_style" value="image" {{ $savedBgStyle === 'image' ? 'checked' : '' }}> Image</label>
                            <label class="hs-bg-opt"><input type="radio" name="hero_bg_style" value="video" {{ $savedBgStyle === 'video' ? 'checked' : '' }}> Video</label>
                            <label class="hs-bg-opt"><input type="radio" name="hero_bg_style" value="solid" {{ $savedBgStyle === 'solid' ? 'checked' : '' }}> Solid Color</label>
                        </div>

                        <label class="hs-label">Gradient Preset</label>
                        <div class="hs-grad-grid">
                            @php $savedBgGrad = $careerSettings->hero_bg_gradient ?? 'linear-gradient(135deg,#4F46E5,#7C3AED)'; @endphp
                            @foreach([
                                'linear-gradient(135deg,#4F46E5,#7C3AED)',
                                'linear-gradient(135deg,#0EA5E9,#2563EB)',
                                'linear-gradient(135deg,#10B981,#059669)',
                                'linear-gradient(135deg,#F59E0B,#EF4444)',
                                'linear-gradient(135deg,#EC4899,#8B5CF6)',
                                'linear-gradient(135deg,#0F172A,#1E293B)',
                            ] as $grad)
                                <div class="hs-grad-swatch {{ $savedBgGrad === $grad ? 'selected' : '' }}" style="background:{{ $grad }};"
                                     onclick="selectGradient(this,'{{ $grad }}')">
                                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                                </div>
                            @endforeach
                            <div class="hs-grad-custom" onclick="document.getElementById('customGradientPicker').click()">
                                <i class="ti ti-palette" style="font-size:15px;"></i>
                                <span>Custom</span>
                                <input type="color" id="customGradientPicker" style="display:none;"
                                       onchange="selectGradient(null,'linear-gradient(135deg,'+this.value+','+this.value+')')">
                            </div>
                        </div>
                        <input type="hidden" name="hero_bg_gradient" id="heroGradientInput" value="{{ $savedBgGrad }}">
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Sticky Preview & Illustration Column --}}
        <div class="cps-col-side sticky-side">

            {{-- Live Hero Preview --}}
            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-eye"></i></div>
                        <div>
                            <div class="cps-card-title">Hero Live Preview</div>
                            <div class="cps-card-sub">Real-time candidate viewport</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="hs-prev-wrap">
                        <div class="hs-prev-nav">
                            <div class="hs-prev-nav-logo">{{ $companyName }}</div>
                            <div class="hs-prev-nav-links">
                                <span class="hs-prev-nav-link">Jobs</span>
                                <span class="hs-prev-nav-link">About</span>
                                <span class="hs-prev-nav-link">Culture</span>
                            </div>
                            <div class="hs-prev-nav-btn">Apply Now</div>
                        </div>
                        <div class="hs-prev-hero" id="hsHeroPreview" style="background:{{ $savedBgGrad }};">
                            <div>
                                <div class="hs-prev-badge" id="hsPreviewBadge">
                                    <i class="ti ti-rocket" style="font-size:9px;"></i>
                                    <span>{{ !empty($careerSettings->hero_badge) ? $careerSettings->hero_badge : 'Hiring Top Talent' }}</span>
                                </div>
                                <div class="hs-prev-heading" id="hsPreviewHeading">
                                    {{ !empty($careerSettings->hero_heading) ? strip_tags($careerSettings->hero_heading) : 'Build the Future with ' . $companyName }}
                                </div>
                                <div class="hs-prev-desc" id="hsPreviewDesc">
                                    {{ !empty($careerSettings->hero_description) ? $careerSettings->hero_description : 'Join our team of innovators and help shape the future of technology.' }}
                                </div>
                                <div class="hs-prev-highlights">
                                    <div class="hs-prev-hl"><i class="ti ti-check"></i> Great Culture</div>
                                    <div class="hs-prev-hl"><i class="ti ti-check"></i> Remote Friendly</div>
                                </div>
                                <div class="hs-prev-btns">
                                    <div class="hs-prev-btn-primary" id="hsPreviewCta1">{{ !empty($careerSettings->hero_cta1_text) ? $careerSettings->hero_cta1_text : 'Explore Roles' }}</div>
                                    <div class="hs-prev-btn-secondary" id="hsPreviewCta2">{{ !empty($careerSettings->hero_cta2_text) ? $careerSettings->hero_cta2_text : 'Learn More' }}</div>
                                </div>
                            </div>
                            <div class="hs-prev-img">
                                @if(!empty($careerSettings->td_hero_img1_path))
                                    <img src="{{ asset($careerSettings->td_hero_img1_path) }}" alt="Hero Illustration">
                                @else
                                    <i class="ti ti-users" style="font-size:34px;color:rgba(255,255,255,0.85);"></i>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Hero Illustration Upload --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-photo-up"></i></div>
                        <div>
                            <div class="cps-card-title">Hero Illustration</div>
                            <div class="cps-card-sub">PNG, SVG · Max 2MB · 600×480px</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <label class="hs-upload-box" for="heroIllustrationUpload">
                        <div class="hs-upload-icon"><i class="ti ti-cloud-upload"></i></div>
                        <div class="hs-upload-title">Click to Upload Hero Artwork</div>
                        <div class="hs-upload-meta">Supports PNG, JPG, SVG, WebP</div>
                        <input type="file" id="heroIllustrationUpload" name="hero_illustration"
                               accept="image/*" style="display:none;" onchange="previewHeroIllus(this)">
                    </label>
                    <div class="hs-illus-grid">
                        <div class="hs-illus-thumb selected"><i class="ti ti-users" style="font-size:18px;color:#4f46e5;"></i></div>
                        <div class="hs-illus-thumb"><i class="ti ti-rocket" style="font-size:18px;color:#10b981;"></i></div>
                        <div class="hs-illus-thumb"><i class="ti ti-building" style="font-size:18px;color:#009ef7;"></i></div>
                        <div class="hs-illus-thumb"><i class="ti ti-chart-bar" style="font-size:18px;color:#ec4899;"></i></div>
                    </div>
                </div>
            </div>

            {{-- Best Practices --}}
            <div class="cps-card accent-amber">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon amber"><i class="ti ti-bulb"></i></div>
                        <div>
                            <div class="cps-card-title">Conversion Best Practices</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="hs-tip"><div class="hs-tip-icon" style="background:#eff6ff;color:#2563eb;">1</div><div>Keep your heading <strong>under 10 words</strong> for crisp mobile readability.</div></div>
                    <div class="hs-tip"><div class="hs-tip-icon" style="background:#fff7ed;color:#f97316;">2</div><div>Use action verbs in CTAs like <strong>Explore Roles</strong> or <strong>Join Our Team</strong>.</div></div>
                    <div class="hs-tip"><div class="hs-tip-icon" style="background:#ecfdf5;color:#16a34a;">3</div><div>Limit highlight tags to <strong>3–4 culture pillars</strong> for visual clarity.</div></div>
                </div>
            </div>

        </div>
    </div>
</div>
