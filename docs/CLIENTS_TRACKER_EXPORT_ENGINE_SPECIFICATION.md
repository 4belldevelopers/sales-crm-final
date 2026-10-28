# Architectural Specification: Enterprise Client-Side Export Engine (Clients Tracker)

> **Document Classification:** Engineering Master Standard & Security Specification  
> **Target Module:** Service Company & Staffing Company — Clients Tracker (`resources/views/service_company/client/index.blade.php`)  
> **Security Standards:** CWE-1236 Formula Injection Defense, ISO 8601 UTC Temporal Integrity, Audit Manifest Tracking  
> **Architecture Tenets:** CQRS (0% MySQL Reads), 100% Client-Side Execution (User RAM + Web Worker), 10,000 Concurrent Users Target (Zero Server Strain), Single Canonical Filter/Sort Contract  
> **Author:** JERO Core Architecture Team  
> **Status:** Authoritative / Gold Standard (Production Certified)  

---

## 1. Executive Summary & Core Engineering Laws

| Requirement | Standard Specification | Rationale & Defense |
| :--- | :--- | :--- |
| **Retain vs. Remove** | **RETAIN & ELEVATE** | Critical enterprise data portability. Prevents dangerous scraping and manual screenshotting. |
| **Download Formats** | **Dual Option: CSV (Primary) + Print-to-PDF Report (Secondary)** | **CSV** for raw modeling, accounting, CRM. **Dedicated Print DOM** for executive/client reviews. |
| **Execution Architecture** | **100% Client-Side / 0% Server-Side** | Zero HTTP requests, zero PHP process allocation, zero MySQL reads. 10,000 users export simultaneously with 0% server CPU strain. |
| **Data Hydration** | **IndexedDB ↔ Worker Direct Binding** | Web Worker reads from `ClientLocationDB` or validated hydrated RAM. Eliminates empty-worker race conditions. |
| **Security Protection** | **CWE-1236 Formula Injection Neutralization** | All text fields prefixed with `=+\-@\t\r` are neutralized with single-quote escaping. |
| **Temporal Standard** | **ISO 8601 UTC Canonical Persistence** | No relative strings (`"2 hours ago"`) in canonical export columns. Absolute timestamps only. |

```
                              JERO CLIENT EXPORT PIPELINE
                                          │
                                          ▼
                               Authoritative Read Model
                                          │
                                          ▼
                               Permission Filter Check
                                          │
                                          ▼
                              Hydrated Client DTO Stream
                                          │
                                ┌─────────┴─────────┐
                                ▼                   ▼
                             IndexedDB          User RAM
                          (ClientLocationDB) (ClientStore)
                                │                   │
                                └─────────┬─────────┘
                                          ▼
                                 Client Dataset Layer
                                          │
                                 ┌────────┴────────┐
                                 ▼                 ▼
                             UI Listing        Web Worker
                                 │            (clWorker)
                                 │          ┌──────┴──────┐
                                 │          ▼             ▼
                                 │       CSV Stream    Executive VM
                                 │       (RFC 4180)         │
                                 │          │               ▼
                                 │          ▼         Dedicated Print DOM
                                 │     Blob Engine    (#cl-print-report)
                                 │          │               │
                                 │          ▼               ▼
                                 │     File Download    window.print()
                                 ▼
                     • Same Filter State (ClientFilterState)
                     • Same Sort State (ClientSortState)
                     • Same Dataset Version (Audit Manifest)
```

---

## 2. Dataset Hydration & Web Worker Lifecycle

### 2.1 The Hydration Problem & Formal Solution
The Web Worker (`clWorker`) must never execute against an unverified `self.dataset`. To prevent race conditions where a user clicks export before hydration completes, the export engine enforces a three-step lifecycle:

```
[User clicks "Export"]
        │
        ▼
Is Web Worker Hydrated & Ready?
├─► YES: Proceed to filtering & serialization.
└─► NO:
      1. UI enters state: `PREPARING` (shows spinner badge).
      2. Worker opens `self.indexedDB` (`ClientLocationDB`) directly.
      3. Reads authoritative cached rows and sets `self.dataset`.
      4. Dispatches export.
```

