# V4 Add Talent Flow: Production Engineering Audit, Architecture Specification & Implementation Blueprint

> **Document Version:** 4.0.0 (Production Engineering Specification: Client-First Architecture, Outbox Decoupling & 10K Concurrency Isolation)  
> **Status:** Authoritative Production Engineering Contract & Implementation Master Specification  
> **Target Module:** Service Company Add Talent, Submit Talent & Bench Listing Pipeline (`service_company/bench-candidates`)  
> **Target Environment:** `testing.jero.works`  
> **Primary Source Files:**  
> - `resources/views/service_company/bench-candidates/create.blade.php` (Add/Edit Talent View)  
> - `resources/views/service_company/bench-candidates/index.blade.php` (Bench Talent Listing & Hero Dashboard)  
> - `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php` (`create()`, `store()`, `edit()`, `data_ajax()`, `data_v2()`)  
> - `app/Services/BenchTalentHeroAnalytics.php` (Hero Pipeline Metrics Service)  
> - `database/schema/mysql-schema.sql` (Authoritative Database Schema)  
> **Client-Side Parser Production Endpoint:**  
> - `https://parser.jero.works/` (Production Cloudflare Worker)  
> - Module Entrypoint: `https://parser.jero.works/jero-resume-parser-2tURGwDx.js` (WebAssembly, PDF.js, Mammoth, Compromise NLP)  
> **Dedicated Companion Subsystem (Strictly Isolated & Off-Limits to Regressions):**  
> - `docs/V4_Jeroworks_Upgrade/v4_add_candidate_audit_and_remediation_plan.md`  
>   *(Governs the Add Candidate ATS pipeline at `/service_company/job-applications/create` â€” strictly preserved & independent).*  
> **Explicit Scope Boundary:**  
> - Per architectural directive, **`product_company` and `staffing_company` are strictly OUT OF SCOPE and must be ignored**. All changes are quarantined to `service_company`. Zero shared stateful scripts with Add Candidate, Drawer, or Marketplace modules.

---

## 1. Executive Summary & Core Architectural Principles

### 1.1 The Golden Architectural Axiom
> **"MySQL is the authoritative transactional system of record. Elasticsearch is the asynchronous read/search projection. Redis provides ephemeral coordination and materialized cache state. The browser performs non-authoritative computation and rendering. The transactional outbox guarantees eventual propagation from MySQL to Elasticsearch."**

This single axiom governs all engineering decisions across the Add Talent, Submit Talent, and Talent Listing flows. Elasticsearch is never treated as the authoritative store for candidate persistence.

### 1.2 Target Performance & Concurrency Profile
- **Target Concurrency:** **10,000 concurrent recruiters per page** without server collapse, DB connection starvation, or UI freezes.
- **Server Read Strain:** **0% MySQL reads** on web/API request paths, autocompletes, and listing page loads.
- **Server CPU Strain:** **0% server CPU for parsing and calculations** achieved via Client-First / Browser-Execution (User RAM + WASM).
- **HTTP Transaction Hold Duration:** **< 15ms MySQL transaction duration** (down from 800msâ€“2500ms).
- **Submit Response Latency SLA:** **< 150ms HTTP 200/201 response** returning authoritative listing redirect URL.
- **Listing Payload Reduction:** **90% payload reduction** (from ~85KB server-rendered HTML to ~8KB normalized JSON via client DOM assembly).

### 1.3 Subsystem Isolation & Anti-Corruption Boundary (Rule 17)
The Talent / Bench Candidates module represents a dedicated business domain: internal service company employee bench management and B2B marketplace sharing.
- **Strict Quarantine:** All Blade views, CSS, JavaScript controllers, and route handlers must remain strictly quarantined to `resources/views/service_company/bench-candidates/` and `ServiceCompanyBenchCandidatesController.php`.
- **Zero Shared Mutable State:** No stateful JavaScript, modal instances, or dropzone controllers may be shared with `job-applications` (Add Candidate) or Drawer components.
- **Zero Business Logic Mutations (Rule 7):** All existing business rulesâ€”coin deductions (`eventId = 5`), stage definitions (1=Available, 2=Bench, 4=Placed, 5=Terminated), outsourcing types (Full Time, Contract, Part Time, Freelance), skills experience ratings (1â€“5), notice period modes, and commercial models (Fixed/Percentage margin, Free/Paid service)â€”must remain **100% untouched**.

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                              JERO V4 ADD TALENT ARCHITECTURE MATRIX                                    â”‚
â”‚                                                                                                        â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚ PIPELINE 1:             â”‚  â”‚ PIPELINE 2:             â”‚  â”‚ PIPELINE 3:                            â”‚  â”‚
â”‚  â”‚ CLIENT RESUME PARSER    â”‚  â”‚ STORAGE & ASSET ENGINE  â”‚  â”‚ IN-BROWSER COMMERCIALS & AUTOCOMPLETE  â”‚  â”‚
â”‚  â”‚ (100% Browser RAM/WASM) â”‚  â”‚ (Direct Browser-to-R2)  â”‚  â”‚ (User RAM + Local Trie Cache)          â”‚  â”‚
â”‚  â”‚                         â”‚  â”‚                         â”‚  â”‚                                        â”‚  â”‚
â”‚  â”‚ â€¢ Client WASM Extractionâ”‚  â”‚ â€¢ Direct Presigned PUT  â”‚  â”‚ â€¢ In-Browser Commercials Calculator    â”‚  â”‚
â”‚  â”‚ â€¢ PDF.js + Mammoth NLP  â”‚  â”‚ â€¢ Background File R2    â”‚  â”‚   (Pay/Bill Rate, Margins, Multi-Cur)  â”‚  â”‚
â”‚  â”‚ â€¢ Auto-populates inputs â”‚  â”‚ â€¢ Asynchronous Relocate â”‚  â”‚ â€¢ Local Sharded Skills Trie (0ms HTTP) â”‚  â”‚
â”‚  â”‚ â€¢ 0 Server CPU Strain   â”‚  â”‚ â€¢ No R2 checks in DB Tx â”‚  â”‚ â€¢ Local Sharded Geo Trie (0ms HTTP)    â”‚  â”‚
â”‚  â”‚ â€¢ Single 'Remove' UI    â”‚  â”‚ â€¢ Clean Orphan Purge    â”‚  â”‚ â€¢ Tab Session Scoping (__tabInstanceId)â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚               â”‚ (Autofills empty inputs)   â”‚ (Verified asset payload)          â”‚                       â”‚
â”‚               â–¼                            â–¼                                   â–¼                       â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚ PIPELINE 4: SUB-15ms TRANSACTIONAL PERSISTENCE + TRANSACTIONAL OUTBOX PATTERN                    â”‚  â”‚
â”‚  â”‚                                                                                                  â”‚  â”‚
â”‚  â”‚ â€¢ 3-Layer Idempotency: Client UUID â†’ Redis SETNX (120s) â†’ MySQL Unique Constraint                â”‚  â”‚
â”‚  â”‚ â€¢ Pure MySQL Write: INSERT `job_applications` + Bulk Relational Writes (skills, assign, details) â”‚  â”‚
â”‚  â”‚ â€¢ Transactional Outbox: INSERT `candidate_outbox_events` (Payload for Bench Talent)              â”‚  â”‚
â”‚  â”‚ â€¢ ZERO External Network I/O Inside `DB::beginTransaction()` (Zero ES HTTP, Zero R2 HEAD checks)   â”‚  â”‚
â”‚  â”‚ â€¢ IMMEDIATE JSON Response + Dual-Trigger Redirect (SweetAlert Click + 2000ms Auto-Timer)         â”‚  â”‚
â”‚  â”‚ â€¢ Outbox Horizon Worker: `FOR UPDATE SKIP LOCKED` -> Asynchronous Elasticsearch Ingestion        â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â”‚                                            â”‚                                                           â”‚
â”‚                                            â–¼                                                           â”‚
â”‚  â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”  â”‚
â”‚  â”‚ PIPELINE 5: 10K-READY BENCH LISTING DASHBOARD (0% MYSQL READ STRAIN)                             â”‚  â”‚
â”‚  â”‚                                                                                                  â”‚  â”‚
â”‚  â”‚ â€¢ 100% Elasticsearch Reads: Universal query serving across `ELASTIC_CANDIDATES_BENCH`             â”‚  â”‚
â”‚  â”‚ â€¢ Client-Side DOM Assembly: High-performance `data_v2` JSON endpoint (8KB vs 85KB HTML)         â”‚  â”‚
â”‚  â”‚ â€¢ In-Memory Filter & Stage Switching: Hero analytics & pipeline stages rendered in browser RAM   â”‚  â”‚
â”‚  â”‚ â€¢ Multi-Tenant Atomic Cache Invalidation: Post-commit Redis version bump clears listing caches    â”‚  â”‚
â”‚  â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜  â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

