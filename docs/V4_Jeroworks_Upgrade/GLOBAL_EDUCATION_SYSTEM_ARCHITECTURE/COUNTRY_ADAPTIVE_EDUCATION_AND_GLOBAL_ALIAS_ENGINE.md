# Country-Adaptive Education & Global Alias Engine
## Dual-Scope Recruiter Experience, 195+ Nationality Standard & Browser-RAM Search Architecture

---

| Metadata | Details |
| :--- | :--- |
| **System** | JERO ATS & Talent Marketplace |
| **Module** | Add Bench Talent (`resources/views/service_company/bench-candidates/create.blade.php`) |
| **Subsystems** | Client Resume Parser (`jero-bench-parser.blade.php`), Form Styles (`_form_styles.blade.php`) |
| **Architectural Standard** | CQRS + Client-First / Browser Execution (User RAM + WASM) |
| **Performance Target** | 10,000 Concurrent Users, 0% MySQL Read Strain, < 10ms Search Latency |
| **Date** | September 2026 |
| **Status** | Architectural Specification & Implementation Blueprint |

---

## 1. Executive Summary & Design Paradigm

### 1.1 The Challenge
In a multi-tenant global recruitment SaaS, managing educational qualifications across international borders poses a classic UX dilemma:
1. **The "Unbounded List" Failure:** A single dropdown containing hundreds of degrees worldwide (B.Tech, B.S., HND, Diplom, Baccalauréat, Licenciatura, etc.) overwhelms recruiters with irrelevant choices and excessive scrolling.
2. **The "Rigid Nationality Gate" Failure:** Forcing education to strictly match the candidate's nationality breaks real-world recruiting: an Indian engineer holding an M.S. from Stanford (USA) or a Canadian executive holding an Oxford MBA (UK) cannot select their actual degree if their country dropdown restricts them to native credentials.

### 1.2 The Innovation: Dual-Scope Adaptive Education Engine
This specification formalizes the **Dual-Scope Adaptive Architecture**:
* **Scope 1 (Default / Idle State — Curated Country Focus):** When a recruiter selects a candidate's `Nationality` (e.g., `Indian`), the `Highest Qualification` dropdown immediately displays a tight, curated, highly relevant list of native credentials (e.g., *B.Tech, B.E., BCA, MCA, M.Tech, MBA, CA, MBBS*). Recruiter cognitive load drops to zero for 90% of candidates.
* **Scope 2 (Active / Typing State — Global Search Escape Hatch):** If the candidate completed education abroad, the recruiter does not need to change or fake the candidate's nationality. The moment the recruiter types $\ge 1$ characters into the search bar (e.g., `"MS"` or `"Stanford"` or `"BSc"`), the dropdown **instantly unlocks the entire worldwide education taxonomy and synonym aliases in browser memory**.

