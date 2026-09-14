<div class="jh-filter-bar">
    <div class="jh-search-wrap">
        <svg class="jh-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" class="jh-search-input inputJobSearch" name="inputJobSearch"
               placeholder="Search by job title, skills, company..." id="kt_tagify_2"
               autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"/>
        <button type="button" id="jobSearchBtn" class="jh-search-btn" onclick="fetchCandidates(1)">
            Search
        </button>
    </div>

    <div class="jh-filter-group">

        {{-- Hidden status input so status tabs continue updating table --}}
        <input type="hidden" id="status" value="all">

        {{-- JOB TYPE Dropdown --}}
        <div class="jh-select-wrap jh-custom-select">
            <span class="jh-control-icon">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
                    <rect x="3" y="7" width="18" height="13" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </span>
            <div class="jh-control-copy">
                <div class="jh-select-label">JOB TYPE</div>
                <button type="button" class="jh-select-trigger" data-select-target="employment_type" aria-expanded="false">
                    <span class="jh-select-value">All Types</span>
                </button>
                <select class="jh-select-native" id="employment_type" onchange="fetchCandidates(1)" tabindex="-1" aria-hidden="true">
                    <option value="all" selected>All Types</option>
                    <option value="0">Full-time</option>
                    <option value="1">Contract</option>
                    <option value="2">Freelance</option>
                    <option value="3">Part Time</option>
                </select>
            </div>
            <svg class="jh-select-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            <div class="jh-select-menu" data-select-menu="employment_type">
                <button type="button" class="jh-select-option is-selected" data-value="all">All Types</button>
                <button type="button" class="jh-select-option" data-value="0">Full-time</button>
                <button type="button" class="jh-select-option" data-value="1">Contract</button>
                <button type="button" class="jh-select-option" data-value="2">Freelance</button>
                <button type="button" class="jh-select-option" data-value="3">Part Time</button>
            </div>
        </div>

        {{-- EXPERIENCE Dropdown --}}
        <div class="jh-select-wrap jh-custom-select">
            <span class="jh-control-icon">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
                    <path d="M22 10v6M2 10l10-5 10 5-10 5z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6 12v5c0 2 3 3 6 3s6-1 6-3v-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <div class="jh-control-copy">
                <div class="jh-select-label">EXPERIENCE</div>
                <button type="button" class="jh-select-trigger" data-select-target="experience_filter" aria-expanded="false">
                    <span class="jh-select-value">All Exp.</span>
                </button>
                <select class="jh-select-native" id="experience_filter" onchange="fetchCandidates(1)" tabindex="-1" aria-hidden="true">
                    <option value="all" selected>All Exp.</option>
                    <option value="0-1">0-1 yr</option>
                    <option value="1-3">1-3 yrs</option>
                    <option value="3-5">3-5 yrs</option>
                    <option value="5+">5+ yrs</option>
                </select>
            </div>
            <svg class="jh-select-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            <div class="jh-select-menu" data-select-menu="experience_filter">
                <button type="button" class="jh-select-option is-selected" data-value="all">All Exp.</button>
                <button type="button" class="jh-select-option" data-value="0-1">0-1 yr</button>
                <button type="button" class="jh-select-option" data-value="1-3">1-3 yrs</button>
                <button type="button" class="jh-select-option" data-value="3-5">3-5 yrs</button>
                <button type="button" class="jh-select-option" data-value="5+">5+ yrs</button>
            </div>
        </div>

        {{-- SORT BY Dropdown --}}
        <div class="jh-select-wrap jh-custom-select">
            <span class="jh-control-icon">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
                    <path d="M4 7h12M4 12h9M4 17h5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                    <path d="M18 6v12m0 0 3-3m-3 3-3-3" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <div class="jh-control-copy">
                <div class="jh-select-label">SORT BY</div>
                <button type="button" class="jh-select-trigger" data-select-target="hiringtype" aria-expanded="false">
                    <span class="jh-select-value">Newest First</span>
                </button>
                <select class="jh-select-native" id="hiringtype" onchange="fetchCandidates(1)" tabindex="-1" aria-hidden="true">
                    <option value="1" selected>Newest First</option>
                    <option value="2">Oldest First</option>
                    <option value="3">Low-High (Salary)</option>
                    <option value="4">High-Low (Salary)</option>
                    <option value="5">A-Z (Company)</option>
                    <option value="6">Z-A (Company)</option>
                </select>
            </div>
            <svg class="jh-select-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
            <div class="jh-select-menu" data-select-menu="hiringtype">
                <button type="button" class="jh-select-option is-selected" data-value="1">Newest First</button>
                <button type="button" class="jh-select-option" data-value="2">Oldest First</button>
                <button type="button" class="jh-select-option" data-value="3">Low-High (Salary)</button>
                <button type="button" class="jh-select-option" data-value="4">High-Low (Salary)</button>
                <button type="button" class="jh-select-option" data-value="5">A-Z (Company)</button>
                <button type="button" class="jh-select-option" data-value="6">Z-A (Company)</button>
            </div>
        </div>
    </div>
</div>