### 1.4 The Tri-Modular Isolation Architecture: 3 Autonomous & Decoupled Subsystems

To prevent regressions, eliminate cross-module dependencies, and guarantee 100% fault isolation, the client-side architecture is strictly partitioned into **three decoupled, autonomous modules** communicating exclusively via an asynchronous, uni-directional Event Bus:

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                           THE TRI-MODULAR DECOUPLED ARCHITECTURE                                 â”‚
â”‚                                                                                                  â”‚
â”‚   â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”            â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”       â”‚
â”‚   â”‚ MODULE 1: CLIENT PARSER ENGINE â”‚            â”‚ MODULE 2: R2 DIRECT STORAGE ENGINE     â”‚       â”‚
â”‚   â”‚ (`jero-talent-parser.js`)      â”‚            â”‚ (`talent-resume-presign.js`)           â”‚       â”‚
â”‚   â”‚                                â”‚            â”‚                                        â”‚       â”‚
â”‚   â”‚ â€¢ 100% In-Browser RAM / WASM   â”‚            â”‚ â€¢ Browser-to-Cloudflare R2 Direct PUT  â”‚       â”‚
â”‚   â”‚ â€¢ PDF.js + Mammoth NLP Engine  â”‚            â”‚ â€¢ Ephemeral Upload Session Lifecycle   â”‚       â”‚
â”‚   â”‚ â€¢ Extracts Text, Skills, Bio   â”‚            â”‚ â€¢ Zero PHP / Web Server Bandwidth      â”‚       â”‚
â”‚   â”‚ â€¢ Zero Knowledge of Storage    â”‚            â”‚ â€¢ Zero Knowledge of Text/Form Parsing  â”‚       â”‚
â”‚   â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜            â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¬â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜       â”‚
â”‚                   â”‚                                                 â”‚                            â”‚
â”‚                   â”‚ Event: `talent:resume-parsed`                   â”‚ Event: `talent:resume-uploaded`
â”‚                   â”‚ (Plain Normalized JSON)                         â”‚ (Immutable Session UUID)   â”‚
â”‚                   â–¼                                                 â–¼                            â”‚
â”‚   â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”   â”‚
â”‚   â”‚ MODULE 3: FORM CONTROLLER, COMMERCIALS ENGINE & PERSISTENCE BRIDGE                       â”‚   â”‚
â”‚   â”‚ (`talent-form-controller.js`)                                                            â”‚   â”‚
â”‚   â”‚                                                                                          â”‚   â”‚
â”‚   â”‚ â€¢ Handles Add Talent (Fresh Insert) & Edit Talent (Hydrate from `edit_token`)            â”‚   â”‚
â”‚   â”‚ â€¢ In-Browser Commercials Calculator (Pay/Bill Rate, Margins, Currency Math @ 60 FPS)     â”‚   â”‚
â”‚   â”‚ â€¢ Browser-RAM Trie Autocomplete (Skills & Locations in < 2ms)                            â”‚   â”‚
â”‚   â”‚ â€¢ Metronic Form Validation & Tab Isolation (`window.__tabInstanceId`)                   â”‚   â”‚
â”‚   â”‚ â€¢ 3-Layer Idempotency Guard (Client UUIDv4 + Redis SETNX Lock + MySQL Constraint)        â”‚   â”‚
â”‚   â”‚ â€¢ Pure CQRS Submit Dispatch (Sub-15ms Transaction & Bulletproof 2000ms Dual Redirect)    â”‚   â”‚
â”‚   â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜   â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

#### Detailed Subsystem Boundaries & Fault Isolation Guarantees:

1. **Module 1: Client-Side Parser Technology Engine (`jero-talent-parser.js`)**
   - **Role:** Pure in-memory WebAssembly / NLP document extraction via `https://parser.jero.works/`.
   - **Independence & Isolation:**
     - Directly imports the production Cloudflare ES module: `import('https://parser.jero.works/jero-resume-parser-2tURGwDx.js')`.
     - Runs strictly inside **User Browser RAM & Browser CPU** via WebAssembly (PDF.js), Mammoth (DOCX), and Compromise NLP.
     - **0% Server Strain:** Generates **0 PHP requests, 0 MySQL queries, 0 Elasticsearch calls, and 0 backend server CPU**.
     - Has **zero knowledge of R2, S3, backend storage paths, or database tables**.
     - Takes raw file bytes from the dropzone, executes local in-memory extraction in < 35ms, and extracts text, candidate name, email, phone, experience, skills, and career summary.
     - Emits `CustomEvent('talent:resume-parsed', { detail: parsedJson })`.
   - **Fault Blast Radius:** If the resume file is password-protected, corrupt, or parsing fails, **Module 2 and Module 3 are completely unaffected**. The recruiter proceeds with 100% manual form entry without any browser lockups or console errors.

2. **Module 2: Direct-to-R2 Edge Storage Transport Engine (`talent-resume-presign.js`)**
   - **Role:** Ephemeral binary byte transport directly to Cloudflare R2 object storage.
   - **Independence & Isolation:**
     - Obtains a short-lived presigned PUT URL and streams the raw file directly from browser to Cloudflare R2 bucket.
     - **0% PHP-FPM or Web Server load:** File bytes never touch the application server memory or disk.
     - Has **zero knowledge of parsed text, candidate fields, or recruitment business logic**.
     - Emits `CustomEvent('talent:resume-uploaded', { detail: { session_uuid, file_url, file_name } })`.
   - **Strict Security & Tenant Isolation Contract:**
     - **Tenant-Scoped Object Paths:** Presigned URLs restrict upload strictly to:
       `r2/companies/{company_id}/resumes/{resume_uuid}.{ext}`
     - **Strict Expiration:** Presigned PUT URLs expire in strictly **15 minutes**.
     - **Payload Constraints:** Strict maximum file size ceiling of **10 MB** enforced via presigned headers.
     - **Strict MIME Whitelist:** `application/pdf`, `application/vnd.openxmlformats-officedocument.wordprocessingml.document`, `application/msword`.
     - **Content-Length & Integrity:** Browser sets exact `Content-Length` and optional `Content-MD5` header.
   - **Formal Resume Upload State Machine:**
     ```
     [FILE SELECTED] â”€â”€â–º UPLOAD_INITIALIZED (Presign session generated)
            â”‚
            â”œâ”€â”€â–º UPLOADING (Direct PUT stream in progress)
            â”‚        â”‚
            â”‚        â”œâ”€â”€â–º UPLOADED (Direct R2 PUT completed)
            â”‚        â”‚        â”‚
            â”‚        â”‚        â”œâ”€â”€â–º VERIFIED (Edge HEAD check confirms bytes)
            â”‚        â”‚        â”‚        â”‚
            â”‚        â”‚        â”‚        â”œâ”€â”€â–º SUBMITTED (Candidate form submitted successfully)
            â”‚        â”‚        â”‚        â”‚
            â”‚        â”‚        â”‚        â””â”€â”€â–º ORPHAN_PENDING (Form abandoned; auto-reaped by cleanup worker)
            â”‚        â”‚        â”‚
            â”‚        â”‚        â””â”€â”€â–º FAILED_VERIFICATION (Retry presign)
            â”‚        â”‚
            â”‚        â””â”€â”€â–º UPLOAD_FAILED (Network error; retryable)
            â”‚
            â””â”€â”€â–º CANCELLED / REMOVED (File removed by recruiter before submit)
     ```
   - **Fault Blast Radius:** If Cloudflare R2 has a momentary network drop or upload is cancelled, **Module 1 (extracted text) remains intact in form fields**, and **Module 3 can still submit the profile** with manual entry or retry the upload independently.

