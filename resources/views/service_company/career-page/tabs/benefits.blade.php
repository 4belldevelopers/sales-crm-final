{{-- Benefits Tab Panel --}}
<div class="cps-panel" id="panel-benefits">
<div class="row g-4">

    {{-- Left: Controls --}}
    <div class="col-lg-7">

        {{-- Benefits Section Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">Benefits Section Settings</div>
            <div class="cps-section-sub">Showcase the perks and advantages of working at your company.</div>

            <div class="row g-3 mb-3">
                {{-- Enable toggle --}}
                <div class="col-6">
                    <label class="cps-label">Enable Benefits Section</label>
                    <div class="bn-toggle-row" style="margin-top:6px;">
                        <label class="cps-switch">
                            <input type="checkbox" name="benefits_enabled" value="1" checked>
                            <span class="cps-slider"></span>
                        </label>
                        <span class="bn-toggle-label">Display benefits section on the career page</span>
                    </div>
                </div>
                {{-- Section Title --}}
                <div class="col-6">
                    <label class="cps-label">Section Title</label>
                    <input type="text" class="bn-input" name="benefits_title" id="bnTitle"
                           maxlength="60" value="Why Work With Us"
                           oninput="document.getElementById('bnTitleCount').textContent=this.value.length+'/60';">
                    <div style="display:flex;justify-content:flex-end;">
                        <span class="bn-char-count" id="bnTitleCount">17/60</span>
                    </div>
                </div>
            </div>

            {{-- Section Description --}}
            <div class="mb-4">
                <label class="cps-label">Section Description</label>
                <textarea class="bn-textarea" name="benefits_description" id="bnDesc" maxlength="200"
                          oninput="document.getElementById('bnDescCount').textContent=this.value.length+'/200';"
                          placeholder="e.g. We believe in building a culture where people can do their best work and grow together.">We believe in building a culture where people can do their best work and grow together. Here are some of the benefits our team enjoys.</textarea>
                <div style="display:flex;justify-content:flex-end;">
                    <span class="bn-char-count" id="bnDescCount">117/200</span>
                </div>
            </div>

            <div class="row g-3">
                {{-- Display Style --}}
                <div class="col-7">
                    <label class="cps-label">Display Style</label>
                    <p style="font-size:11px;color:#94A3B8;margin:0 0 10px;">Choose how benefits are displayed</p>
                    <div class="bn-style-grid">
                        <div class="bn-style-card selected" onclick="selectBnStyle('grid',this)">
                            <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                            <i class="ti ti-layout-grid bn-style-icon"></i>
                            <div class="bn-style-name">Grid View</div>
                            <div class="bn-style-desc">Best for most cases</div>
                        </div>
                        <div class="bn-style-card" onclick="selectBnStyle('icon-list',this)">
                            <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                            <i class="ti ti-list bn-style-icon"></i>
                            <div class="bn-style-name">Icon List</div>
                            <div class="bn-style-desc">Simple list layout</div>
                        </div>
                        <div class="bn-style-card" onclick="selectBnStyle('card-row',this)">
                            <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                            <i class="ti ti-layout-columns bn-style-icon"></i>
                            <div class="bn-style-name">Card Row</div>
                            <div class="bn-style-desc">Horizontal scroll</div>
                        </div>
                        <div class="bn-style-card" onclick="selectBnStyle('two-col',this)">
                            <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                            <i class="ti ti-columns bn-style-icon"></i>
                            <div class="bn-style-name">Two Column</div>
                            <div class="bn-style-desc">Compact layout</div>
                        </div>
                    </div>
                    <input type="hidden" name="benefits_display_style" id="bnStyleInput" value="grid">
                </div>

                {{-- Number of Benefits to Show --}}
                <div class="col-5">
                    <label class="cps-label">Number of Benefits to Show</label>
                    <p style="font-size:11px;color:#94A3B8;margin:0 0 10px;">Limit the number of benefits visible on the career page</p>
                    <select class="bn-select" name="benefits_count">
                        @foreach([4,6,8,10,12] as $n)
                        <option value="{{ $n }}" {{ $n==8?'selected':'' }}>{{ $n }} Benefits</option>
                        @endforeach
                    </select>
                    <p style="font-size:11px;color:#94A3B8;margin:8px 0 0;">You can add more benefits, but only selected number will be shown.</p>
                </div>
            </div>
        </div>

        {{-- Manage Benefits --}}
        <div class="cps-section">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
                <div>
                    <div class="cps-section-title" style="margin:0;">Manage Benefits</div>
                    <div class="cps-section-sub" style="margin:2px 0 0;">Add, edit and reorder benefits. Drag to reorder the items.</div>
                </div>
                <button type="button" class="bn-add-btn" onclick="bnAddBenefit()">
                    <i class="ti ti-plus" style="font-size:13px;"></i> Add Benefit
                </button>
            </div>

            <div id="bnBenefitsList" style="margin-top:16px;">
                @php
                $bnBenefits = [
                    ['title'=>'Health Insurance',      'desc'=>'Comprehensive health coverage for you and your family',    'icon'=>'ti-heart',        'bg'=>'#DCFCE7','ic'=>'#16A34A'],
                    ['title'=>'Flexible Work',         'desc'=>'Work from anywhere with flexible hours',                   'icon'=>'ti-layout-grid',  'bg'=>'#EFF6FF','ic'=>'#2563EB'],
                    ['title'=>'Learning & Development','desc'=>'Continuous learning with courses and certifications',       'icon'=>'ti-book',         'bg'=>'#FAF5FF','ic'=>'#7C3AED'],
                    ['title'=>'Career Growth',         'desc'=>'Clear career path and growth opportunities',               'icon'=>'ti-trending-up',  'bg'=>'#FFF7ED','ic'=>'#F97316'],
                    ['title'=>'Paid Time Off',         'desc'=>'Generous leave policy for work-life balance',              'icon'=>'ti-umbrella',     'bg'=>'#FFF1F2','ic'=>'#E11D48'],
                ];
                @endphp
                @foreach($bnBenefits as $bn)
                <div class="bn-benefit-row">
                    <span class="bn-drag"><i class="ti ti-grip-vertical"></i></span>
                    <div class="bn-benefit-icon" style="background:{{ $bn['bg'] }};">
                        <i class="ti {{ $bn['icon'] }}" style="font-size:18px;color:{{ $bn['ic'] }};"></i>
                    </div>
                    <div class="bn-benefit-info">
                        <div class="bn-benefit-title">{{ $bn['title'] }}</div>
                        <div class="bn-benefit-desc">{{ $bn['desc'] }}</div>
                    </div>
                    <div class="bn-benefit-actions">
                        <button type="button" class="bn-act-btn eye" title="Toggle visibility">
                            <i class="ti ti-eye"></i>
                        </button>
                        <button type="button" class="bn-act-btn edit" title="Edit">
                            <i class="ti ti-pencil"></i>
                        </button>
                        <button type="button" class="bn-act-btn del" title="Delete"
                                onclick="this.closest('.bn-benefit-row').remove()">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Tip bar --}}
            <div class="bn-tip-bar">
                <i class="ti ti-info-circle" style="font-size:14px;flex-shrink:0;"></i>
                Tip: Keep benefit titles short and descriptions clear for better engagement.
            </div>
        </div>

    </div>

    {{-- Right: Preview + Tips --}}
    <div class="col-lg-5">

        {{-- Live Preview --}}
        <div class="cps-sidebar-card" style="padding:0;overflow:hidden;">
            <div style="padding:14px 18px 12px;border-bottom:1px solid #F1F5F9;">
                <h4 style="margin:0 0 2px;">Live Preview</h4>
                <p class="sub" style="margin:0;">See how the benefits section will appear on your career page.</p>
            </div>
            <div style="padding:14px;">
                <div class="bn-prev-wrap">
                    <div class="bn-prev-nav">
                        <div class="bn-prev-nav-logo">
                            <i class="ti ti-building" style="font-size:12px;color:#2563EB;"></i>
                            {{ $companyName }}
                        </div>
                        <div class="bn-prev-nav-links">
                            <span class="bn-prev-nav-link">Jobs</span>
                            <span class="bn-prev-nav-link">Teams</span>
                            <span class="bn-prev-nav-link">Life at 4Bell</span>
                            <span class="bn-prev-nav-link">Benefits</span>
                            <span class="bn-prev-nav-link">About Us</span>
                        </div>
                        <div class="bn-prev-nav-btn">View Openings</div>
                    </div>
                    <div class="bn-prev-section">
                        <div class="bn-prev-title">Why Work With Us</div>
                        <div class="bn-prev-desc">We believe in building a culture where people can do their best work and grow together. Here are some of the benefits our team enjoys.</div>
                        <div class="bn-prev-grid">
                            @php
                            $prevCards = [
                                ['icon'=>'ti-heart',       'bg'=>'#DCFCE7','ic'=>'#16A34A','name'=>'Health Insurance',      'desc'=>'Comprehensive health coverage for you and your family'],
                                ['icon'=>'ti-layout-grid', 'bg'=>'#EFF6FF','ic'=>'#2563EB','name'=>'Flexible Work',         'desc'=>'Work from anywhere with flexible hours'],
                                ['icon'=>'ti-book',        'bg'=>'#FAF5FF','ic'=>'#7C3AED','name'=>'Learning & Development','desc'=>'Continuous learning with courses and certifications'],
                                ['icon'=>'ti-trending-up', 'bg'=>'#FFF7ED','ic'=>'#F97316','name'=>'Career Growth',         'desc'=>'Clear career path and growth opportunities'],
                                ['icon'=>'ti-umbrella',    'bg'=>'#FFF1F2','ic'=>'#E11D48','name'=>'Paid Time Off',         'desc'=>'Generous leave policy for work-life balance'],
                                ['icon'=>'ti-users-group', 'bg'=>'#F0FDF4','ic'=>'#059669','name'=>'Team Culture',          'desc'=>'Collaborative, inclusive and supportive environment'],
                                ['icon'=>'ti-heart-rate',  'bg'=>'#EFF6FF','ic'=>'#0EA5E9','name'=>'Wellness Program',      'desc'=>'Mental and physical wellness support'],
                                ['icon'=>'ti-gift',        'bg'=>'#FFFBEB','ic'=>'#D97706','name'=>'Performance Bonus',     'desc'=>'Rewarding performance and contributions'],
                            ];
                            @endphp
                            @foreach($prevCards as $pc)
                            <div class="bn-prev-card">
                                <div class="bn-prev-card-icon" style="background:{{ $pc['bg'] }};">
                                    <i class="ti {{ $pc['icon'] }}" style="font-size:14px;color:{{ $pc['ic'] }};"></i>
                                </div>
                                <div class="bn-prev-card-name">{{ $pc['name'] }}</div>
                                <div class="bn-prev-card-desc">{{ $pc['desc'] }}</div>
                            </div>
                            @endforeach
                        </div>
                        <div class="bn-prev-link">
                            View Full Career Page <i class="ti ti-external-link" style="font-size:9px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tips --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <h4 style="margin:0 0 14px;">Tips for Great Benefits Section</h4>
            <div class="bn-tips-list">
                <div class="bn-tip-item">
                    <i class="ti ti-star" style="color:#F59E0B;"></i>
                    Highlight 6-8 key benefits that matter most to candidates.
                </div>
                <div class="bn-tip-item">
                    <i class="ti ti-circle-check" style="color:#2563EB;"></i>
                    Use simple icons and short descriptions.
                </div>
                <div class="bn-tip-item">
                    <i class="ti ti-heart" style="color:#E11D48;"></i>
                    Focus on unique benefits that differentiate your company.
                </div>
                <div class="bn-tip-item">
                    <i class="ti ti-refresh" style="color:#16A34A;"></i>
                    Keep the section updated and relevant.
                </div>
                <div class="bn-tip-item">
                    <i class="ti ti-chart-bar" style="color:#7C3AED;"></i>
                    Benefits help improve candidate trust and engagement.
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
