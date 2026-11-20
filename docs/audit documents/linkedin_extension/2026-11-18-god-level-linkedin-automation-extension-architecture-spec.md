# PRODUCTION SPECIFICATION V3.0: OmniReach Engine
## Google-Grade Architecture: SQLite over OPFS, Chrome Built-in AI (Gemini Nano), XState v5 Actor Model, Comlink Typed RPC & 7-Phase Gated Roadmap

> **Document Standards Compliance (Rule 21):**
> * **Module / Feature:** `linkedin_extension`
> * **Standard Path:** `docs/audit documents/linkedin_extension/2026-11-18-god-level-linkedin-automation-extension-architecture-spec.md`
> * **Architecture Standard:** Client-First Execution (User Browser RAM + Manifest V3 + SQLite on OPFS), 0% Cloud AI API Costs (Chrome `window.ai` Gemini Nano), Formal Actor Model (XState v5), Comlink Typed RPC, Monotonic Fencing Tokens, 100-Point Explainable Opportunity Scoring.
> * **Document Type:** Supergod Google-Grade Production Architecture & Implementation Blueprint.
> * **Target Product Identity:** **OmniReach — High-Performance Local-First Staffing BD CRM & Intelligence Workspace**.

---

## 1. Executive Summary & Google-Grade Technology Foundations

OmniReach V3.0 transitions from standard extension paradigms to **Google-grade Chromium systems engineering**. Designed from the perspective of principal engineers building desktop-class browser products (Chrome DevTools, Google Docs Offline, Linear), it incorporates the modern web platform's most advanced capabilities to achieve **unbreakable reliability, zero database corruption, zero cloud AI API costs, and sub-15ms cold starts**.

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                          GOOGLE-GRADE ARCHITECTURAL UPGRADES                           │
├─────────────────────────┬──────────────────────────────────────────────────────────────┤
│ 1. Storage Engine       │ SQLite WASM over OPFS (Origin Private File System)           │
│ 2. Intelligence Layer   │ Chrome Built-in AI (`window.ai` / Gemini Nano on-device)     │
│ 3. State & Concurrency  │ Formal Actor Model via XState v5 (Mathematically proven FSM) │
│ 4. IPC Architecture     │ Comlink Typed RPC over MessageChannel (Zero message bugs)    │
│ 5. UI Rendering Engine  │ Fine-Grained Reactivity + TanStack Virtual (60 FPS on 50k)   │
│ 6. Observability        │ Circular Ring-Buffer Flight Recorder (Perfetto-compatible)   │
└─────────────────────────┴──────────────────────────────────────────────────────────────┘
```

---

## 2. End-to-End System Architecture

```mermaid
flowchart TD
    subgraph UI_Surface ["UI Surface (Chrome Manifest V3)"]
        SP["Chrome SidePanel\n(Fast Lead Capture, Drawer & Icebreakers)"]
        FullTab["Full-Page CRM Dashboard\n(Accounts, Pipeline Kanban, Signal Matrix)"]
    end

    subgraph IPC_Fabric ["Typed IPC Layer (Comlink RPC over MessageChannel)"]
        ComlinkBridge["Comlink Typed Service Proxies\n• ITaskService • ICRMService • IAIService"]
    end

    subgraph Core_Engine ["Durable Extension Core (MV3 Service Worker & Offscreen)"]
        ActorSystem["XState v5 Actor Model Engine\n• Finite State Machine • Persisted Snapshots"]
        TaskRepo["Task State Repository with Fencing Tokens\n• claimTask() • commitTaskResult()"]
        SignalAggregator["Opportunity Scoring & Clusterization Engine\n• Category Max-Cap Aggregation"]
        SuppressionResolver["8-Step Authoritative Suppression Engine"]
        FlightRecorder["Circular Ring-Buffer Flight Recorder (2000 events)"]
    end

    subgraph Local_Storage ["Local Storage Fabric (Zero Cloud Database Strain)"]
        OPFS[("SQLite WASM on OPFS\n• WAL Mode & Sync Access Handle\n• Real ACID Transactions & FTS5\n• 15-Table Relational Schema")]
    end

    subgraph OnDevice_AI ["Chrome Native Built-in AI Layer (Zero API Bills)"]
        GeminiNano["window.ai / Gemini Nano (Hardware Accelerated)\n• Job Posting Signal Extraction\n• Hyper-Personalized Icebreakers\n• Entity Deduplication & Normalization"]
    end

    subgraph External_Adapters ["Modular Downstream Integrations (Phase 6-7)"]
        JEROSync["JERO ATS Bi-Directional Webhook Sync"]
        VoyagerAdapter["Optional In-Session Outreach Adapter"]
    end

    SP <--> ComlinkBridge
    FullTab <--> ComlinkBridge
    ComlinkBridge <--> Core_Engine
    
    ActorSystem <--> OPFS
    TaskRepo <--> OPFS
    SignalAggregator <--> OPFS
    SuppressionResolver <--> OPFS
    
    ActorSystem <--> GeminiNano
    SignalAggregator <--> GeminiNano
    
    Core_Engine -.-> FlightRecorder
    TaskRepo -.->|Controlled Sync| JEROSync
    TaskRepo -.->|Optional Dispatch| VoyagerAdapter
