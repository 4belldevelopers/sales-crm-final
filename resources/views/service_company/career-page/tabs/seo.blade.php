{{-- SEO & Social Tab Panel --}}
<div class="cps-panel" id="panel-seo">
<div class="row g-4">

    {{-- Left --}}
    <div class="col-lg-7">

        {{-- SEO Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">SEO Settings</div>
            <div class="cps-section-sub">Optimize your career page for search engines and social sharing.</div>

            <div class="row g-3">
                {{-- Meta Title --}}
                <div class="col-6">
                    <label class="cps-label">Meta Title</label>
                    <input type="text" class="seo-input" name="seo_meta_title" id="seoMetaTitle"
                           maxlength="60" value="{{ $careerSettings->seo_meta_title ?? ('Careers at ' . $companyName . ' | Join Our Team') }}"
                           oninput="document.getElementById('seoTitleCount').textContent=this.value.length+'/60';">
                    <div style="display:flex;justify-space-between;align-items:center;margin-top:3px;">
                        <span class="seo-hint">This will appear as the title in search engine results.</span>
                        <span class="seo-char" id="seoTitleCount">{{ strlen($careerSettings->seo_meta_title ?? ('Careers at ' . $companyName . ' | Join Our Team')) }}/60</span>
                    </div>
                </div>

                {{-- Meta Keywords --}}
                <div class="col-6">
                    <label class="cps-label">Meta Keywords <span style="font-size:10px;color:#94A3B8;font-weight:400;">(Optional)</span></label>
                    <input type="text" class="seo-input" name="seo_keywords" id="seoKeywords"
                           value="{{ $careerSettings->seo_keywords ?? 'careers, jobs, hiring, work with us, join our team' }}"
                           placeholder="careers, jobs, hiring">
                    <div class="seo-hint" style="margin-top:5px;">Add relevant keywords separated by commas.</div>
                </div>

                {{-- Meta Description --}}
                <div class="col-6">
                    <label class="cps-label">Meta Description</label>
                    <textarea class="seo-textarea" name="seo_meta_desc" id="seoMetaDesc" maxlength="160"
                              oninput="document.getElementById('seoDescCount').textContent=this.value.length+'/160';"
                              rows="4">{{ $careerSettings->seo_meta_desc ?? ('Explore exciting career opportunities at ' . $companyName . '. Join our mission to build innovative solutions and grow your career with a talented team.') }}</textarea>
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:3px;">
                        <span class="seo-hint">This will appear as the description in search engine results.</span>
                        <span class="seo-char" id="seoDescCount">{{ strlen($careerSettings->seo_meta_desc ?? ('Explore exciting career opportunities at ' . $companyName . '. Join our mission to build innovative solutions and grow your career with a talented team.')) }}/160</span>
                    </div>
                </div>

                {{-- Robots Meta Tag --}}
                <div class="col-6">
                    <label class="cps-label">Robots Meta Tag</label>
                    @php $seoRobots = $careerSettings->seo_robots ?? 'index,follow'; @endphp
                    <select class="seo-select" name="seo_robots" style="margin-top:2px;">
                        <option value="index,follow" {{ $seoRobots === 'index,follow' ? 'selected' : '' }}>Index, Follow</option>
                        <option value="noindex,follow" {{ $seoRobots === 'noindex,follow' ? 'selected' : '' }}>No Index, Follow</option>
                        <option value="index,nofollow" {{ $seoRobots === 'index,nofollow' ? 'selected' : '' }}>Index, No Follow</option>
                        <option value="noindex,nofollow" {{ $seoRobots === 'noindex,nofollow' ? 'selected' : '' }}>No Index, No Follow</option>
                    </select>
                    <div class="seo-hint">Choose how search engines should index your page.</div>
                </div>

                {{-- URL Slug --}}
                <div class="col-6">
                    <label class="cps-label">URL Slug</label>
                    <div class="seo-slug-wrap">
                        <input type="text" class="seo-input" name="seo_slug" id="seoSlug"
                               value="careers" placeholder="careers">
                    </div>
                    <div class="seo-slug-url">
                        Your career page URL will be:
                        <a href="{{ $careerUrl }}" target="_blank">{{ $careerUrl }}</a>
                        <i class="ti ti-external-link" style="font-size:10px;margin-left:2px;"></i>
                    </div>
                </div>

                {{-- Canonical URL --}}
                <div class="col-6">
                    <label class="cps-label">Canonical URL <span style="font-size:10px;color:#94A3B8;font-weight:400;">(Optional)</span></label>
                    <input type="url" class="seo-input" name="seo_canonical"
                           placeholder="https://www.yoursite.com/careers" value="{{ $careerSettings->seo_canonical ?? $careerUrl }}">
                    <div class="seo-hint">Use canonical URL to prevent duplicate content issues.</div>
                </div>
            </div>
        </div>

        {{-- Social Media Links --}}
        <div class="cps-section">
            <div class="cps-section-title">Social Media Links</div>
            <div class="cps-section-sub">Add your social media profiles to display on the career page.</div>

            @php
            $seoSocials = [
                ['icon'=>'ti-brand-linkedin','bg'=>'#EFF6FF','ic'=>'#0077B5','label'=>'LinkedIn',    'name'=>'seo_linkedin',   'url'=>$careerSettings->seo_linkedin ?? 'https://www.linkedin.com/company/4bell','checked'=>true],
                ['icon'=>'ti-brand-x',       'bg'=>'#F8FAFC', 'ic'=>'#0F172A','label'=>'X (Twitter)', 'name'=>'seo_twitter',    'url'=>$careerSettings->seo_twitter ?? 'https://twitter.com/4bell_tech','checked'=>true],
                ['icon'=>'ti-brand-facebook','bg'=>'#EFF6FF','ic'=>'#1877F2','label'=>'Facebook',    'name'=>'seo_facebook',   'url'=>$careerSettings->seo_facebook ?? 'https://www.facebook.com/4belltechnology','checked'=>true],
                ['icon'=>'ti-brand-instagram','bg'=>'#FFF1F2','ic'=>'#E1306C','label'=>'Instagram',  'name'=>'seo_instagram',  'url'=>$careerSettings->seo_instagram ?? 'https://www.instagram.com/4bell_tech','checked'=>true],
                ['icon'=>'ti-brand-youtube', 'bg'=>'#FFF1F2','ic'=>'#FF0000','label'=>'YouTube',     'name'=>'seo_youtube',    'url'=>$careerSettings->seo_youtube ?? 'https://www.youtube.com/@4belltech','checked'=>true],
            ];
            @endphp

            <div id="seoSocialList">
                @foreach($seoSocials as $ss)
                <div class="seo-social-row">
                    <div class="seo-social-icon" style="background:{{ $ss['bg'] }};">
                        <i class="ti {{ $ss['icon'] }}" style="color:{{ $ss['ic'] }};"></i>
                    </div>
                    <div class="seo-social-label">{{ $ss['label'] }}</div>
                    <input type="url" class="seo-social-input" name="{{ $ss['name'] }}" value="{{ $ss['url'] }}" placeholder="https://">
                    <label class="cps-switch" style="flex-shrink:0;">
                        <input type="checkbox" name="{{ $ss['name'] }}_enabled" value="1" {{ $ss['checked']?'checked':'' }}>
                        <span class="cps-slider"></span>
                    </label>
                </div>
                @endforeach
            </div>

            <button type="button" class="seo-add-link" onclick="seoAddSocialRow()">
                <i class="ti ti-plus" style="font-size:13px;"></i> Add Social Link
            </button>
        </div>

    </div>

    {{-- Right --}}
    <div class="col-lg-5">

        {{-- Search Engine Preview --}}
        <div class="cps-sidebar-card">
            <h4 style="margin:0 0 3px;">Search Engine Preview</h4>
            <p class="sub">See how your career page will appear in search results.</p>
            <div class="seo-se-prev">
                <div class="seo-se-url">https://www.4bell.com/careers</div>
                <div class="seo-se-title" id="seoPreviewTitle">Careers at 4Bell Technology | Join Our Team</div>
                <div class="seo-se-desc" id="seoPreviewDesc">Explore exciting career opportunities at 4Bell Technology. Join our mission to build innovative solutions and grow your career with a talented team.</div>
            </div>
        </div>

        {{-- Social Share Preview --}}
        <div class="cps-sidebar-card" style="margin-top:16px;padding:0;overflow:hidden;">
            <div style="padding:14px 18px 12px;border-bottom:1px solid #F1F5F9;">
                <h4 style="margin:0 0 2px;">Social Share Preview</h4>
                <p class="sub" style="margin:0;">See how your career page will look when shared on social platforms.</p>
            </div>
            <div style="padding:14px;">
                <div class="seo-share-prev">
                    <div class="seo-share-logo-bar">
                        <i class="ti ti-building" style="font-size:14px;color:#2563EB;"></i>
                        <span style="font-size:11px;font-weight:700;color:#0F172A;">{{ $companyName }}</span>
                    </div>
                    <div class="seo-share-hero">
                        {{-- decorative person silhouette --}}
                        <div style="position:absolute;right:0;top:0;bottom:0;width:45%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;">
                            <i class="ti ti-users" style="font-size:52px;color:rgba(255,255,255,.3);"></i>
                        </div>
                        <div class="seo-share-hero-text">
                            <div class="seo-share-hero-title">We're Building<br>the Future Together</div>
                            <div class="seo-share-hero-sub">Join our team of innovators and<br>create impact that matters.</div>
                        </div>
                    </div>
                    <div class="seo-share-body">
                        <div class="seo-share-og-title">Careers at 4Bell Technology | Join Our Team</div>
                        <div class="seo-share-og-desc">Explore exciting career opportunities at 4Bell Technology. Join our mission to build innovative solutions and grow your career with a talented team.</div>
                        <div class="seo-share-og-url">4bell.com</div>
                        <div class="seo-share-btns">
                            <div class="seo-share-btn"><i class="ti ti-brand-facebook" style="color:#1877F2;font-size:12px;"></i> Facebook</div>
                            <div class="seo-share-btn"><i class="ti ti-brand-linkedin" style="color:#0077B5;font-size:12px;"></i> LinkedIn</div>
                            <div class="seo-share-btn"><i class="ti ti-brand-x" style="color:#0F172A;font-size:12px;"></i> X (Twitter)</div>
                            <div class="seo-share-btn"><i class="ti ti-brand-whatsapp" style="color:#25D366;font-size:12px;"></i> WhatsApp</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Additional SEO Options --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <h4 style="margin:0 0 4px;">Additional SEO Options</h4>
            @php
            $seoAddOpts = [
                ['icon'=>'ti-world',        'bg'=>'#EFF6FF','ic'=>'#2563EB','title'=>'Enable Open Graph Tags',         'desc'=>'Allow search engines to better understand your content.',   'name'=>'seo_og_tags',      'checked'=>true],
                ['icon'=>'ti-brand-twitter','bg'=>'#EFF6FF','ic'=>'#1DA1F2','title'=>'Enable Twitter Card Tags',       'desc'=>'Optimize your page for better visibility on Twitter.',      'name'=>'seo_twitter_card', 'checked'=>true],
                ['icon'=>'ti-code',         'bg'=>'#F0FDF4','ic'=>'#16A34A','title'=>'Add Structured Data (JSON-LD)',  'desc'=>'Help search engines understand your job postings.',         'name'=>'seo_structured_data','checked'=>true],
                ['icon'=>'ti-sitemap',      'bg'=>'#FAF5FF','ic'=>'#7C3AED','title'=>'Automatically Generate Sitemap','desc'=>'Include career page in sitemap.xml',                        'name'=>'seo_sitemap',      'checked'=>true],
            ];
            @endphp
            @foreach($seoAddOpts as $ao)
            <div class="seo-addopt-row">
                <div class="seo-addopt-left">
                    <div class="seo-addopt-icon" style="background:{{ $ao['bg'] }};">
                        <i class="ti {{ $ao['icon'] }}" style="font-size:15px;color:{{ $ao['ic'] }};"></i>
                    </div>
                    <div>
                        <div class="seo-addopt-title">{{ $ao['title'] }}</div>
                        <div class="seo-addopt-desc">{{ $ao['desc'] }}</div>
                    </div>
                </div>
                <label class="cps-switch" style="flex-shrink:0;margin-top:3px;">
                    <input type="checkbox" name="{{ $ao['name'] }}" value="1" {{ $ao['checked']?'checked':'' }}>
                    <span class="cps-slider"></span>
                </label>
            </div>
            @endforeach
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
