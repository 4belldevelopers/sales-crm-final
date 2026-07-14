# AI Kickoff Prompt — paste this into any AI before it writes code

Use this when your AI tool does **not** auto-load the rules (ChatGPT, Copilot Chat, Gemini,
or any model that doesn't read `CLAUDE.md` automatically). Claude Code, Cursor, and Windsurf
already auto-load `CLAUDE.md` / `.cursorrules` / `.windsurfrules`, but pasting this still
helps. Paste it once at the **start of a session**, after the repo files are in place.

---

## ▶ COPY-PASTE PROMPT (generic, any AI model)

```
You are working in the JERO codebase: a multi-tenant Laravel 11 B2B hiring SaaS
(an ATS + a talent marketplace). This repo contains an AI knowledge base — you MUST use it.

BEFORE writing or changing ANY code, read these files in the repo and follow them:
1. CLAUDE.md                      (project memory + the hard rules)
2. docs/AI_RULES.md               (full rules)
3. docs/AI_BOOTSTRAP.md           (the working protocol)
4. docs/AI_GUARDRAILS.md          (verify before acting; never invent)
5. docs/GLOSSARY.md               (domain terms)
Then, for the area you're touching, read the matching docs/ file and .ai/tasks/*.md,
and use docs/RECIPES.md for step-by-step procedures.

NON-NEGOTIABLE RULES:
- DATABASE: the real structure is database/schema/mysql-schema.sql (217 tables). READ IT to
  confirm exact tables/columns BEFORE writing any query or migration. NEVER invent columns.
  The schema is NOT in Laravel migrations; do not create migrations without explicit sign-off.
- TENANCY: data is multi-tenant by company_id. Eloquent is auto-scoped, but raw DB::table()
  queries are NOT — you must add ->where('company_id', ...) yourself or you leak data.
- SEARCH: Elasticsearch is the read store. Any write to an indexed entity must update the
  matching app/Services/Elastic*Index service and re-index (Observer/Job). Don't bypass it.
- CONFIG: use config(), never env() outside config/ (env() returns null under config:cache).
- PERFORMANCE: queue heavy work (resume parse/upload, mail, bulk, ES reindex). No inline heavy ops.
- BIG FILES: never read 500KB+ controllers or full route files whole — grep for the method.
- UI: follow the Metronic theme, font = Poppins, max font size 14px, clean icon-based design.
  NO 3D / WebGL / Three.js — 2D charts only.
- NAMING: preserve existing spellings even if they look like typos (condidate, commericial,
  Caompany, transection, metronics=Metronic-theme) — they map to real tables/indices.
- DOCUMENTATION: add a short purpose comment/docblock to every new file/class/method
  (see docs/CODE_DOCUMENTATION_STANDARD.md). Undocumented new code = incomplete.
- SCOPE: do not change business logic (ranking/matching, billing, status pipelines) without
  sign-off. Match existing patterns; no unprompted refactors or new architectural layers.

HOW TO WORK:
1. State a short plan and the exact files you will change BEFORE editing.
2. Confirm facts against ground truth (db:table / route:list / the schema file) — don't guess.
3. Build features as vertical slices and satisfy docs/DEFINITION_OF_DONE.md.
4. After changes, verify per docs/VERIFY.md and report exactly what you ran and the result.
5. If anything is uncertain, or a DB-schema / business-logic / cross-tenant change is implied,
   STOP and ask instead of guessing.

Confirm you have read CLAUDE.md and database/schema/mysql-schema.sql before proceeding.
```

---

## Shorter version (for quick tasks / small context windows)
```
Read CLAUDE.md and docs/AI_RULES.md first and follow them. For any DB work, read
database/schema/mysql-schema.sql to confirm exact columns — never invent columns or
migrations. Respect: company_id tenancy on raw queries; keep Elasticsearch in sync;
config() not env(); queue heavy work; UI = Metronic + Poppins + max 14px + icon-based (no 3D);
document every new file/class/method. Plan files first; follow docs/RECIPES.md; verify per
docs/VERIFY.md; ask before schema/business-logic changes.
```

> Tip: for tasks, there are purpose-built prompts in [PROMPT_LIBRARY.md](PROMPT_LIBRARY.md)
> (add feature, fix bug, optimize, review, write tests). Use those for the actual work.