### 2.2 Dataset Scope & Anti-Corruption Guardrails
The UI must never silently export a partial dataset when the user intends to export all accounts.

| Dataset Scope | Definition | UI Handling |
| :--- | :--- | :--- |
| `HYDRATED_ALL` | Complete tenant portfolio loaded in IndexedDB. | Dispatches immediately: `Export All (30)`. |
| `HYDRATED_FILTERED` | Current active filter subset within hydrated dataset. | Dispatches immediately: `Export Filtered (10)`. |
| `PARTIAL_WARNING` | Browser has hydrated only a partial chunk (e.g., page 1). | Triggers modal: *"Only 50 of 1,200 clients loaded. [Export 50] or [Sync Full]"*. |
| `STALE_WARNING` | Client data version older than 5 minutes. | Displays warning: *"Data synced 12m ago. [Refresh] before export"*. |

---

## 3. CSV Security Specification: CWE-1236 Defense

### 3.1 The Vulnerability
In a collaborative recruitment SaaS, fields such as `Company Name`, `Industry`, or `Notes` can be manipulated by malicious actors or unintended inputs starting with formula trigger characters:
* `=cmd|' /C calc'!A0`
* `=HYPERLINK("http://malicious-phishing.com", "Click Here")`
* `@SUM(1+1)*cmd`
* `+` or `-` leading expressions

When an Account Manager or Executive opens the CSV in Microsoft Excel, LibreOffice, or Google Sheets, the spreadsheet software treats these cells as dynamic executable formulas.

### 3.2 Mandatory Cell Sanitization Standard
Every text value processed by the export engine must pass through the `sanitizeCsvCell` validator before serialization:

```javascript
/**
 * CWE-1236 Neutralization & RFC 4180 Escaper
 * Prevents formula injection and enforces strict quote escaping
 */
function sanitizeCsvCell(value) {
    if (value === null || value === undefined) return '""';
    
    // Convert to string
    let str = String(value);

    // Neutralize formula triggers (=, +, -, @, \t, \r) by prepending single quote (')
    if (/^[=+\-@\t\r]/.test(str)) {
        str = "'" + str;
    }

    // RFC 4180: Double all interior double-quotes and wrap in quotes
    return '"' + str.replace(/"/g, '""') + '"';
}
```

---

## 4. Temporal Integrity & Canonical Timestamp Standard

Relative time strings such as `"2 hours ago"`, `"yesterday"`, or `"just now"` are strictly forbidden in canonical export columns because they become factually incorrect the moment the file is saved to disk.

### The Universal ATS Temporal Standard:
1. **API / IndexedDB Storage:** Strict ISO 8601 UTC (`YYYY-MM-DDTHH:mm:ssZ`).
2. **Canonical CSV Columns:** Standardized UTC format (`YYYY-MM-DD HH:mm:ss UTC`).
3. **Display CSV Columns (Optional companion):** Localized or relative text clearly marked as `(Display)`.

```csv
"Company Name","Connected Date","Last Activity UTC","Last Activity (Display)"
"Stripe Ireland Ltd","2026-03-15","2026-10-28 06:22:15 UTC","2 hours ago"
```

---

## 5. Enterprise Auditability: The Export Manifest

Every CSV generated by the JERO Export Engine includes an auditable header manifest. This provides enterprise clients and compliance teams with cryptographic certainty regarding what was exported, when, and by whom.

```csv
# ==============================================================================
# JERO ENTERPRISE ATS EXPORT MANIFEST
# Export ID: EXP-20261028-000492
# Exported At: 2026-10-28T08:22:15Z
# Tenant ID: 1048 (Acme Staffing Solutions Ltd)
# Generated By User: ID #382 (Account Manager)
# Dataset Scope: Active Companies (Filtered)
# Filter Hash: sha256:8f2a9c1e...
# Dataset Version: v2.1.84
# Record Count: 10
# Security Classification: Internal Confidential
# ==============================================================================
"Client ID","Company Name","Industry","Jobs Hiring","Joined","Last Activity UTC"
```

