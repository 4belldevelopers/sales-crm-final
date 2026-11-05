# 🧠 Rule 01: Founder Intent Decoder & Autonomous Expansion

> **Scope**: Always active across all conversations.
> **Purpose**: Decodes the Founder's (Vibhanshu's) shorthand directives into complete God-Level architectural mandates.

When the user provides a brief or shorthand request, the AI MUST automatically interpret and execute it according to this translation matrix without requiring the user to repeat the full constraints:

---

### 1. Translation Matrix

#### Shorthand: `"fix counter"` / `"tab count"` / `"counter accuracy"`
- **Meaning**: Audit and fix tab or KPI counter badges.
- **Enforced Execution**:
  - Counter MUST be backed by Elasticsearch aggregations or in-memory Web Worker array length.
  - ZERO MySQL `SELECT COUNT(*)` on request path.
  - Numbers MUST have CSS `font-variant-numeric: tabular-nums;`.
  - When switching tabs, counter must update in real time without refreshing the page.
  - Format with soft-tint pill badges; if count is 0, render muted state `#94a3b8`.

#### Shorthand: `"in client drawer"` / `"review drawer"` / `"drawer tabs"`
- **Meaning**: Work on the Direct Client or Candidate/Job sliding activity drawers.
- **Enforced Execution**:
  - Persona scope: `service_company` and `admin` only. Never touch `product_company` or `staffing_company`.
  - Responsive fit: Must fit 15.6" screen (1366×768 & 1920×1080) with zero horizontal overflow; drawer body must have internal vertical scrolling (`overflow-y: auto`).
  - Active tab styling: Single active primary blue highlight (`#009ef7`), soft background (`#f1faff`), font-weight 600.
  - Event safety: Retain all existing event listeners on `#kt_activitiesClientjob`, `#kt_activitiesRecruiter`, and Web Worker state buses.

#### Shorthand: `"modern card"` / `"make card subcard"` / `"refactor listing"`
- **Meaning**: Implement Rule 20 (Modern Card + Sub-Card Enterprise Design System).
- **Enforced Execution**:
  - Tier 1: Toolbar card (`#ffffff`, border `#e2e8f0`, radius 10px, 300ms debounced search + clear button).
  - Tier 2: Dedicated sub-tabs card with active blue indicator (`#009ef7`) and live counter pills.
  - Tier 3: Primary card stream with discrete upper sub-cards (Entity info, Comp/Location, Status/Pipeline) and bottom activity strip.
  - Left border status accent: `border-left: 3.5px solid #10b981` (active) or `#cbd5e1` (inactive).
  - Max font 14px Poppins, zero 3D elements, no raw sprawling HTML tables.

#### Shorthand: `"optimize"` / `"slow"` / `"fast search"`
- **Meaning**: 10K concurrency & 0% server read strain mandate.
- **Enforced Execution**:
  - Move sorting, filtering, and tab switching into User RAM (`SessionRAMCache`, Web Worker `cl-data-worker.js`, or IndexedDB).
  - Zero MySQL queries on keystroke search.
  - Debounce keystrokes by 300ms; filter locally in RAM with sub-10ms response time.

#### Shorthand: `"commit and push"`
- **Meaning**: Safe pre-commit verification and staging push.
- **Enforced Execution**:
  - Run `php -l` on all modified `.php` and `.blade.php` files.
  - Run `scripts/ai-guards.sh` (or check for `dd()`, `dump()`, raw `env()`, unescaped `{!! !!}`).
  - Confirm `company_id` tenancy isolation on all modified queries.
  - Stage only intentional files; format conventional commit message.
  - Push strictly to `origin/staging` (NEVER push to `main`).
  - Append the decision to `.ai/PROJECT_MEMORY.md`.

#### Shorthand: `"audit"` / `"review"` / `"feedback"` / `"changes"` / `"suggestions"` / `"new feature"`
- **Meaning**: Execute audit, feedback capture, changelog/refactor documentation, feature design, or suggestions.
- **Enforced Execution**:
  - Store the resulting document permanently in its dedicated module or feature subfolder: `docs/audit documents/<module_or_feature_name>/YYYY-MM-DD-<topic>-<type>.md`. Zero loose files allowed.
  - Structure the document using the standard uniform pattern: Header Metadata, Executive Summary & Objective, Architecture & Data Flow, File Changes Matrix, 15.6" Screen Fit, and Verification Evidence so anyone can immediately understand the changes.
  - Never keep critical review findings solely in ephemeral chat context or memory.

