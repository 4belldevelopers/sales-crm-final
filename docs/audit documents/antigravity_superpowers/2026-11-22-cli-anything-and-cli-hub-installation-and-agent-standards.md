# Agent-Native CLI Architecture & CLI-Anything Integration Standard
## Transforming JERO ATS Subsystems into Token-Efficient, Deterministic Agent Tools

> **Document Type:** Architectural Blueprint & Tooling Integration Spec  
> **Module / Scope:** Global AI Agent Engineering Capabilities (`antigravity_superpowers` & AI Brains)  
> **Platform:** JERO.WORKS — Enterprise B2B Multi-Tenant ATS & Talent Trading Marketplace  
> **Target Concurrency:** 10,000 Concurrent Users per page with 0% Avoidable DB/Server Strain  
> **Date:** November 22, 2026  
> **Status:** Fully Installed, Configured & Live across Workspace and Global Agent Config  

---

## 🏛️ 1. Executive Summary & Core Paradigm

As AI coding agents (Antigravity, Claude Code, Cursor, Codex) take on deep development and verification tasks across JERO's codebase, relying on heavy **Model Context Protocol (MCP)** server schemas or brittle **GUI pixel-clicking ("computer use")** introduces critical inefficiencies:
1. **Context Window Bloat (The MCP Tax):** Ingesting multi-thousand line JSON-RPC tool schemas into every prompt consumes precious context window tokens and degrades reasoning precision.
2. **Execution Latency & Brittleness:** GUI/DOM automation (clicking buttons, waiting for animations, analyzing screenshots) is slow (3–10s per action), token-expensive, and prone to hydration or styling breaks.