3. **Module 3: In-Browser Form Controller, Commercials Calculator & Persistence Bridge (`talent-form-controller.js`)**
   - **Role:** Form state management, validation, dynamic math, autocomplete, and submission bridge for both **Add Talent** and **Edit Talent**.
   - **Independence & Isolation:**
     - Consumes events from Module 1 (to autofill empty fields without overwriting manual edits) and Module 2 (to bind `resume_session_uuid`).
     - Houses the **In-Browser Commercials Engine (`TalentCommercialCalculator`)** for pay rate, bill rate, hike %, margin %, and currency conversion (USD/EUR/GBP/AED to INR) running at 60 FPS in browser RAM.
     - **Hierarchical Sharded Location Engine (IndexedDB + Web Worker):**
       - **Why not sessionStorage?** The global location dataset comprises ~1.5 million records (~45MB JSON). Storing this in `sessionStorage` causes browser quota exhaustion (5MB ceiling) and thread freezes.
       - **Architecture:** Static compressed master dictionary $\to$ **IndexedDB (`ats_geo_cache`)** $\to$ Web Worker $\to$ in-memory Country $\to$ State $\to$ City shards loaded progressively. 
       - Autocomplete searches execute against active shards in Web Worker memory in < 2ms without ever touching PHP or MySQL.
     - **Granular Idempotency Semantics (Create vs. Update):**
       - **CREATE Operations:** `idempotency_key = company_id + ":" + client_submission_uuid`
       - **UPDATE Operations:** `idempotency_key = company_id + ":" + candidate_id + ":" + client_operation_uuid`
       - **Authoritative Guarantee vs. Concurrency Guard:**
         - **Redis `SETNX` (120s TTL):** A fast ephemeral concurrency guard to short-circuit in-flight duplicate button clicks.
         - **MySQL Unique Constraint (`uq_company_submission_uuid`):** The authoritative, persistent idempotency guarantee ensuring zero duplicate rows at the storage engine level.
     - Handles **Create Mode** (`store`) and **Edit Mode** (`update`) with clean tab isolation (`window.__tabInstanceId`).
     - Dispatches the sub-15ms transactional submit request to the backend.
   - **Fault Blast Radius:** The form controller does not care whether fields were filled by the parser or typed manually by the recruiter. It guarantees a sub-150ms HTTP response and executes the bulletproof dual-trigger redirect to `bench-candidates.index`.

---

## 2. Forensic Audit of Current Add Talent Flow (V1/V2 Gaps & Root Causes)

A forensic line-by-line inspection of [ServiceCompanyBenchCandidatesController.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php) and [create.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/bench-candidates/create.blade.php) reveals 8 critical architectural defects that prevent the module from sustaining 10,000 concurrent recruiters.

### Defect 1: Synchronous Elasticsearch Network I/O Inside Active MySQL Transactions
- **Location:** `ServiceCompanyBenchCandidatesController.php` lines 1682â€“1740 & 1834â€“1870.
- **The Code Flaw:**
  ```php
  DB::beginTransaction();
  // ...
  $this->elasticService->getClient()->deleteByQuery([
      'index' => ELASTIC_BENCH_ASSIGN,
      'body' => ['query' => ['bool' => ['must' => [['term' => ['bench_applications_id' => (int) $benchApplication->id]]]]]]
  ]);
  foreach ($recruiterArrId as $recArr) {
      $response = $this->elasticService->getClient()->get(['index' => ELASTIC_USERS_LIST, 'id' => (int) $recArr]);
      $insertGetId = DB::table('bench_assign')->insertGetId(...);
      $this->elasticService->getClient()->update(['index' => ELASTIC_BENCH_ASSIGN, 'id' => $insertGetId, ...]);
  }
  $this->elasticService->getClient()->deleteByQuery(['index' => ELASTIC_BENCH_ADDITIONAL_DETAIL, ...]);
  foreach ($customDetailsQuestion as $question) {
      $insertGetIdAdd = DB::table('bench_additional_detail')->insertGetId(...);
      $this->elasticService->getClient()->update(['index' => ELASTIC_BENCH_ADDITIONAL_DETAIL, ...]);
  }
  ```
- **Consequence:** Under 10,000 concurrency, performing multiple synchronous HTTP roundtrips to the Elasticsearch cluster while holding an active MySQL transaction inflates transaction hold duration from <15ms to 1200msâ€“3500ms. This exhausts the database connection pool (`max_connections`), triggers MySQL row lock deadlocks, and cascades into HTTP 504 Gateway Timeouts.

### Defect 2: Synchronous Cloudflare R2 Network Calls Inside Transaction
- **Location:** `ServiceCompanyBenchCandidatesController.php` lines 1584â€“1600.
- **The Code Flaw:**
  ```php
  $r2Exists = Storage::disk('r2')->exists('resumes/'.$resumeSession->uuid);
  $finalResumeUrl = app(\App\Services\ResumeUploadSession::class)->moveToCompanyPath($resumeSession);
  ```
- **Consequence:** Invoking HTTPS S3/R2 HEAD requests and object file copy operations over WAN inside `DB::beginTransaction()` ties database thread lifetime to external cloud storage latency.

### Defect 3: N+1 Location Parsing SQL Queries
- **Location:** `ServiceCompanyBenchCandidatesController.php` lines 1455â€“1478.
- **The Code Flaw:** Iterates over `kt_tagify_work_location` and executes a 3-table SQL join query (`city_list` $\bowtie$ `state_list` $\bowtie$ `countries`) inside a `foreach` loop for every selected location tag.
- **Consequence:** A burst of 10,000 submissions creates 30,000+ unindexed SQL reads against the relational master database.

### Defect 4: Server Memory Bloat on View Initialization
- **Location:** `ServiceCompanyBenchCandidatesController.php` lines 1280â€“1289.
- **The Code Flaw:** When `feature.talents_v2_enabled` is default (`false`), `create()` executes `GeneralMethod::allCities()` and `GeneralMethod::allCitiesSelected([])`, loading tens of thousands of city rows into PHP-FPM memory per request.
- **Consequence:** Memory footprint spikes to >64MB per worker, starving PHP-FPM and crashing web containers under high concurrency.

### Defect 5: Fragmented In-Browser Parsing vs. Unified Client Engine
- **Location:** `resources/views/service_company/bench-candidates/create.blade.php` lines 4085â€“4165.
- **The Code Flaw:** The Add Talent page uses an unmaintained, naive regex script (`/([A-Z]+ [A-Z]+) \w+/i`) and raw PDF.js loops instead of the hardened, production-tested WASM parser engine (`jero-client-parser.blade.php`).
- **Consequence:** Parsing frequently fails on modern resume layouts, freezes browser threads with un-throttled synchronous loops, and provides no fallback for corrupt files.

### Defect 6: Missing 3-Layer Submission Idempotency
- **The Code Flaw:** Unlike Add Candidate (which enforces client UUID + Redis NX 120s lock + database unique constraint), Add Talent relies solely on an optional 10-second `Cache::lock()` that only executes if `feature.talents_v2_enabled` is true. No unique constraint exists in MySQL for talent submissions.
- **Consequence:** Fast double-clicking on "Submit" creates duplicate bench profiles in MySQL and triggers duplicate coin deductions (`coinsDeductions`).

### Defect 7: Fragile Immediate Page Redirect
- **Location:** `create.blade.php` lines 3519 & 3656.
- **The Code Flaw:**
  ```javascript
  Swal.fire({ text: "You have successfully Submitted", ... });
  window.location = response.url; // Executes synchronously outside modal promise!
  ```
- **Consequence:** The browser immediately navigates away before the SweetAlert modal can render or be seen by the recruiter, causing visual page tearing and truncated network connections.

### Defect 8: Server-Rendered HTML on Listing Dashboard (`data_ajax`)
- **Location:** `ServiceCompanyBenchCandidatesController.php` lines 580â€“585 & `index.blade.php` line 1124.
- **The Code Flaw:** The bench listing dashboard requests `route('service_company.bench-candidates.data_ajax')`, which calls `view('service_company.ajax_pages.bench-candidate-list-ajax')->render()` on the server for every filter, search keystroke, and pagination click.
- **Consequence:** Generating multi-kilobyte HTML strings on the web server under 10,000 concurrent recruiters consumes massive CPU cycles. Network transfer is 10x larger than necessary (85KB HTML vs 8KB JSON).