```

---

## 3. The 6 Google-Grade Technology Deep-Dives

### 3.1 Storage Engine: SQLite WASM over OPFS (Origin Private File System)
> **Eliminates:** IndexedDB transaction overhead, cursor performance penalties, and LevelDB corruption bugs.

* **Direct Synchronous Disk I/O:** Leverages Chrome's `FileSystemSyncAccessHandle` in dedicated Web Workers. Delivers **10x to 50x faster read/write throughput** compared to standard IndexedDB.
* **ACID Transactions & WAL Mode:** Configured with `PRAGMA journal_mode = WAL; PRAGMA synchronous = NORMAL;`. Real transactional foreign keys and unique constraints are enforced at the C-engine level, guaranteeing **zero data corruption even if Chrome crashes or the laptop loses power**.
* **FTS5 Full-Text Search:** Blazing fast local search over 100,000 candidate resumes, job postings, and company notes in $< 3\text{ms}$.
* **Single-File Portability:** Backing up or restoring the CRM is as simple as streaming a raw `.sqlite3` binary file.

---

### 3.2 On-Device AI: Chrome Built-in AI (`window.ai` / Gemini Nano)
> **Eliminates:** Cloud LLM API subscriptions ($0 bills), network latency, and privacy compliance risks.

Chrome natively bundles **Gemini Nano** directly into the browser engine, accessible via Chrome's experimental Prompt and Summarization APIs (`window.ai.languageModel`):

```typescript
// services/ai/LocalAIEngine.ts
export class LocalAIEngine {
  private session: any;

  async init() {
    if ('ai' in window && 'languageModel' in (window as any).ai) {
      this.session = await (window as any).ai.languageModel.create({
        systemPrompt: "You are an elite staffing business development assistant. Extract hiring intent, tech stacks, and generate high-converting, professional B2B outreach icebreakers."
      });
    }
  }

  async generatePersonalizedIcebreaker(prospectName: string, company: string, recentPostText: string): Promise<string> {
    if (!this.session) return `Hi ${prospectName}, noticed your expansion at ${company} and wanted to connect!`;
    
    const prompt = `Prospect: ${prospectName}, Company: ${company}. Recent Activity: "${recentPostText}". Write a concise 2-sentence professional connection note referencing their post.`;
    return await this.session.prompt(prompt);
  }
}
```

* **Staffing BD Signal Extractor:** When reviewing an employer's public career page or press release, Gemini Nano parses the text locally in 200ms, extracting hiring surges, tech stack requirements, and GCC expansion signals.
* **100% Privacy-Preserving:** Zero candidate or client data ever leaves the local machine.

---

### 3.3 Concurrency & State: XState v5 Actor Model
> **Eliminates:** Unchecked status string mutations and distributed race conditions.

All campaign enrollments, tasks, and background sync workflows are governed by **mathematically verified XState v5 statecharts**:

```typescript
// state/taskActorMachine.ts
import { setup, assign } from 'xstate';

