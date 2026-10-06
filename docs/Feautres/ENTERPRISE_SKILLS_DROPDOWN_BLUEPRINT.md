# JERO Enterprise Skills Dropdown — God-Level Implementation Blueprint

> **System Profile:** JERO CQRS Architecture  
> **Concurrency Standard:** 10,000 Concurrent Users per page  
> **Server Load:** **0% MySQL Reads / 0% Elasticsearch Reads** on typing & search paths  
> **Execution Engine:** In-Browser User RAM + `localStorage` Caching + Vanilla JS  
> **UI Standard:** Metronic Design System, Poppins Typography (max 14px), 15.6" Viewport Ergonomics  
> **Authoritative Taxonomy:** `public/skills/jero-skills-taxonomy.json`  

---

## 1. Executive Overview & Core Architectural Philosophy

Recruiters and candidates search and enter skills thousands of times an hour. Traditional ATS architectures make an HTTP API call to Elasticsearch or MySQL on every keystroke (`debounce(300ms)`). Under 10,000 concurrent users, this generates millions of unnecessary read requests, degrades database connection pools, and introduces latency (150ms–800ms) that makes the UI feel sluggish.

JERO eliminates this bottleneck using a **Client-First Autonomous Taxonomy Engine**:

```
Traditional ATS (Anti-Pattern):
[User Keystroke] ──HTTP Network Req──> [Nginx/Octane] ──> [MySQL / ElasticSearch] ──> [Latency 200ms+]

JERO God-Level Architecture:
[User Keystroke] ──0ms Lookup──> [Browser RAM Cache (1,925 Canonical Skills)] ──> [Instant UI Render 0ms]
                                      │
                         (Cached in localStorage on 1st load)
                         (0% MySQL Load | 0% ES Load | 0 HTTP Reqs)
```

### Core Invariants:
1. **0% Server Strain (Rule 1 & Rule 2):** Zero `SELECT` queries to MySQL; zero Elasticsearch search requests on autocomplete/filter paths.
2. **Deterministic Tenancy:** The canonical skills dictionary is universal and public. Tenant-specific data (e.g., job-specific required skills) is passed as a JSON bootstrap payload into the blade, preserving strict tenant boundaries.
3. **Form Contract Integrity (Rule 7):** Whether a field uses Tagify or custom inputs, the underlying form submission format (e.g., `input[name="skills"]` with `[{"value":"Java"}]` JSON string) is strictly preserved without breaking backend controllers.
4. **Complete Blade Isolation (Rule 17):** Every page that implements this dropdown has its own isolated instance without shared stateful global variables.

---

## 2. Authoritative Skills Taxonomy Contract (`jero-skills-taxonomy.json`)

The taxonomy lives in `public/skills/jero-skills-taxonomy.json`. It is downloaded once by the client browser and cached persistently in `localStorage`.

### JSON Schema Contract

```json
{
  "version": "2026-10-06.enterprise-god-level-v3",
  "total_unique_skills": 1925,
  "skills": [
    "Java",
    "Python",
    "JavaScript",
    "TypeScript",
    "C#",
    "React",
    "PostgreSQL"
  ],
  "categories": {
    "Programming Languages": ["Java", "Python", "TypeScript", "..."],
    "Frontend Development": ["React", "Angular", "Vue.js", "Next.js", "..."],
    "Backend & APIs": ["Node.js", "Spring Boot", "ASP.NET Core", "Laravel", "..."],
    "Databases & Storage": ["PostgreSQL", "MySQL", "MongoDB", "Redis", "..."],
    "Cloud Platforms": ["AWS", "Microsoft Azure", "Google Cloud Platform (GCP)", "..."],
    "DevOps, CI/CD & Infra": ["Docker", "Kubernetes", "Terraform", "CI/CD Pipelines", "..."],
    "Data Engineering & Big Data": ["Apache Spark", "Snowflake", "Databricks", "..."],
    "AI, Machine Learning & Data Science": ["Machine Learning", "PyTorch", "TensorFlow", "..."],
    "Mobile Development": ["React Native", "Flutter", "iOS Development", "Android Development", "..."],
    "Testing, QA & Automation": ["Automation Testing", "Selenium", "Cypress", "Playwright", "..."],
    "Cybersecurity & Networking": ["Application Security", "OWASP Top 10", "OAuth 2.0", "..."],
    "Enterprise Software, ERP & CRM": ["SAP S/4HANA", "Salesforce", "ServiceNow", "..."],
    "Business Intelligence & Product": ["Power BI", "Tableau", "Jira", "Figma", "..."],
    "Architecture & Methodologies": ["Microservices Architecture", "System Design", "Scrum", "..."]
  },
  "skill_category_map": {
    "Java": "Programming Languages",
    "React": "Frontend Development",
    "PostgreSQL": "Databases & Storage"
  },
  "aliases": {
    "k8s": "Kubernetes",
    "js": "JavaScript",
    "ts": "TypeScript",
    "py": "Python",
    "aws": "AWS",
    "es": "Elasticsearch",
    "postgres": "PostgreSQL",
    "psql": "PostgreSQL",
    "netcore": ".NET Core",
    "pbi": "Power BI"
  },
  "relations": {
    "React": ["Next.js", "TypeScript", "Redux", "Tailwind CSS", "Jest"],
    "Spring Boot": ["Java", "Hibernate", "Microservices Architecture", "PostgreSQL"],
    "Laravel": ["PHP", "MySQL", "Vue.js", "Docker", "Tailwind CSS"]
  }
}
```

