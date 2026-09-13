<div class="jh-filter-bar">
    <input type="hidden" id="status" value="all">
    <input type="hidden" id="experience_filter" value="all">

    <div class="jh-search-wrap" style="position:relative;">
        <svg class="jh-search-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
        </svg>
        <input type="text" class="jh-search-input inputJobSearch" name="inputJobSearch"
               placeholder="Search by job title, skills, company..." id="kt_tagify_2"
               style="padding-right:100px;"/>
        <button type="button" id="jobSearchBtn" onclick="fetchCandidates(1)"
            style="position:absolute;right:4px;top:50%;transform:translateY(-50%);padding:6px 14px;border-radius:6px;border:none;background:#2563EB;color:#fff;font-family:'Poppins',sans-serif;font-size:12px;font-weight:600;cursor:pointer;transition:background .15s;"
            onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563EB'">
            Search
        </button>
    </div>

    <div class="jh-filter-group">

        <div class="jh-select-wrap">
            <div class="jh-select-label">Job Type</div>
            <select class="jh-select" id="employment_type" onchange="filtersdata()">
                <option value="all" selected>All Types</option>
                <option value="0">Full-time</option>
                <option value="1">Contract</option>
                <option value="2">Freelance</option>
                <option value="3">Part Time</option>
            </select>
            <svg class="jh-select-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
        </div>

        <div class="jh-select-wrap">
            <div class="jh-select-label">Sort By</div>
            <select class="jh-select" id="hiringtype" onchange="filtersdata()">
                <option value="1" selected>Newest First</option>
                <option value="2">Oldest First</option>
                <option value="3">Low–High (Salary)</option>
                <option value="4">High–Low (Salary)</option>
                <option value="5">A–Z (Company)</option>
                <option value="6">Z–A (Company)</option>
            </select>
            <svg class="jh-select-caret" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
        </div>


    </div>
</div>
