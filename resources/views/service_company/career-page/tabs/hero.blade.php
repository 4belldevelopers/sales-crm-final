{{-- Hero Section Tab Panel --}}
<div class="cps-panel" id="panel-hero">
<div class="row g-4">

    {{-- Left: Controls --}}
    <div class="col-lg-7">

        {{-- Layout Style --}}
        <div class="cps-section">
            <div class="cps-section-title">Layout Style</div>
            <div class="cps-section-sub">Choose how your hero section is structured</div>
            @php $currentLayout = $careerSettings->hero_layout ?? 'left-right'; @endphp
            <div class="hs-layout-grid">
                <div class="hs-layout-card {{ $currentLayout === 'left-right' ? 'selected' : '' }}" onclick="selectHeroLayout('left-right',this)">
                    <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                    <div class="hs-lc-thumb">
                        <svg width="52" height="30" viewBox="0 0 52 30"><rect x="2" y="4" width="22" height="22" rx="2" fill="#A5B4FC"/><rect x="28" y="4" width="22" height="22" rx="2" fill="#C7D2FE"/></svg>
                    </div>
                    <div class="hs-lc-name">Left Content<br>Right Image</div>
                </div>
                <div class="hs-layout-card {{ $currentLayout === 'center' ? 'selected' : '' }}" onclick="selectHeroLayout('center',this)">
                    <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                    <div class="hs-lc-thumb">
                        <svg width="52" height="30" viewBox="0 0 52 30"><rect x="8" y="8" width="36" height="14" rx="2" fill="#A5B4FC"/></svg>
                    </div>
                    <div class="hs-lc-name">Center<br>Content</div>
                </div>
                <div class="hs-layout-card {{ $currentLayout === 'fullwidth' ? 'selected' : '' }}" onclick="selectHeroLayout('fullwidth',this)">
                    <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                    <div class="hs-lc-thumb">
                        <svg width="52" height="30" viewBox="0 0 52 30"><rect x="2" y="2" width="48" height="26" rx="2" fill="#A5B4FC"/></svg>
                    </div>
                    <div class="hs-lc-name">Full Width<br>Background</div>
                </div>
                <div class="hs-layout-card {{ $currentLayout === 'video' ? 'selected' : '' }}" onclick="selectHeroLayout('video',this)">
                    <div class="hs-lc-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                    <div class="hs-lc-thumb">
                        <svg width="52" height="30" viewBox="0 0 52 30"><rect x="2" y="2" width="48" height="26" rx="2" fill="#C7D2FE"/><polygon points="20,10 20,20 34,15" fill="#6366F1"/></svg>
                    </div>
                    <div class="hs-lc-name">Video<br>Background</div>
                </div>
            </div>
            <input type="hidden" name="hero_layout" id="heroLayoutInput" value="{{ $currentLayout }}">
        </div>

        {{-- Badge / Eyebrow Text --}}
        <div class="cps-section">
            <label class="hs-label">Badge / Eyebrow Text</label>
            <input type="text" class="hs-input" name="hero_badge" id="heroBadge"
                   maxlength="80" placeholder="e.g. 🚀 Now Hiring — Join Our Team"
                   value="{{ $careerSettings->hero_badge ?? '' }}"
                   oninput="document.getElementById('heroBadgeCount').textContent=this.value.length+'/80';">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                <span class="hs-hint">Short text shown above the heading to grab attention</span>
                <span class="hs-char-count" id="heroBadgeCount">{{ strlen($careerSettings->hero_badge ?? '') }}/80</span>
            </div>
        </div>

        {{-- Main Heading --}}
        <div class="cps-section">
            <label class="hs-label">Main Heading</label>
            <input type="text" class="hs-input" name="hero_heading" id="heroHeading"
                   maxlength="80" placeholder="e.g. Build the Future with TechCorp"
                   value="{{ $careerSettings->hero_heading ?? '' }}"
                   oninput="document.getElementById('heroHeadingCount').textContent=this.value.length+'/80';">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                <span class="hs-hint">Use <code style="font-size:10px;background:#F1F5F9;padding:1px 4px;border-radius:4px;">&lt;span class="text-primary"&gt;keyword&lt;/span&gt;</code> to highlight a word</span>
                <span class="hs-char-count" id="heroHeadingCount">{{ strlen($careerSettings->hero_heading ?? '') }}/80</span>
            </div>
        </div>

        {{-- Description --}}
        <div class="cps-section">
            <label class="hs-label">Description</label>
            <textarea class="hs-textarea" name="hero_description" id="heroDesc" maxlength="200" rows="3"
                      placeholder="e.g. Join our team of innovators and help shape the future of technology. We offer great culture, growth, and benefits."
                      oninput="document.getElementById('heroDescCount').textContent=this.value.length+'/200';">{{ $careerSettings->hero_description ?? '' }}</textarea>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px;">
                <span class="hs-hint">Brief description shown below the heading</span>
                <span class="hs-char-count" id="heroDescCount">{{ strlen($careerSettings->hero_description ?? '') }}/200</span>
            </div>
        </div>

        {{-- Highlight Points --}}
        <div class="cps-section">
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
            <div class="hs-hint" style="margin-top:6px;">These appear as checkmarks in the hero section</div>
        </div>

        {{-- Primary CTA Button --}}
        <div class="cps-section">
            <label class="hs-label">Primary CTA Button</label>
            <div class="hs-cta-grid">
                <div>
                    <label class="hs-label" style="font-weight:500;font-size:11px;">Button Text</label>
                    <input type="text" class="hs-input" name="hero_cta1_text" value="{{ $careerSettings->hero_cta1_text ?? '' }}" placeholder="e.g. Explore Open Roles">
                </div>
                <div>
                    <label class="hs-label" style="font-weight:500;font-size:11px;">Button Link</label>
                    <input type="text" class="hs-input" name="hero_cta1_link" value="{{ $careerSettings->hero_cta1_link ?? '' }}" placeholder="e.g. /careers/jobs">
                </div>
            </div>
            <label class="hs-cta-newtab" style="margin-top:8px;">
                <input type="checkbox" name="hero_cta1_newtab" value="1" {{ !empty($careerSettings->hero_cta1_newtab) ? 'checked' : '' }} style="accent-color:#2563EB;width:14px;height:14px;">
                Open in new tab
            </label>
        </div>

        {{-- Secondary CTA --}}
        <div class="cps-section">
            <label class="hs-label">Secondary CTA Button <span style="font-size:10px;color:#94A3B8;font-weight:400;">(Optional)</span></label>
            <div class="hs-cta-grid">
                <div>
                    <label class="hs-label" style="font-weight:500;font-size:11px;">Button Text</label>
                    <input type="text" class="hs-input" name="hero_cta2_text" value="{{ $careerSettings->hero_cta2_text ?? '' }}" placeholder="e.g. Learn About Us">
                </div>
                <div>
                    <label class="hs-label" style="font-weight:500;font-size:11px;">Button Link</label>
                    <input type="text" class="hs-input" name="hero_cta2_link" value="{{ $careerSettings->hero_cta2_link ?? '' }}" placeholder="e.g. /about">
                </div>
            </div>
            <label class="hs-cta-newtab" style="margin-top:8px;">
                <input type="checkbox" name="hero_cta2_newtab" value="1" {{ !empty($careerSettings->hero_cta2_newtab) ? 'checked' : '' }} style="accent-color:#2563EB;width:14px;height:14px;">
                Open in new tab
            </label>
        </div>

        {{-- Background Style --}}
        <div class="cps-section">
            <div class="cps-section-title">Background Style</div>
            <div class="cps-section-sub">Choose the background style for your hero section</div>
            <div class="hs-bg-options">
                @php $savedBgStyle = $careerSettings->hero_bg_style ?? 'gradient'; @endphp
                <label class="hs-bg-opt">
                    <input type="radio" name="hero_bg_style" value="gradient" {{ $savedBgStyle === 'gradient' ? 'checked' : '' }}> Gradient
                </label>
                <label class="hs-bg-opt">
                    <input type="radio" name="hero_bg_style" value="image" {{ $savedBgStyle === 'image' ? 'checked' : '' }}> Image
                </label>
                <label class="hs-bg-opt">
                    <input type="radio" name="hero_bg_style" value="video" {{ $savedBgStyle === 'video' ? 'checked' : '' }}> Video
                </label>
                <label class="hs-bg-opt">
                    <input type="radio" name="hero_bg_style" value="solid" {{ $savedBgStyle === 'solid' ? 'checked' : '' }}> Solid Color
                </label>
            </div>
            <label class="hs-label" style="margin-bottom:10px;">Gradient Style</label>
            <div class="hs-grad-grid">
                <div class="hs-grad-swatch selected" style="background:linear-gradient(135deg,#4F46E5,#7C3AED);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#4F46E5,#7C3AED)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-swatch" style="background:linear-gradient(135deg,#0EA5E9,#2563EB);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#0EA5E9,#2563EB)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-swatch" style="background:linear-gradient(135deg,#10B981,#059669);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#10B981,#059669)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-swatch" style="background:linear-gradient(135deg,#F59E0B,#EF4444);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#F59E0B,#EF4444)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-swatch" style="background:linear-gradient(135deg,#EC4899,#8B5CF6);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#EC4899,#8B5CF6)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-swatch" style="background:linear-gradient(135deg,#0F172A,#1E293B);"
                     onclick="selectGradient(this,'linear-gradient(135deg,#0F172A,#1E293B)')">
                    <div class="hs-gs-check"><i class="ti ti-check" style="font-size:8px;color:#fff;"></i></div>
                </div>
                <div class="hs-grad-custom" onclick="document.getElementById('customGradientPicker').click()">
                    <i class="ti ti-palette" style="font-size:16px;"></i>
                    <span>Custom</span>
                    <input type="color" id="customGradientPicker" style="display:none;"
                           onchange="selectGradient(null,'linear-gradient(135deg,'+this.value+','+this.value+')')">
                </div>
            </div>
            <input type="hidden" name="hero_bg_gradient" id="heroGradientInput" value="linear-gradient(135deg,#4F46E5,#7C3AED)">
        </div>

    </div>

    {{-- Right: Preview + Upload + Tips --}}
    <div class="col-lg-5">

        {{-- Live Preview --}}
        <div class="cps-sidebar-card" style="padding:16px;">
            <h4 style="margin-bottom:3px;">Live Preview</h4>
            <p class="sub">How your hero section will look</p>
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
                @php
                    $savedBgGrad = $careerSettings->hero_bg_gradient ?? 'linear-gradient(135deg,#EEF2FF,#E0E7FF)';
                @endphp
                <div class="hs-prev-hero" id="hsHeroPreview" style="background:{{ $savedBgGrad }};">
                    <div>
                        <div class="hs-prev-badge" id="hsPreviewBadge">
                            <i class="ti ti-rocket" style="font-size:8px;"></i>
                            <span>{{ !empty($careerSettings->hero_badge) ? $careerSettings->hero_badge : '🚀 Hiring Top Talent' }}</span>
                        </div>
                        <div class="hs-prev-heading" id="hsPreviewHeading">
                            @if(!empty($careerSettings->hero_heading))
                                {!! $careerSettings->hero_heading !!}
                            @else
                                Build the Future with <span>{{ $companyName }}</span>
                            @endif
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
                        <i class="ti ti-users" style="font-size:36px;color:#818CF8;"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Background Illustration --}}
        <div class="cps-sidebar-card" style="padding:16px;">
            <h4 style="margin-bottom:3px;">Background Illustration</h4>
            <p class="sub">Upload or choose an illustration for the hero section</p>
            <label class="hs-upload-box" for="heroIllustrationUpload">
                <div class="hs-upload-icon"><i class="ti ti-cloud-upload"></i></div>
                <div class="hs-upload-title">Click to Upload Image</div>
                <div class="hs-upload-meta">PNG, SVG · Max 2MB · Recommended 600×480px</div>
                <input type="file" id="heroIllustrationUpload" name="hero_illustration"
                       accept="image/*" style="display:none;" onchange="previewHeroIllus(this)">
            </label>
            <div class="hs-illus-grid" style="margin-top:12px;">
                <div class="hs-illus-thumb selected">
                    <i class="ti ti-users" style="font-size:18px;color:#818CF8;"></i>
                </div>
                <div class="hs-illus-thumb">
                    <i class="ti ti-rocket" style="font-size:18px;color:#34D399;"></i>
                </div>
                <div class="hs-illus-thumb">
                    <i class="ti ti-building" style="font-size:18px;color:#60A5FA;"></i>
                </div>
                <div class="hs-illus-thumb">
                    <i class="ti ti-chart-bar" style="font-size:18px;color:#F472B6;"></i>
                </div>
            </div>
        </div>

        {{-- Tips --}}
        <div class="cps-sidebar-card" style="padding:16px;">
            <h4 style="margin-bottom:10px;">💡 Tips for a great hero</h4>
            <div class="hs-tips">
                <div class="hs-tip">
                    <div class="hs-tip-icon" style="background:#EFF6FF;color:#2563EB;">1</div>
                    <div>Keep your heading <strong>under 10 words</strong> for best readability on mobile.</div>
                </div>
                <div class="hs-tip">
                    <div class="hs-tip-icon" style="background:#FFF7ED;color:#F97316;">2</div>
                    <div>Use <strong>action words</strong> in your CTA: "Explore Roles", "Join Us Today".</div>
                </div>
                <div class="hs-tip">
                    <div class="hs-tip-icon" style="background:#F0FDF4;color:#16A34A;">3</div>
                    <div>Add <strong>3–5 highlight points</strong> to quickly communicate your culture.</div>
                </div>
                <div class="hs-tip">
                    <div class="hs-tip-icon" style="background:#FFF1F2;color:#E11D48;">4</div>
                    <div>Choose a <strong>gradient that matches</strong> your brand's primary color.</div>
                </div>
                <div class="hs-tip">
                    <div class="hs-tip-icon" style="background:#FAF5FF;color:#7C3AED;">5</div>
                    <div>A <strong>high-contrast illustration</strong> on the right keeps focus on the text.</div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- Bottom action bar --}}
<div class="hs-action-bar">
    <button type="button" class="hs-reset-btn" onclick="resetHeroSection()">
        <i class="ti ti-refresh" style="font-size:14px;margin-right:4px;"></i> Reset to Default
    </button>
    <button type="submit" form="careerSettingsForm" class="hs-save-btn">
        <i class="ti ti-device-floppy" style="font-size:15px;"></i> Save Changes
    </button>
</div>

</div>