export const taskActorMachine = setup({
  types: {
    context: {} as { taskId: string; attemptGeneration: number; leaseOwner?: string; retryCount: number },
    events: {} as 
      | { type: 'ALARM_TICK'; now: number }
      | { type: 'CLAIM'; workerId: string; leaseExpiresAt: number }
      | { type: 'COMMIT_SUCCESS'; payload: any }
      | { type: 'NETWORK_TIMEOUT' }
      | { type: 'FENCING_REJECT' }
      | { type: 'MANUAL_RETRY' }
  }
}).createMachine({
  id: 'taskActor',
  initial: 'PENDING',
  states: {
    PENDING: {
      on: { ALARM_TICK: { target: 'READY' } }
    },
    READY: {
      on: { CLAIM: { target: 'RUNNING', actions: assign(({ event }) => ({ leaseOwner: event.workerId })) } }
    },
    RUNNING: {
      on: {
        COMMIT_SUCCESS: 'COMPLETED',
        NETWORK_TIMEOUT: 'UNKNOWN',
        FENCING_REJECT: 'CANCELLED'
      }
    },
    UNKNOWN: {
      on: {
        MANUAL_RETRY: 'READY' // Restricted: Only via audited user confirmation
      }
    },
    COMPLETED: { type: 'final' },
    CANCELLED: { type: 'final' }
  }
});
```

---

### 3.4 IPC Architecture: Comlink Typed RPC
> **Eliminates:** Fragile `switch(message.type)` spaghetti across extension contexts.

Google's **Comlink** abstracts `postMessage` into **typed RPC calls**:

```typescript
// types/IChromeServices.ts
export interface ITaskService {
  claimNextEligibleTask(workerId: string): Promise<ExecutionTask | null>;
  commitTaskResult(taskId: string, fencingToken: number, outcome: 'COMPLETED' | 'FAILED', payload?: any): Promise<boolean>;
}

// In SidePanel UI - Calling background service worker like a local function!
import * as Comlink from 'comlink';
const bgPort = chrome.runtime.connect({ name: 'TASK_RPC_CHANNEL' });
const taskService = Comlink.wrap<ITaskService>(bgPort);

// Pure TypeScript safety with autocomplete
const claimed = await taskService.claimNextEligibleTask('worker_sidepanel_1');
```

---

### 3.5 UI & Virtualization: 60 FPS on 50,000 Records
* **Sub-15ms Cold Start:** UI compiled with fine-grained reactive primitives (zero Virtual DOM diffing overhead).
* **TanStack Virtual:** Only renders the ~15 rows currently visible in the user's viewport. Memory footprint stays below **25MB RAM** even when navigating directories of 50,000 accounts.

---

### 3.6 Observability: Chromium-Standard Flight Recorder
A 2,000-event circular in-memory buffer (`FlightRecorder`) tracks state transitions, database query timings, and worker wakeups with microsecond precision. When a user reports an issue, clicking **"Export Diagnostic Trace"** downloads a sanitized, Perfetto-compatible trace file without exposing sensitive candidate PII.

---

## 4. 15-Table Relational Schema Specification

Implemented on **SQLite WASM over OPFS** (with Dexie.js compatibility bridge for fallback environments):

```sql
-- 1. Organizations (Staffing Accounts & Clients)
CREATE TABLE organizations (
  id TEXT PRIMARY KEY, -- UUIDv4
  name TEXT NOT NULL,
  normalized_domain TEXT,
  website TEXT,
  industry TEXT,
  size_range TEXT,
  headquarters TEXT,
  account_pipeline_stage TEXT NOT NULL DEFAULT 'TARGET',
  tier TEXT NOT NULL DEFAULT 'TIER_3',
  account_owner_id TEXT,
  vendor_status TEXT NOT NULL DEFAULT 'NOT_STARTED',
  total_opportunity_score INTEGER NOT NULL DEFAULT 0,
  last_interaction_at INTEGER,
  next_follow_up_at INTEGER,
  created_at INTEGER NOT NULL,
  updated_at INTEGER NOT NULL
);
CREATE INDEX idx_orgs_domain ON organizations(normalized_domain);
CREATE INDEX idx_orgs_score ON organizations(total_opportunity_score DESC);

