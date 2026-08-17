{{-- Job Settings Tab Panel --}}
<div class="cps-panel" id="panel-jobs">
<div class="row g-4">

    {{-- Left --}}
    <div class="col-lg-7">

        {{-- Job Listing Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">Job Listing Settings</div>
            <div class="cps-section-sub">Configure how jobs are displayed and apply process for candidates.</div>

            <div class="row g-4">
                {{-- Left col: dropdowns --}}
                <div class="col-6">
                    <div class="mb-4">
                        <label class="cps-label">Job Display Style</label>
                        <select class="js-select" name="js_display_style">
                            <option value="card" selected>Card View</option>
                            <option value="list">List View</option>
                            <option value="compact">Compact View</option>
                        </select>
                        <div class="js-hint">Choose how job openings are displayed on your career page.</div>
                    </div>
                    <div class="mb-4">
                        <label class="cps-label">Jobs Per Page</label>
                        <select class="js-select" name="js_per_page">
                            @foreach([6,9,12,15,20] as $n)
                            <option value="{{ $n }}" {{ $n==12?'selected':'' }}>{{ $n }} Jobs</option>
                            @endforeach
                        </select>
                        <div class="js-hint">Number of jobs to display per page.</div>
                    </div>
                    <div>
                        <label class="cps-label">Default Job Sort By</label>
                        <select class="js-select" name="js_sort_by">
                            <option value="newest" selected>Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="relevant">Most Relevant</option>
                            <option value="salary">Highest Salary</option>
                        </select>
                        <div class="js-hint">Choose the default sorting for job listings.</div>
                    </div>
                </div>

                {{-- Right col: toggles --}}
                <div class="col-6" style="display:flex;flex-direction:column;gap:20px;">
                    @php
                    $jsToggles = [
                        ['name'=>'js_show_posted_date',  'label'=>'Show Job Posted Date',       'desc'=>'Display the job posted date on job cards.',      'checked'=>true],
                        ['name'=>'js_show_experience',   'label'=>'Show Experience on Job Card', 'desc'=>'Display experience required on job cards.',      'checked'=>true],
                        ['name'=>'js_show_salary',       'label'=>'Show Salary on Job Card',     'desc'=>'Display salary or salary range on job cards.',   'checked'=>true],
                        ['name'=>'js_show_tags',         'label'=>'Show Job Tags / Skills',      'desc'=>'Display skills and tags on job cards.',          'checked'=>true],
                    ];
                    @endphp
                    @foreach($jsToggles as $jt)
                    <div class="js-toggle-row">
                        <div>
                            <div class="js-toggle-label">{{ $jt['label'] }}</div>
                            <div class="js-toggle-desc">{{ $jt['desc'] }}</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="{{ $jt['name'] }}" value="1" {{ $jt['checked']?'checked':'' }}>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Apply Process Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">Apply Process Settings</div>
            <div class="cps-section-sub">Manage the application process and required information.</div>

            <div class="row g-4">
                {{-- Apply Option radios --}}
                <div class="col-6">
                    <label class="cps-label">Apply Option</label>
                    <div class="js-radio-group" style="margin-top:8px;">
                        <label class="js-radio-item">
                            <input type="radio" name="js_apply_option" value="resume" checked>
                            <div>
                                <div class="js-radio-label">Apply with Resume</div>
                                <div class="js-radio-desc">Candidates can apply by uploading resume.</div>
                            </div>
                        </label>
                        <label class="js-radio-item">
                            <input type="radio" name="js_apply_option" value="profile">
                            <div>
                                <div class="js-radio-label">Apply with Profile</div>
                                <div class="js-radio-desc">Candidates must fill the application form.</div>
                            </div>
                        </label>
                        <label class="js-radio-item">
                            <input type="radio" name="js_apply_option" value="both">
                            <div>
                                <div class="js-radio-label">Both Resume & Profile</div>
                                <div class="js-radio-desc">Candidates can choose either option.</div>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Right: toggles + redirect --}}
                <div class="col-6" style="display:flex;flex-direction:column;gap:18px;">
                    <div class="js-toggle-row">
                        <div>
                            <div class="js-toggle-label">Require Login to Apply</div>
                            <div class="js-toggle-desc">Candidates must login or register to apply.</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="js_require_login" value="1">
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    <div class="js-toggle-row">
                        <div>
                            <div class="js-toggle-label">Auto Acknowledgement Email</div>
                            <div class="js-toggle-desc">Send confirmation email to candidates after applying.</div>
                        </div>
                        <label class="cps-switch" style="flex-shrink:0;">
                            <input type="checkbox" name="js_auto_ack_email" value="1" checked>
                            <span class="cps-slider"></span>
                        </label>
                    </div>
                    <div>
                        <label class="cps-label">Redirect After Apply</label>
                        <select class="js-select" name="js_redirect_after_apply" style="margin-top:4px;">
                            <option value="thank_you" selected>Thank You Page</option>
                            <option value="job_list">Job Listings Page</option>
                            <option value="home">Career Home Page</option>
                            <option value="custom">Custom URL</option>
                        </select>
                        <div class="js-hint">Choose where candidates will be redirected.</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Job Detail Settings --}}
        <div class="cps-section">
            <div class="cps-section-title">Job Detail Settings</div>
            <div class="cps-section-sub">Choose what information appears on the job detail page.</div>
            <div class="js-chk-grid" style="margin-top:4px;">
                @php
                $jsChks = [
                    ['name'=>'js_show_description',   'label'=>'Show Job Description',      'checked'=>true],
                    ['name'=>'js_show_key_resp',       'label'=>'Show Key Responsibilities', 'checked'=>true],
                    ['name'=>'js_show_requirements',   'label'=>'Show Requirements',         'checked'=>true],
                    ['name'=>'js_show_benefits',       'label'=>'Show Benefits',             'checked'=>true],
                    ['name'=>'js_show_skills',         'label'=>'Show Skills / Tags',        'checked'=>true],
                    ['name'=>'js_show_salary_range',   'label'=>'Show Salary Range',         'checked'=>true],
                    ['name'=>'js_show_company_info',   'label'=>'Show Company Info',         'checked'=>true],
                    ['name'=>'js_show_similar_jobs',   'label'=>'Show Similar Jobs',         'checked'=>true],
                    ['name'=>'js_show_deadline',       'label'=>'Show Application Deadline', 'checked'=>false],
                    ['name'=>'js_show_department',     'label'=>'Show Department',           'checked'=>false],
                    ['name'=>'js_show_job_id',         'label'=>'Show Job ID',               'checked'=>false],
                ];
                @endphp
                @foreach($jsChks as $jc)
                <label class="js-chk-item">
                    <input type="checkbox" name="{{ $jc['name'] }}" value="1" {{ $jc['checked']?'checked':'' }}>
                    {{ $jc['label'] }}
                </label>
                @endforeach
            </div>
        </div>

    </div>

    {{-- Right: Preview + Additional Options --}}
    <div class="col-lg-5">

        {{-- Live Preview --}}
        <div class="cps-sidebar-card" style="padding:0;overflow:hidden;">
            <div style="padding:14px 18px 12px;border-bottom:1px solid #F1F5F9;">
                <h4 style="margin:0 0 2px;">Live Preview</h4>
                <p class="sub" style="margin:0;">See how job listings will appear on your career page.</p>
            </div>
            <div style="padding:12px;">
                <div class="js-prev-wrap">
                    <div class="js-prev-nav">
                        <div class="js-prev-nav-logo">
                            <i class="ti ti-building" style="font-size:11px;color:#2563EB;"></i>
                            {{ $companyName }}
                        </div>
                        <div class="js-prev-nav-links">
                            <span class="js-prev-nav-link">Jobs</span>
                            <span class="js-prev-nav-link">Teams</span>
                            <span class="js-prev-nav-link">Life at 4Bell</span>
                            <span class="js-prev-nav-link">Benefits</span>
                            <span class="js-prev-nav-link">About Us</span>
                        </div>
                        <div class="js-prev-nav-btn">View Openings</div>
                    </div>
                    <div class="js-prev-body">

                        {{-- Job Card 1 --}}
                        <div class="js-job-card">
                            <span class="js-job-bookmark"><i class="ti ti-bookmark"></i></span>
                            <div class="js-job-badge">New</div>
                            <div class="js-job-title">Senior Backend Developer</div>
                            <div class="js-job-meta">
                                <span><i class="ti ti-briefcase" style="font-size:8px;"></i> Engineering</span>
                                <span>•</span>
                                <span><i class="ti ti-map-pin" style="font-size:8px;"></i> Gurugram, India</span>
                                <span>•</span>
                                <span>Full-time</span>
                                <span style="margin-left:auto;font-size:8px;color:#94A3B8;">Posted 2 days ago</span>
                            </div>
                            <div class="js-job-info">
                                <span><i class="ti ti-clock" style="font-size:8px;"></i> 5-7 Years</span>
                                <span><i class="ti ti-currency-rupee" style="font-size:8px;"></i> ₹18 – ₹25 LPA</span>
                            </div>
                            <div class="js-job-desc">We are looking for a skilled Backend Developer to build scalable and high-performance</div>
                            <div class="js-job-tags">
                                <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                    <span class="js-tag">PHP</span>
                                    <span class="js-tag">Laravel</span>
                                    <span class="js-tag">MySQL</span>
                                    <span class="js-tag">REST API</span>
                                    <span class="js-tag">AWS</span>
                                    <span class="js-tag" style="color:#2563EB;background:#EFF6FF;">+3 more</span>
                                </div>
                                <div class="js-job-btn">View Job</div>
                            </div>
                        </div>

                        {{-- Job Card 2 --}}
                        <div class="js-job-card">
                            <span class="js-job-bookmark"><i class="ti ti-bookmark"></i></span>
                            <div class="js-job-title" style="margin-bottom:2px;">UI/UX Designer</div>
                            <div class="js-job-meta">
                                <span><i class="ti ti-palette" style="font-size:8px;"></i> Design</span>
                                <span>•</span>
                                <span><i class="ti ti-world" style="font-size:8px;"></i> Remote</span>
                                <span>•</span>
                                <span>Full-time</span>
                                <span style="margin-left:auto;font-size:8px;color:#94A3B8;">Posted 5 days ago</span>
                            </div>
                            <div class="js-job-info">
                                <span><i class="ti ti-clock" style="font-size:8px;"></i> 2-4 Years</span>
                                <span><i class="ti ti-currency-rupee" style="font-size:8px;"></i> ₹8 – ₹12 LPA</span>
                            </div>
                            <div class="js-job-desc">Create intuitive and engaging user experiences for our web and mobile applications.</div>
                            <div class="js-job-tags">
                                <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                    <span class="js-tag">Figma</span>
                                    <span class="js-tag">Adobe XD</span>
                                    <span class="js-tag">UI Design</span>
                                    <span class="js-tag">Wireframing</span>
                                    <span class="js-tag" style="color:#2563EB;background:#EFF6FF;">+2 more</span>
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

        {{-- Additional Options --}}
        <div class="cps-sidebar-card" style="margin-top:16px;">
            <h4 style="margin:0 0 4px;">Additional Options</h4>
            @php
            $jsAddOpts = [
                ['icon'=>'ti-bookmark',  'bg'=>'#EFF6FF','ic'=>'#2563EB','title'=>'Enable Save Job',       'desc'=>'Allow candidates to save jobs for later.',      'name'=>'js_enable_save_job',  'checked'=>true],
                ['icon'=>'ti-share',     'bg'=>'#F0FDF4','ic'=>'#16A34A','title'=>'Enable Share Job',      'desc'=>'Allow candidates to share jobs with others.',   'name'=>'js_enable_share_job', 'checked'=>true],
                ['icon'=>'ti-printer',   'bg'=>'#FFF7ED','ic'=>'#F97316','title'=>'Enable Print Job',      'desc'=>'Allow candidates to print job details.',        'name'=>'js_enable_print_job', 'checked'=>false],
                ['icon'=>'ti-eye',       'bg'=>'#FAF5FF','ic'=>'#7C3AED','title'=>'Show Job View Count',   'desc'=>'Display number of views on job details.',       'name'=>'js_show_view_count',  'checked'=>false],
            ];
            @endphp
            @foreach($jsAddOpts as $ao)
            <div class="js-addopt-row">
                <div class="js-addopt-left">
                    <div class="js-addopt-icon" style="background:{{ $ao['bg'] }};">
                        <i class="ti {{ $ao['icon'] }}" style="font-size:16px;color:{{ $ao['ic'] }};"></i>
                    </div>
                    <div>
                        <div class="js-addopt-title">{{ $ao['title'] }}</div>
                        <div class="js-addopt-desc">{{ $ao['desc'] }}</div>
                    </div>
                </div>
                <label class="cps-switch" style="flex-shrink:0;margin-top:4px;">
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
