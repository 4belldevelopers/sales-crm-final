# JERO OpenDoc WASM Engine — Phase 6 Detailed Implementation Plan
## In-Browser Dynamic Offer Letter & Consulting Contract Generator

> **Document Status:** Complete Architecture & Implementation Specification  
> **Target Version:** Laravel 11 / OpenDoc WASM 2.0 / Client-Side Engine  
> **Target Audience:** Core Engineering, ATS Architects, HR Tech Solutions, Security & Compliance  
> **Master Reference:** [docs/OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_MASTER_IMPLEMENTATION_PLAN.md)  
> **Phase 3 Reference:** [docs/OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_3_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Phase 4 Reference:** [docs/OPENDOC_WASM_PHASE_4_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_4_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Phase 5 Reference:** [docs/OPENDOC_WASM_PHASE_5_DETAILED_IMPLEMENTATION_PLAN.md](OPENDOC_WASM_PHASE_5_DETAILED_IMPLEMENTATION_PLAN.md)  
> **Rule Adherence:** Multi-Tenancy Scoped (`company_id`), Zero LibreOffice/Puppeteer Server Load, Metronic UI Standard, Poppins Font $\le$ 14px

---

## 1. Executive Summary & Core Objectives

Phase 6 implements a **100% client-side, in-browser Dynamic Offer Letter & Consulting Contract Generator**. In modern recruitment workflows, companies spend thousands of dollars annually on third-party SaaS contracts/e-sign platforms (e.g. DocuSign, PandaDoc, Adobe Sign) or suffer server crashes caused by legacy headless browsers (`wkhtmltopdf`, Puppeteer, LibreOffice) attempting to render dynamic PDFs under heavy concurrency.

Phase 6 completely eliminates both pain points:
1. **Zero Server CPU & Zero License Costs:** All template ingestion, merge tag parsing, typography-preserving XML substitutions, and high-fidelity PDF rendering occur in browser memory via WebAssembly with **0% server load**.
2. **Native Microsoft Word `.docx` Template Ingestion:** HR managers and recruiters upload their own standard corporate offer letters in `.docx` format containing simple merge tags (`{{candidate_name}}`, `{{annual_ctc}}`, `{{joining_date}}`).
3. **Typography & Style-Preserving XML Substitution:** In-memory XML run tree replacement guarantees that fonts, brand colors, tables, headers, footers, and alignments remain pixel-perfect.
4. **Interactive WYSIWYG Modal Preview:** Instant live canvas preview rendered in $< 300\text{ms}$ with pinch-to-zoom, page navigation, and live field editing.
5. **Dual Export Pipeline:** 1-click export to finalized high-res vector PDF (with optional digital signature block) or clean, downloadable `.docx` file.

---

## 2. Business Workflow & Recruiter Experience

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                   OFFER LETTER WORKFLOW (RECRUITER PERSPECTIVE)             │
├─────────────────────────────────────────────────────────────────────────────┤
│ 1. Candidate reaches "Offered" Stage in AppTracker / CV Tracker             │
│       │                                                                     │
│       ▼                                                                     │
│ 2. Recruiter clicks "Generate Offer Letter" button in Drawer                │
│       │                                                                     │
│       ▼                                                                     │
│ 3. Metronic Modal opens -> Selects Template:                                │
│    [ Standard Software Engineer Offer (DOCX) ▼ ]                            │
│       │                                                                     │
│       ▼                                                                     │
│ 4. Form Pre-Populates with Candidate & Job Data:                            │
│    - Candidate Name: "Alex Morgan" (from Candidate Record)                  │
│    - Designation: "Senior Backend Engineer" (from Job Opening)             │
│    - Annual CTC: "$145,000" (Recruiter Input / Offer Stage DB)              │
│    - Joining Date: "October 15, 2026" (Date Picker)                         │
│    - Reporting Manager: "Sarah Jenkins, VP Engineering"                    │
│       │                                                                     │
│       ▼                                                                     │
│ 5. Real-Time Interactive Canvas Preview updates as recruiter types (< 250ms)│
│       │                                                                     │
│       ▼                                                                     │
│ 6. Recruiter clicks:                                                        │
│    ├── Option A: "Download Signed PDF" -> Instant client-side download     │
│    ├── Option B: "Download Editable DOCX" -> Populated Word document        │
│    └── Option C: "Send Offer to Candidate" -> Sends email notification      │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Technical Architecture & In-Memory XML Substitution

### 3.1 The Split-Run Problem in OpenXML (`.docx`)
A major challenge when manipulating Microsoft Word documents is that Word frequently splits contiguous text into multiple run elements (`<w:r>`). For example, `{{candidate_name}}` is often stored in `document.xml` as:
```xml
<w:r><w:t>{{cand</w:t></w:r>
<w:r><w:t>idate_</w:t></w:r>
<w:r><w:t>name}}</w:t></w:r>
```
Naive string search-and-replace fails completely on split runs.

### 3.2 OpenDoc WASM Run-Tree Normalizer & Replacer
Phase 6 implements a robust client-side token aggregator:
1. **AST Run Aggregation:** Traverses all child `<w:r>` elements inside each paragraph (`<w:p>`) and constructs a unified virtual character index.
2. **Regex Merge-Tag Locator:** Identifies tag boundaries: `/\{\{([a-zA-Z0-9_\.]+)\}\}/g`.
3. **Style-Preserving Splice:**
   - Injects the replacement value (e.g. `"Alex Morgan"`) into the primary run element, inheriting the exact font family, size (`<w:sz>`), bolding (`<w:b>`), italicization (`<w:i>`), and color (`<w:color>`).
   - Safely removes remnant runs from the tag without destroying surrounding punctuation or paragraph formatting.
4. **Repack & Render:** Re-serializes the XML tree into the document ZIP container in WebAssembly memory and streams it directly to the canvas renderer.

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                       WASM XML SUBSTITUTION PIPELINE                        │
├─────────────────────────────────────────────────────────────────────────────┤
│ Master Template ArrayBuffer (Cached)                                        │
│       │                                                                     │
│       ▼                                                                     │
│ JSZip in-memory unzip -> word/document.xml, word/header1.xml, footer1.xml   │
│       │                                                                     │
│       ▼                                                                     │
│ AgyOfferEngine.normalizeParagraphRuns(xmlDoc)                               │
│       │                                                                     │
│       ▼                                                                     │
│ AgyOfferEngine.replaceMergeTags(xmlDoc, {                                   │
│     candidate_name: 'Alex Morgan',                                          │
│     designation: 'Senior Backend Engineer',                                 │
│     annual_ctc: '$145,000',                                                 │
│     joining_date: 'October 15, 2026',                                       │
│     signing_bonus: '$10,000',                                               │
│     work_location: 'San Francisco, CA (Hybrid)'                             │
│ })                                                                          │
│       │                                                                     │
│       ▼                                                                     │
│ Re-pack Word Container -> Modified ArrayBuffer (Zero Server Round-Trip)     │
│       │                                                                     │
│       ▼                                                                     │
│ OpenDoc WASM Engine instantiates Document -> 288 DPI Canvas Render          │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. Supported Merge Tags Standard

Templates support standardized dot-notation and underscore tags across 4 core domains:

| Category | Supported Tag Syntax | Source in JERO Database |
| :--- | :--- | :--- |
| **Candidate Details** | `{{candidate_name}}`<br>`{{candidate_first_name}}`<br>`{{candidate_email}}`<br>`{{candidate_phone}}`<br>`{{candidate_address}}` | `candidates` / `job_applications` |
| **Job & Position** | `{{job_title}}`<br>`{{job_code}}`<br>`{{department}}`<br>`{{employment_type}}`<br>`{{work_location}}`<br>`{{reporting_manager}}` | `jobs` / `company_departments` |
| **Compensation** | `{{annual_ctc}}`<br>`{{monthly_gross}}`<br>`{{currency}}`<br>`{{signing_bonus}}`<br>`{{stock_options}}`<br>`{{probation_months}}` | `job_applications` (Offer Details) |
| **Dates & Validity** | `{{offer_date}}`<br>`{{joining_date}}`<br>`{{offer_expiry_date}}`<br>`{{current_year}}` | Recruiter Date Picker / Dynamic |
| **Company & Entity** | `{{company_name}}`<br>`{{company_address}}`<br>`{{company_signatory_name}}`<br>`{{company_signatory_title}}` | `companies` (Tenant Profile) |

*Unmapped Tags Guard:* If a template contains a tag not supplied by the form, the engine leaves the text blank or displays a discreet highlighted token (`[Pending: signing_bonus]`) so the recruiter never accidentally sends raw curly braces to a candidate.

---

## 5. Digital Signature & Verification Block

Phase 6 provides an integrated vector signature pad directly inside the preview modal:
1. **Recruiter Signatory Block:**
   - Pre-loads company signatory signature from Company Settings or allows recruiter to draw/type their signature.
   - Embeds signature PNG into the document footer coordinates as a high-resolution vector image.
2. **Candidate Acceptance Block:**
   - Inserts standardized signature line:  
     *"Accepted & Agreed By: ___________________ Date: ____________"*
   - When sent via candidate portal, candidate can sign directly on their smartphone or laptop with touch/stylus.
3. **Tamper-Evident SHA-256 Audit Trail:**
   - When the offer is finalized, the client generates a SHA-256 hash of the rendered PDF array buffer.
   - Hashes are logged to the database alongside timestamp, IP address, and authenticated user ID, providing legal non-repudiation.

---

## 6. Multi-Tenancy Isolation Architecture (`company_id`)

Security and strict data separation are paramount for corporate contracts:

```php
/**
 * Strict Tenancy Scoping for Offer Templates & Generated Offers
 */
namespace App\Http\Controllers\ServiceCompany;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OfferLetterTemplateController extends Controller
{
    /**
     * Intent: Fetch only templates owned by the authenticated recruiter's company.
     * Tenancy Note: company_id strictly enforced.
     */
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $templates = DB::table('offer_letter_templates')
            ->where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->orderBy('is_default', 'desc')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'file_name', 'file_url', 'is_default', 'created_at']);

        return response()->json(['templates' => $templates]);
    }

    /**
     * Intent: Verify template download ownership before signing URL.
     * Tenancy Note: Prevents cross-tenant template exfiltration.
     */
    public function downloadTemplate(Request $request, $id)
    {
        $companyId = auth()->user()->company_id;

        $template = DB::table('offer_letter_templates')
            ->where('id', $id)
            ->where('company_id', $companyId)
            ->whereNull('deleted_at')
            ->first();

        if (!$template) {
            abort(403, 'Unauthorized access to company template.');
        }

        // Return signed temporary Cloudflare R2 URL
        return response()->json(['url' => $template->file_url]);
    }
}
```

---

## 7. UI/UX Design Specifications (Metronic Standard)

- **Modal Container:** Fullscreen Metronic modal (`modal-fullscreen` or `modal-xl`) with two-column responsive split:
  - **Left Column (`col-lg-4`):** Merge tag fields, dynamic salary breakdown calculator, signature pad, and export actions.
  - **Right Column (`col-lg-8`):** Live WYSIWYG OpenDoc WASM canvas viewer with page pagination controls (`< Prev Page`, `Page 1 of 3`, `Next Page >`) and 100%/150%/Fit-to-Width zoom controls.
- **Typography:** Metronic standard Poppins font family, maximum body font size 14px.
- **Iconography:** Clean SVG vector icons for download (`svg-icon-download`), edit (`svg-icon-pencil`), and send (`svg-icon-paper-plane`). Zero 3D elements.
- **Live Debouncing:** As recruiter adjusts salary or joining date, the preview updates after a 200ms debounce without UI stutter.

---

## 8. Performance Budget & Memory Management

| Operation | Target Budget | Technical Strategy |
| :--- | :--- | :--- |
| **Template DOCX Unzip & Parse** | $< 150\text{ms}$ | Uses fast client-side JSZip in-memory streaming |
| **Run-Tree Normalization & Tag Replace** | $< 80\text{ms}$ | Regex scan over parsed DOM tree in browser RAM |
| **Canvas Re-Render on Field Change** | $< 250\text{ms}$ | Re-renders only the currently visible page canvas |
| **Multi-Page Vector PDF Export** | $< 800\text{ms}$ | Direct canvas vector stream conversion via WASM |
| **WASM Memory Disposal** | $< 50\text{ms}$ | Calls `.free()` on active document instance on modal close |

---

## 9. Error Handling & Edge Cases

| Edge Case | Solution / Safeguard |
| :--- | :--- |
| **Corrupted `.docx` Template** | Client-side XML validator inspects archive integrity before saving; displays friendly Metronic alert if corrupted. |
| **Split Merge Tags Across XML Runs** | Paragraph run normalizer stitches split runs (`{{cand` + `idate}}`) before regex execution. |
| **Missing Candidate Information** | Missing fields are highlighted in yellow in the left form panel; default placeholder text is suggested. |
| **Table Formatting in Compensation** | Preserves `<w:tbl>` cell widths, borders, and shading perfectly without column wrapping errors. |

---

## 10. Verification & Acceptance Checklist

- [ ] **Zero Server CPU:** Confirm that generating a 3-page offer letter and exporting to PDF causes 0% server CPU usage.
- [ ] **Typography Preservation:** Verify that corporate fonts, colors, headers, footers, and table styles in the master `.docx` match the exported PDF 1:1.
- [ ] **Split-Run Tag Replacement:** Verify that merge tags split across multiple `<w:r>` runs by Microsoft Word are cleanly recognized and replaced.
- [ ] **Dynamic Live Preview:** Confirm that updating the "Annual CTC" input field updates the preview canvas in $< 250\text{ms}$.
- [ ] **Signature Injection:** Verify that drawn signatures are cleanly embedded as high-res vector graphics in the document signature block.
- [ ] **Multi-Tenancy Scoping:** Verify that Company A cannot view or download offer templates or generated contracts created by Company B.
- [ ] **Dual Export Quality:** Confirm that both exported `.pdf` and populated `.docx` open cleanly in Adobe Acrobat and Microsoft Word without formatting corruption.
