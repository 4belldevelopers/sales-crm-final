<div class="jh-filter-bar">
    <input type="hidden" id="status" value="all">
    <input type="hidden" id="experience_filter" value="all">

    <div class="jh-search-wrap">
        <svg class="jh-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" class="jh-search-input inputJobSearch" name="inputJobSearch"
               placeholder="Search by job title, skills, company..." id="kt_tagify_2"/>
        <button type="button" id="jobSearchBtn" class="jh-search-btn" onclick="fetchCandidates(1)">
            Search
        </button>
    </div>

    <div class="jh-filter-group">

        <div class="jh-select-wrap">
            <span class="jh-control-icon">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
                    <rect x="3" y="7" width="18" height="13" rx="2" stroke="currentColor" stroke-width="1.8"/>
                    <path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2" stroke="currentColor" stroke-width="1.8"/>
                </svg>
            </span>
            <div class="jh-control-copy">
                <div class="jh-select-label">Job Type</div>
                <select class="jh-select" id="employment_type" onchange="filtersdata()">
                    <option value="all" selected>All Types</option>
                    <option value="0">Full-time</option>
                    <option value="1">Contract</option>
                    <option value="2">Freelance</option>
                    <option value="3">Part Time</option>
                </select>
            </div>
            <svg class="jh-select-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
        </div>

        <div class="jh-select-wrap">
            <span class="jh-control-icon">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24">
                    <path d="M4 7h12M4 12h9M4 17h5" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                    <path d="M18 6v12m0 0 3-3m-3 3-3-3" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <div class="jh-control-copy">
                <div class="jh-select-label">Sort By</div>
                <select class="jh-select" id="hiringtype" onchange="filtersdata()">
                    <option value="1" selected>Newest First</option>
                    <option value="2">Oldest First</option>
                    <option value="3">Low-High (Salary)</option>
                    <option value="4">High-Low (Salary)</option>
                    <option value="5">A-Z (Company)</option>
                    <option value="6">Z-A (Company)</option>
                </select>
            </div>
            <svg class="jh-select-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
        </div>
    </div>
</div>