---

## 3. The 10K Concurrency & 0% Server Strain Architecture

To match the GOD-level stability of Add Candidate and sustain 10,000 concurrent recruiters, the Add Talent module must be engineered around 5 foundational pillars:

```
                                  10K CONCURRENCY ARCHITECTURAL PILLARS
                                  
   â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
   â”‚ 1. CLIENT-FIRST BROWSER RAM EXECUTION                                                  â”‚
   â”‚    â€¢ 100% Client-Side WASM Resume Parser (0 PHP, 0 MySQL, 0 ES)                        â”‚
   â”‚    â€¢ In-Browser Commercials & Currency Calculator (Real-time 60fps Math)                â”‚
   â”‚    â€¢ Browser-RAM Trie Autocomplete for Skills & Locations (0ms Network Latency)        â”‚
   â”‚    â€¢ Tab-Scoped Session Isolation (`window.__tabInstanceId`)                           â”‚
   â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
   â”‚ 2. ZERO-READ PAGE LOAD HYDRATION                                                       â”‚
   â”‚    â€¢ Static Reference Data cached in Client `sessionStorage` (24h TTL)                 â”‚
   â”‚    â€¢ Master options (qualifications, countries, currencies) pre-warmed via Redis       â”‚
   â”‚    â€¢ 0% MySQL reads when 10,000 recruiters open the Add Talent page simultaneously     â”‚
   â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
   â”‚ 3. PURE CQRS TRANSACTION PERSISTENCE (< 15ms)                                          â”‚
   â”‚    â€¢ Zero external HTTP calls or ES queries inside `DB::beginTransaction()`            â”‚
   â”‚    â€¢ 3-Layer Idempotency Guard (Client UUID + Redis SETNX 120s + MySQL Unique Index)   â”‚
   â”‚    â€¢ Bulk Relational Writes: Single multi-row SQL INSERT statements                    â”‚
   â”‚    â€¢ Transactional Outbox Pattern: Atomic event insert into `candidate_outbox_events`  â”‚
   â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
   â”‚ 4. ASYNCHRONOUS SEARCH INGESTION & OUTBOX WORKER                                       â”‚
   â”‚    â€¢ Background Outbox Worker with Two-Step `SKIP LOCKED` and Lease Fencing            â”‚
   â”‚    â€¢ Asynchronous indexing to `ELASTIC_BENCH_ASSIGN` & `ELASTIC_CANDIDATES_BENCH`      â”‚
   â”‚    â€¢ Dedicated queue isolation (`candidate-index` on Redis/Horizon)                   â”‚
   â”œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”¤
   â”‚ 5. CLIENT-SIDE DOM ASSEMBLY ON LISTING DASHBOARD                                       â”‚
   â”‚    â€¢ Shift from server-rendered HTML (`data_ajax`) to normalized JSON (`data_v2`)      â”‚
   â”‚    â€¢ 100% Reads served from Elasticsearch (0% MySQL strain)                            â”‚
   â”‚    â€¢ Client Browser RAM renders candidate cards and pagination                         â”‚
   â”‚    â€¢ Dual-Trigger Listing Redirect: Instant on click + 2000ms background auto-timer    â”‚
   â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

---

## 4. Complete End-to-End Workflow Specifications

### Workflow 1: Add Talent (Page Load, Hydration & In-Browser Execution)

#### 1.1 Page Load & Pre-flight Tenancy Check (`GET /service_company/bench-candidates/create`)
- **Route:** `service_company.bench-candidates.create`
- **Controller Action:** `ServiceCompanyBenchCandidatesController@create`
- **Architecture Invariant:** Zero MySQL reads on page load. All reference lookups must be served from cache.
- **Execution Steps:**
  1. Authenticate user and verify permission: `abort_if(Gate::denies('add_job_applications'), 403)`.
  2. Validate coin package balance: `GeneralMethod::coinsValidationsAllcompany($companyId, 5, 1)`. If insufficient coins, redirect immediately to listing with flash message.
  3. Hydrate static master datasets from Redis cache (24h TTL):
     - `qualification_master` via `Cache::remember('qualification_master_list', 86400, ...)`
     - `country_list` via `Cache::remember('country_list_json', 86400, ...)`
     - `currency_rates` via `Cache::remember('currency_converter_rates', 3600, ...)`
  4. Return isolated Metronic Blade view with cache headers:
     ```php
     return response()->view('service_company.bench-candidates.create', $this->data)
         ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
         ->header('Pragma', 'no-cache');
     ```

#### 1.2 In-Browser WASM Resume Parsing via `https://parser.jero.works/` (0% Server CPU)
- **Engine:** Client-side WebAssembly parser integration via dedicated bridge `jero-talent-parser.js` that dynamically imports the production bundle:
  `import('https://parser.jero.works/jero-resume-parser-2tURGwDx.js')`.
- **Execution In Browser RAM:**
  1. Recruiter drags & drops a PDF or DOCX file into `#dropzone-upload`.
  2. WebAssembly (PDF.js) and Mammoth.js execute directly inside the browser's execution thread in User RAM.
  3. Extracts candidate text and parses:
     - Full Name (`full_name`)
     - Email Address (`email`)
     - Mobile Number & Calling Code (`phone`, `phone_code`)
     - Total Experience (`total_exp_year`, `total_exp_month`)
     - Skills Array (`skills[]`)
     - Career Summary (`careersummary`)
  4. **Idempotent Population:** Automatically populates empty form inputs without overwriting fields manually typed by the recruiter.
  5. UI completely hides the dropzone and displays the Uploaded Resume card (single "Remove" action, zero "Replace" button confusion).

#### 1.3 In-Browser Real-Time Commercials & Currency Calculator
- **Engine:** `TalentCommercialCalculator` (Client-side JavaScript module running in User RAM).
- **Business Math (Preserved 100%):**
  - **Pay Rate to Annual:** Converts hourly (`* 2080`), monthly (`* 12`), or yearly pay rate into normalized annual pay in base currency.
  - **Bill Rate to Annual:** Converts hourly, monthly, or yearly client billing rate into normalized annual bill rate.
  - **Margin Calculation:** 
    $$\text{Margin Amount} = \text{Annual Bill} - \text{Annual Pay}$$
    $$\text{Margin \%} = \left(\frac{\text{Margin Amount}}{\text{Annual Bill}}\right) \times 100$$
  - **Hike Percentage:** Automatically calculates hike percentage when comparing previous CTC to expected pay rate.
  - **Multi-Currency Conversion:** Converts USD, EUR, GBP, AED to INR in real time using cached exchange rates stored in `window._exchangeRates`.
- **Server Benefit:** Zero server HTTP requests during slider adjustments or salary inputs; updates at 60 FPS in browser RAM.

#### 1.4 Browser-RAM Trie Autocomplete (Skills & Locations)
- **Engine:** In-browser Radix / Prefix Trie initialized on window load (`window._talentSkillTrie`, `window._talentLocationTrie`).
- **Execution:**
  1. On first load, browser checks `sessionStorage.getItem('ats_skills_trie')`. If absent, fetches pre-compressed dictionary once and builds the Trie in memory.
  2. Keystrokes in Tagify skills or location inputs execute instant prefix matching in <2ms directly from browser RAM.
  3. Selected locations automatically bind pre-resolved numeric IDs (`city_id`, `state_id`, `country_id`), eliminating the server-side 3-table SQL join loop entirely.

---

### Workflow 2: Submit Talent (Transactional Persistence & Outbox Guarantee)

#### 2.1 3-Layer Idempotency & Rate Protection
```
[ Recruiter clicks 'Submit' ]
       â”‚
       â–¼
Layer 1: Client Browser RAM
  â€¢ Generates `client_submission_uuid` (UUIDv4)
  â€¢ Disables `#save-form` button & renders loading spinner
  â€¢ Attaches `X-Idempotency-Key: {client_submission_uuid}`
       â”‚
       â–¼