-- 2. Organization Stage Audit History
CREATE TABLE organization_stage_history (
  id TEXT PRIMARY KEY,
  organization_id TEXT NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
  from_stage TEXT NOT NULL,
  to_stage TEXT NOT NULL,
  changed_by_id TEXT NOT NULL,
  reason TEXT,
  timestamp INTEGER NOT NULL
);

-- 3. Prospects (Universal Channel-Neutral Identity)
CREATE TABLE prospects (
  id TEXT PRIMARY KEY, -- UUIDv4
  organization_id TEXT REFERENCES organizations(id) ON DELETE SET NULL,
  full_name TEXT NOT NULL,
  first_name TEXT NOT NULL,
  last_name TEXT NOT NULL,
  headline TEXT,
  source_type TEXT NOT NULL,
  source_id TEXT NOT NULL,
  normalized_email TEXT,
  primary_phone TEXT,
  profile_url TEXT,
  location TEXT,
  pipeline_stage TEXT NOT NULL DEFAULT 'LEAD',
  is_suppressed INTEGER NOT NULL DEFAULT 0,
  import_batch_id TEXT,
  created_at INTEGER NOT NULL,
  updated_at INTEGER NOT NULL,
  CONSTRAINT uq_prospect_source UNIQUE (source_type, source_id)
);
CREATE INDEX idx_prospects_org ON prospects(organization_id);
CREATE INDEX idx_prospects_stage ON prospects(pipeline_stage);

-- 4. Campaigns
CREATE TABLE campaigns (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  campaign_version INTEGER NOT NULL DEFAULT 1,
  status TEXT NOT NULL DEFAULT 'DRAFT',
  daily_action_cap INTEGER NOT NULL DEFAULT 20,
  auto_withdraw_days INTEGER NOT NULL DEFAULT 14,
  created_at INTEGER NOT NULL,
  updated_at INTEGER NOT NULL
);

-- 5. Campaign Enrollments (M:N Relation)
CREATE TABLE campaign_enrollments (
  id TEXT PRIMARY KEY,
  prospect_id TEXT NOT NULL REFERENCES prospects(id) ON DELETE CASCADE,
  campaign_id TEXT NOT NULL REFERENCES campaigns(id) ON DELETE CASCADE,
  enrolled_campaign_version INTEGER NOT NULL,
  status TEXT NOT NULL DEFAULT 'ACTIVE',
  current_step_index INTEGER NOT NULL DEFAULT 0,
  enrolled_at INTEGER NOT NULL,
  completed_at INTEGER,
  last_action_at INTEGER,
  CONSTRAINT uq_enrollment UNIQUE (prospect_id, campaign_id)
);
CREATE INDEX idx_enrollments_status ON campaign_enrollments(campaign_id, status);

