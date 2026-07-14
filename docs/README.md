# /docs — AI-Friendly Documentation Index

Generated to optimize this repo for AI coding agents (Claude Code, Cursor, Windsurf).
**The auto-loaded entry point is [`../CLAUDE.md`](../CLAUDE.md)** (mirrored by `AGENTS.md`,
`.cursorrules`, `.windsurfrules`). Docs describe the code as analyzed; items are marked
**[INFERRED]** / **[UNCERTAIN]** where not authoritatively confirmed.

## Read order
0. [AI_SETUP_FOR_DEVELOPERS.md](AI_SETUP_FOR_DEVELOPERS.md) — **new devs start here**: where files live + how your AI tool loads them
0b. [AI_KICKOFF_PROMPT.md](AI_KICKOFF_PROMPT.md) — paste-into-any-AI prompt to load the rules + read the schema first
1. [../CLAUDE.md](../CLAUDE.md) — project memory & entry point (start here)
2. [AI_BOOTSTRAP.md](AI_BOOTSTRAP.md) — start-here protocol
3. [PRODUCT_VISION.md](PRODUCT_VISION.md) — what we're building & why (the concept)
4. [ARCHITECTURE.md](ARCHITECTURE.md) — system map & tech stack
5. [GLOSSARY.md](GLOSSARY.md) — domain vocabulary
6. [MODULE_MAP.md](MODULE_MAP.md) — files per module
7. [AI_RULES.md](AI_RULES.md) — hard rules (tenancy, ES sync, no schema guessing)

## Reference
- [DB_SCHEMA.md](DB_SCHEMA.md) — inferred schema (real schema is in the live DB ⚠️)
- [API_CONTRACTS.md](API_CONTRACTS.md) — routes, middleware, DataTables/webhook contracts
- [DATA_FLOW.md](DATA_FLOW.md) — step-by-step workflow traces
- [DEPENDENCY_GRAPH.md](DEPENDENCY_GRAPH.md) — coupling, hotspots, dead code
- [BAD_PATTERNS.md](BAD_PATTERNS.md) — tech-debt register
- [PERFORMANCE_BUDGET.md](PERFORMANCE_BUDGET.md) — targets & bottlenecks
- [DOCUMENTATION_TODO.md](DOCUMENTATION_TODO.md) — where inline docs are most needed
- [VERIFY.md](VERIFY.md) — how to validate changes safely
- [CODE_DOCUMENTATION_STANDARD.md](CODE_DOCUMENTATION_STANDARD.md) — **comment the purpose of every new code** (templates + rules)

## How to execute well (AI performance kit)
- [RECIPES.md](RECIPES.md) — end-to-end cookbooks for common tasks (the speed unlock)
- [AI_GUARDRAILS.md](AI_GUARDRAILS.md) — ground truth, anti-hallucination, pre/post self-checks
- [DEFINITION_OF_DONE.md](DEFINITION_OF_DONE.md) — the single gate every change must pass
- [TESTING.md](TESTING.md) — how to test this app (tenant-isolation, ES mocking, schema:dump)
- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) — common errors → causes → fixes
- [PROMPT_LIBRARY.md](PROMPT_LIBRARY.md) — copy-paste prompts to drive the AI
- [ENFORCEMENT.md](ENFORCEMENT.md) — automated guards/hooks/CI/PHPStan that make the rules unskippable
- [ENTERPRISE_PLAYBOOK.md](ENTERPRISE_PLAYBOOK.md) — GOD-mode strategy: lightning speed, near-zero server load, clean icon-based design, feature velocity + AI operating doctrine
- [adr/](adr/) — architecture decision records

## Product & engineering strategy (architect-level)
- [PRODUCT_VISION.md](PRODUCT_VISION.md) — the concept: pitch, personas, problem, how it works, principles
- [ROADMAP.md](ROADMAP.md) — sequenced build plan (Phase 0 → 6) tying every strategy doc together
- [GLOSSARY.md](GLOSSARY.md) — domain & system vocabulary
- [MARKETPLACE_VISION.md](MARKETPLACE_VISION.md) — talent-trading marketplace: architecture + phased build plan
- [ATS_FEATURES.md](ATS_FEATURES.md) — GOD-level ATS feature roadmap (have vs. build)
- [SCALABILITY.md](SCALABILITY.md) — scaling to 10K companies / 100M+ records/yr without failing
- [UI_UX_STANDARDS.md](UI_UX_STANDARDS.md) — Metronic, ≤14px type scale, fits 15.6" + all laptops, auto-fit data
- [SECURITY.md](SECURITY.md) — security risk register + remediation program
- [BUG_REPORT.md](BUG_REPORT.md) — concrete bugs found (with line refs) + fix plan
- [NAMING_CONVENTIONS.md](NAMING_CONVENTIONS.md) — naming standards + SAFE rename procedure (names map to real tables/indices)

## AI memory & machine-readable context
- [`../CLAUDE.md`](../CLAUDE.md) · `../AGENTS.md` · `../.cursorrules` · `../.windsurfrules` — auto-loaded entry files (one shared brain across tools)
- [`../.ai/PROJECT_MEMORY.md`](../.ai/PROJECT_MEMORY.md) — **living** state, decision log, open questions, gotchas (update this over time)
- `../.ai/summaries/` — one-line-per-class indexes (controllers, services, models, jobs)
- `../.ai/tasks/` — per-module playbooks

## Golden rules (full list in AI_RULES.md)
- Never read 500 KB+ controllers or full route files whole — grep for the method.
- Schema is in the live DB, not migrations — confirm columns, don't invent migrations.
- Eloquent is tenant-scoped by `company_id`; raw `DB::table()` is **not** — filter it.
- Keep MySQL ↔ Elasticsearch in sync (observers/jobs); don't bypass.
- Preserve baked-in typos (`condidate`, `commericial`, `Caompany`) — they map to real names.
- All work goes in `testing.jero.works`, never `hire.jero.works`.
