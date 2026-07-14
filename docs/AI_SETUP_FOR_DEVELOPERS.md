# AI Setup for Developers — Read This First

> For every developer on the team who uses AI to write code (Claude Code, Cursor,
> Windsurf, Copilot, etc.). It explains **where the knowledge files live**, **how your AI
> tool reads them automatically**, and **how to make the AI read the database schema
> before touching the DB**. 5-minute setup, then your AI works the way our team works.

---

## 1. Why this exists
This repo ships with a full **AI knowledge base** so the AI builds code the right way for
*this* project (multi-tenant Laravel ATS + talent marketplace, Elasticsearch read store,
Metronic UI, etc.) instead of guessing. The files are **already in the repo** — you just
need to make sure your tool loads them and that you point the AI at them.

## 2. Where the files live (do NOT move them — locations are what make them auto-load)

```
<repo root>/                                  ← same folder as composer.json / artisan
├── CLAUDE.md            ← auto-loaded by Claude Code (the master entry point)
├── AGENTS.md            ← read by many AI agent tools; mirror of the rules
├── .cursorrules         ← auto-loaded by Cursor
├── .windsurfrules       ← auto-loaded by Windsurf
├── docs/                ← the human-readable knowledge base (start: docs/README.md)
│   ├── README.md            (index of everything)
│   ├── AI_BOOTSTRAP.md      (the 5-step working protocol)
│   ├── AI_RULES.md          (the hard rules)
│   ├── AI_GUARDRAILS.md     (verify-before-acting / anti-hallucination)
│   ├── RECIPES.md           (step-by-step cookbooks for common tasks)
│   ├── DB_SCHEMA.md         (DB guide → points to the real schema file)
│   ├── UI_UX_STANDARDS.md   (Metronic, Poppins, ≤14px, icon-based, NO 3D)
│   └── … (architecture, security, scalability, product vision, etc.)
├── .ai/
│   ├── PROJECT_MEMORY.md     (living decisions / state / open questions)
│   ├── summaries/            (1-line index of controllers/services/models/jobs)
│   └── tasks/                (per-module playbooks)
├── database/schema/
│   └── mysql-schema.sql      ← THE REAL DATABASE STRUCTURE (217 tables) — source of truth
├── scripts/ai-guards.sh      ← blocks bad code (debug calls, env() misuse)
├── .githooks/pre-commit      ← runs the guards before each commit
├── .github/workflows/ci.yml  ← runs the guards in CI on every PR
└── phpstan.neon.dist         ← static analysis config
```

**Rule:** these stay at the repo root / these exact paths. That placement is *why* the AI
tools find them automatically.

## 3. One-time setup per developer (5 min)
After you `git clone` and `cd` into the repo:
```bash
composer install            # restores vendor/ (not in repo)
npm install                 # restores node_modules/ (not in repo)
git config core.hooksPath .githooks   # turns on the quality guard before commits
```
Get the **`.env`** file from the team **securely** (password manager / DM) — it is NOT in
the repo and never should be. `.env.example` shows what keys are needed.

## 4. How each AI tool picks up the rules
| Tool | What it auto-reads | You do |
|---|---|---|
| **Claude Code** | `CLAUDE.md` (automatically, every session) | nothing — it just loads |
| **Cursor** | `.cursorrules` (and `.cursor/rules/` if present) | nothing — it just loads |
| **Windsurf** | `.windsurfrules` | nothing — it just loads |
| **Copilot / others** | not automatic | paste/point it at `CLAUDE.md` + `docs/AI_RULES.md` at the start |

> ⚠️ **Honest expectation:** auto-loading puts the rules in front of the AI every session
> — a very strong nudge — but it does **not** 100% force the AI to obey. The only *hard*
> enforcement is the pre-commit hook + CI (which block specific bad code). So: trust the
> auto-load, **use the prompts below**, and let the hook/CI catch the rest.

## 5. How to make the AI read the schema (and rules) BEFORE doing DB work
The database structure is in **`database/schema/mysql-schema.sql`** (217 tables). The rules
already tell the AI to read it before DB work — but make it explicit in your prompt:

**Copy-paste starter prompt for any task:**
```
Read CLAUDE.md and docs/AI_RULES.md first. For anything touching the database, read
database/schema/mysql-schema.sql to confirm exact tables/columns before writing queries —
do not guess or invent columns. Follow docs/RECIPES.md for the task and satisfy
docs/DEFINITION_OF_DONE.md before saying done.
```
There are ready-made prompts for common tasks in **`docs/PROMPT_LIBRARY.md`** — use them.

## 6. The non-negotiables (the short version — full list in `docs/AI_RULES.md`)
1. Don't read 800 KB controllers whole — grep for the method.
2. **DB truth = `database/schema/mysql-schema.sql`** — read it before DB work; never invent columns/migrations.
3. Tenancy: raw `DB::table()` must filter `company_id` (Eloquent is auto-scoped; raw is not).
4. Keep MySQL ↔ Elasticsearch in sync on writes to indexed entities.
5. Use `config()`, never `env()` outside `config/`.
6. Queue heavy work; match existing patterns; no business-logic changes without sign-off.
7. UI = Metronic, **Poppins**, **max 14px**, clean **icon-based** design — **NO 3D**.
8. **Document the purpose of every new file/class/method** (`docs/CODE_DOCUMENTATION_STANDARD.md`).
9. Build features as tested vertical slices (`docs/RECIPES.md`).
10. Verify changes (`docs/VERIFY.md`); be honest about what you ran.

## 7. Where to start reading (humans)
`docs/README.md` → `CLAUDE.md` → `docs/AI_BOOTSTRAP.md` → `docs/PRODUCT_VISION.md` →
`docs/GLOSSARY.md`. Then the `docs/` and `.ai/tasks/` file for whatever you're building.

## 8. Keeping it current
When the database changes, regenerate the schema file (a developer, on a **local/dev** DB —
never production). When a decision is made, add it to `.ai/PROJECT_MEMORY.md`. When you find
a repeatable pattern, add it to `docs/RECIPES.md`. The repo is the single source of truth —
edit the files here, commit, and everyone (and their AI) gets the update on `git pull`.