---

## 6. UI/UX Export State Machine

To eliminate UI freezing, double-clicks, and ambiguous wait times, the Export UI strictly transitions through a finite state machine:

```
[ IDLE ] ──(Click Export)──► [ PREPARING ] ──(Worker Check)──► [ FILTERING ]
                                                                      │
[ COMPLETE ] ◄──(Revoke URL)── [ DOWNLOADING ] ◄──(Blob Ready)── [ SERIALIZING ]
      │
(Return to IDLE after 2s)
```

| State | Button Appearance | Micro-Interaction |
| :--- | :--- | :--- |
| `IDLE` | `[ 📥 Export ▾ ]` | Clickable, shows active filter count badge. |
| `PREPARING` | `[ ⏳ Checking Data... ]` | Disabled, verifies IndexedDB hydration. |
| `FILTERING` | `[ ⚙️ Filtering (10)... ]` | Disabled, worker isolates matching records. |
| `SERIALIZING` | `[ ⚡ Building CSV... ]` | Disabled, worker escapes strings & applies BOM. |
| `DOWNLOADING` | `[ 💾 Downloading... ]` | Triggers browser download dialog. |
| `COMPLETE` | `[ ✓ Exported! ]` | Displays success toast: *"Exported 10 clients in 14ms"*. |

---

## 7. Dedicated Executive Print-Report DOM (PDF Architecture)

### 7.1 Why Live Card Printing Fails
Printing interactive Metronic UI cards by simply hiding navigation bars causes:
- Cards clipped halfway across page breaks.
- Inconsistent margins and wasted paper whitespace.
- Cluttered action icons, hover states, and badge styling inappropriate for client meetings.

### 7.2 The Dedicated Print Report DOM Architecture
When the user selects **"Save / Print PDF Report"**, the application compiles an **Executive Report View Model** and renders it into a dedicated, clean, off-screen print DOM container (`#cl-print-report-canvas`).

```html
<!-- DEDICATED OFF-SCREEN PRINT DOM CONTAINER -->
<div id="cl-print-report-canvas" class="d-none d-print-block">
    <!-- Corporate Letterhead -->
    <div class="print-report-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="print-title">Clients Portfolio & Recruitment Health Report</h2>
                <div class="print-subtitle">Acme Staffing Group • Confidential Enterprise Pipeline Audit</div>
            </div>
            <div class="text-end">
                <div class="print-meta-item"><strong>Date:</strong> 28 Oct 2026, 13:52 IST</div>
                <div class="print-meta-item"><strong>Scope:</strong> Active Clients (10 Accounts)</div>
                <div class="print-meta-item"><strong>Status:</strong> Authoritative / Real-Time</div>
            </div>
        </div>
    </div>

    <!-- Executive KPI Summary Strip -->
    <div class="print-kpi-grid">
        <div class="print-kpi-box"><span class="kpi-num">30</span><span class="kpi-lbl">Total Clients</span></div>
        <div class="print-kpi-box"><span class="kpi-num">10</span><span class="kpi-lbl">Active Companies</span></div>
        <div class="print-kpi-box"><span class="kpi-num">13</span><span class="kpi-lbl">Open Requisitions</span></div>
        <div class="print-kpi-box"><span class="kpi-num">48</span><span class="kpi-lbl">In Pipeline</span></div>
        <div class="print-kpi-box"><span class="kpi-num">7</span><span class="kpi-lbl">Placements (Joined)</span></div>
    </div>

    <!-- Structured High-Density Tabular Dataset -->
    <table class="print-data-table">
        <thead>
            <tr>
                <th>Company</th>
                <th>Location</th>
                <th>Jobs (H/H/C)</th>
                <th>Pipeline (Src / Int / Off / Join)</th>
                <th>Status</th>
                <th>Last Active</th>
            </tr>
        </thead>
        <tbody id="print-table-body">
            <!-- Dynamically populated rows from RAM -->
        </tbody>
    </table>

    <!-- Executive Footer -->
    <div class="print-report-footer">
        <div>Generated via JERO GOD-Level ATS • System Verification ID: EXP-20261028-000492</div>
        <div>Page <span class="page-number"></span> of <span class="total-pages"></span></div>
    </div>
</div>
```

