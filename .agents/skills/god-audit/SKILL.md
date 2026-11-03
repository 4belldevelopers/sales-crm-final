---
name: god-audit
description: Forensic God-Level architecture, UI/UX, performance, and multi-tenant security audit for JERO modules.
---

# 🏛️ God-Level Audit Protocol

This skill executes a forensic, multi-dimensional audit of any JERO view, controller, drawer, or listing screen before code modifications occur.

## Audit Checklist & Non-Negotiables

### 1. CQRS 100% Read Compliance
- 0% MySQL reads on web/API request paths.
- All listing, search, filtering, and tab counts must query Elasticsearch.
- Write paths (Add/Edit/Delete) must go to MySQL and sync to ES asynchronously via Jobs or Outbox events.

### 2. 10K Concurrency & Client-First Execution
- User RAM, IndexedDB, and Web Workers must handle in-memory sorting, keystroke filtering, and formatting.
- Client state must be isolated and resilient against main-thread UI freezes (>50ms long tasks forbidden).
- Document exports (PDF, CSV) must run in-browser (e.g. `pdf-lib`, WASM) with zero server compute strain.

### 3. Multi-Tenant Scoping & Security
- Every raw query (`DB::table(...)`) must be scoped by `company_id`.
- Tenant cache keys in Redis/Dragonfly and client memory must be tagged with `company_id`.
- Check for unescaped `{!! !!}` statements in Blade templates on untrusted inputs.

### 4. UI/UX Modern Card + Sub-Card Enterprise Standard (Rule 20)
- Verify compliance with 3-tier layout:
  - **Tier 1**: Pure white toolbar card (`#ffffff`, border `#e2e8f0`, radius 10px) with 300ms debounced search & clear button.
  - **Tier 2**: Pure white sub-tabs card (`#ffffff`, border `#e2e8f0`, radius 10px) with single active primary blue highlight (`#009ef7`).
  - **Tier 3**: Primary card stream with discrete upper sub-cards and bottom activity strip.
- Strict 15.6" screen fit (`1366×768` and `1920×1080`) with 0 horizontal page scroll.
- Poppins font, max 14px, tabular numerals (`font-variant-numeric: tabular-nums;`).

## Deliverable Format
Always persist the complete audit findings into a markdown document inside `docs/audit documents/` (`docs/audit documents/YYYY-MM-DD-<module>-audit.md`) and mirror as a **Brain Artifact** containing:
1. Executive Summary & Root-Cause Analysis.
2. Violation Matrix (File : Line, Severity, Impact).
3. Mermaid Data-Flow Architecture Diagram.
4. Step-by-Step Remediation Plan with `RequestFeedback: true`.
5. Permanent repository file reference link to `docs/audit documents/`.