Layer 2: Server Redis Distributed Lock
  â€¢ Key: `talent:idempotency:{company_id}:{client_submission_uuid}`
  â€¢ Atomic check: `Redis::set($lockKey, 'in_progress', 'EX', 120, 'NX')`
  â€¢ If lock fails: Return HTTP 409 Conflict ("Submission already in progress")
       â”‚
       â–¼
Layer 3: MySQL Relational Constraint
  â€¢ Database UNIQUE Index: `uq_company_talent_submission_uuid` on `(company_id, client_submission_uuid)`
  â€¢ Storage engine rejects duplicate writes even if Redis is bypassed
```

#### 2.2 Sub-15ms MySQL Transaction Execution
The controller executes strictly local MySQL database operations inside `DB::beginTransaction()`:
```php
DB::beginTransaction();
try {
    // 1. Resilient save of Candidate / JobApplication entity
    $benchApplication->client_submission_uuid = $clientSubmissionUuid;
    $benchApplication->condidate_type = 'bench_condidate';
    $this->resilientSaveTalentApplication($benchApplication);

    // 2. Coin Deduction (Event 5) - MySQL record update
    if ($isNewRecord) {
        GeneralMethod::coinsDeductions((int)$companyId, 5, auth()->id(), $companyId, '', '');
    }

    // 3. Batch insert skills into job_application_skills (Single multi-row INSERT)
    if (!empty($bulkSkills)) {
        DB::table('job_application_skills')->where('job_application_id', $benchApplication->id)->delete();
        DB::table('job_application_skills')->insert($bulkSkills);
    }

    // 4. Batch insert recruiters into bench_assign (Single multi-row INSERT)
    if (!empty($bulkRecruiters)) {
        DB::table('bench_assign')->where('bench_applications_id', $benchApplication->id)->delete();
        DB::table('bench_assign')->insert($bulkRecruiters);
    }

    // 5. Batch insert custom details into bench_additional_detail (Single multi-row INSERT)
    if (!empty($bulkCustomDetails)) {
        DB::table('bench_additional_detail')->where('bench_id', $benchApplication->id)->delete();
        DB::table('bench_additional_detail')->insert($bulkCustomDetails);
    }

    // 6. Batch update skill experience ratings into bench_candidate_skill_experience
    if (!empty($bulkSkillExperience)) {
        DB::table('bench_candidate_skill_experience')->upsert($bulkSkillExperience, ['candidate_id', 'skill_key'], ['year', 'month', 'rating', 'updated_at']);
    }

    // 7. Transactional Outbox Event Persistence with Event Versioning
    $newVersion = ($benchApplication->resume_version ?: 1);
    DB::table('candidate_outbox_events')->insert([
        'company_id'        => $companyId,
        'aggregate_type'    => 'BENCH_CANDIDATE',
        'aggregate_id'      => $benchApplication->id,
        'aggregate_version' => $newVersion,
        'event_type'        => $isNewRecord ? 'BENCH_TALENT_CREATED' : 'BENCH_TALENT_UPDATED',
        'payload'           => json_encode(array_merge($talentOutboxPayload, ['_version' => $newVersion])),
        'status'            => 'PENDING',
        'available_at'      => now(),
        'idempotency_key'   => $isNewRecord 
            ? "{$companyId}:{$clientSubmissionUuid}" 
            : "{$companyId}:{$benchApplication->id}:{$clientSubmissionUuid}",
        'created_at'        => now(),
        'updated_at'        => now(),
    ]);

    DB::commit();
} catch (\Throwable $e) {
    DB::rollBack();
    throw $e;
}
```

#### 2.3 Post-Commit Asynchronous Processing & Cache Wiping
Immediately following `DB::commit()`:
1. **Multi-Tenant Atomic Cache Invalidation:**
   - Evict tenant Redis keys:
     ```php
     Cache::forget('ats:allbenchcandidates:company:'.$companyId);
     Cache::forget('benchcandidates_stats_company_'.$companyId);
     GeneralMethod::cacheClean('allbenchcandidates_version_company_'.$companyId);
     ```
2. **Immediate HTTP Response with Explicit Eventual Consistency Contract:**
   ```json
   {
       "status": "success",
       "message": "Talent profile successfully submitted.",
       "candidate_id": 48291,
       "index_status": "processing",
       "url": "https://testing.jero.works/service_company/bench-candidates"
   }
   ```
3. **Asynchronous Horizon Dispatch:** Dispatches `ProcessOutboxEventJob` to the isolated `candidate-index` queue on Redis Horizon.

#### 2.4 Outbox Search Ingestion: 3-Phase Execution Architecture (Phase A, B, C)
> **Crucial Mandate (Gate G-35):** The worker must NEVER keep a MySQL transaction open while communicating with Elasticsearch, Cloudflare R2, or any external network service. Keeping row locks open during HTTP network I/O simply moves the concurrency failure from the web server to the worker daemon.

```
â”Œâ”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”
â”‚                           THE 3-PHASE OUTBOX WORKER ARCHITECTURE                                 â”‚
â”‚                                                                                                  â”‚
â”‚   [PHASE A: SHORT MYSQL TRANSACTION (Duration < 5ms)]                                            â”‚
â”‚   â€¢ SELECT id, payload, aggregate_id, aggregate_version FROM candidate_outbox_events             â”‚
â”‚     WHERE status = 'PENDING' AND available_at <= NOW()                                           â”‚
â”‚     ORDER BY id ASC LIMIT 50 FOR UPDATE SKIP LOCKED;                                             â”‚
â”‚   â€¢ UPDATE candidate_outbox_events                                                               â”‚
â”‚     SET status = 'PROCESSING', lease_token = :uuid, lease_until = NOW() + INTERVAL 60 SECOND     â”‚
â”‚     WHERE id IN (:claimedIds);                                                                   â”‚
â”‚   â€¢ DB::commit();  â—„â”€â”€ [ROW LOCKS RELEASED IMMEDIATELY]                                          â”‚
â”‚                                                                                                  â”‚
â”‚   [PHASE B: ZERO MYSQL TRANSACTION / EXTERNAL I/O (Duration ~ 20ms - 80ms)]                      â”‚
â”‚   â€¢ NO open database transaction! Zero database row locks held!                                 â”‚
â”‚   â€¢ Decode payload and assemble Elasticsearch document projection.                              â”‚
â”‚   â€¢ Bulk HTTP request to Elasticsearch (`ELASTIC_CANDIDATES_BENCH`).                             â”‚
â”‚   â€¢ Apply Event Versioning Protection: Elasticsearch document specifies version.                â”‚
â”‚     If an older event (e.g. version 16) arrives after version 17, Elasticsearch rejects/ignores.â”‚
â”‚   â€¢ Synchronize recruiter assignments in `ELASTIC_BENCH_ASSIGN` out-of-band.                     â”‚
â”‚                                                                                                  â”‚
â”‚   [PHASE C: SHORT MYSQL TRANSACTION (Duration < 3ms)]                                            â”‚
â”‚   â€¢ DB::beginTransaction();                                                                      â”‚
â”‚   â€¢ UPDATE candidate_outbox_events                                                               â”‚
â”‚     SET status = 'COMPLETED', updated_at = NOW()                                                 â”‚
â”‚     WHERE id = :id AND lease_token = :uuid;                                                      â”‚
â”‚   â€¢ DB::commit();  â—„â”€â”€ [TRANSACTION COMPLETED]                                                   â”‚
â””â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”˜
```

#### 2.5 Bulletproof Dual-Trigger Redirect Protocol
In `create.blade.php`:
```javascript
function executeTalentRedirect(targetUrl) {
    if (window.__talentRedirecting) return;
    window.__talentRedirecting = true;
    window.location.href = targetUrl || "{{ route('service_company.bench-candidates.index') }}";
}

// Inside AJAX success handler:
const redirectUrl = response.url || "{{ route('service_company.bench-candidates.index') }}";

// 1. Establish defensive fallback timer
const redirectTimer = setTimeout(function () {
    executeTalentRedirect(redirectUrl);
}, 2000);

