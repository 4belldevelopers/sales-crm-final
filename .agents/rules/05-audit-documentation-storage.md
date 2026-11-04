# 📑 Rule 05: Mandatory Module-Specific Document Storage & Standard Architecture Pattern

> **Scope**: Always active across all conversations, AI coding assistants (Claude, Cursor, Windsurf, Copilot, Antigravity), and human developers.
> **Target Folder**: `docs/audit documents/<module_or_feature_name>/`
> **Purpose**: Guarantees that every new document generated for audits, feedback, modifications/changelogs, new features, or optimization suggestions is persistently stored in its dedicated module or feature subfolder, following a uniform, easily understandable architectural structure.

---

### Non-Negotiable Directives

1. **Mandatory Module/Feature Named Subfolder (Zero Loose Files)**:
   - Saving documents directly at `docs/` root or directly in `docs/audit documents/` root is **STRICTLY PROHIBITED**.
   - Every document MUST be saved inside its module or feature named directory:
     `docs/audit documents/<module_or_feature_name>/YYYY-MM-DD-<topic>-<type>.md`
   - If a subfolder for that module or feature does not exist yet (e.g. `client_drawer`, `team_invitations`, `company_profile`, `superadmin`), create it immediately.

2. **Standard Document Layout & Pattern**:
   - To ensure that anyone (founder, recruiter, engineer, or future AI) can understand the changes and document instantly, every document MUST include:
     1. **Header Metadata**: Title, Document Type, Module/Feature name, Date, Persona, and Status.
     2. **Executive Summary & Objective**: Problem statement, root-cause analysis, and specific goal.
     3. **Architectural Blueprint & Data Flow**: CQRS 0% SQL read compliance, Elasticsearch indices, client-first memory flow, and Mermaid diagram.
     4. **Detailed Changes / Audit Findings**: File impact table (`File | Action | Purpose`), before/after comparisons, and business-logic safety confirmation.
     5. **15.6" Screen Fit & UI/UX Card+Sub-Card Alignment**: Verification against Rule 11 and Rule 20 (Poppins <= 14px, zero horizontal scroll).
     6. **Verification & Testing Evidence**: `php -l` results, test execution outputs, and tenancy validation.

3. **No Ephemeral-Only Reports**:
   - Do NOT output substantial audit findings, feedback summaries, or new feature proposals solely to transient chat messages or temporary brain artifacts.
   - Always persist a permanent markdown file inside `docs/audit documents/<module_or_feature_name>/`.

4. **Standard File Naming Convention**:
   - Files must strictly follow:
     `docs/audit documents/<module_or_feature_name>/YYYY-MM-DD-<topic>-<type>.md`
   - Common type tags: `-audit.md`, `-feedback.md`, `-changes.md`, `-feature.md`, `-suggestions.md`.

5. **Integration with Audit Skills & Workflows**:
   - When executing the `god-audit` skill or conducting pre-change reviews, output the complete audit deliverable directly into `docs/audit documents/<module_or_feature_name>/` before proceeding with code modifications.
