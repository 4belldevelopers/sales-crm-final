# V4 Architecture Plan — Unified Prescreen Dossier Bridge: Forward Resume & Candidate Drawer

**Document Version:** 4.0.0  
**Target Environment:** JERO ATS & Talent Marketplace (`testing.jero.works`)  
**Scope:** Forward Resume Prescreen Report, Candidate Drawer (`showdetail.blade.php`), CV Tracker (`fwd_resume`), AgyForwardResumeEngine  
**Architectural Pattern:** CQRS (MySQL Writes, Elasticsearch/Memory Reads) + Reactive Client-Side Cache Bridge + SSOT Canonical Data Contract  

---

## 1. Executive Summary & Problem Definition

### 1.1 The Current Problem
Currently, the **Candidate Drawer** and the **Forward Resume Prescreen Report** operate on separate, isolated data-assembly pipelines:
1. **Candidate Drawer (`showdetail.blade.php`)**:
   - Calculates skill match percentage using the modern canonical algorithm: `GeneralMethod::calculateSkillMatch($jobSkills, $candidateSkills)`.
   - Computes 7 real-time validation badges (Notice Period status, Budget/CTC alignment, Relocation willingness, Experience criteria, etc.).
   - Renders the Prescreen Dossier in `#rsData` element and displays PDF via client-side PDFMake engine.
2. **Forward Resume Section (`ServiceCompanyCvTrackerController::fwd_resume`)**:
   - Runs **11 separate, un-optimized MySQL queries** per candidate when loading the modal.
   - Derives match percentage from checkbox data attributes (`data-match`) or falls back to legacy/hardcoded formulas (e.g. `GeneralMethod::skill_percentage` or `85%`).
   - Does **not** share the Candidate Drawer's 7 validation badges or exact skill rating mapping.

**Consequence:** Recruiters see one match percentage and set of badges in the Candidate Drawer, but when they generate or email the Forward Resume Prescreen Report, the numbers, badges, and details can diverge.

### 1.2 The God-Level Architectural Goal
1. **100% Data & Visual Parity (SSOT)**: The Prescreen Report and Candidate Drawer must share the exact same canonical data contract. Match %, badges, notice period, and skill stars must be mathematically and visually identical.
2. **0ms Instant Load (0% Server Load)**: When a candidate's drawer has already been viewed/loaded in the recruiter's session, Forward Resume copies the dossier directly from in-memory cache with zero HTTP roundtrips, 0% MySQL load, and 0% Elasticsearch load.
3. **Reactive Real-Time Freshness (Zero Stale Data Guarantee)**: If any candidate data is updated in the drawer (Notice Period, Skills, CTC, Prescreen Questions), the cache is immediately invalidated and updated. The Prescreen Report **always reflects 100% fresh, updated data**.
4. **Batch Fallback for Table Checkbox Selections**: If candidates are selected directly from table checkboxes without opening their drawers, a single batch dossier endpoint provides the exact canonical drawer schema without running fragmented legacy queries.

---

## 2. End-to-End System Flow Diagram

```mermaid
flowchart TD
    subgraph Browser Client Memory
        CD[Candidate Drawer Opened] -->|Parses #rsData| CC[(window.__candidateDossierCache)]
        MUT[Recruiter Updates Candidate in Drawer] -->|AJAX Save Success| INV[Invalidate & Overwrite Cache]
        INV --> CC
        INV -->|Dispatches Event| EVT[window.dispatchEvent: jero:candidate-dossier-updated]
    end

    subgraph Forward Resume Action
        SEL[Recruiter Selects Candidates & Clicks 'Forward Resume'] --> CHECK{Are candidates in __candidateDossierCache?}
        CHECK -- YES (In Cache & Fresh) --> FAST[0ms Direct Read from Cache]
        CHECK -- NO (Selected from Table, Drawer Unopened) --> FETCH[Fetch Canonical Dossier Batch API]
    end

    subgraph Backend Canonical Resolver (SSOT)
        FETCH --> RESOLVER[CandidateDossierService::buildDossier(candidateId, jobId)]
        RESOLVER -->|Reads ES / MySQL Writes| ES[(Elasticsearch / Read Cache)]
        RESOLVER --> PARSER[Canonical calculateSkillMatch & 7 Validation Badges]
        PARSER --> PAYLOAD[Canonical Dossier JSON Payload]
    end

    PAYLOAD --> CC
    FAST --> ENGINE[AgyForwardResumeEngine.js]
    PAYLOAD --> ENGINE
    EVT -->|Live Listener| ENGINE

    ENGINE --> PDF[100% Identical Prescreen Report & PDF Output]
```

---

## 3. The Core Challenge & Resolution: Cache Freshness vs Stale Data

### 3.1 The Question
> *"If we add the client-side cache bridge, what happens if data on the candidate drawer gets updated? Will the prescreen report show current data, updated data, or stale cached data?"*