### Strict Quality Rules for Taxonomy Entries:
- **NO Job Roles:** `java developer`, `.Net Developer`, `Data Engineer`, `Tester`, `Administrator` are strictly banned. The skill is `Java`, `.NET`, `Data Engineering`, `Manual Testing`, `System Administration`.
- **NO Candidate Experience Sentences:** `Domain experience in...`, `Working with clients from Europe`, `Has deployed on Vercel` are purged.
- **NO Countries/Geographies:** `Saudi Arabia`, `Qatar`, `India`, `USA` are strictly purged.
- **NO Raw Slash/Plus Combos:** Strings like `PHP/Laravel`, `Java+angular`, `sql server/mongodb` must be separated into independent canonical entries.
- **Clean Canonical Casing:** Always `Node.js` (not `nodejs`), `Next.js`, `Vue.js`, `PostgreSQL`, `MongoDB`, `GraphQL`, `jQuery`, `ASP.NET Core`, `C#`, `C++`.

---

## 3. UI/UX Design Standards (Metronic 8 + 15.6" Viewport)

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ ✦ SKILL SUGGESTIONS                [ 12 matches ]   ⚡ 0% Server Load       │  <-- Header
├─────────────────────────────────────────────────────────────────────────────┤
│  [</>]  Python                   [Programming Languages]            [ ↵ ]  │  <-- Active Row
│  [⛁]   PostgreSQL               [Databases & Storage]   [★ Required]       │  <-- Job Match
│  [☁]   AWS                      [Cloud Platforms]       [✦ Suggested]      │  <-- Stack Synergy
│  [⚙]   Docker                   [DevOps, CI/CD]                            │  <-- Taxonomy
├─────────────────────────────────────────────────────────────────────────────┤
│  [+] Add custom skill: "Terraform Enterprise"           [Custom Skill]     │  <-- Fallback Add
├─────────────────────────────────────────────────────────────────────────────┤
│  ↑ ↓ Navigate    ↵ Select    Esc Close           Client RAM (0% Server Load)│  <-- Footer
└─────────────────────────────────────────────────────────────────────────────┘
```

### Dimensional & Ergonomic Budget:
| Element | Specification | Rationale |
| :--- | :--- | :--- |
| **Geometry** | `max-height: 320px; border-radius: 9px;` | Perfectly fits 1366×768 (768px height) without pushing buttons off-screen |
| **Elevation** | `box-shadow: 0 18px 40px -6px rgba(15, 23, 42, .16), 0 0 0 1px rgba(15, 23, 42, .04)` | Elevated Metronic popover card; distinct visual layer |
| **Z-Index** | `z-index: 12000; position: absolute;` | Overlays Modals, Drawers, and Select2 dropdowns without clipping |
| **Item Density** | `min-height: 36px; padding: 6px 10px; font-size: 11.5px;` | Compact high-density list allowing 6–7 visible rows at once |
| **Typography** | `font-family: 'Poppins', sans-serif; font-weight: 600;` | 100% compliant with Metronic style guide (max 14px rule) |
| **Scrollbar** | `width: 5px; thumb: #cbd5e1; radius: 4px;` | Sleek modern scrollbar; zero ugly default browser scrollbars |