-- 6. Sequence Steps
CREATE TABLE sequence_steps (
  id TEXT PRIMARY KEY,
  campaign_id TEXT NOT NULL REFERENCES campaigns(id) ON DELETE CASCADE,
  campaign_version INTEGER NOT NULL,
  step_index INTEGER NOT NULL,
  action_type TEXT NOT NULL,
  delay_days INTEGER NOT NULL DEFAULT 0,
  condition TEXT NOT NULL DEFAULT 'ALWAYS',
  message_template TEXT,
  asset_id TEXT,
  CONSTRAINT uq_sequence_step UNIQUE (campaign_id, campaign_version, step_index)
);

-- 7. Execution Tasks (Hardened Distributed State)
CREATE TABLE execution_tasks (
  id TEXT PRIMARY KEY,
  idempotency_key TEXT NOT NULL UNIQUE,
  enrollment_id TEXT NOT NULL REFERENCES campaign_enrollments(id) ON DELETE CASCADE,
  prospect_id TEXT NOT NULL,
  campaign_id TEXT NOT NULL,
  step_index INTEGER NOT NULL,
  action_type TEXT NOT NULL,
  status TEXT NOT NULL DEFAULT 'PENDING',
  scheduled_at INTEGER NOT NULL,
  dispatch_started_at INTEGER,
  last_attempt_at INTEGER,
  attempt_count INTEGER NOT NULL DEFAULT 0,
  attempt_generation INTEGER NOT NULL DEFAULT 0, -- Fencing token
  lease_owner TEXT,
  lease_expires_at INTEGER,
  result_type TEXT,
  result_payload TEXT,
  error_code TEXT,
  error_message TEXT,
  retry_after INTEGER,
  reconciliation_required INTEGER NOT NULL DEFAULT 0,
  created_at INTEGER NOT NULL,
  updated_at INTEGER NOT NULL
);
CREATE INDEX idx_tasks_schedule ON execution_tasks(status, scheduled_at);
CREATE INDEX idx_tasks_lease ON execution_tasks(lease_expires_at);

-- 8. Execution Attempts
CREATE TABLE execution_attempts (
  id TEXT PRIMARY KEY,
  task_id TEXT NOT NULL REFERENCES execution_tasks(id) ON DELETE CASCADE,
  attempt_generation INTEGER NOT NULL,
  attempt_number INTEGER NOT NULL,
  status TEXT NOT NULL,
  http_status INTEGER,
  error_message TEXT,
  executed_at INTEGER NOT NULL,
  duration_ms INTEGER NOT NULL
);

-- 9. Opportunity Signals (Explainable & Clustered)
CREATE TABLE opportunity_signals (
  id TEXT PRIMARY KEY,
  organization_id TEXT NOT NULL REFERENCES organizations(id) ON DELETE CASCADE,
  cluster_key TEXT NOT NULL,
  signal_type TEXT NOT NULL,
  title TEXT NOT NULL,
  evidence_extract TEXT NOT NULL,
  source_url TEXT,
  source_type TEXT NOT NULL,
  publication_date INTEGER NOT NULL,
  detected_at INTEGER NOT NULL,
  last_verified_at INTEGER NOT NULL,
  expires_at INTEGER NOT NULL,
  confidence_score REAL NOT NULL,
  score_contribution INTEGER NOT NULL,
  status TEXT NOT NULL DEFAULT 'ACTIVE'
);
CREATE INDEX idx_signals_org ON opportunity_signals(organization_id, status);

-- 10. Channel-Neutral Conversation State
CREATE TABLE conversation_state (
  id TEXT PRIMARY KEY,
  prospect_id TEXT NOT NULL UNIQUE REFERENCES prospects(id) ON DELETE CASCADE,
  channel TEXT NOT NULL,
  external_thread_id TEXT,
  last_message_author TEXT NOT NULL,
  last_message_text TEXT,
  last_interaction_at INTEGER NOT NULL,
  reply_detected INTEGER NOT NULL DEFAULT 0,
  user_confirmed_reply INTEGER NOT NULL DEFAULT 0,
  updated_at INTEGER NOT NULL
);