### 3.2 The 3-Tier Reactive Cache-Buster Architecture

To ensure the Prescreen Report **never displays stale cached data**, we deploy three reactive safeguards:

#### Tier 1: Write-Through Cache Mutation
Whenever any drawer mutation succeeds (Candidate details edit, skills edit, prescreen questions update, CTC change), the AJAX success handler immediately writes through to `window.__candidateDossierCache`:
```javascript
/**
 * Global Dossier Cache Manager
 */
window.CandidateDossierBridge = {
    cache: {},

    set: function(candidateId, dossier, revision) {
        this.cache[candidateId] = {
            dossier: dossier,
            revision: revision || Date.now(),
            cachedAt: Date.now()
        };
    },

    get: function(candidateId) {
        return this.cache[candidateId] ? this.cache[candidateId].dossier : null;
    },

    invalidate: function(candidateId) {
        if (candidateId) {
            delete this.cache[candidateId];
        } else {
            this.cache = {};
        }
    },

    // Broadcast mutation to any active prescreen components
    broadcastUpdate: function(candidateId, updatedDossier) {
        this.set(candidateId, updatedDossier);
        window.dispatchEvent(new CustomEvent('jero:candidate-dossier-updated', {
            detail: { candidateId: candidateId, dossier: updatedDossier }
        }));
    }
};
```

#### Tier 2: Revision Timestamp Verification (`updated_at`)
Each dossier object contains an entity timestamp `revision: candidate.updated_at`.
When Forward Resume renders or refreshes:
- It verifies `cache.revision >= drawer.revision`.
- If the candidate was modified in another tab or drawer, the timestamp mismatch triggers a background re-sync.

#### Tier 3: Reactive Event Dispatching (`jero:candidate-dossier-updated`)
When `showdetail.blade.php` saves an edit:
```javascript
// Example in Drawer basic info / skills update callback:
$.ajax({
    url: '/service-company/job-applications/update_candidate',
    method: 'POST',
    data: formData,
    success: function(res) {
        // 1. Update DOM and Drawer UI
        // ...
        // 2. Refresh the Canonical #rsData payload in DOM
        // ...
        // 3. Notify the Dossier Bridge
        const freshDossier = readData(); // Reads updated #rsData
        window.CandidateDossierBridge.broadcastUpdate(candidateId, freshDossier);
    }
});
```
If the Forward Resume preview modal is open, it listens for `jero:candidate-dossier-updated` and automatically re-generates the Prescreen Report preview in real-time with **zero page refresh**.

---

## 4. Architectural Components to Build

### Component 1: Canonical Backend Dossier Service (`App\Services\Candidate\CandidateDossierService`)
Create a single service that acts as the Single Source of Truth for candidate dossier data:
- **Location:** `app/Services/Candidate/CandidateDossierService.php`
- **Responsibilities:**
  1. Assembles Candidate Info, Job Skills vs Candidate Skills.
  2. Executes `GeneralMethod::calculateSkillMatch()` (preserves exact matching logic).
  3. Evaluates the 7 Validation Badges:
     - Notice Period alignment (Immediate, <15 days, 30 days, etc.).
     - Location & Relocation willingness.
     - Budget / CTC match.
     - Experience requirements (Min/Max).
     - Skill experience thresholds.
     - Education qualifications.
     - Candidate availability status.
  4. Returns the exact JSON schema that `#rsData` in `showdetail.blade.php` and `AgyForwardResumeEngine.js` consume.

### Component 2: Unified Schema Contract
The schema returned by the backend and stored in DOM `#rsData` must be identical:
```json
{
  "candidate": {
    "id": 12345,
    "name": "Jane Doe",
    "email": "jane@example.com",
    "phone": "+91 9876543210",
    "city": "Bengaluru",
    "state": "Karnataka",
    "country": "India",
    "current_company": "Acme Corp",
    "current_designation": "Senior Full Stack Engineer",
    "total_experience_months": 84,
    "current_ctc": 2400000,
    "expected_ctc": 3000000,
    "notice_period_days": 15,
    "photo_url": "/uploads/...",
    "updated_at": "2026-09-18T11:20:00Z"
  },
  "job": {
    "id": 789,
    "title": "Lead Laravel & Vue Architect",
    "company_name": "Tech Corp"
  },
  "metrics": {
    "match_percentage": 94,
    "match_level": "Strong Match",
    "match_badge_color": "#10b981",
    "experience_match": true,
    "ctc_match": true,
    "notice_period_match": true,
    "location_match": true
  },
  "skills": [
    { "name": "PHP / Laravel", "candidate_exp_months": 72, "rating": 5, "is_mandatory": true, "is_matched": true },
    { "name": "Elasticsearch", "candidate_exp_months": 48, "rating": 4, "is_mandatory": true, "is_matched": true },
    { "name": "Vue.js / React", "candidate_exp_months": 36, "rating": 4, "is_mandatory": false, "is_matched": true }
  ],
  "validation_badges": [
    { "key": "notice_period", "label": "15 Days Notice", "status": "success", "icon": "fa-calendar-check" },
    { "key": "location", "label": "Bengaluru (Immediate)", "status": "success", "icon": "fa-map-marker-alt" },
    { "key": "budget", "label": "Within Budget", "status": "success", "icon": "fa-wallet" },
    { "key": "experience", "label": "7 Years Total Exp", "status": "success", "icon": "fa-briefcase" }
  ],
  "evaluation_answers": [
    { "question": "Are you comfortable working in hybrid mode?", "answer": "Yes, completely comfortable." }
  ]
}
```