// 2. Prompt user feedback with immediate navigation on click
Swal.fire({
    title: "Success",
    text: response.message || "Talent profile has been successfully saved.",
    icon: "success",
    buttonsStyling: false,
    confirmButtonText: "View Bench Listing",
    customClass: { confirmButton: "btn btn-primary" }
}).then(function () {
    clearTimeout(redirectTimer);
    executeTalentRedirect(redirectUrl);
});
```

---

### Workflow 3: Listing Talent (High-Performance Bench Dashboard)

#### 3.1 100% Elasticsearch Read Path (0% MySQL Reads)
- **Route:** `service_company.bench-candidates.index` & `data_v2`
- **Rule 2 Universal Scope:** Every filter, sort, search query, and counter is served exclusively from Elasticsearch index `ELASTIC_CANDIDATES_BENCH`.
- **Zero Database Load:** MySQL does not receive a single `SELECT` or `JOIN` query when recruiters view or filter the talent list.

#### 3.2 Client-Side DOM Assembly via `data_v2`
- **Current Problem:** `data_ajax` executes server-side Blade compilation (`bench-candidate-list-ajax.blade.php`), consuming web server CPU and transmitting 85KB HTML payloads.
- **V4 Target:** The listing view transitions to consuming normalized JSON from `ServiceCompanyBenchCandidatesController@data_v2`:
  ```json
  {
      "total": 142,
      "page": 1,
      "per_page": 10,
      "candidates": [
          {
              "id": 48291,
              "full_name": "Alexander Wright",
              "designation": "Senior Full-Stack Engineer",
              "total_exp": "8.5",
              "skills": ["PHP", "Laravel", "Vue.js", "Elasticsearch"],
              "pay_rate": "85.00",
              "bill_rate": "120.00",
              "margin_pct": 29.17,
              "talent_stage": 2,
              "notice_period": "Immediate",
              "work_mode": "Hybrid"
          }
      ]
  }
  ```
- **Client Assembly:** A lightweight client-side renderer (`BenchTalentListRenderer`) constructs the card DOM directly in browser memory using DOM fragments.
- **Result:** Network payload reduced from ~85KB to ~8KB (90% reduction). Server CPU utilization drops to 0%.

#### 3.3 Hero Analytics Architecture: Unified Three-Layer Responsibility Model
> **Anti-Pattern Eliminated (Suggestion 18):** Pipeline counts and list data must NOT both be labelled as "client-side memory". Counts and list queries have fundamentally different latency, staleness, and resource contracts. Each layer has one unambiguous responsibility.

| Hero Metric Category | Authoritative Source | Rationale |
| :--- | :--- | :--- |
| **Pipeline Counts** (Total Talent, On Bench, Requests Sent, Accepted, Views, Interviews, Placed) | **Redis Materialized Counters** — atomic `INCR`/`DECR` post-commit inside Horizon worker | Sub-millisecond reads. Zero Elasticsearch calls for scalar counts. No TTL; invalidated on stage-change outbox events. |
| **Candidate List Queries** (filter, sort, paginate, search) | **Elasticsearch `ELASTIC_CANDIDATES_BENCH`** — all list data served exclusively from ES | Full-text search, compound filters, date-range sorts at ES tier. Zero MySQL reads. |
| **Local UI Filter State** (active stage tab, sort column, filter chip memory) | **Browser RAM** (`window.__benchFilterState`) | Pure ephemeral client state. Zero server round-trips for toggling filter chips or switching stage tabs. Updated via DOM events. |

```
HERO ANALYTICS DATA FLOW:

Page Load:
  -> PHP reads 7 Redis MGET keys (< 1ms)
  -> Returns { total: 142, bench: 67, placed: 18, ... }
  -> JS binds counters to DOM spans (0 ES calls for counters)

Stage Tab Click (e.g. "On Bench"):
  -> JS: window.__benchFilterState.stage = 2   (0ms, pure browser RAM)
  -> JS dispatches ES query via data_v2: { stage: 2, page: 1 }
  -> ES returns ~8KB filtered JSON
  -> BenchTalentListRenderer re-renders cards in DOM

Post-Talent-Submit (Outbox Horizon Worker Phase C):
  -> Redis.incr("bench:hero:total:{company_id}")
  -> Redis.incr("bench:hero:bench:{company_id}")
  -> No Elasticsearch required for count increments
```

- **SLA:** Hero counter reads < 5ms. Stage-filter list queries < 50ms. Browser filter state toggle < 1ms (zero network).
- **Multi-Tenant Isolation:** All Redis hero counter keys are tenant-scoped: `bench:hero:{metric}:{company_id}`. Invalidated atomically on stage-change events.
---

## 5. Database Schema & Migration Specification

### 5.1 Required Schema Migration
To support the 3-Layer Idempotency Guard and ensure transactional parity with Add Candidate, the following migration must be applied to `dbmaster`:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ensure client_submission_uuid exists on job_applications with unique company constraint
        Schema::table('job_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('job_applications', 'client_submission_uuid')) {
                $table->char('client_submission_uuid', 36)->nullable()->after('resume_version');
            }
            if (!Schema::hasColumn('job_applications', 'current_resume_id')) {
                $table->unsignedBigInteger('current_resume_id')->nullable()->after('resume');
            }
            if (!Schema::hasColumn('job_applications', 'resume_version')) {
                $table->unsignedInteger('resume_version')->default(1)->after('current_resume_id');
            }
        });

        // 2. Create compound indexes for high-concurrency bench candidate queries
        Schema::table('job_applications', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = array_map(fn($idx) => $idx->getName(), $sm->listTableIndexes('job_applications'));

            if (!in_array('idx_bench_company_status', $indexes)) {
                $table->index(['company_id', 'condidate_type', 'status'], 'idx_bench_company_status');
            }
            if (!in_array('uq_company_submission_uuid', $indexes)) {
                $table->unique(['company_id', 'client_submission_uuid'], 'uq_company_submission_uuid');
            }
        });

        // 3. Ensure relational helper table indexes are optimal
        Schema::table('bench_assign', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = array_map(fn($idx) => $idx->getName(), $sm->listTableIndexes('bench_assign'));

            if (!in_array('idx_bench_assign_composite', $indexes)) {
                $table->index(['bench_applications_id', 'company_id'], 'idx_bench_assign_composite');
            }
        });

        Schema::table('bench_additional_detail', function (Blueprint $table) {
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = array_map(fn($idx) => $idx->getName(), $sm->listTableIndexes('bench_additional_detail'));

            if (!in_array('idx_bench_detail_lookup', $indexes)) {
                $table->index(['bench_id'], 'idx_bench_detail_lookup');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * IRREVERSIBILITY CONTRACT:
     * This migration is intentionally non-reversible in production because:
     *  1. Dropping `client_submission_uuid` from `job_applications` would destroy the idempotency
     *     record of all existing bench talent submissions — creating a window for duplicate
     *     resubmission of historical profiles.
     *  2. Dropping `uq_company_submission_uuid` would silently remove the only storage-engine-level
     *     protection against duplicate bench profiles under high concurrency.
     *  3. The outbox `aggregate_version` column records event sequencing — removal would corrupt
     *     the version-fencing contract and allow stale Elasticsearch documents to overwrite fresh ones.
     *
     * If rollback is genuinely required in a non-production environment, apply the following steps
     * manually and with full team awareness:
     *   ALTER TABLE `job_applications` DROP INDEX `uq_company_submission_uuid`;
     *   ALTER TABLE `job_applications` DROP COLUMN `client_submission_uuid`;
     *   ALTER TABLE `job_applications` DROP COLUMN `current_resume_id`;
     *   ALTER TABLE `job_applications` DROP COLUMN `resume_version`;
     *   DROP INDEX `idx_bench_company_status` ON `job_applications`;
     *   DROP INDEX `idx_bench_assign_composite` ON `bench_assign`;
     *   DROP INDEX `idx_bench_detail_lookup` ON `bench_additional_detail`;
     *
     * Production rollback is BLOCKED by this declaration.
     */
    public function down(): void
    {
        throw new \RuntimeException(
            'V4 Add Talent schema migration is irreversible in production. ' .
            'Manual rollback steps are documented in the migration file docblock. ' .
            'Coordinate with the infrastructure team before proceeding.'
        );
    }
};
```

---

