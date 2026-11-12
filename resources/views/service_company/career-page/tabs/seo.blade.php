{{-- SEO & Social Tab Panel (Rule 20 Card + Sub-Card System) --}}
<div class="cps-panel" id="panel-seo">
    <div class="cps-workspace-grid">

        {{-- Left Main Column --}}
        <div class="cps-col-main">

            {{-- Primary Card 1: Search Engine Optimization --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-world-search"></i></div>
                        <div>
                            <div class="cps-card-title">Search Engine Optimization (SEO)</div>
                            <div class="cps-card-sub">Optimize search snippets, crawler directives, and canonical URLs.</div>
                        </div>
                    </div>
                    <span class="cps-card-pill blue">{{ $careerSettings->seo_robots ?? 'index,follow' }}</span>
                </div>

                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        {{-- Meta Title --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker">
                                <span>Search Title</span>
                                <span class="seo-char" id="seoTitleCount">{{ strlen($careerSettings->seo_meta_title ?? ('Careers at ' . $companyName . ' | Join Our Team')) }}/60</span>
                            </div>
                            <label class="cps-label">Meta Title</label>
                            <input type="text" class="seo-input" name="seo_meta_title" id="seoMetaTitle"
                                   maxlength="60" value="{{ $careerSettings->seo_meta_title ?? ('Careers at ' . $companyName . ' | Join Our Team') }}"
                                   oninput="document.getElementById('seoTitleCount').textContent=this.value.length+'/60';">
                            <div class="seo-hint">Displayed as the clickable headline on Google.</div>
                        </div>

                        {{-- Meta Keywords --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Keywords</span></div>
                            <label class="cps-label">Meta Keywords <span style="font-size:10px;color:#94a3b8;font-weight:400;">(Optional)</span></label>
                            <input type="text" class="seo-input" name="seo_keywords" id="seoKeywords"
                                   value="{{ $careerSettings->seo_keywords ?? 'careers, jobs, hiring, work with us, join our team' }}"
                                   placeholder="careers, jobs, hiring">
                            <div class="seo-hint">Comma-separated discovery keywords.</div>
                        </div>

                        {{-- Meta Description --}}
                        <div class="cps-subcard span-2">
                            <div class="cps-subcard-kicker">
                                <span>Snippet Description</span>
                                <span class="seo-char" id="seoDescCount">{{ strlen($careerSettings->seo_meta_desc ?? ('Explore exciting career opportunities at ' . $companyName . '. Join our mission to build innovative solutions and grow your career with a talented team.')) }}/160</span>
                            </div>
                            <label class="cps-label">Meta Description</label>
                            <textarea class="seo-textarea" name="seo_meta_desc" id="seoMetaDesc" maxlength="160" rows="3"
                                      oninput="document.getElementById('seoDescCount').textContent=this.value.length+'/160';">{{ $careerSettings->seo_meta_desc ?? ('Explore exciting career opportunities at ' . $companyName . '. Join our mission to build innovative solutions and grow your career with a talented team.') }}</textarea>
                        </div>

                        {{-- Robots Meta --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Crawler Policy</span></div>
                            <label class="cps-label">Robots Meta Tag</label>
                            @php $seoRobots = $careerSettings->seo_robots ?? 'index,follow'; @endphp
                            <select class="seo-select" name="seo_robots">
                                <option value="index,follow" {{ $seoRobots === 'index,follow' ? 'selected' : '' }}>Index, Follow</option>
                                <option value="noindex,follow" {{ $seoRobots === 'noindex,follow' ? 'selected' : '' }}>No Index, Follow</option>
                                <option value="index,nofollow" {{ $seoRobots === 'index,nofollow' ? 'selected' : '' }}>Index, No Follow</option>
                                <option value="noindex,nofollow" {{ $seoRobots === 'noindex,nofollow' ? 'selected' : '' }}>No Index, No Follow</option>
                            </select>
                        </div>

                        {{-- URL Slug --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Route Slug</span></div>
                            <label class="cps-label">URL Slug</label>
                            <input type="text" class="seo-input" name="seo_slug" id="seoSlug" value="careers" placeholder="careers">
                            <div class="seo-slug-url">
                                Live URL: <a href="{{ $careerUrl }}" target="_blank">{{ $careerUrl }}</a>
                            </div>
                        </div>

                        {{-- Canonical URL --}}
                        <div class="cps-subcard span-2">
                            <div class="cps-subcard-kicker"><span>Deduplication</span></div>
                            <label class="cps-label">Canonical URL <span style="font-size:10px;color:#94a3b8;font-weight:400;">(Optional)</span></label>
                            <input type="url" class="seo-input" name="seo_canonical"
                                   placeholder="https://www.yoursite.com/careers" value="{{ $careerSettings->seo_canonical ?? $careerUrl }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 2: Social Profiles --}}
            <div class="cps-card accent-emerald">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon emerald"><i class="ti ti-share"></i></div>
                        <div>
                            <div class="cps-card-title">Social Media Profiles</div>
                            <div class="cps-card-sub">Link official social channels in your career portal header and footer.</div>
                        </div>
                    </div>
                    <button type="button" class="cps-btn-secondary" style="height:28px;font-size:11px;" onclick="seoAddSocialRow()">
                        <i class="ti ti-plus"></i> Add Link
                    </button>
                </div>

                <div class="cps-card-body">
                    @php
                    $seoSocials = [
                        ['icon'=>'ti-brand-linkedin',  'bg'=>'#eff6ff','ic'=>'#0077b5','label'=>'LinkedIn',    'name'=>'seo_linkedin',  'url'=>$careerSettings->seo_linkedin ?? '',  'checked'=>true],
                        ['icon'=>'ti-brand-x',         'bg'=>'#f8fafc','ic'=>'#0f172a','label'=>'X (Twitter)', 'name'=>'seo_twitter',   'url'=>$careerSettings->seo_twitter ?? '',   'checked'=>true],
                        ['icon'=>'ti-brand-facebook',  'bg'=>'#eff6ff','ic'=>'#1877f2','label'=>'Facebook',    'name'=>'seo_facebook',  'url'=>$careerSettings->seo_facebook ?? '',  'checked'=>true],
                        ['icon'=>'ti-brand-instagram', 'bg'=>'#fff1f2','ic'=>'#e1306c','label'=>'Instagram',   'name'=>'seo_instagram', 'url'=>$careerSettings->seo_instagram ?? '', 'checked'=>true],
                        ['icon'=>'ti-brand-youtube',   'bg'=>'#fff1f2','ic'=>'#ff0000','label'=>'YouTube',     'name'=>'seo_youtube',   'url'=>$careerSettings->seo_youtube ?? '',   'checked'=>true],
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
                                    <input type="checkbox" name="{{ $ss['name'] }}_enabled" value="1" {{ $ss['checked'] ? 'checked' : '' }}>
                                    <span class="cps-slider"></span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Sticky Preview & Schema Column --}}
        <div class="cps-col-side sticky-side">

            {{-- Google Search Snippet Preview --}}
            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-brand-google"></i></div>
                        <div>
                            <div class="cps-card-title">Search Engine Preview</div>
                            <div class="cps-card-sub">Live SERP snippet simulation</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="seo-se-prev">
                        <div class="seo-se-url">{{ $careerUrl }}</div>
                        <div class="seo-se-title" id="seoPreviewTitle">{{ $careerSettings->seo_meta_title ?? ('Careers at ' . $companyName . ' | Join Our Team') }}</div>
                        <div class="seo-se-desc" id="seoPreviewDesc">{{ $careerSettings->seo_meta_desc ?? ('Explore exciting career opportunities at ' . $companyName . '. Join our mission to build innovative solutions and grow your career with a talented team.') }}</div>
                    </div>
                </div>
            </div>

            {{-- Social Open Graph Preview --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-social"></i></div>
                        <div>
                            <div class="cps-card-title">Social Share Card (OG)</div>
                            <div class="cps-card-sub">LinkedIn, X &amp; WhatsApp link unfurl</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="seo-share-prev">
                        <div class="seo-share-logo-bar">
                            <i class="ti ti-building" style="font-size:13px;color:#009ef7;"></i>
                            <span style="font-size:11px;font-weight:700;color:#0f172a;">{{ $companyName }}</span>
                        </div>
                        <div class="seo-share-hero">
                            <div style="position:absolute;right:0;top:0;bottom:0;width:40%;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;">
                                <i class="ti ti-users" style="font-size:46px;color:rgba(255,255,255,0.3);"></i>
                            </div>
                            <div class="seo-share-hero-text">
                                <div class="seo-share-hero-title">We're Building<br>the Future Together</div>
                                <div class="seo-share-hero-sub">Explore open roles at {{ $companyName }}</div>
                            </div>
                        </div>
                        <div class="seo-share-body">
                            <div class="seo-share-og-title" id="seoOgPreviewTitle">{{ $careerSettings->seo_meta_title ?? ('Careers at ' . $companyName . ' | Join Our Team') }}</div>
                            <div class="seo-share-og-desc" id="seoOgPreviewDesc">{{ $careerSettings->seo_meta_desc ?? ('Explore exciting career opportunities at ' . $companyName . '.') }}</div>
                            <div class="seo-share-og-url">{{ $companyHost }}</div>
                            <div class="seo-share-btns">
                                <div class="seo-share-btn"><i class="ti ti-brand-linkedin" style="color:#0077b5;"></i> LinkedIn</div>
                                <div class="seo-share-btn"><i class="ti ti-brand-x" style="color:#0f172a;"></i> X</div>
                                <div class="seo-share-btn"><i class="ti ti-brand-whatsapp" style="color:#25d366;"></i> WhatsApp</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Structured Data & Technical SEO --}}
            <div class="cps-card accent-purple">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon purple"><i class="ti ti-code"></i></div>
                        <div><div class="cps-card-title">Technical SEO &amp; Schema</div></div>
                    </div>
                </div>
                <div class="cps-card-body">
                    @php
                    $seoAddOpts = [
                        ['icon'=>'ti-world',        'bg'=>'#eff6ff','ic'=>'#009ef7','title'=>'Open Graph Tags',            'desc'=>'Rich previews on LinkedIn & Facebook.',   'name'=>'seo_og_tags',         'checked'=>true],
                        ['icon'=>'ti-brand-twitter','bg'=>'#eff6ff','ic'=>'#1da1f2','title'=>'Twitter Card Tags',          'desc'=>'Summary large image cards on X.',         'name'=>'seo_twitter_card',    'checked'=>true],
                        ['icon'=>'ti-code',         'bg'=>'#ecfdf5','ic'=>'#059669','title'=>'Structured Data (JSON-LD)',  'desc'=>'Google for Jobs schema enrichment.',      'name'=>'seo_structured_data', 'checked'=>true],
                        ['icon'=>'ti-sitemap',      'bg'=>'#f5f3ff','ic'=>'#7c3aed','title'=>'Auto-Generate Sitemap',      'desc'=>'Include portal roles in sitemap.xml.',    'name'=>'seo_sitemap',         'checked'=>true],
                    ];
                    @endphp
                    @foreach($seoAddOpts as $ao)
                        <div class="seo-addopt-row">
                            <div class="seo-addopt-left">
                                <div class="seo-addopt-icon" style="background:{{ $ao['bg'] }};">
                                    <i class="ti {{ $ao['icon'] }}" style="font-size:14px;color:{{ $ao['ic'] }};"></i>
                                </div>
                                <div>
                                    <div class="seo-addopt-title">{{ $ao['title'] }}</div>
                                    <div class="seo-addopt-desc">{{ $ao['desc'] }}</div>
                                </div>
                            </div>
                            <label class="cps-switch" style="flex-shrink:0;">
                                <input type="checkbox" name="{{ $ao['name'] }}" value="1" {{ $ao['checked'] ? 'checked' : '' }}>
                                <span class="cps-slider"></span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>
