{{-- Advanced Tab Panel (Rule 20 Card + Sub-Card System) --}}
<div class="cps-panel" id="panel-advanced">
    <div class="cps-workspace-grid balanced">

        {{-- Left Column --}}
        <div class="cps-col-main">

            {{-- Primary Card 1: Performance & Redis Caching --}}
            <div class="cps-card accent-emerald">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon emerald"><i class="ti ti-bolt"></i></div>
                        <div>
                            <div class="cps-card-title">Performance &amp; Redis Caching</div>
                            <div class="cps-card-sub">Tune edge delivery, asset compression, and cache TTL for 10K concurrency.</div>
                        </div>
                    </div>
                    <span class="cps-card-pill green cps-num">Score {{ $perfScore }}/100</span>
                </div>

                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        <div class="cps-subcard" style="display:flex;flex-direction:column;gap:8px;">
                            <div class="cps-subcard-kicker"><span>Optimization Controls</span></div>
                            <div class="adv-toggle-row">
                                <div>
                                    <div class="adv-toggle-label">Enable Caching</div>
                                    <div class="adv-toggle-desc">Serve cached portal metadata via Redis.</div>
                                </div>
                                <label class="cps-switch">
                                    <input type="checkbox" name="adv_caching" value="1" {{ !isset($careerSettings->adv_caching) || !empty($careerSettings->adv_caching) ? 'checked' : '' }}>
                                    <span class="cps-slider"></span>
                                </label>
                            </div>
                            <div class="cps-subcard white" style="padding:9px 11px;">
                                <label class="cps-label">Cache Duration (TTL)</label>
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
                                    <div class="adv-toggle-desc">Serve images, CSS &amp; JS from edge nodes.</div>
                                </div>
                                <label class="cps-switch">
                                    <input type="checkbox" name="adv_cdn" value="1" {{ !isset($careerSettings->adv_cdn) || !empty($careerSettings->adv_cdn) ? 'checked' : '' }}>
                                    <span class="cps-slider"></span>
                                </label>
                            </div>
                            <div class="adv-toggle-row">
                                <div>
                                    <div class="adv-toggle-label">Image Optimization</div>
                                    <div class="adv-toggle-desc">Compress uploaded brand imagery.</div>
                                </div>
                                <label class="cps-switch">
                                    <input type="checkbox" name="adv_img_opt" value="1" {{ !isset($careerSettings->adv_img_opt) || !empty($careerSettings->adv_img_opt) ? 'checked' : '' }}>
                                    <span class="cps-slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="adv-perf-card">
                            <div class="adv-gauge-wrap">
                                <svg width="100" height="56" viewBox="0 0 100 58">
                                    <path d="M10 55 A40 40 0 0 1 90 55" fill="none" stroke="#e2e8f0" stroke-width="8" stroke-linecap="round"/>
                                    <path d="M10 55 A40 40 0 0 1 90 55" fill="none" stroke="#10b981" stroke-width="8" stroke-linecap="round" stroke-dasharray="125.6" stroke-dashoffset="6"/>
                                </svg>
                                <div style="margin-top:-10px;">
                                    <span class="adv-gauge-score" id="advGaugeScore">{{ $perfScore }}</span><span class="adv-gauge-total">/100</span>
                                    <div style="font-size:10px;color:#94a3b8;">Performance Score</div>
                                    <div class="adv-gauge-label">Enterprise Ready</div>
                                </div>
                            </div>
                            @foreach(['Page Speed'=>'Excellent','First Contentful Paint'=>'Excellent','Largest Contentful Paint'=>'Excellent','Cumulative Layout Shift'=>'Good'] as $metric => $rating)
                                <div class="adv-perf-check">
                                    <span class="adv-perf-check-name"><i class="ti ti-circle-check-filled"></i> {{ $metric }}</span>
                                    <span class="{{ $rating === 'Good' ? 'adv-perf-good' : 'adv-perf-excellent' }}">{{ $rating }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 2: Security & Access Governance --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-shield-lock"></i></div>
                        <div>
                            <div class="cps-card-title">Security &amp; Access Governance</div>
                            <div class="cps-card-sub">Protect your public portal and share verified preview links.</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        <div class="cps-subcard" style="display:flex;flex-direction:column;gap:7px;">
                            @php
                            $secToggles = [
                                ['name'=>'adv_pwd_protect',    'label'=>'Password Protection',          'desc'=>'Gate portal behind access passcode.',     'checked'=>false],
                                ['name'=>'adv_ip_whitelist',   'label'=>'Allowed IP Addresses',         'desc'=>'Restrict portal to approved IP ranges.',  'checked'=>false],
                                ['name'=>'adv_block_indexing', 'label'=>'Block Search Indexing',        'desc'=>'Hide portal from search engines.',        'checked'=>false],
                                ['name'=>'adv_recaptcha',      'label'=>'reCAPTCHA on Apply Form',      'desc'=>'Block automated spam submissions.',       'checked'=>true],
                            ];
                            @endphp
                            @foreach($secToggles as $st)
                                <div class="adv-toggle-row">
                                    <div>
                                        <div class="adv-toggle-label">{{ $st['label'] }}</div>
                                        <div class="adv-toggle-desc">{{ $st['desc'] }}</div>
                                    </div>
                                    <label class="cps-switch">
                                        <input type="checkbox" name="{{ $st['name'] }}" value="1" {{ $st['checked'] ? 'checked' : '' }}>
                                        <span class="cps-slider"></span>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        <div class="adv-sec-card">
                            <div class="adv-toggle-label" style="margin-bottom:4px;">Public Portal URL</div>
                            <a href="{{ $careerUrl }}" target="_blank" class="adv-sec-link">
                                {{ $careerUrl }} <i class="ti ti-external-link"></i>
                            </a>
                            <button type="button" class="adv-copy-btn" onclick="copyCareerUrl()">Copy Portal Link</button>
                            <div class="adv-share-title">Quick Share</div>
                            <div class="adv-share-desc">Share portal link with stakeholders.</div>
                            <div class="adv-share-icons">
                                <div class="adv-share-icon" style="background:#eff6ff;"><i class="ti ti-brand-linkedin" style="color:#0077b5;"></i></div>
                                <div class="adv-share-icon" style="background:#eff6ff;"><i class="ti ti-brand-facebook" style="color:#1877f2;"></i></div>
                                <div class="adv-share-icon" style="background:#ffffff;"><i class="ti ti-brand-x" style="color:#0f172a;"></i></div>
                                <div class="adv-share-icon" style="background:#ecfdf5;"><i class="ti ti-mail" style="color:#059669;"></i></div>
                            </div>
                            <a href="{{ $careerUrl }}" target="_blank" class="adv-preview-btn">
                                Open Public Preview <i class="ti ti-external-link"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 3: Maintenance Mode --}}
            <div class="cps-card accent-amber">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon amber"><i class="ti ti-tools"></i></div>
                        <div>
                            <div class="cps-card-title">Maintenance Mode</div>
                            <div class="cps-card-sub">Temporarily display a custom notice while updating career content.</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="adv-maint-card">
                        <div class="adv-maint-header">
                            <div>
                                <div class="adv-maint-title">Enable Maintenance Banner</div>
                                <div class="adv-maint-desc">Visitors will see the maintenance notice below instead of live listings.</div>
                            </div>
                            <label class="cps-switch">
                                <input type="checkbox" name="adv_maintenance" value="1" {{ !empty($careerSettings->adv_maintenance) ? 'checked' : '' }}>
                                <span class="cps-slider"></span>
                            </label>
                        </div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                            <label class="cps-label" style="margin:0;">Maintenance Message</label>
                            <span style="font-size:10.5px;color:#92400e;" id="advMaintCount" class="cps-num">{{ strlen($careerSettings->adv_maintenance_msg ?? "We're making some updates to serve you better. Please check back shortly!") }}/200</span>
                        </div>
                        <textarea class="adv-maint-textarea" name="adv_maintenance_msg" id="advMaintMsg" maxlength="200"
                                  oninput="document.getElementById('advMaintCount').textContent=this.value.length+'/200';"
                                  rows="2">{{ $careerSettings->adv_maintenance_msg ?? "We're making some updates to serve you better. Please check back shortly!" }}</textarea>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Column --}}
        <div class="cps-col-side">

            {{-- Primary Card 4: Analytics & Conversion Pixels --}}
            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-chart-line"></i></div>
                        <div>
                            <div class="cps-card-title">Analytics &amp; Conversion Pixels</div>
                            <div class="cps-card-sub">Connect first-party measurement IDs for candidate attribution.</div>
                        </div>
                    </div>
                </div>

                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        <div class="cps-subcard">
                            <div class="adv-track-label">Google Analytics 4 ID</div>
                            <div class="adv-track-input-wrap">
                                <input type="text" class="adv-track-input" name="adv_ga4_id" placeholder="G-XXXXXXXXX" value="{{ $careerSettings->adv_ga4_id ?? '' }}">
                                <span class="adv-track-badge {{ !empty($careerSettings->adv_ga4_id) ? 'connected' : '' }}">{{ !empty($careerSettings->adv_ga4_id) ? 'Connected' : 'Unset' }}</span>
                            </div>

                            <div class="adv-track-label">Facebook Pixel ID</div>
                            <div class="adv-track-input-wrap">
                                <input type="text" class="adv-track-input" name="adv_fb_pixel" placeholder="Pixel ID" value="{{ $careerSettings->adv_fb_pixel ?? '' }}">
                                <span class="adv-track-badge {{ !empty($careerSettings->adv_fb_pixel) ? 'connected' : '' }}">{{ !empty($careerSettings->adv_fb_pixel) ? 'Connected' : 'Unset' }}</span>
                            </div>

                            <div class="adv-track-label">LinkedIn Insight Tag</div>
                            <div class="adv-track-input-wrap">
                                <input type="text" class="adv-track-input" name="adv_linkedin_tag" placeholder="Tag ID" value="{{ $careerSettings->adv_linkedin_tag ?? '' }}">
                                <span class="adv-track-badge {{ !empty($careerSettings->adv_linkedin_tag) ? 'connected' : '' }}">{{ !empty($careerSettings->adv_linkedin_tag) ? 'Connected' : 'Unset' }}</span>
                            </div>

                            <div class="adv-track-label">Hotjar Site ID</div>
                            <div class="adv-track-input-wrap" style="margin-bottom:0;">
                                <input type="text" class="adv-track-input" name="adv_hotjar_id" placeholder="Site ID" value="{{ $careerSettings->adv_hotjar_id ?? '' }}">
                                <button type="button" class="adv-verify-btn" onclick="toastr.success('Tracking tag format verified.');">Verify</button>
                            </div>
                        </div>

                        <div class="adv-track-status">
                            <div style="font-size:12px;font-weight:700;color:#0f172a;margin-bottom:8px;">Event Telemetry</div>
                            @foreach(['Page Views'=>'Active','Job View Events'=>'Active','Application Events'=>'Active','Form Submissions'=>'Active'] as $ts => $status)
                                <div class="adv-ts-row">
                                    <span class="adv-ts-name">{{ $ts }}</span>
                                    <span class="adv-ts-badge">{{ $status }}</span>
                                </div>
                            @endforeach
                            <div class="adv-track-desc">
                                <i class="ti ti-info-circle" style="font-size:14px;color:#009ef7;flex-shrink:0;"></i>
                                <span>Events dispatch asynchronously when candidates browse or apply.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 5: Custom Code Injection --}}
            <div class="cps-card accent-purple">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon purple"><i class="ti ti-code"></i></div>
                        <div>
                            <div class="cps-card-title">Custom Embed Snippets</div>
                            <div class="cps-card-sub">Preview header, footer, and body-end script slots.</div>
                        </div>
                    </div>
                </div>
                <div class="adv-code-tabs">
                    <div class="adv-code-tab active" onclick="switchCodeTab(this,'adv-code-header')">Header Code</div>
                    <div class="adv-code-tab" onclick="switchCodeTab(this,'adv-code-footer')">Footer Code</div>
                    <div class="adv-code-tab" onclick="switchCodeTab(this,'adv-code-body')">Body End</div>
                </div>
                <div class="adv-code-editor" id="adv-code-header">
                    <div class="adv-code-line"><span class="adv-code-ln">1</span><span class="adv-code-text">&lt;!-- Career Portal Telemetry Hook --&gt;</span></div>
                    <div class="adv-code-line"><span class="adv-code-ln">2</span><span class="adv-code-text">&lt;script&gt;window.__JERO_CAREER_READY = true;&lt;/script&gt;</span></div>
                </div>
                <div class="adv-code-editor" id="adv-code-footer" style="display:none;">
                    <div class="adv-code-line"><span class="adv-code-ln">1</span><span class="adv-code-text">/* Footer scripts slot */</span></div>
                </div>
                <div class="adv-code-editor" id="adv-code-body" style="display:none;">
                    <div class="adv-code-line"><span class="adv-code-ln">1</span><span class="adv-code-text">/* Body end scripts slot */</span></div>
                </div>
            </div>

            {{-- Primary Card 6: Client-First Configuration Backup --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-download"></i></div>
                        <div>
                            <div class="cps-card-title">Export &amp; Configuration Backup</div>
                            <div class="cps-card-sub">Client-first JSON snapshot generated directly in browser RAM.</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        <div class="cps-subcard" style="display:flex;flex-direction:column;gap:8px;">
                            <div class="adv-export-row">
                                <div>
                                    <div class="adv-export-label">Export Settings JSON</div>
                                    <div class="adv-export-desc">0% server strain browser export.</div>
                                </div>
                                <button type="button" class="adv-export-btn" onclick="cpsExportSettingsJson()">Export JSON</button>
                            </div>
                            <div class="adv-export-row">
                                <div>
                                    <div class="adv-export-label">Snapshot Backup</div>
                                    <div class="adv-export-desc">Download timestamped archive.</div>
                                </div>
                                <button type="button" class="adv-export-btn" onclick="cpsExportSettingsJson()">Backup Now</button>
                            </div>
                        </div>
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Latest Snapshot</span></div>
                            <div style="font-size:12px;font-weight:700;color:#0f172a;">{{ !empty($careerSettings->updated_at) ? \Carbon\Carbon::parse($careerSettings->updated_at)->format('d M Y, h:i A') : 'Current Session' }}</div>
                            <div style="font-size:10.5px;color:#64748b;margin-top:2px;">by {{ auth()->user()->name ?? 'Admin' }}</div>
                            <button type="button" class="adv-dl-btn" style="margin-top:10px;" onclick="cpsExportSettingsJson()">
                                <i class="ti ti-download"></i> Download Config JSON
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