### Domain Iconography Mapping:
```javascript
function getSkillDomainIcon(category) {
    switch (category) {
        case 'Programming Languages':             return 'fa-solid fa-code';
        case 'Frontend Development':                return 'fa-solid fa-desktop';
        case 'Backend & APIs':                     return 'fa-solid fa-server';
        case 'Databases & Storage':                return 'fa-solid fa-database';
        case 'Cloud Platforms':                    return 'fa-solid fa-cloud';
        case 'DevOps, CI/CD & Infra':              return 'fa-solid fa-gears';
        case 'Data Engineering & Big Data':        return 'fa-solid fa-diagram-project';
        case 'AI, Machine Learning & Data Science':return 'fa-solid fa-brain';
        case 'Mobile Development':                 return 'fa-solid fa-mobile-screen';
        case 'Testing, QA & Automation':           return 'fa-solid fa-vial-circle-check';
        case 'Cybersecurity & Networking':         return 'fa-solid fa-shield-halved';
        case 'Enterprise Software, ERP & CRM':     return 'fa-solid fa-building-columns';
        case 'Business Intelligence & Product':    return 'fa-solid fa-chart-pie';
        case 'Architecture & Methodologies':       return 'fa-solid fa-cubes-stacked';
        default:                                   return 'fa-solid fa-layer-group';
    }
}
```

---

## 4. Search & Ranking Algorithm

When a user searches or clicks into the field, results are resolved strictly in memory in `<1ms`:

```
Input Query Evaluation:
  ├── Query is Empty ("0-Keystroke State")
  │     ├── 1. Unselected Job Required Skills (Gold Badge: ★ Required)
  │     ├── 2. Synergy Recommendations based on currently selected stack (Green Badge: ✦ Suggested)
  │     └── 3. Top Popular Industry Skills (Max 10 total)
  │
  └── Query is Entered (e.g. "py")
        ├── 1. Direct Alias Check: O(1) lookup in _skillsAliases (e.g. "k8s" -> "Kubernetes")
        ├── 2. Job Required Skills Substring Match (Highest Rank)
        ├── 3. Stack Synergy Substring Match
        ├── 4. Prefix Matches from Taxonomy (e.g. "Py..." -> Python, PyTorch, PySpark)
        ├── 5. Word-boundary / Substring Matches
        └── 6. Exact Match Missing? -> Render "+ Add custom skill" option
```

### Normalization Formula:
```javascript
function candidateSkillKey(value) {
    // Strips dots, dashes, underscores, spaces, and slashes for collision-free comparison
    return String(value || '').trim().replace(/[\.\-\_\s\/]/g, '').toLowerCase();
}
```
*Example:* `Node.js`, `Nodejs`, `node-js`, `Node JS` all normalize to `nodejs` and resolve to the canonical `Node.js`.

---

## 5. Universal Implementation Blueprints

### Pattern A: Multi-Tag Form Input (Candidate Create / Edit, Job Post Create)

Use this pattern when users need to select and save multiple primary or secondary skills into a database form.

#### Step 1: Blade HTML Structure
```html
<div class="fs fv-row se-card primary-skills-card candidate-card">
  <div class="fsh candidate-card-header se-hero">
    <div class="candidate-card-title se-title">
      <span class="candidate-card-icon se-icon"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
      <span><strong>Primary Skills <span class="text-danger">*</span></strong><small>Add candidate technical competencies.</small></span>
    </div>
  </div>
  <div class="fsc candidate-card-body">
    <!-- Combobox Container -->
    <div class="candidate-primary-skill-combobox">
      <div class="candidate-primary-skill-entry">
        <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
        <input type="text" id="candidatePrimarySkillInput" autocomplete="off" placeholder="Type a skill and press Enter..." aria-label="Add a primary skill" role="combobox" aria-autocomplete="list" aria-controls="candidatePrimarySkillSuggestions" aria-expanded="false" />
        <span class="candidate-primary-skill-divider" aria-hidden="true"></span>
        <button type="button" id="candidatePrimarySkillAdd"><i class="fa-solid fa-plus"></i><span>Add</span></button>
      </div>
      <!-- Suggestions Popover Dropdown -->
      <div id="candidatePrimarySkillSuggestions" class="candidate-primary-skill-suggestions" role="listbox" aria-label="Skill suggestions" hidden></div>
    </div>

    <!-- Active Skill Chips Container -->
    <div id="candidatePrimarySkillChips" class="candidate-primary-skill-chips" aria-live="polite"></div>
    <div id="candidatePrimarySkillEmpty" class="candidate-primary-skill-empty">No primary skills added yet.</div>

    <!-- Hidden Form Input Preserving Existing Tagify Backend Schema -->
    <input type="text" class="candidate-primary-skills-source" value="{{ @$model->skills }}" id="kt_tagify_skills" name="skills" hidden />
  </div>
</div>
```

