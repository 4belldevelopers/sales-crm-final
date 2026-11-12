{{-- Job Settings Tab Panel (Rule 20 Card + Sub-Card System) --}}
<div class="cps-panel" id="panel-jobs">
    <div class="cps-workspace-grid">

        {{-- Left Main Column --}}
        <div class="cps-col-main">

            {{-- Primary Card 1: Job Listing Configuration --}}
            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-briefcase"></i></div>
                        <div>
                            <div class="cps-card-title">Job Listing Stream Settings</div>
                            <div class="cps-card-sub">Configure display style, pagination density, sorting, and visible card fields.</div>
                        </div>
                    </div>
                    <span class="cps-card-pill blue cps-num">{{ $careerSettings->js_per_page ?? 12 }} / Page</span>
                </div>

                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        {{-- Dropdown Sub-Card --}}
                        <div class="cps-subcard" style="display:flex;flex-direction:column;gap:10px;">
                            <div class="cps-subcard-kicker"><span>Stream Parameters</span></div>
                            <div>
                                <label class="cps-label">Job Display Style</label>
                                @php $jsStyle = $careerSettings->js_display_style ?? ($careerSettings->jobs_display ?? 'card'); @endphp
                                <select class="js-select" name="js_display_style">
                                    <option value="card" {{ $jsStyle === 'card' ? 'selected' : '' }}>Card View</option>
                                    <option value="list" {{ $jsStyle === 'list' ? 'selected' : '' }}>List View</option>
                                    <option value="compact" {{ $jsStyle === 'compact' ? 'selected' : '' }}>Compact View</option>
                                </select>
                            </div>
                            <div>
                                <label class="cps-label">Jobs Per Page</label>
                                <select class="js-select" name="js_per_page">
                                    @foreach([6,9,12,15,20] as $n)
                                        <option value="{{ $n }}" {{ ($careerSettings->js_per_page ?? 12) == $n ? 'selected' : '' }}>{{ $n }} Jobs</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="cps-label">Default Job Sort By</label>
                                @php $jsSort = $careerSettings->js_sort_by ?? 'newest'; @endphp
                                <select class="js-select" name="js_sort_by">
                                    <option value="newest" {{ $jsSort === 'newest' ? 'selected' : '' }}>Newest First</option>
                                    <option value="oldest" {{ $jsSort === 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                    <option value="relevant" {{ $jsSort === 'relevant' ? 'selected' : '' }}>Most Relevant</option>
                                    <option value="salary" {{ $jsSort === 'salary' ? 'selected' : '' }}>Highest Salary</option>
                                </select>
                            </div>
                        </div>

                        {{-- Card Field Toggles Sub-Card --}}
                        <div class="cps-subcard" style="display:flex;flex-direction:column;gap:8px;">
                            <div class="cps-subcard-kicker"><span>Card Metadata Visibility</span></div>
                            @php
                            $jsToggles = [
                                ['name'=>'js_show_posted_date', 'label'=>'Show Job Posted Date',       'desc'=>'Display posting age on job cards.',       'checked'=> !isset($careerSettings->js_show_posted_date) || !empty($careerSettings->js_show_posted_date)],
                                ['name'=>'js_show_experience',  'label'=>'Show Experience Range',      'desc'=>'Display required years on job cards.',    'checked'=> !isset($careerSettings->js_show_experience) || !empty($careerSettings->js_show_experience)],
                                ['name'=>'js_show_salary',      'label'=>'Show Salary Range',          'desc'=>'Display compensation band on job cards.', 'checked'=> !isset($careerSettings->js_show_salary) || !empty($careerSettings->js_show_salary)],
                                ['name'=>'js_show_tags',        'label'=>'Show Job Tags / Skills',     'desc'=>'Display primary skill pills on cards.',   'checked'=> !isset($careerSettings->js_show_tags) || !empty($careerSettings->js_show_tags)],
                            ];
                            @endphp
                            @foreach($jsToggles as $jt)
                                <div class="js-toggle-row">
                                    <div>
                                        <div class="js-toggle-label">{{ $jt['label'] }}</div>
                                        <div class="js-toggle-desc">{{ $jt['desc'] }}</div>
                                    </div>
                                    <label class="cps-switch" style="flex-shrink:0;">
                                        <input type="checkbox" name="{{ $jt['name'] }}" value="1" {{ $jt['checked'] ? 'checked' : '' }}>
                                        <span class="cps-slider"></span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 2: Apply Process --}}
            <div class="cps-card accent-emerald">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon emerald"><i class="ti ti-send"></i></div>
                        <div>
                            <div class="cps-card-title">Candidate Apply Workflow</div>
                            <div class="cps-card-sub">Define application intake mode, authentication gate, and post-apply redirect.</div>
                        </div>
                    </div>
                </div>

                <div class="cps-card-body">
                    <div class="cps-subcard-grid-2">
                        <div class="cps-subcard">
                            <div class="cps-subcard-kicker"><span>Intake Mode</span></div>
                            @php $applyOpt = $careerSettings->js_apply_option ?? 'resume'; @endphp
                            <div class="js-radio-group">
                                <label class="js-radio-item">
                                    <input type="radio" name="js_apply_option" value="resume" {{ $applyOpt === 'resume' ? 'checked' : '' }}>
                                    <div>
                                        <div class="js-radio-label">Apply with Resume</div>
                                        <div class="js-radio-desc">Fast 1-step resume upload &amp; client-side parse.</div>
                                    </div>
                                </label>
                                <label class="js-radio-item">
                                    <input type="radio" name="js_apply_option" value="profile" {{ $applyOpt === 'profile' ? 'checked' : '' }}>
                                    <div>
                                        <div class="js-radio-label">Apply with Profile</div>
                                        <div class="js-radio-desc">Candidates complete the structured intake form.</div>
                                    </div>
                                </label>
                                <label class="js-radio-item">
                                    <input type="radio" name="js_apply_option" value="both" {{ $applyOpt === 'both' ? 'checked' : '' }}>
                                    <div>
                                        <div class="js-radio-label">Both Resume &amp; Profile</div>
                                        <div class="js-radio-desc">Let candidates choose their preferred method.</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="cps-subcard" style="display:flex;flex-direction:column;gap:9px;">
                            <div class="cps-subcard-kicker"><span>Application Rules</span></div>
                            <div class="js-toggle-row">
                                <div>
                                    <div class="js-toggle-label">Require Login to Apply</div>
                                    <div class="js-toggle-desc">Gate applications behind candidate sign-in.</div>
                                </div>
                                <label class="cps-switch" style="flex-shrink:0;">
                                    <input type="checkbox" name="js_require_login" value="1">
                                    <span class="cps-slider"></span>
                                </label>
                            </div>
                            <div class="js-toggle-row">
                                <div>
                                    <div class="js-toggle-label">Auto Acknowledgement Email</div>
                                    <div class="js-toggle-desc">Send instant confirmation after submission.</div>
                                </div>
                                <label class="cps-switch" style="flex-shrink:0;">
                                    <input type="checkbox" name="js_auto_ack_email" value="1" checked>
                                    <span class="cps-slider"></span>
                                </label>
                            </div>
                            <div>
                                <label class="cps-label">Redirect After Apply</label>
                                <select class="js-select" name="js_redirect_after_apply">
                                    <option value="thank_you" selected>Thank You Page</option>
                                    <option value="job_list">Job Listings Page</option>
                                    <option value="home">Career Home Page</option>
                                    <option value="custom">Custom URL</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Primary Card 3: Job Detail Page Sections --}}
            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-file-text"></i></div>
                        <div>
                            <div class="cps-card-title">Job Detail Page Modules</div>
                            <div class="cps-card-sub">Toggle individual sections displayed inside a role's full description page.</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="js-chk-grid">
                        @php
                        $jsChks = [
                            ['name'=>'js_show_description',  'label'=>'Job Description',     'checked'=>true],
                            ['name'=>'js_show_key_resp',     'label'=>'Responsibilities',    'checked'=>true],
                            ['name'=>'js_show_requirements', 'label'=>'Requirements',        'checked'=>true],
                            ['name'=>'js_show_benefits',     'label'=>'Benefits',            'checked'=>true],
                            ['name'=>'js_show_skills',       'label'=>'Skills / Tags',       'checked'=>true],
                            ['name'=>'js_show_salary_range', 'label'=>'Salary Range',        'checked'=>true],
                            ['name'=>'js_show_company_info', 'label'=>'Company Info',        'checked'=>true],
                            ['name'=>'js_show_similar_jobs', 'label'=>'Similar Jobs',        'checked'=>true],
                            ['name'=>'js_show_deadline',     'label'=>'Apply Deadline',      'checked'=>false],
                            ['name'=>'js_show_department',   'label'=>'Department',          'checked'=>false],
                            ['name'=>'js_show_job_id',       'label'=>'Public Job ID',       'checked'=>false],
                        ];
                        @endphp
                        @foreach($jsChks as $jc)
                            <label class="js-chk-item">
                                <input type="checkbox" name="{{ $jc['name'] }}" value="1" {{ $jc['checked'] ? 'checked' : '' }}>
                                <span>{{ $jc['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

        {{-- Right Sticky Preview & Additional Options --}}
        <div class="cps-col-side sticky-side">

            <div class="cps-card accent-indigo">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon indigo"><i class="ti ti-eye"></i></div>
                        <div>
                            <div class="cps-card-title">Job Card Live Preview</div>
                            <div class="cps-card-sub">Enterprise role cards rendered on your portal</div>
                        </div>
                    </div>
                </div>
                <div class="cps-card-body">
                    <div class="js-prev-wrap">
                        <div class="js-prev-nav">
                            <div class="js-prev-nav-logo">
                                <i class="ti ti-building" style="font-size:11px;color:#009ef7;"></i>
                                {{ $companyName }}
                            </div>
                            <div class="js-prev-nav-links">
                                <span class="js-prev-nav-link">Jobs</span>
                                <span class="js-prev-nav-link">Culture</span>
                                <span class="js-prev-nav-link">Benefits</span>
                            </div>
                            <div class="js-prev-nav-btn">View Openings</div>
                        </div>
                        <div class="js-prev-body">
                            <div class="js-job-card">
                                <span class="js-job-bookmark"><i class="ti ti-bookmark"></i></span>
                                <div class="js-job-badge">New</div>
                                <div class="js-job-title">Senior Backend Developer</div>
                                <div class="js-job-meta">
                                    <span><i class="ti ti-briefcase" style="font-size:8px;"></i> Engineering</span>
                                    <span>•</span>
                                    <span><i class="ti ti-map-pin" style="font-size:8px;"></i> Hybrid</span>
                                    <span>•</span>
                                    <span>Full-Time</span>
                                    <span style="margin-left:auto;color:#94a3b8;">2d ago</span>
                                </div>
                                <div class="js-job-info">
                                    <span><i class="ti ti-clock" style="font-size:8px;"></i> 5-7 Yrs</span>
                                    <span><i class="ti ti-currency-rupee" style="font-size:8px;"></i> 18 – 25 LPA</span>
                                </div>
                                <div class="js-job-desc">Build high-throughput CQRS services and multi-tenant enterprise hiring workflows.</div>
                                <div class="js-job-tags">
                                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                        <span class="js-tag">PHP</span>
                                        <span class="js-tag">Laravel</span>
                                        <span class="js-tag">Elasticsearch</span>
                                        <span class="js-tag" style="color:#009ef7;background:#eff6ff;">+3 more</span>
                                    </div>
                                    <div class="js-job-btn">View Job</div>
                                </div>
                            </div>

                            <div class="js-job-card">
                                <span class="js-job-bookmark"><i class="ti ti-bookmark"></i></span>
                                <div class="js-job-title">UI/UX Product Designer</div>
                                <div class="js-job-meta">
                                    <span><i class="ti ti-palette" style="font-size:8px;"></i> Design</span>
                                    <span>•</span>
                                    <span><i class="ti ti-world" style="font-size:8px;"></i> Remote</span>
                                    <span>•</span>
                                    <span>Full-Time</span>
                                </div>
                                <div class="js-job-info">
                                    <span><i class="ti ti-clock" style="font-size:8px;"></i> 2-4 Yrs</span>
                                    <span><i class="ti ti-currency-rupee" style="font-size:8px;"></i> 10 – 15 LPA</span>
                                </div>
                                <div class="js-job-tags">
                                    <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                        <span class="js-tag">Figma</span>
                                        <span class="js-tag">Design Systems</span>
                                    </div>
                                    <div class="js-job-btn">View Job</div>
                                </div>
                            </div>

                            <div class="js-prev-link">
                                View Full Career Page <i class="ti ti-external-link" style="font-size:9px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cps-card">
                <div class="cps-card-head">
                    <div class="cps-card-head-left">
                        <div class="cps-card-icon"><i class="ti ti-tools"></i></div>
                        <div><div class="cps-card-title">Candidate Utility Controls</div></div>
                    </div>
                </div>
                <div class="cps-card-body">
                    @php
                    $jsAddOpts = [
                        ['icon'=>'ti-bookmark', 'bg'=>'#eff6ff','ic'=>'#009ef7','title'=>'Enable Save Job',     'desc'=>'Allow candidates to bookmark roles.',   'name'=>'js_enable_save_job',  'checked'=>true],
                        ['icon'=>'ti-share',    'bg'=>'#ecfdf5','ic'=>'#059669','title'=>'Enable Share Job',    'desc'=>'One-click social & link sharing.',     'name'=>'js_enable_share_job', 'checked'=>true],
                        ['icon'=>'ti-printer',  'bg'=>'#fff7ed','ic'=>'#f97316','title'=>'Enable Print Job',    'desc'=>'Print-friendly role specifications.',  'name'=>'js_enable_print_job', 'checked'=>false],
                        ['icon'=>'ti-eye',      'bg'=>'#f5f3ff','ic'=>'#7c3aed','title'=>'Show Job View Count', 'desc'=>'Display popularity counter on role.',  'name'=>'js_show_view_count',  'checked'=>false],
                    ];
                    @endphp
                    @foreach($jsAddOpts as $ao)
                        <div class="js-addopt-row">
                            <div class="js-addopt-left">
                                <div class="js-addopt-icon" style="background:{{ $ao['bg'] }};">
                                    <i class="ti {{ $ao['icon'] }}" style="font-size:15px;color:{{ $ao['ic'] }};"></i>
                                </div>
                                <div>
                                    <div class="js-addopt-title">{{ $ao['title'] }}</div>
                                    <div class="js-addopt-desc">{{ $ao['desc'] }}</div>
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