To solve this, JERO has integrated the **Agent-Native CLI Standard** via **[HKUDS/CLI-Anything](https://github.com/HKUDS/CLI-Anything)** and **CLI-Hub** (`clianything.cc`).

### The Agent-Native CLI Philosophy
Instead of forcing models to click visual elements or loading sprawling protocol schemas:
- **Unix Philosophy First:** Every system tool or domain capability is encapsulated as a clean, high-performance CLI command.
- **Zero Upfront Context Cost:** Agents discover tools on-demand via lightweight `SKILL.md` manifests or `--help` flags.
- **Machine-Deterministic Execution:** Mandatory `--json` output flags guarantee 100% deterministic parsing by LLMs with structured error handling.
- **Stateful REPL with Undo/Redo:** Complex multi-step operations (e.g. document formatting, batch staging) run in a dedicated REPL session with history tracking.

---

## 🛠️ 2. Installed Assets & System Topology

The following tools and skills have been installed and verified live on the system:

### A. Installed Skills
1. **Workspace Level (`JWMaincode28June2026/.agents/skills/`):**
   - [`.agents/skills/cli-anything/SKILL.md`](../../.agents/skills/cli-anything/SKILL.md): Complete 7-phase generation pipeline (`Analyze`, `Design`, `Implement`, `Plan Tests`, `Write Tests`, `Document`, `Publish`) to scaffold Python Click-based agent-native CLIs.
   - [`.agents/skills/cli-hub-meta-skill/SKILL.md`](../../.agents/skills/cli-hub-meta-skill/SKILL.md): Live catalog discovery engine to search, install, and execute pre-built software CLIs (e.g. LibreOffice, GIMP, FreeCAD, Draw.io) and workflow matrices.
2. **Global Agent Config Level (`~/.gemini/config/skills/` & `~/.agents/skills/`):**
   - Installed globally so all Antigravity and subagent sessions retain access across repositories.

### B. Installed Executables & Runtimes
1. **CLI Hub Executable:**
   - Location: `C:\Users\VIBHANSHU JAISWAL.000\.local\bin\cli-hub.exe`
   - Version: `cli-anything-hub v0.4.1` (Powered by standalone CPython 3.14 via `uv`)
   - Environment: Permanently registered into User `PATH`.
2. **Commands Available:**
   - `cli-hub list` — List all available agent-native CLIs.
   - `cli-hub search <keyword>` — Search available harnesses by capability.
   - `cli-hub install <name>` — Install a CLI harness.
   - `cli-hub matrix list` / `cli-hub can "<intent>"` — Query multi-tool workflow matrices with JSON preflight.

---

## 📊 3. Architectural Comparison: CLI vs. MCP vs. GUI Automation

| Dimension | Agent-Native CLI (`CLI-Anything`) | Model Context Protocol (MCP) | Vision / Computer Use (GUI) |
| :--- | :--- | :--- | :--- |
| **Context Window Overhead** | **0 tokens upfront.** Discovered on-demand via `SKILL.md` or `--help`. | **High (2,000–8,000+ tokens)** injected into every prompt turn. | **Very high (1,500–3,000 tokens)** per screenshot step. |
| **Execution Latency** | **Sub-second (<100ms)** local binary or script execution. | Fast, but constrained by JSON-RPC server daemon roundtrips. | **Slow (3,000–10,000ms)** per visual step and OCR phase. |
| **Parsing Reliability** | **100% Deterministic** via structured `--json` and standard exit codes. | Deterministic structured schema. | **Brittle**; susceptible to resolution, DOM re-renders, and CSS changes. |
| **Model Alignment** | **Native.** Pre-trained on billions of lines of Unix/Bash/Click syntax. | Requires explicit function-calling tool format. | Requires specialized vision fine-tuning. |
| **Composability** | **Infinite.** Unix pipes (`cli1 --json \| jq \| cli2`), bash scripts, CI/CD runners. | Agent orchestration loop required. | Isolated to single desktop session. |

---

## 🎯 4. High-Impact JERO Use Cases

Implementing the Agent-Native CLI Standard directly accelerates JERO's enterprise development and verification:

### Use Case 1: Automated Resume Parser Benchmark Harness (`jero-parser-cli`)
- **Scope:** Rule 23 (`JeroIdentityEngine`). 5-vector identity triangulation, employment chronology resolver, 3-tier confidence gating ($\ge 140$ High, $80\text{--}139$ Medium, $< 80$ Low).
- **CLI Implementation:**
  ```bash
  jero-parser parse resume.pdf --json
  jero-parser benchmark --dataset=gold-standard --json
  ```
- **Benefit:** Allows coding agents to test and verify parser adjustments in 50ms without spinning up a headless browser or manual web UI testing.

### Use Case 2: CQRS & Tenancy Verification Harness (`jero-guard-cli`)
- **Scope:** Rule 1 (0% MySQL reads on web/API paths), Rule 2 (100% Elasticsearch reads), and Rule 9 (Tenant scoping by `company_id`).
- **CLI Implementation:**
  ```bash
  jero-guard cqrs --route=/client/jobs --json
  jero-guard tenancy --file=app/Services/JobService.php --json
  ```
- **Benefit:** Pre-commit AI guardrails instantly detect illegal `SELECT` queries or un-scoped `DB::table()` calls before code touches git.

### Use Case 3: Headless OpenDoc & PDF Engine Diagnostics (`jero-doc-cli`)
- **Scope:** Rule 15 (3 isolated OpenDoc WASM engines: Download Resume Center, Forward Resume, Online Editor).
- **CLI Implementation:**
  ```bash
  jero-doc render --template=executive --candidate=4096 --format=pdf --json
  ```
- **Benefit:** Offline rendering and visual layout validation without launching browser sessions.

### Use Case 4: Elasticsearch Index & Mapping Inspector (`jero-es-cli`)
- **Scope:** Rule 2 & Rule 6 (Universal ES reads, 60+ indices, atomic Redis cache invalidation).
- **CLI Implementation:**
  ```bash
  jero-es inspect-index --name=candidates_index --tenant=52 --json
  jero-es verify-sync --entity=candidate --id=1024 --json
  ```
- **Benefit:** Eliminates tedious manual curl requests to check mapping drift or sync status.

---

## 📜 5. Standards & Coding Rules for Agent-Native CLIs

Whenever creating or refactoring developer CLIs or domain harnesses for JERO, all agents must adhere to the following rules:

1. **Mandatory `--json` Flag:** Every command must output structured, machine-parsable JSON when requested.
2. **Deterministic Exit Codes:**
   - `0`: Success / OK.
   - `1`: Execution failure / exception.
   - `2`: Syntax or parameter usage error.
   - `3`: Partial success / preflight gaps detected.
3. **Accompanying `SKILL.md`:** Every harness must include a concise `SKILL.md` documenting usage, flag matrices, and output schemas so other AI agents can discover and operate it automatically.
4. **Tenant Isolation Guard:** Any CLI interacting with database or Elasticsearch state must require or automatically resolve the tenant context (`company_id`).