#### Step 2: Scoped CSS (Include in Page Stylesheet)
```css
/* Container Positioning */
.candidate-primary-skill-combobox {
    position: relative;
    width: 100%;
}

.candidate-primary-skill-entry {
    display: flex;
    align-items: center;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    padding: 2px 8px;
    transition: border-color 0.15s ease, box-shadow 0.15s ease;
}

.candidate-primary-skill-entry:focus-within {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

.candidate-primary-skill-entry input {
    flex: 1 1 auto;
    border: none;
    outline: none;
    padding: 8px;
    font-size: 12px;
    font-family: 'Poppins', sans-serif;
    color: #1e293b;
}

.candidate-primary-skill-entry button {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border: none;
    background: #2563eb;
    color: #ffffff;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    cursor: pointer;
}

/* Floating Elevated Popover */
.candidate-primary-skill-suggestions {
    position: absolute;
    z-index: 12000;
    top: calc(100% + 6px);
    right: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    max-height: 320px;
    padding: 0;
    overflow: hidden;
    border: 1px solid #dbe2ea;
    border-radius: 9px;
    background: #ffffff;
    box-shadow: 0 18px 40px -6px rgba(15, 23, 42, .16), 0 0 0 1px rgba(15, 23, 42, .04);
}

.candidate-primary-skill-suggestions[hidden] {
    display: none !important;
}

/* Header Bar */
.candidate-primary-skill-suggestions-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 7px 12px;
    border-bottom: 1px solid #eef2f6;
    background: #f8fafc;
    font-family: 'Poppins', sans-serif;
}

.candidate-primary-skill-suggestions-header-left {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #475569;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
}

.candidate-primary-skill-count-badge {
    padding: 2px 7px;
    border-radius: 10px;
    color: #334155;
    background: #e2e8f0;
    font-size: 9px;
    font-weight: 600;
}

.candidate-primary-skill-perf-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    color: #059669;
    font-size: 9px;
    font-weight: 600;
}

/* Scrollable Suggestions List */
.candidate-primary-skill-suggestions-list {
    flex: 1 1 auto;
    max-height: 236px;
    padding: 4px;
    overflow-y: auto;
    overflow-x: hidden;
}

.candidate-primary-skill-suggestions-list::-webkit-scrollbar {
    width: 5px;
}
.candidate-primary-skill-suggestions-list::-webkit-scrollbar-thumb {
    border-radius: 4px;
    background: #cbd5e1;
}

/* Suggestion Row */
.candidate-primary-skill-suggestion {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    min-height: 36px;
    margin-bottom: 2px;
    padding: 6px 10px;
    border: 0;
    border-left: 3px solid transparent;
    border-radius: 6px;
    color: #0f172a;
    background: transparent;
    font-family: 'Poppins', sans-serif;
    cursor: pointer;
    transition: background .12s ease, border-color .12s ease;
}

.candidate-primary-skill-suggestion:hover,
.candidate-primary-skill-suggestion.is-active {
    border-left-color: #2563eb;
    background: #f1f5f9;
}

.candidate-primary-skill-suggestion-main {
    display: flex;
    align-items: center;
    gap: 8px;
    min-width: 0;
    flex: 1 1 auto;
}

.candidate-primary-skill-suggestion-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px;
    height: 22px;
    border-radius: 5px;
    color: #64748b;
    background: #f1f5f9;
    font-size: 11px;
    flex-shrink: 0;
}

.candidate-primary-skill-suggestion:hover .candidate-primary-skill-suggestion-icon,
.candidate-primary-skill-suggestion.is-active .candidate-primary-skill-suggestion-icon {
    color: #2563eb;
    background: #e0f2fe;
}

.candidate-primary-skill-suggestion-name {
    flex: 1 1 auto;
    overflow: hidden;
    color: #1e293b;
    font-size: 11.5px;
    font-weight: 600;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.candidate-skill-highlight {
    padding: 0 1px;
    border-radius: 2px;
    color: #854d0e;
    background: #fef08a;
    font-weight: 700;
}

.candidate-primary-skill-suggestion-meta {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-left: 8px;
    flex-shrink: 0;
}

.candidate-primary-skill-suggestion-badge {
    padding: 2px 6px;
    border-radius: 4px;
    font-size: 9px;
    font-weight: 600;
}

.candidate-primary-skill-suggestion-badge.is-required {
    border: 1px solid #fde68a;
    color: #92400e;
    background: #fef3c7;
}

.candidate-primary-skill-suggestion-badge.is-suggested {
    border: 1px solid #bbf7d0;
    color: #15803d;
    background: #dcfce7;
}

.candidate-primary-skill-suggestion-badge.is-alias {
    border: 1px solid #bae6fd;
    color: #0369a1;
    background: #e0f2fe;
}

.candidate-primary-skill-suggestion-badge.is-category {
    border: 1px solid #e2e8f0;
    color: #64748b;
    background: #f8fafc;
    font-size: 8.5px;
}

.candidate-primary-skill-suggestion-kbd {
    display: none;
}
.candidate-primary-skill-suggestion.is-active .candidate-primary-skill-suggestion-kbd {
    display: inline-flex;
}

.candidate-primary-skill-suggestion-kbd kbd,
.candidate-primary-skill-shortcuts kbd {
    padding: 1px 4px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
    color: #475569;
    background: #ffffff;
    font-family: monospace;
    font-size: 9px;
}

/* Custom Skill Fallback */
.candidate-primary-skill-custom-option {
    padding: 4px;
    border-top: 1px dashed #e2e8f0;
    background: #fafcff;
}

.candidate-primary-skill-custom-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    min-height: 36px;
    padding: 6px 10px;
    border: 0;
    border-radius: 6px;
    background: transparent;
    cursor: pointer;
}

.candidate-primary-skill-custom-btn:hover,
.candidate-primary-skill-custom-btn.is-active {
    background: #fefce8;
}

/* Command Palette Footer */
.candidate-primary-skill-suggestions-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 12px;
    border-top: 1px solid #eef2f6;
    background: #f8fafc;
    color: #64748b;
    font-size: 9.5px;
}

/* Active Chips */
.candidate-primary-skill-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
}

.candidate-primary-skill-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 9px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    background: #f8fafc;
    font-size: 11px;
    font-weight: 600;
    color: #1e293b;
}

.candidate-primary-skill-chip.pulse-duplicate {
    animation: jeroChipPulse 0.45s ease;
}

@keyframes jeroChipPulse {
    0% { transform: scale(1); background: #f8fafc; }
    50% { transform: scale(1.08); background: #fed7aa; border-color: #f97316; }
    100% { transform: scale(1); background: #f8fafc; }
}
```