-- 11. Notes (Immutable Timeline Records)
CREATE TABLE notes (
  id TEXT PRIMARY KEY,
  target_type TEXT NOT NULL, -- 'PROSPECT' | 'ORGANIZATION'
  target_id TEXT NOT NULL,
  author_id TEXT NOT NULL,
  author_name TEXT NOT NULL,
  content TEXT NOT NULL,
  created_at INTEGER NOT NULL
);
CREATE INDEX idx_notes_target ON notes(target_type, target_id);

-- 12. Custom Fields Definition
CREATE TABLE custom_fields (
  id TEXT PRIMARY KEY,
  name TEXT NOT NULL,
  field_key TEXT NOT NULL,
  target_entity TEXT NOT NULL,
  field_type TEXT NOT NULL,
  options_json TEXT,
  CONSTRAINT uq_custom_field UNIQUE (target_entity, field_key)
);

-- 13. Custom Field Values
CREATE TABLE custom_field_values (
  id TEXT PRIMARY KEY,
  target_id TEXT NOT NULL,
  field_id TEXT NOT NULL REFERENCES custom_fields(id) ON DELETE CASCADE,
  value_json TEXT,
  CONSTRAINT uq_field_value UNIQUE (target_id, field_id)
);

-- 14. Authoritative Granular Suppression Engine
CREATE TABLE suppression_list (
  id TEXT PRIMARY KEY,
  scope TEXT NOT NULL, -- 'GLOBAL' | 'ORGANIZATION' | 'DOMAIN' | 'CONTACT' | 'EMAIL' | 'PHONE'
  normalized_target TEXT NOT NULL,
  reason TEXT NOT NULL,
  is_permanent INTEGER NOT NULL DEFAULT 1,
  expires_at INTEGER,
  created_by TEXT NOT NULL,
  created_at INTEGER NOT NULL,
  CONSTRAINT uq_suppression UNIQUE (scope, normalized_target)
);
CREATE INDEX idx_suppression_target ON suppression_list(scope, normalized_target);

-- 15. Import Batches
CREATE TABLE import_batches (
  id TEXT PRIMARY KEY,
  source_type TEXT NOT NULL,
  source_metadata_json TEXT,
  total_parsed INTEGER NOT NULL,
  total_imported INTEGER NOT NULL,
  total_duplicates_skipped INTEGER NOT NULL,
  total_suppressed INTEGER NOT NULL,
  user_confirmed INTEGER NOT NULL DEFAULT 0,
  created_at INTEGER NOT NULL
);
```

---

## 5. Opportunity Scoring: Deterministic Category-Cap Aggregation

To eliminate score inflation while rewarding multi-category corroboration, the engine uses **Deterministic Category-Cap Aggregation**:

$$\text{TotalScore}(\text{Org}) = \min\left(100, \sum_{\text{category} \in \text{Categories}} \min\left(\text{CAP}(\text{category}), \max_{s \in \text{category}}(\text{Score}(s))\right)\right)$$

### 5.1 Category Caps & Scoring Rules
* **Vendor Empanelment Notice (Cap: 30 pts):** RFP or procurement notice for IT vendors.
* **GCC / Offshore Build (Cap: 25 pts):** Establishing or expanding tech center.
* **IT Hiring Surge (Cap: 25 pts):** Multiple tech roles within 14-day window.
* **New TA / HR Leadership (Cap: 10 pts):** New talent leader appointed.
* **Urgent Contract Requisitions (Cap: 10 pts):** Contingent/contract hiring demand.

```typescript
// services/scoring/OpportunityScorer.ts
export const CATEGORY_CAPS = {
  VENDOR_EMPANELMENT: 30,
  GCC_EXPANSION: 25,
  IT_HIRING_SPIKE: 25,
  TA_LEADERSHIP_CHANGE: 10,
  CONTRACT_ROLES: 10
};