## 6. 34-Gate Production Acceptance & Zero-Regression Matrix (G-01 to G-34)

Before the V4 Add Talent flow is considered production-ready, every gate in this matrix must pass verification:

| Gate | Category | Invariant & Acceptance Criteria | Verification Method | Status |
| :--- | :--- | :--- | :--- | :--- |
| **G-01** | Subsystem Isolation | Add Talent modifications are strictly quarantined to `service_company/bench-candidates/`. Zero edits to `job-applications`, `product_company`, or `staffing_company`. | `git diff --name-only` inspection | REQUIRED |
| **G-02** | Business Logic | Coin deduction (`eventId = 5`), package redemption, and stage mapping (1, 2, 4, 5) remain 100% mathematically and functionally identical. | Unit test assertion | REQUIRED |
| **G-03** | Commercials Math | Pay rate, bill rate, annual conversions, margin amount, and margin % calculate identically to legacy logic across hourly/monthly/yearly modes. | Automated Math Test Suite | REQUIRED |
| **G-04** | Client WASM Parser | Resume parsing executes 100% in browser RAM; extracts name, email, phone, experience, skills, and career summary without server API calls. | Browser DevTools Network Audit | REQUIRED |
| **G-05** | Parser Non-Destructive | Form auto-fill populates empty fields only; never overwrites user-edited inputs. | Manual & automated browser test | REQUIRED |
| **G-06** | Dropzone UI Invariant | Single "Remove" action displayed upon upload; dropzone completely hides; zero "Replace" button confusion. | Visual regression check | REQUIRED |
| **G-07** | Manual Entry Fallback | Recruiter can submit a talent profile without uploading a resume file (100% functional manual form). | Submission test with null resume | REQUIRED |
| **G-08** | In-Browser Trie | Skill and location autocomplete requests hit browser memory Trie (`window._talentSkillTrie`); zero backend AJAX per keystroke. | DevTools XHR tab (0 requests) | REQUIRED |
| **G-09** | Multi-Tab Isolation | Form state, upload sessions, and submission UUIDs scoped to `window.__tabInstanceId`. | Multi-tab concurrent submit test | REQUIRED |
| **G-10** | Client Idempotency | Submit button disables immediately upon click; loading spinner renders; `client_submission_uuid` injected into payload. | Double-click simulation | REQUIRED |
| **G-11** | Redis Idempotency | Redis `SETNX` lock on `talent:idempotency:{company_id}:{uuid}` with 120s TTL blocks concurrent duplicate submissions with HTTP 409. | Concurrent curl submission test | REQUIRED |
| **G-12** | MySQL Idempotency | Database unique constraint `uq_company_submission_uuid` guarantees single-row persistence at storage engine layer. | Raw SQL duplicate insert test | REQUIRED |
| **G-13** | Transaction Boundary | Zero Elasticsearch queries (`getClient()->...`) inside `DB::beginTransaction()`. | Static code analysis & trace | REQUIRED |
| **G-14** | Transaction Boundary | Zero Cloudflare R2 / S3 network calls (`Storage::disk()->...`) inside `DB::beginTransaction()`. | Static code analysis & trace | REQUIRED |
| **G-15** | Transaction Duration | MySQL transaction hold time benchmarked at P95 < 15ms. | Laravel Telescope / APM log | REQUIRED |
| **G-16** | Batch Relational Writes | `job_application_skills`, `bench_assign`, and `bench_additional_detail` inserted via single bulk `insert()` queries (0 N+1 loops). | Query log assertion (â‰¤ 7 queries) | REQUIRED |
| **G-17** | Skills Experience Matrix | `bench_candidate_skill_experience` updated via bulk `upsert()` query. | Query log verification | REQUIRED |
| **G-18** | Outbox Event Write | Event written to `candidate_outbox_events` inside the same MySQL transaction as candidate record. | Transaction rollback test | REQUIRED |
| **G-19** | Outbox Worker Locking | Outbox event processor claims records using `SELECT ... FOR UPDATE SKIP LOCKED`. | Multi-worker queue test | REQUIRED |
| **G-20** | Search Sync Parity | Candidate, assigned recruiters, and custom details indexed in Elasticsearch out-of-band via Horizon queue. | Elasticsearch document check | REQUIRED |
| **G-21** | Dead-Letter Policy | Failed Elasticsearch indexing retried 5 times with exponential backoff before dead-letter notification. | Simulated ES timeout test | REQUIRED |
| **G-22** | Schema Deployment Compatibility Guard | `resilientSaveTalentApplication()` detects MySQL error 1054 (Unknown Column â€” schema not yet deployed) and falls back to the legacy column path. **This is NOT a circuit breaker.** A real circuit breaker manages CLOSEDâ†’OPENâ†’HALF-OPEN state against transient remote-dependency failures. This method handles a local schema deployment sequencing issue only. It must be instrumented with a warning log and removed once the target migration is confirmed deployed in all environments. | Controller unit test + `php artisan migrate:status` | REQUIRED |
| **G-23** | Dual-Trigger Redirect | Redirect executes immediately upon SweetAlert confirmation OR via 2000ms background auto-timer. | Browser redirect automation test | REQUIRED |
| **G-24** | Listing Redirect Target | Form submission redirects authoritatively to `service_company.bench-candidates.index`. | HTTP response URL assertion | REQUIRED |
| **G-25** | Cache Invalidation | Tenant cache keys (`allbenchcandidates_version_company_{id}`) cleared post-commit. | Redis key inspection post-submit | REQUIRED |
| **G-26** | 0% MySQL Reads on List | Bench listing dashboard (`/bench-candidates`) serves 100% of queries, filters, and counts from Elasticsearch. | MySQL general query log audit | REQUIRED |
| **G-27** | Client DOM Assembly | Listing dashboard consumes `data_v2` JSON payload and renders cards in User RAM. | Network payload audit (< 10KB) | REQUIRED |
| **G-28** | Hero Analytics Cache | Hero pipeline counts (Total, Bench, Requests, Placed) served from Redis with instant cache eviction. | Hero performance test | REQUIRED |
| **G-29** | Page Load DB Reads | `create()` view initialization performs 0 un-cached master SQL reads. | Telescope DB count on page load | REQUIRED |
| **G-30** | Response Latency SLA | Submit talent HTTP endpoint returns response in P95 < 150ms. | k6 benchmark test | REQUIRED |
| **G-31** | 10K Concurrency Scale | System sustains 10,000 virtual users across Add/Submit/Listing without thread exhaustion or 5xx errors. | Sustained concurrency load test | REQUIRED |
| **G-32** | Responsive UI Layout | Metronic 8 layout renders perfectly on 15.6" screens (1366Ã—768 & 1920Ã—1080) with internal card scrolling. | Visual multi-resolution audit | REQUIRED |
| **G-33** | Mobile/Secondary Contacts | Secondary contact details (mobile, email, phone code) preserved and persisted accurately. | Data persistence verification | REQUIRED |
| **G-34** | Zero Module Regressions | Zero changes or impact on Add Candidate, Requisitions, Job Applications, or Client Management modules. | Full test suite execution (`phpunit`) | REQUIRED |
| **G-35** | Outbox Worker Phase Separation | Outbox worker NEVER holds a MySQL transaction open while communicating with Elasticsearch or Cloudflare R2. Phase A (claim, < 5ms TX), Phase B (ES/R2 I/O, zero TX), Phase C (mark complete, < 3ms TX) are strictly isolated. | Code review + integration trace | REQUIRED |
| **G-36** | ES Read P95 Latency | Elasticsearch P95 query latency < 50ms across all bench candidate queries (listing, filter, search, count). | Elasticsearch slow-log + APM monitoring | REQUIRED |
| **G-37** | ES Read P99 Latency | Elasticsearch P99 query latency < 120ms. Sustained breaches must trigger alerting and capacity review. | APM percentile dashboard | REQUIRED |
| **G-38** | ES JVM Heap Usage | Elasticsearch JVM heap usage < 75% under sustained 10K concurrency listing load. Heap > 75% triggers GC pressure and query latency degradation. | ES `_nodes/stats` heap_used_percent | REQUIRED |
| **G-39** | ES GC Pause Duration | Elasticsearch GC pause duration < 200ms per cycle. GC pauses > 200ms cause query timeouts cascading to listing page failures. | ES `_nodes/stats` gc.collectors | REQUIRED |
| **G-40** | ES Thread Pool Queue | Elasticsearch search thread pool queue depth < 100 pending tasks. Depth > 100 indicates indexing throughput deficit. | ES `_cat/thread_pool?v` | REQUIRED |
| **G-41** | ES Rejected Requests | Elasticsearch rejected requests counter = 0 under normal operating load. Any non-zero rejection count is a P1 incident. | ES `_cat/thread_pool?v` rejections | REQUIRED |
| **G-42** | ES Shard Size | Each `ELASTIC_CANDIDATES_BENCH` shard size < 50GB to preserve segment merge performance and search throughput. | ES `_cat/shards?v` | REQUIRED |
| **G-43** | ES Indexing Throughput | Outbox Horizon worker achieves minimum 500 bench candidate documents indexed per second at steady state. | ES `_cat/indices?v` indexing rate | REQUIRED |