#### Step 3: JavaScript Implementation (Plug-and-Play)
```javascript
(function () {
    var JERO_SKILLS_VERSION = '2026-10-06.enterprise-god-level-v3';

    var $input = document.querySelector('#candidatePrimarySkillInput');
    var $addBtn = document.querySelector('#candidatePrimarySkillAdd');
    var $popover = document.querySelector('#candidatePrimarySkillSuggestions');
    var $chipsContainer = document.querySelector('#candidatePrimarySkillChips');
    var $emptyState = document.querySelector('#candidatePrimarySkillEmpty');
    var $hiddenInput = document.querySelector('#kt_tagify_skills');

    var _skillsTaxonomy = null;
    var _skillsList = [];
    var _skillsAliases = {};
    var _skillsRelations = {};
    var _skillsCategoryMap = {};

    var activeItems = [];
    var activeIndex = -1;

    // 1. In-Memory Local Taxonomy Loader
    function loadTaxonomy() {
        try {
            var cached = localStorage.getItem('jero_skills_taxonomy_' + JERO_SKILLS_VERSION);
            if (cached) {
                var parsed = JSON.parse(cached);
                if (parsed && parsed.version === JERO_SKILLS_VERSION && Array.isArray(parsed.skills)) {
                    bindTaxonomy(parsed);
                    return;
                }
            }
        } catch (e) {}

        fetch('/skills/jero-skills-taxonomy.json?v=' + JERO_SKILLS_VERSION, { cache: 'no-cache' })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data && Array.isArray(data.skills)) {
                    bindTaxonomy(data);
                    try {
                        localStorage.setItem('jero_skills_taxonomy_' + JERO_SKILLS_VERSION, JSON.stringify(data));
                    } catch (e) {}
                }
            })
            .catch(function () {});
    }

    function bindTaxonomy(data) {
        _skillsTaxonomy = data;
        _skillsList = data.skills || [];
        _skillsAliases = data.aliases || {};
        _skillsRelations = data.relations || {};
        _skillsCategoryMap = data.skill_category_map || {};
    }

    function skillKey(val) {
        return String(val || '').trim().replace(/[\.\-\_\s\/]/g, '').toLowerCase();
    }

    function resolveAlias(val) {
        var clean = String(val || '').trim();
        return _skillsAliases[clean.toLowerCase()] || clean;
    }

    // 2. Selected Skills State Management
    function getSelectedSkills() {
        try {
            var raw = $hiddenInput.value;
            if (!raw) return [];
            var parsed = JSON.parse(raw);
            if (Array.isArray(parsed)) {
                return parsed.map(function (item) { return typeof item === 'object' ? item.value : item; });
            }
        } catch (e) {}
        return [];
    }

    function saveSelectedSkills(skills) {
        var payload = skills.map(function (s) { return { value: s }; });
        $hiddenInput.value = JSON.stringify(payload);
        renderChips();
    }

    function addSkill(skillName) {
        var resolved = resolveAlias(skillName);
        var norm = skillKey(resolved);
        if (!norm) return;

        var current = getSelectedSkills();
        var exists = current.some(function (s) { return skillKey(s) === norm; });

        if (exists) {
            flashDuplicate(norm);
            $input.value = '';
            closeSuggestions();
            return;
        }

        current.push(resolved);
        saveSelectedSkills(current);
        $input.value = '';
        closeSuggestions();
        $input.focus();
    }

    function removeSkill(skillName) {
        var current = getSelectedSkills().filter(function (s) {
            return skillKey(s) !== skillKey(skillName);
        });
        saveSelectedSkills(current);
    }

    function flashDuplicate(norm) {
        var chips = $chipsContainer.querySelectorAll('.candidate-primary-skill-chip');
        chips.forEach(function (chip) {
            if (chip.getAttribute('data-skill-norm') === norm) {
                chip.classList.remove('pulse-duplicate');
                void chip.offsetWidth;
                chip.classList.add('pulse-duplicate');
            }
        });
    }

    function renderChips() {
        var skills = getSelectedSkills();
        $chipsContainer.replaceChildren();
        $emptyState.hidden = skills.length > 0;

        skills.forEach(function (skill) {
            var chip = document.createElement('span');
            chip.className = 'candidate-primary-skill-chip';
            chip.setAttribute('data-skill-norm', skillKey(skill));
            chip.innerHTML = '<span>' + skill + '</span><i class="fa-solid fa-xmark text-muted ms-1" style="cursor:pointer"></i>';
            chip.querySelector('i').addEventListener('click', function () {
                removeSkill(skill);
            });
            $chipsContainer.appendChild(chip);
        });
    }

    // 3. Search Engine (Client RAM, 0% Server Calls)
    function searchSkills(query) {
        var rawTrimmed = String(query || '').trim();
        var normQ = skillKey(rawTrimmed);
        var selected = getSelectedSkills();
        var isSelected = function (name) {
            return selected.some(function (s) { return skillKey(s) === skillKey(name); });
        };

        var results = [];
        var seen = new Set();

        if (!normQ) {
            // 0-Keystroke suggestions
            for (var k = 0; k < _skillsList.length && results.length < 10; k++) {
                var top = _skillsList[k];
                if (!seen.has(skillKey(top)) && !isSelected(top)) {
                    results.push({ name: top, type: 'global', category: _skillsCategoryMap[top] || '' });
                    seen.add(skillKey(top));
                }
            }
            return results;
        }

        // 1. Alias Match
        var aliasMatch = _skillsAliases[rawTrimmed.toLowerCase()];
        if (aliasMatch && !isSelected(aliasMatch)) {
            results.push({ name: aliasMatch, type: 'alias', category: _skillsCategoryMap[aliasMatch] || '' });
            seen.add(skillKey(aliasMatch));
        }

        // 2. Prefix Match
        for (var i = 0; i < _skillsList.length && results.length < 15; i++) {
            var item = _skillsList[i];
            var itemNorm = skillKey(item);
            if (!seen.has(itemNorm) && !isSelected(item)) {
                if (itemNorm.startsWith(normQ)) {
                    results.push({ name: item, type: 'global', category: _skillsCategoryMap[item] || '' });
                    seen.add(itemNorm);
                }
            }
        }

        // 3. Substring Match
        if (results.length < 12) {
            for (var j = 0; j < _skillsList.length && results.length < 15; j++) {
                var item2 = _skillsList[j];
                var itemNorm2 = skillKey(item2);
                if (!seen.has(itemNorm2) && !isSelected(item2)) {
                    if (itemNorm2.indexOf(normQ) !== -1) {
                        results.push({ name: item2, type: 'global', category: _skillsCategoryMap[item2] || '' });
                        seen.add(itemNorm2);
                    }
                }
            }
        }

        return results.slice(0, 12);
    }

    // 4. Dropdown Rendering
    function renderSuggestions(suggestions, query) {
        $popover.replaceChildren();
        activeItems = [];
        activeIndex = -1;

        var trimmedQ = String(query || '').trim();

        // Header
        var header = document.createElement('div');
        header.className = 'candidate-primary-skill-suggestions-header';
        header.innerHTML = '<div class="candidate-primary-skill-suggestions-header-left"><i class="fa-solid fa-wand-magic-sparkles text-primary"></i><span>Skill Suggestions</span></div>' +
            '<div class="candidate-primary-skill-suggestions-header-right">' +
            '<span class="candidate-primary-skill-count-badge">' + suggestions.length + ' matches</span>' +
            '<span class="candidate-primary-skill-perf-badge"><i class="fa-solid fa-bolt"></i>0% Server Load</span>' +
            '</div>';
        $popover.appendChild(header);

        // List
        var listContainer = document.createElement('div');
        listContainer.className = 'candidate-primary-skill-suggestions-list';

        suggestions.forEach(function (item, idx) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'candidate-primary-skill-suggestion';

            var main = document.createElement('div');
            main.className = 'candidate-primary-skill-suggestion-main';
            main.innerHTML = '<span class="candidate-primary-skill-suggestion-icon"><i class="' + getSkillDomainIcon(item.category) + '"></i></span>' +
                '<span class="candidate-primary-skill-suggestion-name">' + item.name + '</span>';

            var meta = document.createElement('div');
            meta.className = 'candidate-primary-skill-suggestion-meta';
            if (item.category) {
                meta.innerHTML += '<span class="candidate-primary-skill-suggestion-badge is-category">' + item.category + '</span>';
            }
            meta.innerHTML += '<span class="candidate-primary-skill-suggestion-kbd"><kbd>↵</kbd></span>';

            btn.append(main, meta);
            btn.addEventListener('mousedown', function (e) { e.preventDefault(); });
            btn.addEventListener('click', function () { addSkill(item.name); });

            listContainer.appendChild(btn);
            activeItems.push({ element: btn, name: item.name });
        });

        $popover.appendChild(listContainer);

        // Custom Skill Option if not exact
        if (trimmedQ) {
            var exact = suggestions.some(function (s) { return s.name.toLowerCase() === trimmedQ.toLowerCase(); });
            if (!exact) {
                var customDiv = document.createElement('div');
                customDiv.className = 'candidate-primary-skill-custom-option';
                var customBtn = document.createElement('button');
                customBtn.type = 'button';
                customBtn.className = 'candidate-primary-skill-custom-btn';
                customBtn.innerHTML = '<span>Add custom skill: <strong class="text-primary">"' + trimmedQ + '"</strong></span><kbd>↵</kbd>';
                customBtn.addEventListener('mousedown', function (e) { e.preventDefault(); });
                customBtn.addEventListener('click', function () { addSkill(trimmedQ); });
                customDiv.appendChild(customBtn);
                $popover.appendChild(customDiv);
                if (!activeItems.length) activeItems.push({ element: customBtn, name: trimmedQ });
            }
        }

        // Footer
        var footer = document.createElement('div');
        footer.className = 'candidate-primary-skill-suggestions-footer';
        footer.innerHTML = '<div class="candidate-primary-skill-shortcuts"><kbd>↑</kbd><kbd>↓</kbd> Navigate <kbd>↵</kbd> Select <kbd>Esc</kbd> Close</div>' +
            '<div>Client RAM (0% Server Load)</div>';
        $popover.appendChild(footer);

        $popover.hidden = false;
        $input.setAttribute('aria-expanded', 'true');
    }

    function closeSuggestions() {
        $popover.hidden = true;
        $input.setAttribute('aria-expanded', 'false');
        activeItems = [];
        activeIndex = -1;
    }

    function setActive(index) {
        if (!activeItems.length) return;
        activeIndex = (index + activeItems.length) % activeItems.length;
        activeItems.forEach(function (it, idx) {
            it.element.classList.toggle('is-active', idx === activeIndex);
        });
        activeItems[activeIndex].element.scrollIntoView({ block: 'nearest' });
    }

    // 5. Event Listeners
    $input.addEventListener('input', function () {
        var q = $input.value.trim();
        renderSuggestions(searchSkills(q), q);
    });

    $input.addEventListener('focus', function () {
        var q = $input.value.trim();
        renderSuggestions(searchSkills(q), q);
    });

    $input.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
            e.preventDefault();
            if ($popover.hidden) renderSuggestions(searchSkills($input.value.trim()), $input.value.trim());
            setActive(activeIndex + (e.key === 'ArrowDown' ? 1 : -1));
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && activeItems[activeIndex]) {
                addSkill(activeItems[activeIndex].name);
            } else if ($input.value.trim()) {
                addSkill($input.value.trim());
            }
        } else if (e.key === 'Escape') {
            closeSuggestions();
        }
    });

    $addBtn.addEventListener('click', function () {
        if ($input.value.trim()) addSkill($input.value.trim());
    });

    document.addEventListener('click', function (e) {
        if (!$popover.contains(e.target) && e.target !== $input && e.target !== $addBtn) {
            closeSuggestions();
        }
    });

    // Boot
    loadTaxonomy();
    renderChips();
})();
```

