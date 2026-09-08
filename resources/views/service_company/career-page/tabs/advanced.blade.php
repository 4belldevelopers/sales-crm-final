{{-- Advanced Tab Panel --}}
<div class="cps-panel" id="panel-advanced">
<div class="row g-4">

    {{-- Left --}}
    <div class="col-lg-6">

        {{-- Performance & Caching --}}
        <div class="cps-section">
            <div class="cps-section-title">Performance & Caching</div>
            <div class="cps-section-sub">Improve speed and deliver a seamless experience to your candidates.</div>

            <div class="row g-0">
                <div class="col-7" style="padding-right:16px;">
                    <div class="adv-toggle-row">
                        <div>
                            <div class="adv-toggle-label">Enable Caching</div>
                            <div class="adv-toggle-desc">Cache career page content for faster load times.</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="adv_caching" value="1" {{ !isset($careerSettings->adv_caching) || !empty($careerSettings->adv_caching) ? 'checked' : '' }}>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    <div style="padding:12px 0 12px;">
                        <div class="adv-toggle-label" style="margin-bottom:4px;">Cache Duration (TTL)</div>
                        <div class="adv-hint" style="margin-bottom:4px;">Select how long cached content should be stored.</div>
                        @php $advTtl = $careerSettings->adv_cache_ttl ?? 86400; @endphp
                        <select class="adv-select" name="adv_cache_ttl">
                            <option value="1800" {{ $advTtl == 1800 ? 'selected' : '' }}>30 Minutes</option>
                            <option value="3600" {{ $advTtl == 3600 ? 'selected' : '' }}>1 Hour</option>
                            <option value="21600" {{ $advTtl == 21600 ? 'selected' : '' }}>6 Hours</option>
                            <option value="86400" {{ $advTtl == 86400 ? 'selected' : '' }}>24 Hours</option>
                        </select>
                    </div>
                    <div class="adv-toggle-row">
                        <div>
                            <div class="adv-toggle-label">CDN Delivery</div>
                            <div class="adv-toggle-desc">Serve career page assets (images, CSS, JS) via CDN.</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="adv_cdn" value="1" {{ !isset($careerSettings->adv_cdn) || !empty($careerSettings->adv_cdn) ? 'checked' : '' }}>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    <div class="adv-toggle-row" style="border-bottom:none;padding-bottom:0;">
                        <div>
                            <div class="adv-toggle-label">Image Optimization</div>
                            <div class="adv-toggle-desc">Automatically compress and optimize images.</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="adv_img_opt" value="1" {{ !isset($careerSettings->adv_img_opt) || !empty($careerSettings->adv_img_opt) ? 'checked' : '' }}>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                </div>
                <div class="col-5">
                    <div class="adv-perf-card">
                        {{-- SVG gauge --}}
                        <div class="adv-gauge-wrap">
                            <svg width="100" height="58" viewBox="0 0 100 58">
                                <path d="M10 55 A40 40 0 0 1 90 55" fill="none" stroke="#E2E8F0" stroke-width="8" stroke-linecap="round"/>
                                <path d="M10 55 A40 40 0 0 1 90 55" fill="none" stroke="#16A34A" stroke-width="8" stroke-linecap="round" stroke-dasharray="125.6" stroke-dashoffset="5"/>
                            </svg>
                            <div style="margin-top:-10px;">
                                <span class="adv-gauge-score">97</span><span class="adv-gauge-total">/100</span>
                                <div style="font-size:10px;color:#94A3B8;margin-top:1px;">Performance Score</div>
                                <div class="adv-gauge-label">Excellent</div>
                            </div>
                        </div>
                        @foreach(['Page Speed'=>'Excellent','First Contentful Paint'=>'Excellent','Largest Contentful Paint'=>'Excellent','Cumulative Layout Shift'=>'Good'] as $metric=>$rating)
                        <div class="adv-perf-check">
                            <span class="adv-perf-check-name"><i class="ti ti-circle-check-filled"></i> {{ $metric }}</span>
                            <span class="{{ $rating==='Good'?'adv-perf-good':'adv-perf-excellent' }}">{{ $rating }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Security & Access --}}
        <div class="cps-section">
            <div class="cps-section-title">Security & Access</div>
            <div class="cps-section-sub">Control access and protect your career page.</div>

            <div class="row g-0">
                <div class="col-7" style="padding-right:16px;">
                    @php
                    $secToggles = [
                        ['name'=>'adv_pwd_protect',   'label'=>'Password Protection',           'desc'=>'Restrict access to your career page with a password.',      'checked'=>false],
                        ['name'=>'adv_ip_whitelist',  'label'=>'Allowed IP Addresses',          'desc'=>'Allow access only from specific IP addresses.',             'checked'=>false],
                        ['name'=>'adv_block_indexing','label'=>'Block Search Engine Indexing',  'desc'=>'Prevent search engines from indexing your career page.',    'checked'=>false],
                        ['name'=>'adv_recaptcha',     'label'=>'Enable reCAPTCHA on Apply Form','desc'=>'Add reCAPTCHA to prevent spam applications.',              'checked'=>true],
                    ];
                    @endphp
                    @foreach($secToggles as $st)
                    <div class="adv-toggle-row" style="{{ $loop->last?'border-bottom:none;padding-bottom:0;':'' }}">
                        <div>
                            <div class="adv-toggle-label">{{ $st['label'] }}</div>
                            <div class="adv-toggle-desc">{{ $st['desc'] }}</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="{{ $st['name'] }}" value="1" {{ $st['checked']?'checked':'' }}>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    @endforeach
                </div>
                <div class="col-5">
                    <div class="adv-sec-card">
                        <div class="adv-toggle-label" style="margin-bottom:6px;">Career Page URL</div>
                        <a href="{{ $careerUrl }}" target="_blank" class="adv-sec-link">
                            {{ $careerUrl }} <i class="ti ti-external-link" style="font-size:11px;"></i>
                        </a>
                        <button type="button" class="adv-copy-btn" onclick="copyCareerUrl()">Copy Link</button>
                        <div class="adv-share-title">Share Preview</div>
                        <div class="adv-share-desc">Share a preview of your career page.</div>
                        <div class="adv-share-icons">
                            <div class="adv-share-icon" style="background:#EFF6FF;"><i class="ti ti-brand-linkedin" style="color:#0077B5;"></i></div>
                            <div class="adv-share-icon" style="background:#EFF6FF;"><i class="ti ti-brand-facebook" style="color:#1877F2;"></i></div>
                            <div class="adv-share-icon" style="background:#F8FAFC;"><i class="ti ti-brand-x" style="color:#0F172A;"></i></div>
                            <div class="adv-share-icon" style="background:#F0FDF4;"><i class="ti ti-mail" style="color:#16A34A;"></i></div>
                        </div>
                        <button type="button" class="adv-preview-btn">
                            View Public Preview <i class="ti ti-external-link" style="font-size:11px;"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Maintenance Mode --}}
        <div class="cps-section">
            <div class="cps-section-title">Maintenance Mode</div>
            <div class="cps-section-sub">Put your career page in maintenance mode.</div>

            <div class="adv-maint-card">
                <div class="adv-maint-header">
                    <div style="display:flex;align-items:flex-start;gap:12px;">
                        <div style="width:36px;height:36px;border-radius:9px;background:#FFF7ED;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="ti ti-tools" style="font-size:18px;color:#F97316;"></i>
                        </div>
                        <div>
                            <div class="adv-maint-title">Maintenance Mode</div>
                            <div class="adv-maint-desc">When enabled, visitors will see a maintenance message.</div>
                        </div>
                    </div>
                    <label class="cps-switch" style="flex-shrink:0;margin-top:4px;">
                        <input type="checkbox" name="adv_maintenance" value="1" {{ !empty($careerSettings->adv_maintenance) ? 'checked' : '' }}>
                        <span class="cps-slider"></span>
                    </label>
                </div>
                <label class="cps-label">Maintenance Message</label>
                <textarea class="adv-maint-textarea" name="adv_maintenance_msg" id="advMaintMsg" maxlength="200"
                          oninput="document.getElementById('advMaintCount').textContent=this.value.length+'/200';"
                          rows="3">{{ $careerSettings->adv_maintenance_msg ?? "We're making some updates to serve you better. Please check back shortly!" }}</textarea>
                <div style="display:flex;justify-content:flex-end;margin-top:3px;">
                    <span style="font-size:11px;color:#94A3B8;" id="advMaintCount">{{ strlen($careerSettings->adv_maintenance_msg ?? "We're making some updates to serve you better. Please check back shortly!") }}/200</span>
                </div>
            </div>
        </div>

    </div>

    {{-- Right --}}
    <div class="col-lg-6">

        {{-- Custom Code --}}
        <div class="cps-sidebar-card" style="padding:0;overflow:hidden;margin-bottom:16px;">
            <div style="padding:14px 18px 0;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                    <h4 style="margin:0;">Custom Code</h4>
                    <a href="javascript:void(0);" style="font-size:12px;font-weight:600;color:#2563EB;display:flex;align-items:center;gap:4px;text-decoration:none;">
                        Learn more <i class="ti ti-external-link" style="font-size:11px;"></i>
                    </a>
                </div>
                <p class="sub" style="margin:0 0 12px;">Add custom code to further customize your career page.</p>
                <div class="adv-code-tabs">
                    <div class="adv-code-tab active" onclick="switchCodeTab(this,'adv-code-header')">Header Code</div>
                    <div class="adv-code-tab" onclick="switchCodeTab(this,'adv-code-footer')">Footer Code</div>
                    <div class="adv-code-tab" onclick="switchCodeTab(this,'adv-code-body')">Body End Code</div>
                </div>
            </div>
            <div class="adv-code-editor" id="adv-code-header">
                @php
                $codeLines = [
                    ['ln'=>1,  'text'=>'<!-- Google Tag Manager -->'],
                    ['ln'=>2,  'text'=>'<script>'],
                    ['ln'=>3,  'text'=>'(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({\'gtm.start\':'],
                    ['ln'=>4,  'text'=>'new Date().getTime(),event:\'gtm.js\'});var f=d.getElementsByTagName(s)[0],'],
                    ['ln'=>5,  'text'=>'j=d.createElement(s),dl=l!=\'dataLayer\'?\'&l=\'+l:\'\';j.async=true;j.src='],
                    ['ln'=>6,  'text'=>'\'https://www.googletagmanager.com/gtm.js?id=\'+i+dl;'],
                    ['ln'=>7,  'text'=>'f.parentNode.insertBefore(j,f);'],
                    ['ln'=>8,  'text'=>'})(window,document,\'script\',\'dataLayer\',\'GTM-XXXXXXX\');'],
                    ['ln'=>9,  'text'=>'</script>'],
                    ['ln'=>10, 'text'=>'<!-- End Google Tag Manager -->'],
                ];
                @endphp
                @foreach($codeLines as $cl)
                <div class="adv-code-line">
                    <span class="adv-code-ln">{{ $cl['ln'] }}</span>
                    <span class="adv-code-text">{{ $cl['text'] }}</span>
                </div>
                @endforeach
            </div>
            <div class="adv-code-editor" id="adv-code-footer" style="display:none;">
                <div class="adv-code-line"><span class="adv-code-ln">1</span><span class="adv-code-text adv-code-comment">{{-- Add footer scripts here --}}</span></div>
            </div>
            <div class="adv-code-editor" id="adv-code-body" style="display:none;">
                <div class="adv-code-line"><span class="adv-code-ln">1</span><span class="adv-code-text adv-code-comment">{{-- Add body end scripts here --}}</span></div>
            </div>
        </div>

        {{-- Analytics & Tracking --}}
        <div class="cps-sidebar-card" style="margin-bottom:16px;">
            <h4 style="margin:0 0 3px;">Analytics & Tracking</h4>
            <p class="sub">Track and analyze candidate behavior and page performance.</p>

            <div class="row g-0">
                <div class="col-7" style="padding-right:12px;">
                    <div class="adv-track-label">Google Analytics 4 Measurement ID</div>
                    <div class="adv-track-input-wrap">
                        <input type="text" class="adv-track-input" name="adv_ga4_id" placeholder="G-XXXXXXXXX" value="{{ $careerSettings->adv_ga4_id ?? '' }}">
                        <span class="adv-track-badge">{{ !empty($careerSettings->adv_ga4_id) ? 'Connected' : 'Not Connected' }}</span>
                    </div>
                    <div class="adv-track-label">Facebook Pixel ID <span style="font-weight:400;color:#94A3B8;">(Optional)</span></div>
                    <div class="adv-track-input-wrap">
                        <input type="text" class="adv-track-input" name="adv_fb_pixel" placeholder="Pixel ID" value="{{ $careerSettings->adv_fb_pixel ?? '' }}">
                        <span class="adv-track-badge">{{ !empty($careerSettings->adv_fb_pixel) ? 'Connected' : 'Not Connected' }}</span>
                    </div>
                    <div class="adv-track-label">LinkedIn Insight Tag ID <span style="font-weight:400;color:#94A3B8;">(Optional)</span></div>
                    <div class="adv-track-input-wrap">
                        <input type="text" class="adv-track-input" name="adv_linkedin_tag" placeholder="Tag ID" value="{{ $careerSettings->adv_linkedin_tag ?? '' }}">
                        <span class="adv-track-badge">{{ !empty($careerSettings->adv_linkedin_tag) ? 'Connected' : 'Not Connected' }}</span>
                    </div>
                    <div class="adv-track-label">Hotjar Site ID <span style="font-weight:400;color:#94A3B8;">(Optional)</span></div>
                    <div class="adv-track-input-wrap">
                        <input type="text" class="adv-track-input" name="adv_hotjar_id" placeholder="Site ID" value="{{ $careerSettings->adv_hotjar_id ?? '' }}">
                        <button type="button" class="adv-verify-btn">Verify Connection</button>
                    </div>
                </div>
                <div class="col-5">
                    <div class="adv-track-status">
                        <div style="font-size:12px;font-weight:700;color:#0F172A;margin-bottom:8px;">Tracking Status</div>
                        @foreach(['Page Views'=>'Active','Job View Events'=>'Active','Application Events'=>'Active','Form Submissions'=>'Active'] as $ts=>$status)
                        <div class="adv-ts-row">
                            <span class="adv-ts-name">{{ $ts }}</span>
                            <span class="adv-ts-badge">{{ $status }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="adv-track-desc" style="margin-top:10px;">
                        <i class="ti ti-chart-line" style="font-size:16px;color:#2563EB;flex-shrink:0;margin-top:2px;"></i>
                        Tracking events are automatically fired when candidates interact with your career page.
                    </div>
                </div>
            </div>
        </div>

        {{-- Export & Backup --}}
        <div class="cps-sidebar-card">
            <h4 style="margin:0 0 3px;">Export & Backup</h4>
            <p class="sub">Backup your career page settings or export configuration.</p>

            <div class="row g-0">
                <div class="col-7" style="padding-right:12px;">
                    <div class="adv-export-row">
                        <div>
                            <div class="adv-export-label">Export Settings</div>
                            <div class="adv-export-desc">Download all career page settings as a JSON file.</div>
                        </div>
                        <button type="button" class="adv-export-btn">Export JSON</button>
                    </div>
                    <div class="adv-export-row" style="border-bottom:none;padding-bottom:0;">
                        <div>
                            <div class="adv-export-label">Backup Now</div>
                            <div class="adv-export-desc">Create a backup of your current career page settings.</div>
                        </div>
                        <button type="button" class="adv-export-btn">Create Backup</button>
                    </div>
                </div>
                <div class="col-5">
                    <div style="border:1.5px solid #E2E8F0;border-radius:10px;padding:14px;background:#F8FAFC;">
                        <div style="font-size:12px;font-weight:700;color:#0F172A;margin-bottom:6px;">Last Backup</div>
                        <div class="adv-backup-info">14 May 2025, 11:45 AM</div>
                        <div class="adv-backup-date" style="margin-top:2px;">by {{ auth()->user()->name ?? 'Admin' }}</div>
                        <button type="button" class="adv-dl-btn" style="margin-top:10px;">
                            <i class="ti ti-download" style="font-size:13px;"></i> Download Backup
                        </button>
                    </div>
                </div>
            </div>
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