---

## 7. Failure & Recovery Matrix

> **Architectural Mandate (Suggestion 25):** Every dependency in the Add Talent pipeline must have an explicit, documented failure behavior. Silent failures that corrupt data or block recruiter workflows without clear error messages are P0 incidents.

| Dependency | Failure Mode | System Behavior | User-Visible Impact | Recovery Path |
| :--- | :--- | :--- | :--- | :--- |
| **MySQL** (Primary Write Store) | Database unreachable / connection exhausted | `DB::beginTransaction()` throws; controller returns HTTP 503. | "Service temporarily unavailable — please retry." Toast error. Form data preserved in browser. | Automatic reconnect via Octane. Recruiter retries via preserved form state. |
| **Redis** (Idempotency Guard + Cache) | Redis unreachable | Redis `SETNX` lock times out. System **degrades gracefully**: skips Redis lock and falls back to MySQL unique constraint as the sole idempotency authority. | No visible degradation for normal submissions. Rare duplicate-click resolved by MySQL constraint (HTTP 409). | Redis auto-recovery via Sentinel. Cache invalidation resumes on reconnect. |
| **Elasticsearch** (Read Projection) | ES cluster unreachable or returning errors | Talent profile **MUST still save successfully** to MySQL. Outbox event written. Horizon worker retries ES indexing (max 5 attempts, exponential backoff). | Recruiter sees `index_status: "processing"`. Bench listing may not immediately show new talent. | Horizon worker retries automatically. Dead-letter notification after 5 failures. Listing updates when ES recovers. |
| **Cloudflare R2** (Resume Storage) | R2 unavailable or presign fails | Upload State Machine transitions to `UPLOAD_FAILED`. Module 2 emits failure event. Form remains fully submittable without a resume. | "Resume upload failed — submit without resume or retry." Upload chip shows error state. | Recruiter retries upload or submits without resume. R2 auto-recovery. |
| **Client-Side Parser** (`parser.jero.works`) | Cloudflare Worker unreachable or JS module fails to load | `import(...)` rejects. Module 1 catches and emits `talent:parser-failed`. Parser silently removed from UI. | Parser dropzone disappears. Form is 100% functional with manual entry — zero workflow disruption. | Recruiter enters fields manually. Parser retries on next file selection. |
| **Horizon Worker** (Outbox Processor) | All Horizon workers stopped / queue down | MySQL write + outbox event write succeed. `candidate_outbox_events` row remains `PENDING`. | Recruiter sees success + redirect. Bench listing delayed until workers restart. No data loss. | Restart Horizon workers. Workers immediately process accumulated `PENDING` events in FIFO order. |
| **Browser IndexedDB** (Location Shard Cache) | Storage quota exceeded or IndexedDB unavailable | `ats_geo_cache` write fails. Web Worker falls back to: (a) server-side paginated location search, or (b) manual text entry without autocomplete. | Location autocomplete degrades to server-fetched suggestions with slight network latency. Form fully functional. | IndexedDB recovers on next page load. Cache rebuilds progressively. |
| **Browser Memory** (Form State / Tab Isolation) | Tab crash or memory pressure | Form data lost if not yet submitted. `__tabInstanceId` scope lost. | Browser displays blank/reloaded form. Recruiter must re-enter. | Consider periodic `localStorage` auto-save of draft form state as a future improvement. |

---

## 8. Implementation Roadmap & Execution Phasing

### Phase 1: Database Migration & Schema Hardening
- **Objective:** Establish the structural foundations for idempotency and indexing performance.
- **Actions:**
  1. Generate Laravel migration `2026_09_28_000001_add_talent_v4_indexes_and_idempotency.php`.
  2. Add `client_submission_uuid` with compound unique index `uq_company_submission_uuid`.
  3. Add compound index `idx_bench_company_status` on `job_applications(company_id, condidate_type, status)`.
  4. Ensure `candidate_outbox_events` table accepts aggregate type `'BENCH_CANDIDATE'`.

### Phase 2: Controller Refactoring & Transaction Decoupling
- **Objective:** Eliminate all toxic network I/O from MySQL transactions and enforce CQRS write speed.
- **Actions:**
  1. In `ServiceCompanyBenchCandidatesController@store`:
     - Implement 3-layer idempotency validation (Client UUID + Redis `SETNX` 120s lock).
     - Strip all calls to `$this->elasticService->getClient()` (`deleteByQuery`, `get`, `update`) from `DB::beginTransaction()`.
     - Strip all calls to `Storage::disk('r2')` from inside the transaction.
     - Replace N+1 loops for skills, recruiters, custom details, and skill experiences with bulk array `insert()` and `upsert()`.
     - Insert talent aggregate event into `candidate_outbox_events` inside the local transaction.
     - Add `resilientSaveTalentApplication()` **Schema Deployment Compatibility Guard** — catches MySQL error 1054 (Unknown Column) during migration transition window only. This is **NOT** a circuit breaker. Must be removed once migration is confirmed deployed in all environments.
     - Invalidate tenant Redis listing caches post-commit.
     - Return standardized JSON response payload: `{ status: 'success', message: '...', url: '...' }`.

### Phase 3: Client-First WASM Parser & In-Browser Commercials Engine
- **Objective:** Shift 100% of resume parsing and commercial math into browser RAM.
- **Actions:**
  1. Integrate the isolated client parser bridge (`jero-talent-parser.js`) into `create.blade.php`.
  2. Extract text and auto-fill empty form inputs using client-side WASM.
  3. Implement `TalentCommercialCalculator` in JavaScript to handle real-time Pay Rate / Bill Rate / Margin / Currency math in user RAM.
  4. Initialize in-browser Trie for skills and locations with `sessionStorage` caching.
  5. Implement dual-trigger redirect pattern (SweetAlert + 2000ms background auto-timer).

### Phase 4: Listing Dashboard Optimization (`data_v2` & Client DOM Assembly)
- **Objective:** Eliminate server-rendered HTML blocks and achieve 0% MySQL read strain on the Bench Listing dashboard.
- **Actions:**
  1. Upgrade `resources/views/service_company/bench-candidates/index.blade.php` to consume `route('service_company.bench-candidates.data_v2')`.
  2. Implement client-side card renderer in browser RAM using lightweight JavaScript DOM fragments.
  3. Enforce 100% Elasticsearch querying across all filter buttons, search inputs, and pagination controls.
  4. Verify Hero Analytics stage updates in <50ms.

### Phase 5: Verification, Benchmarking & Acceptance Sign-Off
- **Objective:** Mathematically and empirically prove 10,000 concurrency compliance.
- **Actions:**
  1. Run `php -l` and code styling verification across all modified files.
  2. Execute feature test suite `tests/Feature/V4AddTalentRemediationTest.php` covering:
     - Form submission persistence and redirect URL integrity.
     - Idempotency lock rejection on duplicate tokens.
     - Bulk relational writes (skills, assignments, custom details).
     - Outbox event generation and transactional rollback safety.
     - Zero Elasticsearch or R2 network calls inside transactions.
  3. Execute k6 load test simulating 10,000 virtual users targeting `create`, `store`, and `data_v2`.
  4. Verify P95 latency < 150ms and 0% MySQL read strain.

