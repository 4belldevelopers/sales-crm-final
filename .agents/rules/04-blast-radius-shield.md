# 🛡️ Rule 04: Blast-Radius & Zero-Regression Shield

> **Scope**: Always active across all file edits, refactors, and feature additions.
> **Purpose**: Prevents regressions, preserves frozen personas, and protects shared state across sessions.

---

### Non-Negotiable Directives

1. **Persona Code Freeze Mandate**:
   - Files under `resources/views/product_company/` and `resources/views/staffing_company/` (and their respective controllers/models) are **STRICTLY FROZEN**.
   - NEVER edit, refactor, or touch these paths unless explicitly instructed.
   - Development is strictly scoped to **Service Company** (`resources/views/service_company/`) and **Admin** (`resources/views/admin/` & `resources/views/super-admin/`).

2. **Complete Page/Blade Anti-Entanglement**:
   - Never share stateful JavaScript controllers, drawer state objects, or coupled scripts across different pages (e.g., CV Tracker vs App Tracker vs Client Drawer).
   - Edits to one drawer or listing must never break or alter another page.

3. **Event Listener Preservation & DOM Safety**:
   - Before modifying any drawer or AJAX container (e.g., `#cl_client_list_cards`, `#kt_activitiesClientjob`), verify what event handlers are bound (`click`, `submit`, `input`).
   - Do NOT wipe out parent container IDs or wrapper classes that external event delegates rely on.

4. **Zero Unannounced Business Logic Alterations**:
   - Status workflows, pipeline stages, scoring, fee calculations, and coin balances must remain 100% untouched during UI/UX refactors.
