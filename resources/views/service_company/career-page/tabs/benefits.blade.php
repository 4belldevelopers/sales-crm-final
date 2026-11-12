{{-- Benefits Tab Panel (Rule 20 Card + Sub-Card System) --}}
<div class="cps-panel" id="panel-benefits">
    <div class="cps-workspace-grid">

        {{-- Left Main Column --}}
        <div class="cps-col-main">

            {{-- Primary Card 1: Section Configuration --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-gift"></i></div>
                        <div>
                            <div class="cps-card-title">Benefits Section Configuration</div>
                            <div class="cps-card-sub">Control visibility, heading copy, display density, and card layout style.</div>
                        </div>
                    </div>
                    <span class="cps-card-pill blue cps-num">{{ $careerSettings->benefits_count ?? 8 }} Visible</span>
                </div>

                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2" style="margin-bottom:10px;">
                        {{-- Enable Toggle --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Visibility</span></div>
                            <label class="cps-label">Enable Benefits Section</label>
                            <div class="bn-toggle-row" style="margin-top:6px;">
                                <label class="cps-switch">
                                    <input type="checkbox" name="benefits_enabled" value="1" {{ !isset($careerSettings->benefits_enabled) || !empty($careerSettings->benefits_enabled) ? 'checked' : '' }}>
                                    <span class="cps-slider"></span>
                                </label>
                                <span class="bn-toggle-label">Show perks &amp; benefits on career portal</span>
                            </div>
                        </div>

                        {{-- Section Title --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker">
                                <span>Heading</span>
                                <span class="bn-char-count" id="bnTitleCount">{{ strlen($careerSettings->benefits_title ?? 'Why Work With Us') }}/60</span>
                            </div>
                            <label class="cps-label">Section Title</label>
                            <input type="text" class="bn-input" name="benefits_title" id="bnTitle"
                                   maxlength="60" value="{{ $careerSettings->benefits_title ?? 'Why Work With Us' }}"
                                   oninput="document.getElementById('bnTitleCount').textContent=this.value.length+'/60';">
                        </div>

                        {{-- Section Description --}}
                        <div class="cps-subcard span-2">
                            <div class="cps-subcard-kicker">
                                <span>Narrative</span>
                                <span class="bn-char-count" id="bnDescCount">{{ strlen($careerSettings->benefits_description ?? 'We believe in building a culture where people can do their best work and grow together. Here are some of the benefits our team enjoys.') }}/200</span>
                            </div>
                            <label class="cps-label">Section Description</label>
                            <textarea class="bn-textarea" name="benefits_description" id="bnDesc" maxlength="200" rows="2"
                                      oninput="document.getElementById('bnDescCount').textContent=this.value.length+'/200';"
                                      placeholder="Describe what makes working with your team rewarding...">{{ $careerSettings->benefits_description ?? 'We believe in building a culture where people can do their best work and grow together. Here are some of the benefits our team enjoys.' }}</textarea>
                        </div>
                    </div>

                    <div class="cps-subcard-grid-2">
                        {{-- Display Style --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Card Layout</span></div>
                            <label class="cps-label">Display Style</label>
                            @php $bnStyle = $careerSettings->benefits_display_style ?? 'grid'; @endphp
                            <div class="bn-style-grid">
                                <div class="bn-style-card {{ $bnStyle === 'grid' ? 'selected' : '' }}" onclick="selectBnStyle('grid',this)">
                                    <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                    <i class="ti ti-layout-grid bn-style-icon"></i>
                                    <div class="bn-style-name">Grid</div>
                                    <div class="bn-style-desc">Balanced</div>
                                </div>
                                <div class="bn-style-card {{ $bnStyle === 'icon-list' ? 'selected' : '' }}" onclick="selectBnStyle('icon-list',this)">
                                    <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                    <i class="ti ti-list bn-style-icon"></i>
                                    <div class="bn-style-name">List</div>
                                    <div class="bn-style-desc">Clean rows</div>
                                </div>
                                <div class="bn-style-card {{ $bnStyle === 'card-row' ? 'selected' : '' }}" onclick="selectBnStyle('card-row',this)">
                                    <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                    <i class="ti ti-layout-columns bn-style-icon"></i>
                                    <div class="bn-style-name">Row</div>
                                    <div class="bn-style-desc">Carousel</div>
                                </div>
                                <div class="bn-style-card {{ $bnStyle === 'two-col' ? 'selected' : '' }}" onclick="selectBnStyle('two-col',this)">
                                    <div class="bn-style-check"><i class="ti ti-check" style="font-size:9px;color:#fff;"></i></div>
                                    <i class="ti ti-columns bn-style-icon"></i>
                                    <div class="bn-style-name">2-Col</div>
                                    <div class="bn-style-desc">Compact</div>
                                </div>
                            </div>
                            <input type="hidden" name="benefits_display_style" id="bnStyleInput" value="{{ $bnStyle }}">
                        </div>

                        {{-- Count --}}
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Display Limit</span></div>
                            <label class="cps-label">Number of Benefits to Show</label>
                            <select class="bn-select" name="benefits_count">
                                @foreach([4,6,8,10,12] as $n)
                                    <option value="{{ $n }}" {{ ($careerSettings->benefits_count ?? 8) == $n ? 'selected' : '' }}>{{ $n }} Benefits</option>
                                @endforeach
                            </select>
                            <div class="cps-hint">Controls how many perk cards render above the fold.</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 2: Manage Benefits List --}}
            <div class="cps-card accent-emerald">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon emerald"><i class="ti ti-list-check"></i></div>
                        <div>
                            <div class="cps-card-title">Manage Employee Perks</div>
                            <div class="cps-card-sub">Curate and organize the benefits highlighted to candidates.</div>
                        </div>
                    </div>
                    <button type="button" class="bn-add-btn" onclick="bnAddBenefit()">
                        <i class="ti ti-plus" style="font-size:13px;"></i> Add Benefit
                    </button>
                </div>

                <div class="cps-card-body">
                    <div id="bnBenefitsList">
                        @php
                        $bnBenefits = [
                            ['title'=>'Health Insurance',      'desc'=>'Comprehensive health coverage for you and your family', 'icon'=>'ti-heart',       'bg'=>'#dcfce7','ic'=>'#16a34a'],
                            ['title'=>'Flexible Work',         'desc'=>'Work from anywhere with flexible hours',                'icon'=>'ti-layout-grid', 'bg'=>'#eff6ff','ic'=>'#009ef7'],
                            ['title'=>'Learning & Development','desc'=>'Continuous learning with courses and certifications',    'icon'=>'ti-book',        'bg'=>'#f5f3ff','ic'=>'#7c3aed'],
                            ['title'=>'Career Growth',         'desc'=>'Clear career path and mentorship opportunities',        'icon'=>'ti-trending-up', 'bg'=>'#fff7ed','ic'=>'#f97316'],
                            ['title'=>'Paid Time Off',         'desc'=>'Generous leave policy for sustainable work-life balance','icon'=>'ti-umbrella',   'bg'=>'#fff1f2','ic'=>'#e11d48'],
                        ];
                        @endphp
                        @foreach($bnBenefits as $bn)
                            <div class="bn-benefit-row">
                                <span class="bn-drag"><i class="ti ti-grip-vertical"></i></span>
                                <div class="bn-benefit-icon" style="background:{{ $bn['bg'] }};">
                                    <i class="ti {{ $bn['icon'] }}" style="font-size:17px;color:{{ $bn['ic'] }};"></i>
                                </div>
                                <div class="bn-benefit-info">
                                    <div class="bn-benefit-title">{{ $bn['title'] }}</div>
                                    <div class="bn-benefit-desc">{{ $bn['desc'] }}</div>
                                </div>
                                <div class="bn-benefit-actions">
                                    <button type="button" class="bn-act-btn eye" title="Visible"><i class="ti ti-eye"></i></button>
                                    <button type="button" class="bn-act-btn edit" title="Edit"><i class="ti ti-pencil"></i></button>
                                    <button type="button" class="bn-act-btn del" title="Delete" onclick="this.closest('.bn-benefit-row').remove()"><i class="ti ti-trash"></i></button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="bn-tip-bar">
                        <i class="ti ti-info-circle" style="font-size:14px;flex-shrink:0;"></i>
                        Keep benefit titles under 4 words and descriptions concise for optimal card symmetry.
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Sticky Preview Column --}}
        <div class="cps-col-side sticky-side">

            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-eye"></i></div>
                        <div>
                            <div class="cps-card-title">Benefits Live Preview</div>
                            <div class="cps-card-sub">Candidate portal rendering preview</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="bn-prev-wrap">
                        <div class="bn-prev-nav">
                            <div class="bn-prev-nav-logo">
                                <i class="ti ti-building" style="font-size:12px;color:#009ef7;"></i>
                                {{ $companyName }}
                            </div>
                            <div class="bn-prev-nav-links">
                                <span class="bn-prev-nav-link">Jobs</span>
                                <span class="bn-prev-nav-link">Culture</span>
                                <span class="bn-prev-nav-link">Benefits</span>
                            </div>
                            <div class="bn-prev-nav-btn">View Openings</div>
                        </div>
                        <div class="bn-prev-section">
                            <div class="bn-prev-title" id="bnPreviewTitle">{{ $careerSettings->benefits_title ?? 'Why Work With Us' }}</div>
                            <div class="bn-prev-desc" id="bnPreviewDesc">{{ $careerSettings->benefits_description ?? 'We believe in building a culture where people can do their best work and grow together.' }}</div>
                            <div class="bn-prev-grid">
                                @php
                                $prevCards = [
                                    ['icon'=>'ti-heart',       'bg'=>'#dcfce7','ic'=>'#16a34a','name'=>'Health Insurance',      'desc'=>'Family coverage'],
                                    ['icon'=>'ti-layout-grid', 'bg'=>'#eff6ff','ic'=>'#009ef7','name'=>'Flexible Work',         'desc'=>'Remote & hybrid'],
                                    ['icon'=>'ti-book',        'bg'=>'#f5f3ff','ic'=>'#7c3aed','name'=>'Learning Budget',       'desc'=>'Courses & certs'],
                                    ['icon'=>'ti-trending-up', 'bg'=>'#fff7ed','ic'=>'#f97316','name'=>'Career Growth',         'desc'=>'Clear progression'],
                                    ['icon'=>'ti-umbrella',    'bg'=>'#fff1f2','ic'=>'#e11d48','name'=>'Paid Time Off',         'desc'=>'Generous PTO'],
                                    ['icon'=>'ti-users-group', 'bg'=>'#ecfdf5','ic'=>'#059669','name'=>'Team Culture',          'desc'=>'Inclusive teams'],
                                    ['icon'=>'ti-heart-rate',  'bg'=>'#eff6ff','ic'=>'#0ea5e9','name'=>'Wellness',              'desc'=>'Mental & physical'],
                                    ['icon'=>'ti-gift',        'bg'=>'#fffbeb','ic'=>'#d97706','name'=>'Annual Bonus',          'desc'=>'Merit rewards'],
                                ];
                                @endphp
                                @foreach($prevCards as $pc)
                                    <div class="bn-prev-card">
                                        <div class="bn-prev-card-icon" style="background:{{ $pc['bg'] }};">
                                            <i class="ti {{ $pc['icon'] }}" style="font-size:13px;color:{{ $pc['ic'] }};"></i>
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

            <div class="cps-card accent-amber">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon amber"><i class="ti ti-sparkles"></i></div>
                        <div><div class="cps-card-title">Employer Brand Tips</div></div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="bn-tip-item"><i class="ti ti-star" style="color:#f59e0b;"></i> Highlight 6–8 perks that genuinely differentiate your offer.</div>
                    <div class="bn-tip-item"><i class="ti ti-circle-check" style="color:#009ef7;"></i> Pair clear icons with measurable benefits (e.g., Learning Stipend).</div>
                    <div class="bn-tip-item"><i class="ti ti-heart" style="color:#e11d48;"></i> Authentic benefits increase applicant conversion rates.</div>
                </div>
            </div>

        </div>
    </div>
</div>