### Component 3: Client-Side Dossier Bridge (`AgyCandidateDossierBridge.js`)
- **Location:** `public/js/opendoc/AgyCandidateDossierBridge.js`
- Integrated into `showdetail.blade.php` and `_forward_resume_engine.blade.php`.
- Bridges DOM `#rsData` into `window.CandidateDossierBridge`.
- Provides 0ms instant payload retrieval for opened drawers.
- Automatically handles cache-invalidation on drawer saves.

### Component 4: Controller Modernization (`ServiceCompanyCvTrackerController.php`)
- Modernize `fwd_resume()` and `test_fwd_resume()`:
  - Replace the 11 ad-hoc queries per candidate with calls to `CandidateDossierService::getBatchDossiers($candidateIds, $jobId)`.
  - Check whether candidate dossier JSON was passed directly from browser bridge (if sent, zero backend query is required!).
  - If not passed from browser, batch load candidates in 1 single optimized query, computing canonical `GeneralMethod::calculateSkillMatch()`.

### Component 5: Unified PDF/Dossier Generator (`AgyForwardResumeEngine.js`)
- `buildDefinition(dossier)` in `AgyForwardResumeEngine.js` becomes the universal builder for:
  1. Drawer "Download Prescreen PDF".
  2. Forward Resume Email Attachment PDF.
  3. Client Forward Portal View.
- Guarantees identical typography, enlarged readable fonts (11pt+), matching badges, and color tokens across the entire platform.

---

## 5. Step-by-Step Implementation Roadmap

| Phase | Action Item | Target Files | Parity & Load Impact |
| :---: | :--- | :--- | :--- |
| **Phase 1** | Extract Single Source of Truth `CandidateDossierService` | `app/Services/Candidate/CandidateDossierService.php` | Eliminates disparate algorithms; unifies match % calculation |
| **Phase 2** | Implement Client-Side Dossier Bridge & Cache Manager | `public/js/opendoc/AgyCandidateDossierBridge.js` | Enables 0ms drawer copy + real-time cache invalidation on save |
| **Phase 3** | Integrate Bridge into Candidate Drawer (`showdetail.blade.php`) | `resources/views/service_company/job-applications/showdetail.blade.php` | Caches `#rsData` on open; invalidates on update |
| **Phase 4** | Connect Forward Resume to Bridge & Canonical Service | `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php`, `fwd_resume.blade.php` | Replaces 11 queries; falls back to single batch resolver |
| **Phase 5** | Unify `AgyForwardResumeEngine.js` Rendering Definition | `public/js/opendoc/AgyForwardResumeEngine.js` | 100% visual styling and badge parity between Drawer & PDF |
| **Phase 6** | Quality Verification & Cache-Busting Regression Tests | Automated tests, PHP syntax check, view cache clear | Verified against staging branch |

---

## 6. Verification & Quality Assurance Plan

### 6.1 Test Scenarios for Zero Discrepancy & Zero Stale Reads
1. **Scenario A (Direct Cache Copy)**:
   - Recruiter opens candidate drawer for Candidate #101.
   - Recruiter opens Forward Resume modal and selects Candidate #101.
   - **Verification:** Network tab shows **0 network calls** for candidate #101. Load time is **< 10ms**. Match % and badges are 100% identical to drawer.
2. **Scenario B (Drawer Update & Reactive Cache-Busting)**:
   - Candidate #101 has Notice Period changed from 60 days to 15 days in Drawer and saved.
   - Forward Resume modal is opened (or already open).
   - **Verification:** Forward Resume Prescreen Report displays "15 Days Notice" immediately without showing the old "60 Days" stale cache.
3. **Scenario C (Unopened Candidate Selection from Table)**:
   - Recruiter selects Candidate #102 directly on CV Tracker table without opening their drawer.
   - Recruiter clicks "Forward Resume".
   - **Verification:** Batch API fetches canonical dossier in 1 query. Recruiter opens drawer for #102 in another tab — both match % scores and badges are 100% identical.

### 6.2 Strict Branch & Deployment Protocols
- **Git Branch Rule:** Strict commit and push to `origin/staging`. No commits to `main`.
- **PHP Linting:** `php -l` executed on all modified PHP classes.
- **Cache Invalidation:** `php artisan view:clear` and `php artisan route:clear`.
