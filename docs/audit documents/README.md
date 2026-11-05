# 📑 Central Audit Documents & Module Archive

> **Mandatory AI & Engineering Storage Standard**:
> All new documentation for **Audits**, **Feedback**, **Code/System Changes**, **New Features/Proposals**, and **Optimization Suggestions** MUST ALWAYS be saved inside their dedicated **Module or Feature Named Folder** inside this directory (`docs/audit documents/<module_or_feature_name>/`).
> **Zero Loose Files Allowed**: Saving documents directly at the root of `docs/` or `docs/audit documents/` without a module/feature subfolder is strictly forbidden.

---

## 🗂️ Module & Feature Subfolder Organization

Every document must be placed into its appropriate module or feature subfolder (create a new folder named after the module or feature if one does not already exist):
`docs/audit documents/<module_or_feature_name>/YYYY-MM-DD-<topic>-<type>.md`

### Existing Module & Feature Directories:
- `superadmin/`: Super Admin portal features, audits, and backend architecture plans.
- `agency_drawer/`: Agency details sliding drawer, commercial terms, and agency cards.
- `client_drawer/`: Direct client sliding drawer, jobs/candidates/interviews tabs.
- `client_onboarding/`: Client creation, wizard components, and validation plans.
- `company_profile/`: Account settings, company details, onboarding tabs, and styling.
- `team_invitations/`: Team member invitations, activation, resend token lifecycle, and outbox events.
- `auth_and_onboarding/`: Unified registration, login, auth guards, and tenant initialization.
- `job_marketplace/`: Agency & superadmin marketplace, publishing workflows, and risk rules.
- `company_intelligence/`: Company intelligence engine, external APIs, and enrichers.
- `clients_tracker/`: Clients tracker listing, exports, and in-RAM filtering.
- `V2_CLIENT_DETAILS/`: Historical client drawer detail tabs documentation.
- `V2_Find_Agency/`: Agency listing audits and corrective reports.
- `V2_SPARTNERLISTING/`: Partner marketplace listing audits.
- `V2_TEAMS/`: Team member invitation and realtime sync plans.
- `Feautres/`: Major cross-module talent architecture specifications.
- *(Any new module or feature should have its own dedicated subfolder created automatically upon document creation)*

---

## 🏷️ Standard Document Pattern & Structure

To ensure consistency and clarity so anyone (engineers, product owners, or AI agents) can immediately understand the context and changes, every new document MUST adhere to this standard layout:

```markdown
# [Module/Feature Name] — [Document Title]

> **Document Type**: [Audit | Feedback | Implementation Plan & Changes | Feature Specification | Suggestion]
> **Module / Feature**: `[e.g., service_company / client_drawer]`
> **Date**: `YYYY-MM-DD`
> **Status**: [Completed | Proposed | In Progress]
> **Author / Persona**: [AI Agent | Engineer | Product Owner]

---

## 1. Executive Summary & Objective
- **Problem Statement**: What challenge, bug, or requirement initiated this document?
- **Root-Cause Analysis** (if audit/bug): Exact failure mechanism or architectural limitation.
- **Goal / Scope**: Desired end state and persona boundaries.

---

## 2. Architectural Blueprint & Data Flow
- **CQRS Read/Write Separation**: 0% MySQL reads verified; 100% Elasticsearch read store indices.
- **Client-First & Concurrency**: In-RAM data flow, Web Workers, and 10K concurrency adherence.
- **Tenancy Scoping**: `company_id` isolation across database, cache, and events.
- **Mermaid Diagram**: Architecture, data flow, or state transition visualization.

---

## 3. Detailed Changes / Audit Findings (The "What" & "Where")
- **File Impact Matrix**:
  | File Path | Action (Create/Modify) | Purpose / Change Description |
  | :--- | :--- | :--- |
  | `path/to/file.php` | Modify | Replaced direct query with ES aggregation |
- **Before / After Comparison**: Exact code or UI diff analysis.
- **Business Logic Protection**: Explicit confirmation of zero business logic mutations.

---

## 4. UI/UX & Responsive Screen Fit (Rule 11 & Rule 20)
- **15.6" Screen Fit**: Responsive behavior across 1366×768 & 1920×1080 (zero horizontal scroll).
- **Modern Card + Sub-Card Compliance**: Tier 1 Toolbar, Tier 2 Sub-Tabs, Tier 3 Cards.
- **Enterprise Typography**: Poppins font <= 14px, `font-variant-numeric: tabular-nums;`.

---

## 5. Verification & Testing Evidence
- **Syntax Checks**: `php -l` output on all modified PHP/Blade files.
- **Verification Scripts**: Automated test execution outputs or scratch verification scripts.
- **Backward-Compatibility Confirmation**: Zero cross-module blast radius.
```

---

## 🏛️ Rule Reference
- **AGENTS.md**: Rule 21 (Mandatory Storage Location & Module Folder Pattern)
- **CLAUDE.md**: Rule 15 & Rule 17 (Mandatory Module Folder Storage & Standard Layout)
- **docs/AI_RULES.md**: Rule 20 (Mandatory Module Folder Pattern)
- **.agents/rules/05-audit-documentation-storage.md**: Rule 05