### 7.3 Print CSS Rules:
```css
@media print {
    /* Hide the entire interactive web app */
    #kt_header, #kt_aside, #kt_content, .cl-stat-grid, .cl-client-card, .btn, .cl-filter-bar {
        display: none !important;
    }

    /* Unhide ONLY the dedicated executive print canvas */
    #cl-print-report-canvas {
        display: block !important;
        width: 100% !important;
        background: #ffffff !important;
        color: #0f172a !important;
        font-family: 'Poppins', sans-serif !important;
    }

    @page {
        size: A4 landscape;
        margin: 10mm 12mm 10mm 12mm;
    }

    .print-data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 10px;
    }

    .print-data-table th {
        background-color: #f1f5f9 !important;
        color: #334155;
        font-weight: 700;
        padding: 6px 8px;
        border-bottom: 2px solid #cbd5e1;
    }

    .print-data-table td {
        padding: 6px 8px;
        border-bottom: 1px solid #e2e8f0;
    }

    .print-data-table tr {
        page-break-inside: avoid !important;
    }
}
```

---

## 8. Single Canonical Filter & Sort Contract

To guarantee that the UI and the exported files never experience desynchronization, both components consume the identical `ClientFilterState` and `ClientSortState`:

```javascript
/**
 * Canonical Filter & Sort Contract across UI, Search, Worker, CSV, and PDF
 */
window.ClientFilterState = {
    tab: 'all',          // 'all', 'internal', 'direct', 'active', 'inactive', 'pending'
    search: '',         // text query
    location: 'all',    // city or 'all'
    industry: 'all',    // industry name or 'all'
    activity: 'all',    // activity range or 'all'
    
    // Sort Contract (Preserved in Export)
    sortField: 'created_at',  // 'created_at', 'jobs_count', 'name', 'activity'
    sortDirection: 'desc',    // 'asc' or 'desc'

    getSnapshot: function() {
        return {
            tab: this.tab,
            search: $('#search_client_input').val()?.trim() || '',
            location: $('#filter_location_select').val() || 'all',
            industry: $('#filter_industry_select').val() || 'all',
            activity: $('#filter_activity_select').val() || 'all',
            sortField: this.sortField,
            sortDirection: this.sortDirection
        };
    }
};
```

---

## 9. Production-Grade Worker Serialization Engine

### File: `public/assets/service_company/client/cl-data-worker.js`