```
┌───────────────────────────────────────────────────────────────────────────────┐
│                           RECRUITER WORKFLOW                                  │
├───────────────────────────────────────────────────────────────────────────────┤
│                                                                               │
│  Step 1: Recruiter Selects Candidate Nationality                              │
│  [ Nationality: 🇮🇳 Indian ▼ ]                                                │
│         │                                                                     │
│         ▼                                                                     │
│  Step 2: Highest Qualification Dropdown Adapts (Idle State)                   │
│  ┌─────────────────────────────────────────────────────────────────────────┐  │
│  │ 🇮🇳 Indian Standard Qualifications (Curated & Familiar)                   │  │
│  │   • B.Tech / B.E (Engineering & Technology)                             │  │
│  │   • BCA (Computer Applications)                                         │  │
│  │   • MCA (Master of Computer Applications)                               │  │
│  │   • M.Tech / M.E (Postgraduate Engineering)                             │  │
│  │   • MBA / PGDM (Business Administration)                                │  │
│  │   • CA / CS / ICWA (Accounting & Corporate Law)                         │  │
│  └─────────────────────────────────────────────────────────────────────────┘  │
│         │                                                                     │
│         ▼ (Candidate studied abroad in USA / UK / Germany?)                   │
│  Step 3: Recruiter Types into Search Input: "MS"                              │
│  ┌─────────────────────────────────────────────────────────────────────────┐  │
│  │ 🌐 Global Search Results for "MS" (Full Worldwide Taxonomy Active)     │  │
│  │   • M.S. / M.Sc (Science & Computing) — [North America / Global]       │  │
│  │   • MS / Master of Surgery — [India / Commonwealth]                    │  │
│  │   • M.Eng (Master of Engineering) — [UK / Canada / Australia]          │  │
│  └─────────────────────────────────────────────────────────────────────────┘  │
└───────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Global Nationality Standard (195+ Demonyms & Top Hubs)

### 2.1 The Current Gap
Currently, `create.blade.php` (line 627–634) has only **4 hardcoded options**: `Indian`, `American`, `British`, `Other`. This excludes over 98% of the world's population and forces global recruiters hiring from Canada, Australia, Germany, UAE, Singapore, France, etc., into a generic `"Other"`.

### 2.2 Database Compatibility (Rule 8 & 9)
In `database/schema/mysql-schema.sql`:
```sql
`nationality` varchar(255) DEFAULT NULL
```
The column is an open `varchar(255)`—**not** an `ENUM`. Any standard ISO demonym string can be stored directly with **zero database migrations**, zero schema regressions, and 100% Elasticsearch compatibility.

### 2.3 The 195+ Demonym Taxonomy
The dropdown will be structured into two distinct in-memory optgroups:

#### A. ⭐ Frequent Global Talent Hubs (Pinned Top Group)
* **Indian** (India)
* **American** (United States)
* **British** (United Kingdom)
* **Canadian** (Canada)
* **Australian** (Australia)
* **Emirati** (United Arab Emirates)
* **Singaporean** (Singapore)
* **German** (Germany)
* **French** (France)
* **Irish** (Ireland)
* **Dutch** (Netherlands)
* **Saudi** (Saudi Arabia)
* **Other / Not Disclosed**

#### B. 🌐 All Sovereign Demonyms (Alphabetical A–Z Group — 195 Nations)
Afghan, Albanian, Algerian, Andorran, Angolan, Argentine, Armenian, Australian, Austrian, Azerbaijani, Bahamian, Bahraini, Bangladeshi, Barbadian, Belarusian, Belgian, Belizean, Beninese, Bhutanese, Bolivian, Bosnian, Brazilian, British, Bruneian, Bulgarian, Burkinabe, Burmese, Burundian, Cambodian, Cameroonian, Canadian, Cape Verdean, Central African, Chadian, Chilean, Chinese, Colombian, Comorian, Congolese, Costa Rican, Croatian, Cuban, Cypriot, Czech, Danish, Djiboutian, Dominican, Dutch, East Timorese, Ecuadorean, Egyptian, Emirati, Equatorial Guinean, Eritrean, Estonian, Ethiopian, Fijian, Filipino, Finnish, French, Gabonese, Gambian, Georgian, German, Ghanaian, Greek, Grenadian, Guatemalan, Guinean, Guyanese, Haitian, Honduran, Hungarian, Icelander, Indian, Indonesian, Iranian, Iraqi, Irish, Israeli, Italian, Ivorian, Jamaican, Japanese, Jordanian, Kazakh, Kenyan, Kuwaiti, Kyrgyz, Laotian, Latvian, Lebanese, Liberian, Libyan, Liechtenstein citizen, Lithuanian, Luxembourgish, Macedonian, Malagasy, Malawian, Malaysian, Maldivian, Malian, Maltese, Marshallese, Mauritanian, Mauritian, Mexican, Micronesian, Moldovan, Monegasque, Mongolian, Montenegrin, Moroccan, Mozambican, Namibian, Nauruan, Nepalese, New Zealander, Nicaraguan, Nigerian, Nigerien, North Korean, Norwegian, Omani, Pakistani, Palauan, Palestinian, Panamanian, Papua New Guinean, Paraguayan, Peruvian, Polish, Portuguese, Qatari, Romanian, Russian, Rwandan, Saint Lucian, Salvadoran, Samoan, San Marinese, Sao Tomean, Saudi, Senegalese, Serbian, Seychellois, Sierra Leonean, Singaporean, Slovak, Slovenian, Solomon Islander, Somali, South African, South Korean, South Sudanese, Spanish, Sri Lankan, Sudanese, Surinamese, Swazi, Swedish, Swiss, Syrian, Taiwanese, Tajik, Tanzanian, Thai, Togolese, Tongan, Trinidadian, Tunisian, Turkish, Turkmen, Tuvaluan, Ugandan, Ukrainian, Uruguayan, Uzbek, Vanuatu, Vatican citizen, Venezuelan, Vietnamese, Yemeni, Zambian, Zimbabwean.

---

## 3. Country-Curated Education Scopes (Idle Dropdown View)

When a recruiter opens the `Highest Qualification` dropdown without typing, the options rendered are scoped to the candidate's selected nationality, organized by UNESCO ISCED levels.

### 3.1 🇮🇳 India & South Asia Curated Catalog
* **Undergraduate (ISCED 6):**
  * `13`: B.Tech / B.E (Engineering & Technology)
  * `16`: BCA (Computer Applications)
  * `5`: BBA / BMS (Business Administration)
  * `6`: B.Com (Commerce & Finance)
  * `12`: B.Sc (Science & Computing)
  * `3`: B.A (Arts & Humanities)
  * `4`: B.Arch (Architecture)
  * `11`: B.Pharma (Pharmacy)
* **Postgraduate (ISCED 7):**
  * `40`: M.Tech / M.E (Engineering & Technology)
  * `42`: MCA (Computer Applications)
  * `41`: MBA / PGDM (Business Administration)
  * `39`: M.Sc (Science & Computing)
  * `35`: M.Com (Commerce & Finance)
  * `32`: M.A (Arts & Humanities)
* **Doctorate (ISCED 8):**
  * `52`: Ph.D. / Doctorate
* **Diplomas & Professional Certifications:**
  * `22`: Diploma / Polytechnic (Technical)
  * `48`: PG Diploma (Post Graduate Diploma)
  * `26`: CA / CPA (Chartered Accounting)
  * `27`: CS (Company Secretary)
  * `29`: ICWA / CMA (Cost Accounting)
  * `24`: MBBS (Medicine & Surgery)
  * `46`: MD / MS (Doctor of Medicine / Surgery)
  * `23`: LLB (Law Degree)
  * `31`: LLM (Master of Laws)
* **Secondary:**
  * `2`: 12th Standard / Intermediate / HSC
  * `1`: 10th Standard / SSC / Matriculation

### 3.2 🇺🇸 North America (USA & Canada) Curated Catalog
* **Undergraduate (ISCED 6):**
  * `12`: B.S. / B.Sc (Bachelor of Science / Computing)
  * `3`: B.A (Bachelor of Arts / Humanities)
  * `13`: B.Eng / B.S.E (Bachelor of Engineering)
  * `5`: BBA / B.Com (Business Administration)
* **Postgraduate (ISCED 7):**
  * `39`: M.S. (Master of Science / Computing)
  * `41`: MBA (Master of Business Administration)
  * `32`: M.A (Master of Arts)
  * `40`: M.Eng (Master of Engineering)
* **Doctorate & Professional Doctorates (ISCED 8):**
  * `52`: Ph.D. (Doctor of Philosophy)
  * `23`: J.D. (Juris Doctor — Law)
  * `24`: M.D. (Doctor of Medicine)
* **Associate Degrees & Diplomas (ISCED 5):**
  * `22`: Associate Degree (A.A. / A.S. / A.A.S.)
  * `48`: Post-Degree Professional Diploma
* **Secondary:**
  * `2`: High School Diploma / GED

### 3.3 🇬🇧 United Kingdom & Commonwealth Curated Catalog
* **Undergraduate (ISCED 6):**
  * `12`: BSc / BSc (Hons) (Science & Computing)
  * `13`: BEng / BEng (Hons) (Engineering)
  * `3`: BA / BA (Hons) (Arts & Humanities)
  * `5`: BBA / BSc Management
* **Postgraduate (ISCED 7):**
  * `39`: MSc (Master of Science)
  * `40`: MEng (Master of Engineering — Integrated)
  * `41`: MBA (Business Administration)
  * `32`: MA (Master of Arts)
* **Doctorate (ISCED 8):**
  * `52`: Ph.D. / DPhil (Doctor of Philosophy)
* **Higher Technical & Vocational (ISCED 5):**
  * `25`: HND / HNC (Higher National Diploma / Certificate)
  * `22`: Foundation Degree / DipHE
* **Secondary:**
  * `49`: A-Levels (General Certificate of Education Advanced)
  * `51`: GCSE / O-Levels

### 3.4 🇪🇺 European Union (Bologna Framework) Curated Catalog
* **Cycle 1 — Bachelor (ISCED 6):**
  * `12`: Bachelor of Science / Licence (Science/IT)
  * `3`: Bachelor of Arts / Licence (Arts/Humanities)
  * `13`: Bachelor of Engineering
* **Cycle 2 — Master (ISCED 7):**
  * `39`: Master of Science / Master 2 (M2)
  * `40`: Master of Engineering / Diplom-Ingenieur
  * `41`: Master of Business Administration
  * `30`: Integrated Master (Bologna Dual Degree)
* **Cycle 3 — Doctorate (ISCED 8):**
  * `52`: Ph.D. / Doctorat / Promotion
* **Secondary & Baccalaureate:**
  * `2`: European Baccalaureate / Abitur / Matura / Baccalauréat

### 3.5 🌐 Universal / Unselected / "Other" Nationality Fallback
When no nationality is chosen yet, or when `"Other"` is selected, the dropdown renders the **Universal 7-Category Global Catalog** (UNESCO ISCED-2011 compliant), ensuring no recruiter is ever stuck.

---

## 4. The Global Search Escape-Hatch & Synonym Alias Engine

### 4.1 Client-First In-Memory Data Model (User RAM / 0% Server Strain)
To satisfy **Rule 1** (0% MySQL reads) and **Rule 3** (Browser RAM first), the entire qualification database and its aliases are embedded as a lightweight JSON constant in the blade template:

```javascript
const JERO_GLOBAL_QUALIFICATIONS = [
  {
    id: 13,
    canonical: "B.Tech / B.E (Engineering & Technology)",
    level: "Bachelor",
    category: "undergraduate",
    regions: ["Indian", "American", "British", "Global"],
    aliases: ["btech", "b.tech", "be", "b.e", "bachelor of technology", "bachelor of engineering", "beng", "b tech"]
  },
  {
    id: 39,
    canonical: "M.S. / M.Sc (Science & Computing)",
    level: "Master",
    category: "postgraduate",
    regions: ["American", "British", "European", "Global"],
    aliases: ["ms", "m.s.", "msc", "m.sc", "master of science", "master science", "ms cs", "ms in computer science"]
  },
  {
    id: 41,
    canonical: "MBA / PGDM (Business Administration)",
    level: "Master",
    category: "postgraduate",
    regions: ["Indian", "American", "British", "Global"],
    aliases: ["mba", "pgdm", "master of business administration", "m.b.a."]
  },
  {
    id: 26,
    canonical: "CA / CPA (Chartered Accounting)",
    level: "Professional",
    category: "professional",
    regions: ["Indian", "American", "British", "Global"],
    aliases: ["ca", "cpa", "chartered accountant", "chartered accounting", "certified public accountant"]
  }
  // ... complete 60 canonical records mapped
];
```
* **Payload footprint:** Approximately **18 KB uncompressed** (less than a single small icon).
* **Search Execution Time:** `< 2 milliseconds` in V8/SpiderMonkey browser RAM.

### 4.2 Dynamic Search Scope Expansion
When the recruiter opens the dropdown:
1. **Search Query Length == 0 (Idle):**
   * Filter dataset where `item.regions.includes(selectedNationality) || item.regions.includes("Global")`.
   * Render in clean ISCED `<optgroup>`s.
2. **Search Query Length $\ge 1$ (Active Search):**
   * **Scope expands to 100% of `JERO_GLOBAL_QUALIFICATIONS`**.
   * Normalize search string: lowercase, remove punctuation (`.` and `/`).
   * Perform fuzzy substring match against `item.canonical` AND `item.aliases`.
   * Render matches with their global region indicator (e.g. `M.S. / M.Sc — [USA/Global]`).

---

## 5. UI/UX Flow & Layout Specification

### 5.1 Field Layout Reordering
In `create.blade.php`, the candidate profile grid currently displays:
```html
<!-- Old Order -->
[ Current Location ] | [ Highest Qualification ] | [ Nationality ]
```
Because `Nationality` governs the default view of `Highest Qualification`, the columns are reordered to follow the logical and visual Left-to-Right sequence:
```html
<!-- New Adaptive Order -->
[ Current Location * ] | [ Nationality * ] | [ Highest Qualification * ]
```

### 5.2 Responsive & 15.6" Screen Constraints (Rule 14)
* **Maximum Font Size:** 13px / 14px in Poppins.
* **Icon-Based Design:** Clean `<i class="fa-regular fa-flag"></i>` and `<i class="fa-solid fa-graduation-cap"></i>` glyphs.
* **Zero 3D Elements:** Strict flat Metronic design system.
* **Dropdown Viewport:** Dropdown container height capped at `280px` with custom internal scrollbars to prevent overflowing 1366×768 and 1920×1080 screens.

---

## 6. Resume Parser Integration (Client WASM Protocol)

In `jero-bench-parser.blade.php`, candidates uploaded via PDF/DOCX resume are parsed entirely in User RAM.

```
┌────────────────────────────────────────────────────────┐
│                   RESUME PARSER PIPELINE               │
├────────────────────────────────────────────────────────┤
│ 1. Resume Text Ingestion (PDF.js / Mammoth in User RAM)│
│                            │                           │
│ 2. Nationality Auto-Detection                          │
│    • Matches international dialing code (+91 → Indian, │
│      +1 → American, +44 → British)                     │
│    • Scans Demonym & Country keywords in header        │
│    • Automatically triggers:                           │
│      $('#nationality').val(detectedNat).trigger('change│
│                            │                           │
│ 3. Qualification Matcher (Via Alias Engine)            │
│    • Normalizes degree strings ("B.Tech in IT" → 13)   │
│    • Checks canonical ID in JERO_GLOBAL_QUALIFICATIONS │
│    • Selects canonical ID in $('#qualification')       │
│    • Preserves selection regardless of country scope   │
└────────────────────────────────────────────────────────┘
```

---

## 7. Tenancy, Elasticsearch & Verification Guarantees

### 7.1 Multi-Tenant Isolation (Rules 6 & 17)
* All blade markup, JavaScript search filters, and style overrides are strictly scoped to the `resources/views/service_company/bench-candidates/create.blade.php` template.
* No shared asset bundles, vendor files, or other recruitment modules are touched.

### 7.2 Database Invariance (Rule 1 & 8)
* `nationality`: Handled as standard string in `ServiceCompanyBenchCandidatesController.php`.
* `qualification`: Handled as standard foreign key `id` (`int(10)`) matching `qualifications.id`.
* Zero schema migrations, zero `ALTER TABLE`, zero raw SQL joins.

### 7.3 Elasticsearch Sync (Rule 2 & 10)
* When a bench candidate is saved, `GeneralMethod.php` and `ProcessCandidateUpdateJob.php` index `nationality` and `qualification` seamlessly.
* Zero mutations to Elasticsearch mapping contracts.

---

## 8. Implementation Verification Checklist

| Test Scenario | Expected Outcome | Verification Method |
| :--- | :--- | :--- |
| **Recruiter selects `Indian`** | Qualification dropdown shows Indian degrees (*B.Tech, BCA, MCA, CA, MBBS*). | Manual browser test & DOM inspection. |
| **Recruiter types `"MS"` while `Indian` is selected** | Dropdown immediately unlocks and displays `M.S. / M.Sc (Science & Computing)` with ID 39. | In-memory search verification. |
| **Recruiter selects `American`** | Dropdown displays American degrees (*B.S., B.A., M.S., MBA, Associate Degree, J.D.*). | Option list validation. |
| **Candidate from Germany (`German`)** | Recruiter selects `German` from 195+ list; dropdown renders Bologna / European degrees. | Full demonym test. |
| **Resume Parser Upload** | PDF containing `+91` and `Bachelor of Technology` auto-selects `Indian` and `B.Tech` with zero clicks. | Drag-and-drop test. |
| **Form Submission & Save** | Bench candidate record saves with correct `nationality` and `qualification_id`; redirects with success. | DB verify & ES verify. |
| **PHP Syntax Check** | Zero lint errors across modified blade templates. | `php -l <file>` |

---

*Authored for JERO ATS by Antigravity Engineering.*