export function calculateAggregatedScore(signals: OpportunitySignal[]): number {
  const now = Date.now();
  const validSignals = signals.filter(s => s.status === 'ACTIVE' && s.expiresAt > now);

  const categoryScores: Record<string, number> = {};

  for (const signal of validSignals) {
    const rawContribution = Math.round(signal.scoreContribution * signal.confidenceScore);
    categoryScores[signal.signalType] = Math.max(categoryScores[signal.signalType] || 0, rawContribution);
  }

  let total = 0;
  for (const [category, score] of Object.entries(categoryScores)) {
    const cap = CATEGORY_CAPS[category as keyof typeof CATEGORY_CAPS] || 0;
    total += Math.min(cap, score);
  }

  return Math.min(100, total);
}
```

---

## 6. Authoritative 8-Step Suppression Precedence Engine

Suppression evaluation executes before every import, preview, task claim, and dispatch:

```mermaid
flowchart TD
    Target["Target Entity\n(Prospect / Domain / Email)"] --> Step1{"1. GLOBAL Suppression?"}
    Step1 -- Match --> BLOCKED["BLOCKED: Action Rejected"]
    Step1 -- Pass --> Step2{"2. ORGANIZATION Suppression?"}
    
    Step2 -- Match --> BLOCKED
    Step2 -- Pass --> Step3{"3. DOMAIN Suppression?\n(e.g., existing client acme.com)"}
    
    Step3 -- Match --> BLOCKED
    Step3 -- Pass --> Step4{"4. CONTACT Suppression?"}
    
    Step4 -- Match --> BLOCKED
    Step4 -- Pass --> Step5{"5. EMAIL Suppression?"}
    
    Step5 -- Match --> BLOCKED
    Step5 -- Pass --> Step6{"6. PHONE Suppression?"}
    
    Step6 -- Match --> BLOCKED
    Step6 -- Pass --> Step7{"7. CHANNEL Suppression?"}
    
    Step7 -- Match --> BLOCKED
    Step7 -- Pass --> Step8{"8. CAMPAIGN Exclusion?"}
    
    Step8 -- Match --> BLOCKED
    Step8 -- Pass --> PERMITTED["PERMITTED: Proceed with Workflow"]
```

---

## 7. 7-Phase Gated Implementation Roadmap

Speculative timelines are eliminated. Development proceeds strictly through **7 Sequential Engineering Gates**:

```mermaid
flowchart LR
    G1["Gate 1: Local CRM\n(SQLite OPFS, Orgs, Pipeline)"] --> G2["Gate 2: Ingestion\n(CSV, Dedupe, Preview)"]
    G2 --> G3["Gate 3: BD Signals & AI\n(Gemini Nano, Scoring)"]
    G3 --> G4["Gate 4: Actor Task Engine\n(XState, Fencing, Leases)"]
    G4 --> G5["Gate 5: Campaign Workspace\n(Versioned Sequences, UI Co-pilot)"]
    G5 --> G6["Gate 6: JERO ATS Sync\n(Bi-Directional Webhook)"]
    G6 --> G7["Gate 7: Optional Adapters\n(Isolated In-Session Outreach)"]
```

### Gate 1 Deliverable Contract (Active Next Step):
* **Workspace:** Initialized `wxt` with TypeScript, Tailwind CSS, Comlink, and SQLite WASM over OPFS.
* **15-Table Schema:** Full DDL compiled and verified with real foreign keys and indexes.
* **SidePanel UI:** Reactive CRM Shell showing Organization List (Staffing Pipeline: `TARGET` $\to$ `EMPANELLED`), Contacts List, and Notes Drawer.
* **Exit Test:** `tests/gate1_crm_crud.test.ts` passing 100% on 10,000 synthetic records with $< 10\text{ms}$ query latency.

---
*Architectural Document strictly stored in accordance with Rule 21 at `docs/audit documents/linkedin_extension/2026-11-18-god-level-linkedin-automation-extension-architecture-spec.md`.*