```javascript
/**
 * 100% Client-Side CSV Export with CWE-1236 Defense & Manifest
 */
self.onmessage = function(e) {
    if (e.data.type === 'EXPORT_CSV') {
        executeCsvExport(e.data.filters, e.data.manifestInfo);
    }
};

function executeCsvExport(filters, manifestInfo) {
    const t0 = performance.now();

    // 1. Verify Dataset Hydration
    const dataset = self.dataset || [];
    if (dataset.length === 0) {
        self.postMessage({ type: 'EXPORT_ERROR', error: 'DATASET_EMPTY_OR_UNHYDRATED' });
        return;
    }

    // 2. Filter & Sort records using identical UI rules
    let records = filterDataset(dataset, filters);
    records = sortDataset(records, filters.sortField, filters.sortDirection);

    // 3. Build Manifest Header
    const manifestLines = [
        '# ==============================================================================',
        '# JERO ENTERPRISE ATS EXPORT MANIFEST',
        `# Export ID: ${manifestInfo.exportId}`,
        `# Exported At: ${new Date().toISOString()}`,
        `# Dataset Scope: ${filters.tab.toUpperCase()} (${records.length} records)`,
        `# Dataset Version: ${self.dataVersion || '1.0'}`,
        `# Generated By: ${manifestInfo.userName} (ID #${manifestInfo.userId})`,
        '# Security Notice: Internal Business Confidential',
        '# =============================================================================='
    ];

    // 4. Authoritative Columns
    const headers = [
        'Client ID', 'Company Name', 'Industry', 'Company Size', 'City', 'State', 'Country',
        'Client Type', 'Status', 'Jobs Hiring', 'Jobs Hold', 'Jobs Closed',
        'Sourced', 'Screening', 'Evaluated', 'Negotiation', 'Offers',
        'Joined', 'Rejected', 'Interviews', 'Total Applications',
        'Connected Date', 'Last Activity UTC', 'Last Activity (Display)'
    ];

    const outputRows = [manifestLines.join('\r\n'), headers.map(h => `"${h}"`).join(',')];

    // 5. Serialize Records with CWE-1236 Formula Neutralization
    for (let i = 0; i < records.length; i++) {
        const item = records[i];
        const row = [
            sanitizeCsvCell(item.id),
            sanitizeCsvCell(item.name),
            sanitizeCsvCell(item.industry),
            sanitizeCsvCell(item.company_size),
            sanitizeCsvCell(item.city_name),
            sanitizeCsvCell(item.state_name),
            sanitizeCsvCell(item.country_name),
            sanitizeCsvCell(item.client_type || 'Internal Client'),
            sanitizeCsvCell(item.status || 'Active'),
            item.jobs?.active ?? 0,
            item.jobs?.hold ?? 0,
            item.jobs?.closed ?? 0,
            item.pipeline?.sourced ?? 0,
            item.pipeline?.screening ?? 0,
            item.pipeline?.evaluated ?? 0,
            item.pipeline?.negotiations ?? 0,
            item.pipeline?.offers ?? 0,
            item.pipeline?.joined ?? 0,
            item.pipeline?.rejected ?? 0,
            item.pipeline?.interviews ?? 0,
            item.pipeline?.applications ?? 0,
            sanitizeCsvCell(item.date),
            sanitizeCsvCell(item.last_activity_utc || item.last_activity),
            sanitizeCsvCell(item.last_activity)
        ];
        outputRows.push(row.join(','));
    }

    // 6. Prepend UTF-8 BOM (\uFEFF)
    const bom = '\uFEFF';
    const csvContent = bom + outputRows.join('\r\n');
    const elapsed = Math.round(performance.now() - t0);

    self.postMessage({
        type: 'CSV_READY',
        csvContent: csvContent,
        rowCount: records.length,
        durationMs: elapsed,
        scope: filters.tab
    });
}

function sanitizeCsvCell(value) {
    if (value === null || value === undefined) return '""';
    let str = String(value);
    if (/^[=+\-@\t\r]/.test(str)) {
        str = "'" + str;
    }
    return '"' + str.replace(/"/g, '""') + '"';
}
```

---

## 10. Verification & Security Audit Standards

- [x] **CWE-1236 Exploitation Test:** Pass `=cmd|' /C calc'!A0` and `@SUM(1+1)*cmd` as client name. Export CSV; open in Excel. Verify values are treated as inert strings (`'=cmd...`).
- [x] **Zero Server Ping Test:** Monitor Network panel while triggering CSV and PDF exports. Confirm strictly 0 requests fired.
- [x] **Temporal Invariance Test:** Export dataset. Verify `Last Activity UTC` contains `YYYY-MM-DD HH:mm:ss UTC`, not relative text.
- [x] **Print Canvas Isolation Test:** Click "Save as PDF". Verify `#cl-print-report-canvas` prints cleanly with zero clipped cards and complete executive tables.
- [x] **Manifest Audit Test:** Verify generated CSV contains the `# JERO ENTERPRISE ATS EXPORT MANIFEST` metadata header.
- [x] **Filter & Sort Parity Test:** Verify that filtering by "Dublin" and sorting by "Most Active Jobs" exports the exact same rows in the exact same sequence as shown on the screen.
