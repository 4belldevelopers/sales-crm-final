# GOD-LEVEL AUDIT + IMPLEMENTATION
## SERVICE COMPANY → INTERVIEW SCHEDULE → TABLE VIEW

### TARGET

`/service_company/interview-schedule/table-view`

---

# NON-NEGOTIABLE OBJECTIVE

Optimize the existing Interview Schedule Table View to handle:

**1,000 → 5,000 → 10,000 concurrent users**

while preserving:

- 100% existing business logic
- 100% existing business rules
- 100% permissions
- 100% workflows
- existing statuses
- existing validations
- existing database meaning
- existing UI functionality
- existing security model
- existing tenant/company isolation

The goal is:

> **0% AVOIDABLE server/PHP/MySQL/Elasticsearch work.**

Do NOT claim literal zero infrastructure usage.

Authoritative operations will still require server/MySQL work.

---

# GOLDEN PRINCIPLE

## BROWSER FIRST, SERVER ONLY WHEN AUTHORITATIVE

Move work to the browser ONLY when it is:

- safe
- deterministic
- non-authoritative
- based on already-authorized data
- not security-sensitive
- not changing business rules

Keep server-side:

- authentication
- authorization
- tenant isolation
- permission validation
- business validation
- duplicate prevention
- authoritative mutations
- database transactions
- authoritative status changes

---

# ABSOLUTE RULE

## DO NOT CODE FIRST.

First:

1. inspect
2. trace
3. measure
4. identify
5. prove
6. plan
7. implement
8. measure again

Do NOT rewrite working code merely because another architecture looks cleaner.

Do NOT introduce a new library/framework unless the existing implementation cannot safely achieve the requirement.

Do NOT introduce Redis, queues, IndexedDB, Reverb, WebSockets, Elasticsearch, service workers, workers, or other infrastructure merely because they sound scalable.

Use existing infrastructure first.

---

# 1. COMPLETE EXISTING WORKFLOW AUDIT

Trace the complete page:

```text
Browser
↓
Route
↓
Middleware
↓
Controller
↓
Service/Helper
↓
MySQL
↓
Elasticsearch if applicable
↓
Response
↓
Browser
↓
DOM
```

Also trace every mutation:

```text
Browser action
↓
API
↓
Authorization
↓
Validation
↓
MySQL transaction
↓
Commit
↓
Existing event/realtime mechanism
↓
Affected browsers
```

Identify exact files and methods.

---

# 2. FRONTEND AUDIT

Find:

- Blade
- JS
- jQuery
- DataTables
- AJAX
- fetch
- filters
- search
- sort
- pagination
- modals
- status actions
- schedule actions
- reschedule
- cancel
- polling
- timers
- reloads
- WebSocket/Echo/Reverb
- localStorage
- sessionStorage
- IndexedDB

Measure:

- requests/page
- duplicate requests
- payload sizes
- JS execution
- DOM rendering
- DOM node count
- table redraws
- browser CPU
- browser RAM
- long tasks
- memory leaks

---

# 3. HARD BROWSER-RAM RULE

Use browser RAM as the PRIMARY client-side state.

Possible state:

```text
interview schedules currently displayed
filters
search
sort
pagination/UI state
selected rows
modal state
pending mutation state
connection state
sync state
```

Prevent:

- duplicate copies
- unbounded arrays
- memory leaks
- stale closures
- abandoned event listeners
- retained DOM references

Set measured limits.

Do NOT load an unnecessarily huge dataset merely to eliminate server queries.

---

# 4. LOCAL STORAGE RULE

Use localStorage ONLY for small non-authoritative UI state where useful.

Examples:

```text
filters
column preferences
sort preference
page-size preference
```

Never use localStorage as authoritative interview data.

Never store security/authorization decisions there.

Never store huge datasets there.

---

# 5. INDEXEDDB RULE

Do NOT introduce IndexedDB by default.

Only use IndexedDB if measurements prove:

```text
browser RAM is insufficient
AND
repeated navigation benefits materially
AND
network reduction is significant
AND
stale-state reconciliation is reliable
```

If not proven:

> DO NOT IMPLEMENT INDEXEDDB.

Keep the solution simpler.

---

# 6. MYSQL RULE

MySQL remains the authoritative transactional source.

Audit every query.

Find:

- N+1
- duplicate queries
- repeated queries
- SELECT *
- unnecessary joins
- unnecessary COUNT
- repeated EXISTS
- repeated lookups
- queries caused by refresh
- queries caused by DataTables redraws

Optimization order:

```text
1. Remove duplicate queries
2. Remove unnecessary queries
3. Fix N+1
4. Select only required fields
5. Verify indexes
6. Optimize joins
7. Optimize expensive counts
8. Move safe presentation work to browser
```

Do NOT add indexes without query-plan evidence.

Do NOT alter business semantics.

---

# 7. ELASTICSEARCH RULE

Elasticsearch is NOT authoritative.

First determine whether this page actually needs ES.

If ES is not required for this page:

> DO NOT CALL ES.

If ES is required:

- eliminate duplicate requests
- minimize payload
- avoid synchronous unnecessary calls
- preserve existing search behavior

Do not replace MySQL transactional truth with Elasticsearch.

---

# 8. REAL-TIME RULE

First inspect the EXISTING realtime architecture.

If reliable realtime already exists:

> Optimize and reuse it.

Do NOT create another realtime system.

If realtime does not exist:

> Determine whether real-time synchronization is actually required by the existing functionality before introducing WebSocket/Reverb.

Do not introduce infrastructure for theoretical reasons.

---

# 9. 100% CURRENT DATA RULE

Interpret “100% updated data” as:

> The browser must NEVER silently represent stale data as authoritative.

Browser state must have one of:

```text
LIVE
SYNCING
RECONCILIATING
OFFLINE
```

During normal operation:

```text
Server authoritative mutation
↓
Committed state
↓
Minimal event/delta
↓
Affected browser
↓
Targeted local update
```

Do NOT reload the complete table.

---

# 10. WEBSOCKET / EVENT RULE

If existing realtime is used, events must be minimal.

Prefer:

```text
event_id
entity_id
operation
version
changed fields
```

Do not broadcast the complete table.

Do not broadcast unrelated records.

Do not send unnecessary candidate/job datasets.

---

# 11. STALE EVENT PROTECTION

Never allow:

```text
OLD EVENT
```

to overwrite:

```text
NEWER STATE
```

Use the existing authoritative version/timestamp mechanism if one exists.

Do NOT invent a complex versioning system if the current database already provides sufficient ordering information.

---

# 12. DISCONNECT / RECONNECT

When realtime disconnects:

```text
LIVE
↓
DISCONNECTED
↓
SYNCING
↓
RECONNECT
↓
AUTHORITATIVE RECONCILIATION
↓
LIVE
```

Never silently assume that no data changed during disconnection.

If safe delta recovery is possible:

> recover only the missed changes.

Only perform a full refresh if authoritative reconciliation cannot safely determine the missing state.

---

# 13. NO FULL RELOAD RULE

After a mutation:

### DO NOT automatically:

```text
location.reload()
```

or:

```text
full DataTable reload
```

or:

```text
GET entire table again
```

unless measurement and correctness prove it necessary.

Preferred:

```text
Mutation succeeds
↓
authoritative response
↓
update affected local record
↓
targeted DOM update
```

---

# 14. FILTER / SEARCH / SORT

Use client-side processing ONLY when the required authorized dataset is already safely available.

Safe candidates:

```text
search loaded records
sort loaded records
filter loaded records
presentation formatting
row selection
UI grouping
```

Never move to browser:

```text
authorization
tenant isolation
eligibility
business rules
permission checks
duplicate enforcement
database mutations
```

Do NOT download massive datasets just to claim zero server filtering.

---

# 15. TABLE RENDERING

Prefer:

```text
ADD ROW
UPDATE ROW
REMOVE ROW
```

over:

```text
DESTROY TABLE
FETCH EVERYTHING
REBUILD TABLE
```

Prevent:

- excessive DOM nodes
- repeated redraw
- forced layout
- unnecessary reflow
- full-table rerender

The UI must remain responsive.

---

# 16. PAGE FREEZE PROTECTION

The page MUST NOT freeze during:

- search
- filtering
- sorting
- realtime updates
- large response processing
- mutation
- reconnect
- table updates

Measure long tasks.

If browser processing is expensive:

- process incrementally
- avoid unnecessary DOM work
- batch safe DOM updates
- yield to the browser when necessary

Do NOT solve browser CPU problems by moving unnecessary work back to the server.

---

# 17. DOUBLE CLICK / DUPLICATE MUTATION

Test:

```text
double click
rapid click
slow network + retry
browser refresh during request
two tabs
two users
```

Mutation must remain server-authoritative.

Client-side button disabling is UX protection only.

It is NOT duplicate protection.

Server-side duplicate/concurrency rules must remain intact.

---

# 18. TIMEOUT RULE

If mutation times out:

DO NOT blindly retry if the first request may already have succeeded.

Instead:

```text
UNKNOWN RESULT
↓
reconcile with authoritative server
↓
determine final state
↓
update browser
```

Never create duplicate records.

---

# 19. MULTI-USER CONCURRENCY

Test:

```text
User A updates interview
User B updates same interview
```

Preserve the existing business behavior.

Never allow stale browser state to overwrite newer authoritative state.

Use transaction/locking/version checks ONLY where the existing business semantics require them.

---

# 20. FAILURE MATRIX

Test all of:

```text
401
403
419
422
429
500
timeout
offline
slow network
MySQL failure
Elasticsearch failure
WebSocket failure
browser refresh
multiple tabs
duplicate event
out-of-order event
missed event
stale cache
```

Every failure requires:

```text
detection
safe recovery
user feedback
logging
retry only when safe
no duplicate mutation
```

No silent failures.

---

# 21. 10K CONCURRENCY

Do NOT claim 10K support without measurement.

Test:

```text
1K
5K
10K
```

Measure:

```text
API RPS
MySQL QPS
ES QPS
WebSocket connections
events/sec
network bandwidth
PHP CPU
MySQL CPU
ES CPU
browser CPU
browser RAM
p50
p95
p99
error rate
timeouts
reconnect rate
```

Clearly label:

```text
MEASURED
CALCULATED
ASSUMED
```

Never represent calculated or assumed numbers as measured.

---

# 22. REALISTIC USER MODEL

Do NOT simulate 10K users continuously refreshing.

Use realistic behavior:

```text
page open
initial load
idle
search
filter
sort
pagination
view
schedule
reschedule
cancel
status change
realtime event
disconnect
reconnect
```

Measure both normal and peak scenarios.

---

# 23. PHASE IMPLEMENTATION

## PHASE 0 — AUDIT

No code changes.

Deliver:

```text
current architecture
file map
request map
query map
ES map
realtime map
browser performance
network performance
baseline metrics
```

---

## PHASE 1 — BROWSER OPTIMIZATION

Only measured improvements.

Possible:

- duplicate request removal
- duplicate event listener removal
- local state reuse
- client-side filtering
- client-side sorting
- client-side search
- targeted DOM updates
- safe debouncing

No business logic changes.

---

## PHASE 2 — LOCAL STORAGE / RAM

Prefer browser RAM.

Use localStorage only for small UI preferences.

IndexedDB only if proven necessary.

---

## PHASE 3 — MYSQL/API

Optimize only measured bottlenecks.

Every SQL optimization requires:

```text
before
after
EXPLAIN
execution time
rows examined
behavior comparison
```

---

## PHASE 4 — REALTIME

Only if required.

Reuse existing infrastructure.

Implement minimal deltas.

Prevent stale/out-of-order updates.