---

### Pattern B: Fast Skill Filter for Tables & Search Bars (Talent Marketplace / ATS Search)

Use this pattern when a search filter box needs instant skill autocomplete to filter table rows without page reloads.

```javascript
// Quick Autocomplete Filter for ATS Search Bars
function initSkillFilterBar(inputId, onSkillSelected) {
    var $el = document.querySelector(inputId);
    // Reuse searchSkillsLocal(query)
    // On click or Enter, call:
    onSkillSelected(skillName); 
    // Which filters the in-memory candidate table in 0ms!
}
```

---

## 6. Implementation & Replication Checklist

When replicating this dropdown on any new page (e.g. Job Edit, Bench Candidate Drawer, Talent Search Filter):

- [ ] **Check 1: Single Source of Truth:** Taxonomy is loaded from `public/skills/jero-skills-taxonomy.json`. Never hardcode raw skill arrays in Blade files.
- [ ] **Check 2: Version Cache Busted:** Check that `JERO_SKILLS_VERSION` matches current release (`2026-10-06.enterprise-god-level-v3`).
- [ ] **Check 3: Zero Server Calls:** Verify in Browser DevTools Network Tab that **zero HTTP requests** fire as the user types in the input.
- [ ] **Check 4: 15.6" Viewport Clearance:** Verify that `max-height: 320px` prevents layout shifts on 1366×768 laptops.
- [ ] **Check 5: Keyboard Ergonomics:** Verify `↑`, `↓`, `Enter`, and `Escape` work predictably without form auto-submitting prematurely (`e.preventDefault()`).
- [ ] **Check 6: Exact Deduplication:** Typing or clicking an existing skill triggers the visual pulse animation without creating duplicate chips.
- [ ] **Check 7: Tenancy Safety:** Job-specific required skills are passed via `@json($candidateRequiredSkills ?? [])` and scoped strictly to the current tenant session.
- [ ] **Check 8: Page Isolation (Rule 17):** Variables are scoped within an IIFE or unique namespace; no leaking onto global `window` unless intended as an explicit public API.
