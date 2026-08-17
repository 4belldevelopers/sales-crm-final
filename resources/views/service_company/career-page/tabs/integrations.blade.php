{{-- Integrations Tab Panel --}}
<div class="cps-panel" id="panel-integrations">
<div class="row g-4">

    {{-- Left: Connect & Automate --}}
    <div class="col-lg-7">

        <div class="cps-section-title" style="font-size:16px;margin-bottom:3px;">Connect & Automate</div>
        <div class="cps-section-sub" style="margin-bottom:18px;">Integrate your career page with external tools and services to streamline your hiring workflow.</div>

        {{-- Search + Filter --}}
        <div class="int-search-bar">
            <div class="int-search-wrap">
                <i class="ti ti-search"></i>
                <input type="text" class="int-search-input" placeholder="Search integrations...">
            </div>
            <select class="int-cat-select">
                <option>All Categories</option>
                <option>ATS & HR Tools</option>
                <option>Analytics</option>
                <option>Marketing</option>
                <option>Communication</option>
                <option>Automation</option>
                <option>Other</option>
            </select>
        </div>

        {{-- Category Tabs --}}
        <div class="int-tabs">
            <div class="int-tab active">All Integrations</div>
            <div class="int-tab">ATS & HR Tools</div>
            <div class="int-tab">Analytics</div>
            <div class="int-tab">Marketing</div>
            <div class="int-tab">Communication</div>
            <div class="int-tab">Automation</div>
            <div class="int-tab">Other</div>
        </div>

        {{-- Integration Cards --}}
        @php
        $intCards = [
            ['name'=>'Greenhouse',        'desc'=>'Sync jobs, candidates and applications seamlessly with Greenhouse.',   'connected'=>true,  'color'=>'#24A862'],
            ['name'=>'Lever',             'desc'=>'Export job postings and import candidates from Lever.',                'connected'=>false, 'color'=>'#999'],
            ['name'=>'Workday',           'desc'=>'Integrate with Workday Recruiting for centralized talent management.', 'connected'=>false, 'color'=>'#CC0000'],
            ['name'=>'BambooHR',          'desc'=>'Sync candidate data and employee information effortlessly.',           'connected'=>false, 'color'=>'#73AC39'],
            ['name'=>'Zapier',            'desc'=>'Connect with 5,000+ apps and automate your hiring workflows.',        'connected'=>false, 'color'=>'#FF4A00'],
            ['name'=>'Make (Integromat)', 'desc'=>'Build scenarios and automate repetitive tasks.',                      'connected'=>false, 'color'=>'#6D04D9'],
            ['name'=>'Google Analytics 4','desc'=>'Track career page traffic and candidate engagement.',                  'connected'=>true,  'color'=>'#E37400'],
            ['name'=>'Microsoft Clarity', 'desc'=>'Understand user behavior on your career page.',                       'connected'=>false, 'color'=>'#0078D4'],
            ['name'=>'Hotjar',            'desc'=>'See how candidates interact with your career site.',                  'connected'=>false, 'color'=>'#FD3A5C'],
        ];
        $intIcons = [
            'Greenhouse'        => ['ti-leaf',          '#24A862'],
            'Lever'             => ['ti-adjustments',   '#555'],
            'Workday'           => ['ti-briefcase',      '#CC0000'],
            'BambooHR'          => ['ti-tree',           '#73AC39'],
            'Zapier'            => ['ti-bolt',           '#FF4A00'],
            'Make (Integromat)' => ['ti-puzzle',         '#6D04D9'],
            'Google Analytics 4'=> ['ti-chart-bar',      '#E37400'],
            'Microsoft Clarity' => ['ti-activity',       '#0078D4'],
            'Hotjar'            => ['ti-flame',          '#FD3A5C'],
        ];
        @endphp

        <div class="int-grid">
            @foreach($intCards as $ic)
            @php $ico = $intIcons[$ic['name']] ?? ['ti-plug','#64748B']; @endphp
            <div class="int-card">
                <div class="int-card-logo">
                    <div style="width:38px;height:38px;border-radius:10px;background:{{ $ic['color'] }}18;display:flex;align-items:center;justify-content:center;">
                        <i class="ti {{ $ico[0] }}" style="font-size:22px;color:{{ $ico[1] }};"></i>
                    </div>
                </div>
                <div class="int-card-name">{{ $ic['name'] }}</div>
                <div class="int-card-desc">{{ $ic['desc'] }}</div>
                <div class="int-card-footer">
                    @if($ic['connected'])
                        <span class="int-connected-badge">
                            <i class="ti ti-circle-check-filled" style="font-size:11px;"></i> Connected
                        </span>
                        <button type="button" class="int-manage-btn">Manage Settings</button>
                    @else
                        <button type="button" class="int-connect-btn">Connect</button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Request Integration --}}
        <div class="int-request-card">
            <div class="int-request-icon">
                <i class="ti ti-plug-connected" style="font-size:28px;color:#2563EB;"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div class="int-request-title">Can't find the integration you need?</div>
                <div class="int-request-desc">We're constantly adding new integrations. Let us know which tools you'd like to connect.</div>
            </div>
            <button type="button" class="int-request-btn">Request Integration</button>
        </div>

    </div>

    {{-- Right --}}
    <div class="col-lg-5">

        {{-- Active Integrations --}}
        <div class="cps-sidebar-card">
            <h4 style="margin:0 0 3px;">Active Integrations</h4>
            <p class="sub">These integrations are currently connected to your career page.</p>

            @php
            $activeInts = [
                ['icon'=>'ti-leaf',      'bg'=>'#DCFCE7','ic'=>'#24A862','name'=>'Greenhouse',        'sync'=>'Last synced: 2 mins ago'],
                ['icon'=>'ti-chart-bar', 'bg'=>'#FFF7ED','ic'=>'#E37400','name'=>'Google Analytics 4','sync'=>'Last synced: 15 mins ago'],
                ['icon'=>'ti-mail',      'bg'=>'#EFF6FF','ic'=>'#2563EB','name'=>'SendGrid',           'sync'=>'Last synced: 1 hour ago'],
            ];
            @endphp
            @foreach($activeInts as $ai)
            <div class="int-active-row">
                <div class="int-active-left">
                    <div class="int-active-logo" style="background:{{ $ai['bg'] }};">
                        <i class="ti {{ $ai['icon'] }}" style="font-size:18px;color:{{ $ai['ic'] }};"></i>
                    </div>
                    <div>
                        <div class="int-active-name">{{ $ai['name'] }}</div>
                        <div class="int-active-sync">{{ $ai['sync'] }}</div>
                    </div>
                </div>
                <div class="int-active-right">
                    <span class="int-connected-sm">Connected</span>
                    <div class="int-gear-btn"><i class="ti ti-settings"></i></div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Webhooks --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                <h4 style="margin:0;">Webhooks</h4>
                <button type="button" class="int-add-wh-btn">
                    <i class="ti ti-plus" style="font-size:12px;"></i> Add Webhook
                </button>
            </div>
            <p class="sub">Use webhooks to send real-time data to your endpoints.</p>

            @php
            $webhooks = [
                ['name'=>'Application Received','url'=>'https://hooks.4bell.com/career/applications'],
                ['name'=>'Candidate Hired',      'url'=>'https://hooks.4bell.com/career/hired'],
            ];
            @endphp
            @foreach($webhooks as $wh)
            <div class="int-wh-row">
                <div class="int-wh-left">
                    <div class="int-wh-icon">
                        <i class="ti ti-webhook" style="font-size:16px;color:#64748B;"></i>
                    </div>
                    <div>
                        <div class="int-wh-name">{{ $wh['name'] }}</div>
                        <div class="int-wh-url">{{ $wh['url'] }}</div>
                    </div>
                </div>
                <div class="int-wh-right">
                    <span class="int-active-badge">Active</span>
                    <div class="int-dots-btn">⋮</div>
                </div>
            </div>
            @endforeach

            <div class="int-view-all">View All Webhooks</div>
        </div>

        {{-- Other Settings --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <h4 style="margin:0 0 4px;">Other Settings</h4>

            @php
            $intOther = [
                ['icon'=>'ti-refresh',    'bg'=>'#EFF6FF','ic'=>'#2563EB','title'=>'Enable Job Posting Sync',     'desc'=>'Automatically sync job openings to connected platforms.', 'toggle'=>true,  'on'=>true,  'link'=>false],
                ['icon'=>'ti-users',      'bg'=>'#F0FDF4','ic'=>'#16A34A','title'=>'Enable Candidate Sync',       'desc'=>'Sync candidate data between integrations.',              'toggle'=>true,  'on'=>true,  'link'=>false],
                ['icon'=>'ti-database',   'bg'=>'#FFF7ED','ic'=>'#F97316','title'=>'Data Mapping',                'desc'=>'Map fields between Jero ATS and integrated platforms.',  'toggle'=>false, 'on'=>false, 'link'=>true],
                ['icon'=>'ti-trash',      'bg'=>'#FFF1F2','ic'=>'#EF4444','title'=>'Disconnect All Integrations', 'desc'=>'Remove all connected integrations from your career page.','toggle'=>false, 'on'=>false, 'link'=>true],
            ];
            @endphp
            @foreach($intOther as $io)
            <div class="int-other-row">
                <div class="int-other-left">
                    <div class="int-other-icon" style="background:{{ $io['bg'] }};">
                        <i class="ti {{ $io['icon'] }}" style="font-size:15px;color:{{ $io['ic'] }};"></i>
                    </div>
                    <div>
                        <div class="int-other-title">{{ $io['title'] }}</div>
                        <div class="int-other-desc">{{ $io['desc'] }}</div>
                    </div>
                </div>
                @if($io['toggle'])
                <label class="cps-switch" style="flex-shrink:0;margin-top:3px;">
                    <input type="checkbox" value="1" {{ $io['on']?'checked':'' }}>
                    <span class="cps-slider"></span>
                </label>
                @else
                <div class="int-arrow-btn"><i class="ti ti-chevron-right"></i></div>
                @endif
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