---

## PHASE 5 — FAILURE / CONCURRENCY

Test:

```text
double click
retry
timeout
two tabs
two users
disconnect
reconnect
stale event
duplicate event
```

---

## PHASE 6 — LOAD TEST

Run:

```text
1K
5K
10K
```

No production claim without evidence.

---

## PHASE 7 — PRODUCTION

```text
Development
↓
QA
↓
Staging
↓
Limited Production
↓
Monitor
↓
Expand
↓
100%
```

Maintain rollback.

---

# 24. CURRENT VS PROPOSED

Produce:

| Metric | Current | Proposed | Reduction |
|---|---:|---:|---:|
| API requests | Actual | Actual/Test | % |
| MySQL queries | Actual | Actual/Test | % |
| ES queries | Actual | Actual/Test | % |
| Full table reloads | Actual | Target | % |
| Realtime events | Actual | Actual/Test | % |
| Payload | Actual | Actual/Test | % |
| Browser CPU | Actual | Actual/Test | % |
| Browser RAM | Actual | Actual/Test | % |
| PHP CPU | Actual | Actual/Test | % |
| MySQL CPU | Actual | Actual/Test | % |
| ES CPU | Actual | Actual/Test | % |
| p95 | Actual | Test | % |
| p99 | Actual | Test | % |

If unavailable:

```text
NOT MEASURED
```

Never invent values.

---

# 25. REQUIRED FILE-LEVEL OUTPUT

Before implementation provide:

```text
File
Method
Current behavior
Current queries
Current requests
Current browser work
Problem
Proposed change
Why required
Risk
Rollback
Validation
Expected measurable improvement
```

---

# 26. HARD NO-OVER-ENGINEERING RULE

If two solutions provide the same result:

> Choose the simpler solution.

Priority:

```text
Existing code improvement
↓
Browser RAM
↓
localStorage for tiny UI state
↓
Existing API optimization
↓
Existing realtime mechanism
↓
IndexedDB only if proven necessary
↓
New infrastructure ONLY if unavoidable
```

Do NOT introduce architecture merely for theoretical future requirements.

---

# 27. FINAL ACCEPTANCE CRITERIA

Before production, verify:

```text
BUSINESS LOGIC CHANGED: NO
BUSINESS RULES CHANGED: NO
PERMISSIONS CHANGED: NO
SECURITY WEAKENED: NO
DATABASE MEANING CHANGED: NO

UNNECESSARY API CALLS REDUCED: YES
UNNECESSARY MYSQL WORK REDUCED: YES
UNNECESSARY ES WORK REDUCED: YES
FULL TABLE RELOADS REMOVED WHERE SAFE: YES
BROWSER PROCESSING OPTIMIZED: YES
PAGE FREEZE: NO
MEMORY LEAK: NO
STALE DATA SILENTLY SHOWN: NO
DUPLICATE MUTATION: NO
MISSED REALTIME EVENTS: RECOVERED
OUT-OF-ORDER EVENTS: PROTECTED
RECONNECT: HANDLED
10K LOAD TEST: PASSED / NOT PASSED
ROLLBACK: READY
```

---

# FINAL COMMAND

**DO NOT OVER-ENGINEER THIS PAGE.**

Do not add technology unless actual code inspection and measurement prove that it is required.

Do not optimize theoretical problems.

Do not rewrite working functionality.

Do not move authoritative business logic to JavaScript.

Do not use browser state for authorization.

Do not claim literal zero infrastructure usage.

Target:

> **ZERO AVOIDABLE SERVER/MYSQL/ELASTICSEARCH WORK.**

The browser should perform the maximum safe amount of:

- filtering
- searching
- sorting
- presentation
- state management
- DOM updates

using browser RAM/localStorage where appropriate.

The server should perform only the authoritative work that cannot safely be performed by the browser.

The final result must be:

> **Simple + measurable + secure + realtime + responsive + concurrency-safe + 10K-tested + fully reversible + zero business-logic change.**
