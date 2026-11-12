{{-- Integrations Tab Panel (Rule 20 Card + Sub-Card System) --}}
<div class="cps-panel" id="panel-integrations">
    <div class="cps-workspace-grid">

        {{-- Left Main Column: Marketplace & Automation --}}
        <div class="cps-col-main">
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-plug"></i></div>
                        <div>
                            <div class="cps-card-title">Connect &amp; Automate Ecosystem</div>
                            <div class="cps-card-sub">Integrate ATS pipelines, analytics telemetry, and webhook workflows.</div>
                        </div>
                    </div>
                    <span class="cps-card-pill green">2 Connected</span>
                </div>

                <div class="cps-card-body">
                    {{-- Search + Filter Bar --}}
                    <div class="int-search-bar">
                        <div class="int-search-wrap">
                            <i class="ti ti-search"></i>
                            <input type="text" class="int-search-input" id="intSearchInput" placeholder="Filter integrations by name or capability...">
                        </div>
                        <select class="int-cat-select" id="intCatSelect">
                            <option value="all">All Categories</option>
                            <option value="ats">ATS &amp; HR Tools</option>
                            <option value="analytics">Analytics</option>
                            <option value="automation">Automation</option>
                        </select>
                    </div>

                    {{-- Category Sub-Tabs --}}
                    <div class="int-tabs">
                        <div class="int-tab active" data-cat="all">All Integrations</div>
                        <div class="int-tab" data-cat="ats">ATS &amp; HR Tools</div>
                        <div class="int-tab" data-cat="analytics">Analytics</div>
                        <div class="int-tab" data-cat="automation">Automation</div>
                    </div>

                    @php
                    $intCards = [
                        ['name'=>'Greenhouse',        'cat'=>'ats',        'desc'=>'Sync jobs, candidates and applications seamlessly with Greenhouse.',   'connected'=>true,  'color'=>'#24A862', 'icon'=>'ti-leaf'],
                        ['name'=>'Lever',             'cat'=>'ats',        'desc'=>'Export job postings and import candidates from Lever.',                'connected'=>false, 'color'=>'#475569', 'icon'=>'ti-adjustments'],
                        ['name'=>'Workday',           'cat'=>'ats',        'desc'=>'Integrate with Workday Recruiting for centralized talent management.', 'connected'=>false, 'color'=>'#CC0000', 'icon'=>'ti-briefcase'],
                        ['name'=>'BambooHR',          'cat'=>'ats',        'desc'=>'Sync candidate data and employee information effortlessly.',           'connected'=>false, 'color'=>'#73AC39', 'icon'=>'ti-tree'],
                        ['name'=>'Zapier',            'cat'=>'automation', 'desc'=>'Connect with 5,000+ apps and automate your hiring workflows.',        'connected'=>false, 'color'=>'#FF4A00', 'icon'=>'ti-bolt'],
                        ['name'=>'Make (Integromat)', 'cat'=>'automation', 'desc'=>'Build scenarios and automate repetitive hiring tasks.',               'connected'=>false, 'color'=>'#6D04D9', 'icon'=>'ti-puzzle'],
                        ['name'=>'Google Analytics 4','cat'=>'analytics',  'desc'=>'Track career page traffic and candidate engagement funnels.',          'connected'=>true,  'color'=>'#E37400', 'icon'=>'ti-chart-bar'],
                        ['name'=>'Microsoft Clarity', 'cat'=>'analytics',  'desc'=>'Understand candidate scroll depth and heatmaps.',                     'connected'=>false, 'color'=>'#0078D4', 'icon'=>'ti-activity'],
                        ['name'=>'Hotjar',            'cat'=>'analytics',  'desc'=>'See how candidates interact with your career site.',                  'connected'=>false, 'color'=>'#FD3A5C', 'icon'=>'ti-flame'],
                    ];
                    @endphp

                    <div class="int-grid">
                        @foreach($intCards as $ic)
                            <div class="int-card {{ $ic['connected'] ? 'connected' : '' }}" data-name="{{ $ic['name'] }}" data-cat="{{ $ic['cat'] }}">
                                <div class="int-card-logo">
                                    <div style="width:36px;height:36px;border-radius:9px;background:{{ $ic['color'] }}18;display:flex;align-items:center;justify-content:center;">
                                        <i class="ti {{ $ic['icon'] }}" style="font-size:20px;color:{{ $ic['color'] }};"></i>
                                    </div>
                                    <span class="cps-card-pill" style="font-size:9.5px;text-transform:uppercase;">{{ $ic['cat'] }}</span>
                                </div>
                                <div class="int-card-name">{{ $ic['name'] }}</div>
                                <div class="int-card-desc">{{ $ic['desc'] }}</div>
                                <div class="int-card-footer">
                                    @if($ic['connected'])
                                        <span class="int-connected-badge"><i class="ti ti-circle-check-filled"></i> Connected</span>
                                        <button type="button" class="int-manage-btn" onclick="cpsActivateTab('advanced')">Configure</button>
                                    @else
                                        <span style="font-size:10px;color:#94a3b8;">Available</span>
                                        <button type="button" class="int-connect-btn" onclick="toastr.info('Configure API credentials in Advanced or Contact Support to enable {{ $ic['name'] }}.')">Connect</button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="int-request-card">
                        <div class="int-request-icon">
                            <i class="ti ti-plug-connected" style="font-size:24px;color:#009ef7;"></i>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <div class="int-request-title">Need a custom enterprise connector?</div>
                            <div class="int-request-desc">Request SAML, HRIS, or custom REST webhook integrations for your hiring stack.</div>
                        </div>
                        <button type="button" class="int-request-btn" onclick="toastr.success('Integration request logged for your account team.')">Request Integration</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Sticky Column: Active Connectors & Webhooks --}}
        <div class="cps-col-side sticky-side">

            {{-- Active Integrations --}}
            <div class="cps-card accent-emerald">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon emerald"><i class="ti ti-circle-check"></i></div>
                        <div>
                            <div class="cps-card-title">Active Integrations</div>
                            <div class="cps-card-sub">Live connectors synced with your portal</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    @php
                    $activeInts = [
                        ['icon'=>'ti-leaf',      'bg'=>'#dcfce7','ic'=>'#24a862','name'=>'Greenhouse',        'sync'=>'Last synced: 2 mins ago'],
                        ['icon'=>'ti-chart-bar', 'bg'=>'#fff7ed','ic'=>'#e37400','name'=>'Google Analytics 4','sync'=>'Last synced: 15 mins ago'],
                        ['icon'=>'ti-mail',      'bg'=>'#eff6ff','ic'=>'#009ef7','name'=>'SendGrid',          'sync'=>'Last synced: 1 hour ago'],
                    ];
                    @endphp
                    @foreach($activeInts as $ai)
                        <div class="int-active-row">
                            <div class="int-active-left">
                                <div class="int-active-logo" style="background:{{ $ai['bg'] }};">
                                    <i class="ti {{ $ai['icon'] }}" style="font-size:17px;color:{{ $ai['ic'] }};"></i>
                                </div>
                                <div>
                                    <div class="int-active-name">{{ $ai['name'] }}</div>
                                    <div class="int-active-sync">{{ $ai['sync'] }}</div>
                                </div>
                            </div>
                            <div class="int-active-right">
                                <span class="int-connected-sm">Connected</span>
                                <div class="int-gear-btn" onclick="cpsActivateTab('advanced')"><i class="ti ti-settings"></i></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Webhooks --}}
            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-webhook"></i></div>
                        <div>
                            <div class="cps-card-title">Event Webhooks</div>
                            <div class="cps-card-sub">Real-time HTTPS callbacks on candidate events</div>
                        </div>
                    </div>
                    <button type="button" class="int-add-wh-btn" onclick="toastr.info('Webhook endpoint builder coming in API Console.')">
                        <i class="ti ti-plus"></i> Add
                    </button>
                </div>
                <div class="cps-card-body">
                    @php
                    $webhooks = [
                        ['name'=>'Application Received', 'url'=>'https://hooks.' . $companyHost . '/career/applications'],
                        ['name'=>'Candidate Hired',      'url'=>'https://hooks.' . $companyHost . '/career/hired'],
                    ];
                    @endphp
                    @foreach($webhooks as $wh)
                        <div class="int-wh-row">
                            <div class="int-wh-left">
                                <div class="int-wh-icon"><i class="ti ti-webhook" style="font-size:15px;color:#64748b;"></i></div>
                                <div style="min-width:0;">
                                    <div class="int-wh-name">{{ $wh['name'] }}</div>
                                    <div class="int-wh-url">{{ $wh['url'] }}</div>
                                </div>
                            </div>
                            <div class="int-wh-right">
                                <span class="int-active-badge">Active</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Sync Governance --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-arrows-exchange"></i></div>
                        <div><div class="cps-card-title">Sync Governance</div></div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="int-other-row">
                        <div class="int-other-left">
                            <div class="int-other-icon" style="background:#eff6ff;"><i class="ti ti-refresh" style="color:#009ef7;"></i></div>
                            <div>
                                <div class="int-other-title">Auto Job Posting Sync</div>
                                <div class="int-other-desc">Push published roles to connected channels.</div>
                            </div>
                        </div>
                        <label class="cps-switch"><input type="checkbox" value="1" checked><span class="cps-slider"></span></label>
                    </div>
                    <div class="int-other-row">
                        <div class="int-other-left">
                            <div class="int-other-icon" style="background:#ecfdf5;"><i class="ti ti-users" style="color:#059669;"></i></div>
                            <div>
                                <div class="int-other-title">Candidate Profile Sync</div>
                                <div class="int-other-desc">Mirror candidate metadata across ATS stages.</div>
                            </div>
                        </div>
                        <label class="cps-switch"><input type="checkbox" value="1" checked><span class="cps-slider"></span></label>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
