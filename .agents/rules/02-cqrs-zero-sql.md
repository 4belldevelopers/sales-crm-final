# ⚡ Rule 02: CQRS & Zero-SQL Read Mandate

> **Scope**: Always active. Applies to all web controllers, AJAX endpoints, drawers, and UI request paths.
> **Purpose**: Strictly enforces 0% MySQL reads and 100% Elasticsearch read architecture.

---

### Non-Negotiable Directives

1. **Zero SQL Reads on User/Web/API Paths**:
   - `SELECT` queries, Eloquent `all()`, `get()`, `paginate()`, and raw SQL joins are strictly forbidden on web listing and drawer requests.
   - 100% of search queries, filters, counts, pipeline aggregations, and tab badges MUST be queried from **Elasticsearch** (60+ indices).

2. **MySQL is STRICTLY for Writes**:
   - MySQL is reserved exclusively for `INSERT`, `UPDATE`, and `DELETE`.
   - Any write to an indexed entity must sync to Elasticsearch asynchronously (via Laravel Queue Job, Observer, or Outbox Event worker).

3. **Multi-Tenant Scoping (`company_id`)**:
   - Raw queries (`DB::table(...)`) do NOT have automatic global scopes. Whenever writing raw queries, you MUST explicitly include `->where('company_id', $companyId)` or `->where('tenant_id', $tenantId)`.
   - All Redis and client-side cache keys must be partitioned by `company_id`.

4. **Client-First User RAM Fallback**:
   - When data is loaded into the browser, user actions (typing in search bar, switching sub-tabs, clicking column sorts) must be processed **locally inside User RAM or Web Worker** (`cl-data-worker.js`), not by making fresh HTTP requests back to the server.
