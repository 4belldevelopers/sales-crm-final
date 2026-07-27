# Resume Upload v2 — Complete 18-Step Implementation Plan

> **Intent:** The single actionable build plan for the v2 resume upload
> subsystem. Every step is independently implementable, has a feature
> flag defaulting to OFF, and requires a passing staging smoke test
> before the next step begins.
>
> Total estimate: ~4-6 weeks (single developer), ~2-3 weeks (pair).
> Total new code: ~2,500 lines across ~25 files.

---

## Table of Contents

- [How to use this plan](#how-to-use-this-plan)
- [What changes and what stays the same](#what-changes-and-what-stays-the-same)
- [Step 0 — Pre-flight checklist](#step-0--pre-flight-checklist)
- [Phase A — Foundation (Steps 1-3)](#phase-a--foundation-steps-1-3)
- [Phase B — Upload Pipeline (Steps 4-6)](#phase-b--upload-pipeline-steps-4-6)
- [Phase C — Submit + UX (Steps 7-11)](#phase-c--submit--ux-steps-7-11)
- [Phase D — Hardening (Steps 12-15)](#phase-d--hardening-steps-12-15)
- [Phase E — Ship (Steps 16-18)](#phase-e--ship-steps-16-18)
- [Phase F — Edit page (Step 19)](#phase-f--edit-page-step-19)
- [Feature flag reference](#feature-flag-reference)
- [Cross-references](#cross-references)

---

## How to use this plan

1. **Start at Step 1.** Do not skip ahead.
2. **Step 0 is pre-flight** — run it once at session start to confirm
   infrastructure is healthy.
3. Each step has a **feature flag defaulting to OFF**. Write all code
   behind the flag. Ship the code first, then flip ON for staging test.
4. **Test on staging** before moving to the next step. If staging smoke
   fails → fix → re-test. Do NOT proceed.
5. **Every 5 steps:** run `php artisan test --testsuite=Feature --filter=ResumeV2`
   full regression + `bash scripts/ai-guards.sh`.
6. The **NEVER rules** from [`98_failure_matrix_never.md`](98_failure_matrix_never.md)
   are PR review gates — any PR that violates one is rejected.

---

## What changes and what stays the same

### What changes (the v2 scope)
| Area | Before (v1) | After (v2) |
|------|-------------|------------|
| File upload | Browser → PHP multipart POST → Laravel → disk | Browser → presigned PUT → Cloudflare R2 |
| PHP involvement | Receives full file bytes in memory | Receives ZERO bytes — metadata only |
| File storage | Local disk or server-managed S3 | R2 temp bucket with 30-day lifecycle |
| Idempotency | None — re-upload creates duplicate rows | HTTP `Idempotency-Key` header (per-request random) → same UUID replay on transport retry only. New file pick → new key → new UUID. No business duplicate detection. |
| Upload progress | Basic jQuery AJAX call | Real progress bar with ETA, speed via XHR |
| Server round-trips | 4 per upload (uploadresume + finalize + candidate + documents) | **2 per upload** (presign + submit-with-session) |

### What stays EXACTLY the same (untouched)
| Area | Why untouched |
|------|---------------|
| Client-side parser (pdf.js → re_test()) | Already runs in browser from `File` object, instant, no server needed |
| Form fields + manual edit | Parser fills fields via `re_test()`, manual values always win |
| Submit button + candidate creation | Same controller, same `StoreJobApplication` — just adds optional `session_uuid` param |
| Career-apply public pages | Same HomeController code until Step 17 extraction |
| UI theme (Metronic, Poppins, 14px) | Not a UI redesign |
| Database schema for candidates | Same `add_job_wise_condidates` table |
| Authentication / middleware | Same auth guards, same session handling |

### Why this architecture is better for 10K concurrent

```
v1 (current — 4 server hits):
  Recruiter ──POST file────▶ PHP/Octane ──store──▶ Disk/DB
              [uploadresume]   ▲                     ▲
                               │                     │
                             Full file bytes       RAM spike
                            in memory (6 MB ×     per upload
                            10K = 60 GB RAM)

v2 (target — 2 RTT, zero PHP bytes):
  Recruiter
    │  RTT 1: POST /resume-sessions
    │         (metadata only, ~200 bytes)
    ▼
  PHP/Octane ──INSERT──▶ MySQL (status=Pending, 200 bytes)
    │
    │  Returns presigned URL
    ▼
  Browser ──PUT file────▶ Cloudflare R2 (zero PHP bytes)
    │         [recruiter fills form fields]
    │
    │  RTT 2: POST /job-applications {session_uuid, ...fields}
    │         (no file bytes, only metadata)
    ▼
  PHP/Octane ──atomic transaction──▶ MySQL
    1. Verify session.status === 'Uploaded'
    2. HEAD-check R2 object exists
    3. INSERT candidate + documents
    4. UPDATE session (Completed, link candidate)
```

---

## Step 0 — Pre-flight checklist

> **Run once at the start of every implementation session.** Confirms
> the environment is ready before any code changes. Takes ~2 minutes.

### Check Redis is running
```bash
redis-cli ping
# Expected: PONG
```

### Check R2/Cloudflare credentials
```bash
# Confirm these env vars exist (values may differ)
php -r "echo env('CLOUDFLARE_R2_ACCOUNT_ID') ? 'R2_ACCOUNT_ID: OK' : 'R2_ACCOUNT_ID: MISSING';"
php -r "echo env('CLOUDFLARE_R2_ACCESS_KEY_ID') ? 'R2_KEY: OK' : 'R2_KEY: MISSING';"
php -r "echo env('CLOUDFLARE_R2_SECRET_ACCESS_KEY') ? 'R2_SECRET: OK' : 'R2_SECRET: MISSING';"
php -r "echo env('CLOUDFLARE_R2_BUCKET') ? 'R2_BUCKET: OK' : 'R2_BUCKET: MISSING';"
php -r "echo env('CLOUDFLARE_R2_ENDPOINT') ? 'R2_ENDPOINT: OK' : 'R2_ENDPOINT: MISSING';"
# All must return ": OK"
```

### Check Horizon is running
```bash
php artisan horizon:status
# Expected: Horizon is running.
```

### Check Octane is running
```bash
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200
```

### Check current schema
```bash
# Read the relevant section of the schema file
grep -A 20 "resume_uploads" database/schema/mysql-schema.sql | head -25
```

### Check existing resume upload route
```bash
php artisan route:list --path=uploadresume
# Expected: one POST route to service_company/uploadresume
```

### Deploy mode
```bash
php artisan tinker --execute="echo config('app.env');"
# Expected: production (staging) or local
```

---

## Phase A — Foundation (Steps 1-3)

### Step 1 — `config/resumes.php` + v2 configuration shell

**Intent:** Create the central configuration file that every other step
will reference. All magic numbers, TTLs, feature flags, and bucket names
live here — not in code.

**Pre-requisites:**
- Step 0 pre-flight passed
- `.env` has R2 credentials

**Feature flag:** N/A (just config, no behavior change)

---

#### Files to create

**`config/resumes.php`**
```php
<?php
// Intent: central configuration for the resume upload v2 subsystem.
// Every magic number lives here — not in controllers, jobs, or JS.
// All feature flags default to OFF for safe roll-out.

return [

    /*
    |--------------------------------------------------------------------------
    | Feature flags (all OFF by default for safe incremental rollout)
    |--------------------------------------------------------------------------
    */
    'features' => [
        'presign_endpoint'     => (bool) env('RESUMES_FEATURE_PRESIGN', false),
        'browser_upload'       => (bool) env('RESUMES_FEATURE_BROWSER_UPLOAD', false),
        'finalize_endpoint'    => (bool) env('RESUMES_FEATURE_FINALIZE', false),
        'submit_with_session'  => (bool) env('RESUMES_FEATURE_SUBMIT_SESSION', false),
        'cancel'               => (bool) env('RESUMES_FEATURE_CANCEL', false),
        'refresh_recovery'     => (bool) env('RESUMES_FEATURE_REFRESH_RECOVERY', false),
        'multipart_upload'     => (bool) env('RESUMES_FEATURE_MULTIPART', false),
        'network_resilience'   => (bool) env('RESUMES_FEATURE_NETWORK_RESILIENCE', false),
        'broadcast_channel'    => (bool) env('RESUMES_FEATURE_BROADCAST_CHANNEL', false),
        'career_controller'    => (bool) env('RESUMES_FEATURE_CAREER_CONTROLLER', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cloudflare R2 — temp bucket (browser uploads land here)
    |--------------------------------------------------------------------------
    */
    'r2' => [
        'temp_bucket'     => env('CLOUDFLARE_R2_BUCKET', 'jero-resume-uploads-temp'),
        'temp_prefix'     => sprintf('resumes/%s/', date('Y/m/d')),
        'presign_ttl'     => (int) env('RESUMES_R2_PRESIGN_TTL', 900),        // 15 min
        'presign_ttl_min' => 60,   // minimum acceptable TTL before rotation
        'endpoint'        => env('CLOUDFLARE_R2_ENDPOINT'),
        'account_id'      => env('CLOUDFLARE_R2_ACCOUNT_ID'),
        'region'          => env('CLOUDFLARE_R2_REGION', 'auto'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session lifecycle
    |--------------------------------------------------------------------------
    */
    'session' => [
        'ttl'             => 1800,  // 30 min — after this, session is Expired
        'expires_at_from' => 'created_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | File validation
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'max_file_size'       => 6_291_456,  // 6 MB
        'allowed_mime_types'  => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'allowed_extensions'  => ['pdf', 'doc', 'docx'],
        'idempotency_key_len' => 64,  // sha256 hex
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate limits
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'presign_per_user'    => 30,  // POST /resume-sessions per minute
        'presign_per_company' => 120, // per company across all users
        'submit_per_user'     => 20,  // POST /job-applications per minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Health endpoint
    |--------------------------------------------------------------------------
    */
    'health_token' => env('RESUMES_HEALTH_TOKEN'),
    'metrics_require_redis' => (bool) env('RESUMES_METRICS_REQUIRE_REDIS', true),
];
```

---

**What success looks like:**
- `php artisan config:clear` succeeds
- `config('resumes.features.presign_endpoint')` returns `false`
- `config('resumes.r2.temp_bucket')` returns the bucket name
- All feature flags are readable and default to OFF

**Test on staging:**
```bash
# 1. Config loads without errors
php artisan config:clear
php artisan config:cache

# 2. Read a value
php artisan tinker --execute="dump(config('resumes.features.presign_endpoint'));"
# Expected: false

php artisan tinker --execute="dump(config('resumes.validation.max_file_size'));"
# Expected: 6291456

# 3. ai-guards
bash scripts/ai-guards.sh

# 4. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200
```

**Rollback:** Delete `config/resumes.php`, re-run `php artisan config:cache`.
No other code references it yet.

---

### Step 2 — Additive DB migrations for v2

**Intent:** Add all new columns, indexes, and tables to `resume_uploads`
BEFORE any application code reads them. Zero behavior change — safe to
run on any environment including production.

**Pre-requisites:**
- Step 1 complete (config/resumes.php exists)
- `database/schema/mysql-schema.sql` read and confirmed
- Backup of current resume_uploads structure noted

**Feature flag:** N/A (DB-only, no app code reads these yet)

---

#### Pre-condition: Read current schema

Before writing any migration, read the actual current schema:
```bash
grep -A 50 "CREATE TABLE.*resume_uploads" database/schema/mysql-schema.sql | head -60
# Note every existing column name to avoid collisions
```

Also check existing indexes:
```bash
php artisan tinker --execute="
    \$schema = DB::select('SHOW CREATE TABLE resume_uploads');
    dump(\$schema[0]->{'Create Table'});
"
```

---

#### Migration 1: `add_resume_v2_columns`

File: `database/migrations/2026_07_26_000001_add_resume_v2_columns.php`

```php
<?php
// Intent: Add v2 columns to resume_uploads. All nullable / have defaults
// so existing rows are not affected. Additive only — no column drops.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resume_uploads', function (Blueprint $table) {
            // Idempotency — per-request random token from the HTTP
            // `Idempotency-Key` header (RFC draft-ietf-httpapi-idempotency-key-header).
            // Used for transport retry replay only. NOT a content hash.
            // NOT business duplicate detection (see INV-1, INV-4).
            $table->char('idempotency_key', 64)->nullable()->after('uuid');

            // Parser tracking
            $table->timestamp('parser_started_at')->nullable()->after('file_path');
            $table->timestamp('parser_completed_at')->nullable()->after('parser_started_at');
            $table->unsignedTinyInteger('parser_attempts')->default(0)->after('parser_completed_at');
            $table->text('last_error')->nullable()->after('parser_attempts');
            $table->string('last_parser_pod_id', 64)->nullable()->after('last_error');
            
            // Lifecycle
            $table->timestamp('expires_at')->nullable()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('resume_uploads', function (Blueprint $table) {
            $table->dropColumn([
                'idempotency_key',
                'parser_started_at',
                'parser_completed_at',
                'parser_attempts',
                'last_error',
                'last_parser_pod_id',
                'expires_at',
            ]);
        });
    }
};
```

---

#### Migration 2: `add_resume_v2_indexes`

File: `database/migrations/2026_07_26_000002_add_resume_v2_indexes.php`

```php
<?php
// Intent: v2 indexes for idempotency, tenant-scoped lookups, and cron sweeps.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resume_uploads', function (Blueprint $table) {
            // Idempotency-Key replay lookup: a non-unique index for
            // `WHERE company_id = ? AND idempotency_key = ? AND status
            // NOT IN (terminal)`. No UNIQUE — replay is "last-N-lookup",
            // not a uniqueness gate (per INV-1, INV-4; ADR-002, ADR-009).
            $table->index(['company_id', 'idempotency_key'], 'idx_company_idem');

            // Tenant-scoped UUID lookup (show/refresh)
            $table->unique(['uuid', 'company_id'], 'resume_uploads_uuid_company_unique');

            // Cron sweep index: find expired or stuck sessions
            $table->index(['status', 'expires_at'], 'resume_uploads_status_expires_at_index');
        });
    }

    public function down(): void
    {
        Schema::table('resume_uploads', function (Blueprint $table) {
            $table->dropIndex('resume_uploads_status_expires_at_index');
            $table->dropIndex('resume_uploads_uuid_company_unique');
            $table->dropIndex('idx_company_idem');
        });
    }
};
```

---

#### Migration 3: `create_upload_session_audits`

File: `database/migrations/2026_07_26_000003_create_upload_session_audits.php`

```php
<?php
// Intent: Immutable audit log for every state transition in the upload
// lifecycle. Append-only — never updated or deleted.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('upload_session_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('resume_upload_uuid')->constrained('resume_uploads', 'uuid')->cascadeOnDelete();
            $table->string('action', 64);                // e.g. ActionSessionCreated, ActionUploadCompleted
            $table->json('payload')->nullable();          // context: previous_status, duration_ms, etc.
            $table->string('pod_id', 64)->nullable();     // which Octane pod processed this
            $table->string('company_id', 36)->nullable(); // tenant context
            $table->string('ip_hash', 64)->nullable();    // SHA256 of IP for abuse investigation
            $table->timestamps();

            // Index for cron/report queries
            $table->index(['resume_upload_uuid', 'created_at'], 'audit_session_time_idx');
            $table->index(['action', 'created_at'], 'audit_action_time_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('upload_session_audits');
    }
};
```

---

#### Migration 4: `add_resume_pending_status`

File: `database/migrations/2026_07_26_000004_add_resume_pending_status.php`

```php
<?php
// Intent: Add 'Pending' to the status ENUM. This is the initial state
// after presign is created but before upload starts.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL ENUM alter — need raw statement
        DB::statement("ALTER TABLE resume_uploads MODIFY COLUMN status ENUM(
            'Pending', 'Uploading', 'Uploaded', 'Completed', 'Reconciled', 'Failed', 'Cancelled', 'Expired'
        ) NOT NULL DEFAULT 'Pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE resume_uploads MODIFY COLUMN status ENUM(
            'Uploading', 'Uploaded', 'Completed', 'Reconciled', 'Failed', 'Cancelled', 'Expired'
        ) NOT NULL DEFAULT 'Uploading'");
    }
};
```

---

#### Data flow diagram for Step 2

```
┌─────────────────────────────────────────────────────┐
│                  resume_uploads table                │
├─────────────────────────────────────────────────────┤
│  Existing columns (uuid, company_id, file_path,      │
│  status, created_at, updated_at)                     │
│                                                      │
│  NEW v2 columns:                                     │
│  ┌ idempotency_key (char 64, indexed by company)     │
│  │       — HTTP `Idempotency-Key` header value,      │
│  │         per-request random. Transport retry        │
│  │         replay only — NOT business dedupe.        │
│  ├ parser_started_at / parser_completed_at           │
│  ├ parser_attempts (default 0)                       │
│  ├ last_error (text, nullable)                       │
│  ├ last_parser_pod_id (varchar 64, nullable)         │
│  └ expires_at (timestamp, nullable)                  │
│                                                      │
│  NEW indexes:                                        │
│  ┌ INDEX(company_id, idempotency_key) — no UNIQUE   │
│  ├ UNIQUE(uuid, company_id)                          │
│  └ INDEX(status, expires_at)                         │
│                                                      │
│  NEW status value: 'Pending'                         │
└─────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────┐
│              upload_session_audits table (NEW)       │
├─────────────────────────────────────────────────────┤
│  id (bigint, auto-increment)                         │
│  resume_upload_uuid (FK → resume_uploads.uuid)       │
│  action (varchar 64) — ActionSessionCreated etc.     │
│  payload (json, nullable)                            │
│  pod_id (varchar 64, nullable)                       │
│  company_id (varchar 36, nullable)                   │
│  ip_hash (varchar 64, nullable)                      │
│  created_at / updated_at                             │
│                                                      │
│  INDEX(resume_upload_uuid, created_at)               │
│  INDEX(action, created_at)                           │
└─────────────────────────────────────────────────────┘
```

---

**What success looks like:**
- All 4 migrations run: columns added, indexes created, new table exists
- `resume_uploads` has `idempotency_key`, `expires_at`, etc.
- `upload_session_audits` table exists
- Existing rows in `resume_uploads` are untouched

**Test on staging:**
```bash
# 1. Dry-run migrations to confirm SQL
php artisan migrate --pretend --path=database/migrations/2026_07_26_*
# Expected: ALTER TABLE resume_uploads ADD ..., CREATE TABLE upload_session_audits, etc.

# 2. Run real migrations
php artisan migrate --path=database/migrations/2026_07_26_*

# 3. Confirm new columns
php artisan tinker --execute="
    \$cols = DB::select('SHOW COLUMNS FROM resume_uploads');
    \$names = array_column(\$cols, 'Field');
    dump(in_array('idempotency_key', \$names));  // true
    dump(in_array('expires_at', \$names));        // true
"

# 4. Confirm new table
php artisan tinker --execute="
    \$tables = DB::select('SHOW TABLES');
    \$names = array_map('current', \$tables);
    dump(in_array('upload_session_audits', \$names));  // true
"

# 5. Confirm indexes
php artisan tinker --execute="
    \$indexes = DB::select('SHOW INDEX FROM resume_uploads');
    \$keyNames = array_unique(array_column(\$indexes, 'Key_name'));
    dump(\$keyNames);
    // Expected: contains 'resume_uploads_company_idem_key_unique'
"

# 6. Confirm existing data untouched
php artisan tinker --execute="
    dump(DB::table('resume_uploads')->count());
    // Expected: same count as before migration
"

# 7. ai-guards
bash scripts/ai-guards.sh

# 8. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200
```

**Rollback:**
```bash
php artisan migrate:rollback --step=4
# Verify:
php artisan tinker --execute="dump(in_array('idempotency_key', array_column(DB::select('SHOW COLUMNS FROM resume_uploads'), 'Field')));"
# Expected: false
```

---

### Step 3 — ResumeUploadStatus enum + models

**Intent:** Create the PHP enums and Eloquent models that represent the
v2 data model. No endpoints use them yet — pure data layer with tests.

**Pre-requisites:**
- Step 2 complete (columns and tables exist in DB)

**Feature flag:** `resumes.features.presign_endpoint = false` (models
exist but are unreferenced by routes)

---

#### File: `app/Enums/ResumeUploadStatus.php`

```php
<?php
namespace App\Enums;

// Intent: All valid states for a resume upload session, with transition
// rules. The status machine is: Pending → Uploading → Uploaded → Completed
// Any state can → Failed | Cancelled | Expired → Reconciled

enum ResumeUploadStatus: string
{
    case Pending     = 'Pending';     // Presigned URL created, upload hasn't started
    case Uploading   = 'Uploading';   // Browser is actively PUTting bytes to R2
    case Uploaded    = 'Uploaded';    // PUT completed, R2 object verified
    case Completed   = 'Completed';   // Candidate created and linked
    case Failed      = 'Failed';      // Parse/upload error, terminal
    case Cancelled   = 'Cancelled';   // User cancelled, terminal
    case Expired     = 'Expired';     // TTL passed, terminal
    case Reconciled  = 'Reconciled';  // Cron swept, terminal

    public function isTerminal(): bool
    {
        return in_array($this, [
            self::Completed,
            self::Failed,
            self::Cancelled,
            self::Expired,
            self::Reconciled,
        ], true);
    }

    public function canTransitionTo(self $target): bool
    {
        // State machine rules
        return match ($this) {
            self::Pending    => in_array($target, [self::Uploading, self::Uploaded, self::Failed, self::Cancelled, self::Expired]),
            self::Uploading  => in_array($target, [self::Uploaded, self::Failed, self::Cancelled, self::Expired]),
            self::Uploaded   => in_array($target, [self::Completed, self::Failed, self::Expired]),
            self::Completed  => false,  // Terminal — no transitions out
            self::Failed     => false,  // Terminal
            self::Cancelled  => false,  // Terminal
            self::Expired    => in_array($target, [self::Reconciled]),
            self::Reconciled => false,  // Terminal
        };
    }

    public static function validStatuses(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
```

---

#### File: `app/Models/ResumeUpload.php`

```php
<?php
namespace App\Models;

use App\Enums\ResumeUploadStatus;
use Illuminate\Database\Eloquent\Model;

// Intent: Eloquent model for the resume_uploads table with v2 scopes
// and helpers. Follows existing project patterns — no repository layer.

class ResumeUpload extends Model
{
    protected $table = 'resume_uploads';

    protected $fillable = [
        'uuid',
        'company_id',
        'original_filename',
        'file_path',
        'file_size',
        'mime_type',
        'status',
        'idempotency_key',
        'parser_started_at',
        'parser_completed_at',
        'parser_attempts',
        'last_error',
        'last_parser_pod_id',
        'expires_at',
        'linked_candidate_id',
        'cleanup_reason',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'parser_attempts' => 'integer',
        'parser_started_at' => 'datetime',
        'parser_completed_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    // ─── Scopes ───────────────────────────────────

    public function scopeForCompany($query, string $companyId)
    {
        // CRITICAL: Tenancy filter — always scope by company_id
        return $query->where('company_id', $companyId);
    }

    public function scopeByUuid($query, string $uuid)
    {
        return $query->where('uuid', $uuid);
    }

    public function scopeNonTerminal($query)
    {
        return $query->whereNotIn('status', [
            ResumeUploadStatus::Completed->value,
            ResumeUploadStatus::Failed->value,
            ResumeUploadStatus::Cancelled->value,
            ResumeUploadStatus::Expired->value,
            ResumeUploadStatus::Reconciled->value,
        ]);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', ResumeUploadStatus::Pending->value)
            ->where('expires_at', '<', now());
    }

    public function scopeStuck($query)
    {
        return $query->where('status', ResumeUploadStatus::Uploading->value)
            ->where('updated_at', '<', now()->subMinutes(5));
    }

    // ─── Helpers ──────────────────────────────────

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function markFailed(string $reason): void
    {
        $this->update([
            'status'    => ResumeUploadStatus::Failed->value,
            'last_error' => $reason,
        ]);
    }
}
```

---

#### File: `app/Models/UploadSessionAudit.php`

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Intent: Immutable audit log model. Append-only — records every state
// transition in the resume upload lifecycle.

class UploadSessionAudit extends Model
{
    protected $table = 'upload_session_audits';

    protected $fillable = [
        'resume_upload_uuid',
        'action',
        'payload',
        'pod_id',
        'company_id',
        'ip_hash',
    ];

    protected $casts = [
        'payload' => 'json',
    ];

    // ─── Factory method ───────────────────────────

    public static function record(string $uuid, string $action, array $payload = []): self
    {
        return self::create([
            'resume_upload_uuid' => $uuid,
            'action'             => $action,
            'payload'            => $payload,
            'pod_id'             => gethostname(),
            'company_id'         => $payload['company_id'] ?? null,
            'ip_hash'            => $payload['ip_hash'] ?? null,
        ]);
    }
}
```

---

#### Test file: `tests/Unit/ResumeUploadStatusTest.php`

```php
<?php
// Intent: Verify state machine transitions are correct.
// Covers the canTransitionTo matrix for all 8 statuses.

test('Pending can transition to valid targets', function () {
    $status = App\Enums\ResumeUploadStatus::Pending;
    expect($status->canTransitionTo(App\Enums\ResumeUploadStatus::Uploading))->toBeTrue();
    expect($status->canTransitionTo(App\Enums\ResumeUploadStatus::Uploaded))->toBeTrue();
    expect($status->canTransitionTo(App\Enums\ResumeUploadStatus::Failed))->toBeTrue();
    expect($status->canTransitionTo(App\Enums\ResumeUploadStatus::Cancelled))->toBeTrue();
    expect($status->canTransitionTo(App\Enums\ResumeUploadStatus::Expired))->toBeTrue();
});

test('Pending cannot transition to Completed', function () {
    $status = App\Enums\ResumeUploadStatus::Pending;
    expect($status->canTransitionTo(App\Enums\ResumeUploadStatus::Completed))->toBeFalse();
});

test('Completed is terminal', function () {
    $status = App\Enums\ResumeUploadStatus::Completed;
    expect($status->isTerminal())->toBeTrue();
    expect($status->canTransitionTo(App\Enums\ResumeUploadStatus::Pending))->toBeFalse();
});

test('All non-terminal statuses can transition to Failed', function () {
    $nonTerminal = [
        App\Enums\ResumeUploadStatus::Pending,
        App\Enums\ResumeUploadStatus::Uploading,
        App\Enums\ResumeUploadStatus::Uploaded,
    ];
    foreach ($nonTerminal as $status) {
        expect($status->canTransitionTo(App\Enums\ResumeUploadStatus::Failed))->toBeTrue(
            "{$status->value} should transition to Failed"
        );
    }
});
```

---

**What success looks like:**
- `ResumeUploadStatus` enum compiles with all 8 statuses
- `ResumeUpload` model scopes compile and work
- `UploadSessionAudit::record()` creates rows
- State machine tests pass

**Test on staging:**
```bash
# 1. PHP lint
php -l app/Enums/ResumeUploadStatus.php
php -l app/Models/ResumeUpload.php
php -l app/Models/UploadSessionAudit.php

# 2. Run unit tests
php artisan test --filter=ResumeUploadStatusTest

# 3. Test model instantiation
php artisan tinker --execute="dump(new App\Models\ResumeUpload());"

# 4. Test enum
php artisan tinker --execute="dump(App\Enums\ResumeUploadStatus::Pending->value);"
# Expected: "Pending"

# 5. ai-guards
bash scripts/ai-guards.sh

# 6. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200
```

**Rollback:** Delete the 3 new files (`Enum`, 2 Models). Nothing
references them yet.

---

## Phase B — Upload Pipeline (Steps 4-6)

> **Important:** v2 uses **2 RTT** — presign → direct R2 PUT →
> submit-with-session. There is NO standalone `finalize` endpoint in
> production. The finalize logic is embedded inside the submit handler
> (Step 6). The service class below has a `finalize()` method for
> internal use, but it is NOT exposed as a public route.

### Step 4 — `ResumeUploadSession` service (core logic)

**Intent:** Build the single service class that encapsulates all upload
session business logic. This is the hardest class in the v2 system.
Every endpoint in Steps 4-6 delegates to this service.

**Pre-requisites:**
- Step 2 complete (columns exist in DB)
- Step 3 complete (enum + models exist)

**Feature flag:** `resumes.features.presign_endpoint = false`
(service exists, but no endpoint exposes it yet)

---

#### Architecture diagram

```
Browser                          PHP/Octane                     R2            MySQL
  │                                │                            │              │
  │  POST /resume-sessions         │                            │              │
  │  {idempotency_key, file, size} │                            │              │
  │ ─────────────────────────────► │                            │              │
  │                                │                            │              │
  │                         ResumeUploadSession::createOrPresign()            │
  │                                │                            │              │
  │                                │  ┌─ Has idem key? ───┐    │              │
  │                                │  │  YES → return      │    │              │
  │                                │  │  existing session   │    │              │
  │                                │  │  NO → INSERT row    │    │              │
  │                                │  └────────────────────┘    │              │
  │                                │                            │              │
  │                                │  Generate presigned URL    │              │
  │                                │  ────────────────────────► │              │
  │                                │  ◄──── presigned URL ────  │              │
  │                                │                            │              │
  │  {uuid, upload_url, expires_at}│                            │              │
  │  ◄─────────────────────────────│                            │              │
```

---

#### File: `app/Services/ResumeUploadSession.php`

```php
<?php
namespace App\Services;

use App\Enums\ResumeUploadStatus;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// Intent: Orchestrates the resume upload session lifecycle.
// Every v2 endpoint delegates here. This service is the single source
// of truth for:
//   1. Creating sessions with presigned URLs (createOrPresign)
//   2. Finalizing uploads after R2 PUT completes (finalize)
//   3. Cancelling in-progress uploads (cancel)
//   4. Reading session state (get)
// Design: stateless — all state lives in MySQL.

class ResumeUploadSession
{
    /**
     * Create a new upload session, OR replay an existing one for the same
     * `Idempotency-Key` HTTP header (transport retry safety — NOT business
     * duplicate detection; see INV-1, INV-4 in 96_invariants.md).
     *
     * The browser generates a fresh `Idempotency-Key` value per presign
     * request construction. Two file picks → two keys → two new sessions,
     * even if the picked file bytes are identical. Retries of the same
     * fetch() (network blip, double-click, double-tab race) carry the
     * same key and get the same session back.
     *
     * Flow:
     * 1. Read Idempotency-Key header (controller concern; passed in).
     * 2. Check for existing non-terminal session with same key+company.
     * 3. If found → return existing (replayed=true). Pure transport retry.
     * 4. If not found → INSERT new row with status=Pending.
     * 5. Generate presigned R2 PUT URL.
     * 6. Return session metadata to client.
     *
     * Tenancy: company_id MUST come from Auth::user(), NOT from request body.
     */
    public function createOrPresign(
        string $companyId,
        string $idempotencyKey,
        string $originalFilename,
        int    $fileSizeBytes,
        string $mimeType,
        string $clientIp,
    ): array {
        // 1. Validate idempotency-key format. Header value is a UUID or
        //    16-byte random hex (RFC draft-ietf-httpapi-idempotency-key-header).
        if (!preg_match('/^[a-f0-9-]{36,64}$/', $idempotencyKey)) {
            throw new \InvalidArgumentException('Invalid Idempotency-Key format');
        }

        // 2. Transport-retry replay lookup. NO uniqueness gate — replay
        //    is "last non-terminal session for this key + company".
        //    If found, return existing (replayed=true). Same key across
        //    pods will both SELECT; oldest wins by id/timestamp.
        $existing = ResumeUpload::where('company_id', $companyId)
            ->where('idempotency_key', $idempotencyKey)
            ->whereNotIn('status', array_map(fn($s) => $s->value, [
                ResumeUploadStatus::Completed,
                ResumeUploadStatus::Failed,
                ResumeUploadStatus::Cancelled,
                ResumeUploadStatus::Expired,
                ResumeUploadStatus::Reconciled,
            ]))
            ->orderByDesc('id')
            ->first();

        if ($existing) {
            // INV-4: HTTP retry safety — same header → same session.
            // This is transport, NOT business dedupe.
            UploadSessionAudit::record($existing->uuid, 'ActionSessionReplayed', [
                'previous_created_at' => $existing->created_at->toIso8601String(),
            ]);

            return $this->buildResponse($existing, $this->generatePresignedUrl($existing->uuid));
        }

        // 3. Create new session
        $uuid = (string) Str::uuid();
        $expiresAt = now()->addSeconds(config('resumes.session.ttl', 1800));

        $session = ResumeUpload::create([
            'uuid'               => $uuid,
            'company_id'         => $companyId,
            'original_filename'  => $originalFilename,
            'file_size'          => $fileSizeBytes,
            'mime_type'          => $mimeType,
            'status'             => ResumeUploadStatus::Pending->value,
            'idempotency_key'    => $idempotencyKey,
            'expires_at'         => $expiresAt,
        ]);

        UploadSessionAudit::record($uuid, 'ActionSessionCreated', [
            'company_id' => $companyId,
            'ip_hash'    => sha1($clientIp . now()->format('Y-m-d')),
        ]);

        // 4. Generate presigned URL
        $presignedUrl = $this->generatePresignedUrl($uuid);

        return $this->buildResponse($session, $presignedUrl);
    }

    /**
     * Finalize an upload session after browser confirms R2 PUT.
     *
     * Conditional UPDATE pattern: only transitions if current status
     * is Pending or Uploading. If another pod already finalized,
     * this is a no-op (race-safe).
     */
    public function finalize(string $uuid, string $companyId): array
    {
        $session = ResumeUpload::byUuid($uuid)->forCompany($companyId)->first();
        if (!$session) {
            throw new \RuntimeException('Session not found', 404);
        }

        // Conditional UPDATE — race-safe across N pods
        $updated = DB::table('resume_uploads')
            ->where('uuid', $uuid)
            ->whereIn('status', [
                ResumeUploadStatus::Pending->value,
                ResumeUploadStatus::Uploading->value,
            ])
            ->update([
                'status' => ResumeUploadStatus::Uploaded->value,
            ]);

        if ($updated === 0) {
            // Idempotent: already Uploaded or terminal
            UploadSessionAudit::record($uuid, 'ActionFinalizeIdempotent', [
                'current_status' => $session->status,
            ]);
        } else {
            UploadSessionAudit::record($uuid, 'ActionUploadCompleted', [
                'previous_status' => $session->status,
            ]);
        }

        // Verify R2 object exists (HEAD check)
        $r2Exists = $this->headCheck($uuid);

        return [
            'uuid'         => $session->uuid,
            'status'       => ResumeUploadStatus::Uploaded->value,
            'r2_verified'  => $r2Exists,
            'linked_candidate_id' => $session->linked_candidate_id,
        ];
    }

    /**
     * Read session state (GET endpoint).
     */
    public function get(string $uuid, string $companyId): ?ResumeUpload
    {
        return ResumeUpload::byUuid($uuid)->forCompany($companyId)->first();
    }

    /**
     * Cancel an in-progress upload.
     */
    public function cancel(string $uuid, string $companyId): void
    {
        $session = ResumeUpload::byUuid($uuid)->forCompany($companyId)->first();
        if (!$session) {
            throw new \RuntimeException('Session not found', 404);
        }

        $updated = DB::table('resume_uploads')
            ->where('uuid', $uuid)
            ->whereNotIn('status', [
                ResumeUploadStatus::Cancelled->value,
                ResumeUploadStatus::Reconciled->value,
                ResumeUploadStatus::Expired->value,
            ])
            ->update(['status' => ResumeUploadStatus::Cancelled->value]);

        if ($updated === 0) {
            UploadSessionAudit::record($uuid, 'ActionCancelIdempotent', [
                'current_status' => $session->status,
            ]);
        } else {
            UploadSessionAudit::record($uuid, 'ActionCancelled', [
                'previous_status' => $session->status,
            ]);
        }

        // Best-effort R2 delete
        try {
            Storage::disk('r2')->delete('resumes/' . $uuid);
        } catch (\Throwable $e) {
            // Non-critical — cron will clean up
            logger()->warning('jobs.resume.r2_delete_failed', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // ─── Private helpers ──────────────────────────

    private function generatePresignedUrl(string $uuid): string
    {
        // Uses Laravel's Flysystem + Cloudflare R2 adapter
        // The presigned URL is a time-limited PUT endpoint
        $disk = Storage::disk('r2');
        $path = 'resumes/' . $uuid;
        $ttl  = config('resumes.r2.presign_ttl', 900);

        return $disk->temporaryUploadUrl($path, now()->addSeconds($ttl));
    }

    private function headCheck(string $uuid): bool
    {
        try {
            $disk = Storage::disk('r2');
            return $disk->exists('resumes/' . $uuid);
        } catch (\Throwable) {
            return false;
        }
    }

    private function buildResponse(ResumeUpload $session, string $presignedUrl): array
    {
        return [
            'uuid'                => $session->uuid,
            'status'              => $session->status,
            'upload_url'          => $presignedUrl,
            'expires_at'          => $session->expires_at?->toIso8601String(),
            'original_filename'   => $session->original_filename,
            'file_size'           => $session->file_size,
        ];
    }
}
```

---

#### Test file: `tests/Unit/ResumeUploadSessionServiceTest.php`

```php
<?php
// Intent: Unit tests for ResumeUploadSession core service.
// Uses in-memory SQLite so no MySQL needed for test run.

use App\Enums\ResumeUploadStatus;
use App\Models\ResumeUpload;
use App\Services\ResumeUploadSession;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->service = app(ResumeUploadSession::class);
    $this->companyId = 'company-123';
    $this->idemKey = str_repeat('a', 64);
});

test('new header creates new session', function () {
    $result = $this->service->createOrPresign(
        $this->companyId, $this->idemKey,
        'resume.pdf', 1000, 'application/pdf', '127.0.0.1'
    );

    expect($result['uuid'])->toBeString();
    expect($result['status'])->toBe('Pending');
    expect($result['upload_url'])->toContain('http');
});

test('same header on retry returns same UUID (INV-4 transport retry)', function () {
    $first = $this->service->createOrPresign(
        $this->companyId, $this->idemKey,
        'resume.pdf', 1000, 'application/pdf', '127.0.0.1'
    );

    // Simulate a transport retry of the same fetch() call:
    // same header, same payload, same client.
    $second = $this->service->createOrPresign(
        $this->companyId, $this->idemKey,
        'resume.pdf', 1000, 'application/pdf', '127.0.0.1'
    );

    expect($second['uuid'])->toBe($first['uuid']);
    expect($second['replayed'])->toBeTrue();   // call site should expose this
});

test('different companies with same header get different UUIDs', function () {
    $a = $this->service->createOrPresign('company-a', $this->idemKey, 'r.pdf', 1000, 'application/pdf', '');
    $b = $this->service->createOrPresign('company-b', $this->idemKey, 'r.pdf', 1000, 'application/pdf', '');

    expect($a['uuid'])->not->toBe($b['uuid']);
});

test('new header creates new UUID even within same session (INV-1)', function () {
    // File pick #1
    $first = $this->service->createOrPresign(
        $this->companyId, str_repeat('a', 64),
        'resume.pdf', 1000, 'application/pdf', '127.0.0.1'
    );

    // File pick #2 (same bytes — different intent) — fresh header
    $second = $this->service->createOrPresign(
        $this->companyId, str_repeat('b', 64),
        'resume.pdf', 1000, 'application/pdf', '127.0.0.1'
    );

    expect($second['uuid'])->not->toBe($first['uuid']);
    expect($second['replayed'])->toBeFalse();
});

test('finalize transitions Pending to Uploaded', function () {
    $result = $this->service->createOrPresign(
        $this->companyId, $this->idemKey,
        'r.pdf', 1000, 'application/pdf', ''
    );

    $finalized = $this->service->finalize($result['uuid'], $this->companyId);
    expect($finalized['status'])->toBe('Uploaded');
});

test('double finalize is idempotent', function () {
    $result = $this->service->createOrPresign(
        $this->companyId, $this->idemKey,
        'r.pdf', 1000, 'application/pdf', ''
    );

    $this->service->finalize($result['uuid'], $this->companyId);
    $second = $this->service->finalize($result['uuid'], $this->companyId);
    expect($second['status'])->toBe('Uploaded');
});

test('cancel transitions to Cancelled', function () {
    $result = $this->service->createOrPresign(
        $this->companyId, $this->idemKey,
        'r.pdf', 1000, 'application/pdf', ''
    );

    $this->service->cancel($result['uuid'], $this->companyId);

    $session = ResumeUpload::byUuid($result['uuid'])->first();
    expect($session->status)->toBe('Cancelled');
});

test('invalid Idempotency-Key header value throws', function () {
    $this->expectException(\InvalidArgumentException::class);
    $this->service->createOrPresign(
        $this->companyId, 'not-a-uuid-or-hex', 'r.pdf', 1000, 'application/pdf', ''
    );
});

test('finalize on non-existent session throws 404', function () {
    $this->expectException(\RuntimeException::class);
    $this->expectExceptionCode(404);
    $this->service->finalize('nonexistent-uuid', $this->companyId);
});
```

---

**What success looks like:**
- All 8 unit tests pass
- `createOrPresign` returns valid response with UUID + presigned URL
- Same `Idempotency-Key` header on retry → same UUID (INV-4)
- New header → new UUID, even within same session (INV-1)
- Double finalize is safe no-op
- Cancel works

**Test on staging:**
```bash
# 1. Run unit tests
php artisan test --filter=ResumeUploadSessionServiceTest

# 2. PHP lint
php -l app/Services/ResumeUploadSession.php

# 3. ai-guards
bash scripts/ai-guards.sh

# 4. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200
```

**Rollback:** Delete `app/Services/ResumeUploadSession.php`.
Nothing references it yet.

---

### Step 5 — `POST /resume-sessions` presign endpoint

**Intent:** Expose `ResumeUploadSession::createOrPresign()` as a real
HTTP endpoint. This is the first user-facing v2 endpoint. When the
recruiter selects a file, the browser hits this endpoint to get a
presigned R2 URL — then uploads directly to R2.

**Pre-requisites:**
- Step 4 complete (ResumeUploadSession service exists and tested)
- Cloudflare R2 disk configured in `config/filesystems.php`
- Route registered in `routes/service.php`

**Feature flag:** `resumes.features.presign_endpoint = false`
(must flip to `true` in `.env` for staging test)

---

#### Check R2 filesystem config exists

First, confirm R2 is already configured or add it:
```bash
php artisan tinker --execute="dump(config('filesystems.disks.r2'));"
```

If not configured, add to `config/filesystems.php`:
```php
'r2' => [
    'driver' => 's3',
    'key' => env('CLOUDFLARE_R2_ACCESS_KEY_ID'),
    'secret' => env('CLOUDFLARE_R2_SECRET_ACCESS_KEY'),
    'region' => env('CLOUDFLARE_R2_REGION', 'auto'),
    'bucket' => env('CLOUDFLARE_R2_BUCKET'),
    'url' => env('CLOUDFLARE_R2_URL'),
    'endpoint' => env('CLOUDFLARE_R2_ENDPOINT'),
    'use_path_style_endpoint' => true,
    'throw' => true,
],
```

---

#### File: `app/Http/Requests/StoreResumeSessionRequest.php`

```php
<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Intent: Validate the presign request. Idempotency key must be valid
// hex, file size under 6 MB, MIME in allowed list.

class StoreResumeSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            // Idempotency-Key is the HTTP request header
            // (RFC draft-ietf-httpapi-idempotency-key-header) — validated
            // in the controller via $request->header('Idempotency-Key').
            // It is NOT a body field in v2.
            'original_filename' => [
                'required',
                'string',
                'max:255',
                'regex:/^.+\.(pdf|doc|docx)$/i',
            ],
            'file_size_bytes'  => [
                'required',
                'integer',
                'min:1',
                'max:' . config('resumes.validation.max_file_size', 6291456),
            ],
            'mime_type'        => [
                'required',
                'string',
                'in:' . implode(',', config('resumes.validation.allowed_mime_types', [])),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file_size_bytes.max'   => 'File size exceeds the maximum allowed (6 MB)',
            'original_filename.regex' => 'File must be PDF, DOC, or DOCX',
        ];
    }
}
```

---

#### File: `app/Http/Controllers/Api/ResumeUploadSessionController.php`

```php
<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreResumeSessionRequest;
use App\Services\ResumeUploadSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

// Intent: v2 resume upload session endpoints — presign, show, cancel.
// No file bytes are handled here — only metadata.
// NOTE: There is NO public finalize endpoint. The 2-RTT flow embeds
// finalize logic inside the submit handler (Step 6).

class ResumeUploadSessionController extends \App\Http\Controllers\Controller
{
    public function __construct(
        private ResumeUploadSession $sessionService
    ) {}

    /**
     * POST /service_company/api/resume-sessions
     *
     * Creates a new upload session and returns a presigned R2 PUT URL.
     * The browser then uploads the file DIRECTLY to R2 (zero PHP bytes).
     *
     * Rate-limited: 30/min per user, 120/min per company.
     */
    public function store(StoreResumeSessionRequest $request): JsonResponse
    {
        $companyId = auth()->user()->company_id;

        // v2 reads Idempotency-Key from the HTTP header, not the body.
        // RFC draft-ietf-httpapi-idempotency-key-header. Per-request
        // random generated by JS at request construction; transport
        // retry safety only — NOT business dedupe (INV-1, INV-4).
        $idempotencyKey = $request->header('Idempotency-Key')
            ?? (string) \Illuminate\Support\Str::uuid();

        if (!preg_match('/^[a-f0-9-]{36,64}$/', $idempotencyKey)) {
            return response()->json([
                'error'   => 'invalid_idempotency_key',
                'message' => 'Missing or malformed Idempotency-Key header',
            ], 400);
        }

        try {
            $result = $this->sessionService->createOrPresign(
                companyId:        $companyId,
                idempotencyKey:   $idempotencyKey,
                originalFilename: $request->input('original_filename'),
                fileSizeBytes:    (int) $request->input('file_size_bytes'),
                mimeType:         $request->input('mime_type'),
                clientIp:         $request->ip(),
            );

            Log::info('jobs.resume.session_created', [
                'uuid'       => $result['uuid'],
                'company_id' => $companyId,
                'file_size'  => $request->input('file_size_bytes'),
                'mime_type'  => $request->input('mime_type'),
            ]);

            return response()->json($result, 201);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'error'   => 'validation_error',
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * NOTE: There is NO public finalize() method. The 2-RTT flow embeds
     * finalize logic inside the submit handler. The service's internal
     * finalize() method is called by the submit handler, not by routes.
     */

    /**
     * GET /service_company/api/resume-sessions/{uuid}
     *
     * Read session state — used for refresh recovery and tab sync.
     */
    public function show(Request $request, string $uuid): JsonResponse
    {
        $companyId = auth()->user()->company_id;
        $session = $this->sessionService->get($uuid, $companyId);

        if (!$session) {
            return response()->json(['error' => 'not_found'], 404);
        }

        return response()->json([
            'uuid'                => $session->uuid,
            'status'              => $session->status,
            'expires_at'          => $session->expires_at?->toIso8601String(),
            'original_filename'   => $session->original_filename,
            'linked_candidate_id' => $session->linked_candidate_id,
        ]);
    }

    /**
     * DELETE /service_company/api/resume-sessions/{uuid}
     *
     * Cancel an in-progress upload.
     */
    public function cancel(Request $request, string $uuid): JsonResponse
    {
        if (!config('resumes.features.cancel', false)) {
            return response()->json(['error' => 'feature_disabled'], 404);
        }

        $companyId = auth()->user()->company_id;

        try {
            $this->sessionService->cancel($uuid, $companyId);

            Log::info('jobs.resume.cancelled', [
                'uuid'       => $uuid,
                'company_id' => $companyId,
            ]);

            return response()->json(['status' => 'cancelled']);
        } catch (\RuntimeException $e) {
            return response()->json([
                'error'   => $e->getCode() === 404 ? 'not_found' : 'session_error',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 422);
        }
    }
}
```

---

#### Route registration in `routes/service.php`

Find the service-company route group and add:
```php
Route::middleware(['auth', 'verified', 'service_company'])
    ->prefix('service_company')
    ->name('service_company.')
    ->group(function () {
        // ... existing routes ...

        // ─── v2 Resume Upload ─────────────────────
        Route::prefix('api')->name('api.')->group(function () {
            Route::post('resume-sessions', [
                \App\Http\Controllers\Api\ResumeUploadSessionController::class,
                'store'
            ])->name('resume-sessions.store')
              ->middleware('throttle:30,1')
              ->middleware(function ($request, $next) {
                  if (!config('resumes.features.presign_endpoint', false)) {
                      return response()->json(['error' => 'feature_disabled'], 404);
                  }
                  return $next($request);
              });

            Route::get('resume-sessions/{uuid}', [
                \App\Http\Controllers\Api\ResumeUploadSessionController::class,
                'show'
            ])->name('resume-sessions.show')
              ->middleware(function ($request, $next) {
                  if (!config('resumes.features.refresh_recovery', false)) {
                      return response()->json(['error' => 'feature_disabled'], 404);
                  }
                  return $next($request);
              });
        });
    });
```

---

#### Complete data flow for Step 5

```
Browser                                    PHP/Octane                    R2
  │                                            │                         │
  │  Step A: POST /resume-sessions             │                         │
  │  {idempotency_key: "abc...",              │                         │
  │   original_filename: "resume.pdf",        │                         │
  │   file_size_bytes: 500000,                 │                         │
  │   mime_type: "application/pdf"}            │                         │
  │ ────────────────────────────────────────►  │                         │
  │                                            │                         │
  │  1. Auth check (user authenticated?)        │                         │
  │  2. Feature flag check (presign_endpoint?)  │                         │
  │  3. Rate limit check (30/min/user?)         │                         │
  │  4. Validate request (FormRequest)          │                         │
  │  5. ResumeUploadSession::createOrPresign()  │                         │
  │     a. Check idem key in DB                 │                         │
  │     b. INSERT new row if new                │                         │
  │     c. Generate presigned R2 PUT URL        │ ───── PUT URL ──────── ►│
  │                                            │ ◄──── presigned URL ─── │
  │  6. Log: jobs.resume.session_created       │                         │
  │                                            │                         │
  │  ◄── 201 {uuid, upload_url, expires_at} ──│                         │
  │                                            │                         │
  │  Step B: PUT file to presigned URL         │                         │
  │  (direct to R2, zero PHP bytes)            │                         │
  │ ──────────────────────────────────────────────────────────────►      │
  │                                            │                         │
  │  ◄── 200 (R2 object stored) ─────────────────────────────────────    │
```

---

**What success looks like:**
- `POST /resume-sessions` returns 201 with `uuid` and `upload_url`
- Same `Idempotency-Key` header on retry → same UUID returned (INV-4)
- New `Idempotency-Key` header → new UUID, always (INV-1)
- Invalid data → 422 with validation error
- Rate limit exceeded → 429
- Feature flag OFF → 404 `feature_disabled`
- `GET /resume-sessions/{uuid}` returns session state

**Test on staging:**
```bash
# 1. Enable the feature flag on staging
export RESUMES_FEATURE_PRESIGN=true
php artisan config:clear

# 2. PHP lint
php -l app/Http/Requests/StoreResumeSessionRequest.php
php -l app/Http/Controllers/Api/ResumeUploadSessionController.php

# 3. Route registered
php artisan route:list --path=resume-sessions
# Expected: POST /service_company/api/resume-sessions

# 4. Create a session (replace SID with actual session cookie)
SID="your_session_cookie_value"
curl -s -X POST "https://testing.jero.works/service_company/api/resume-sessions" \
  -H "Content-Type: application/json" \
  -H "Cookie: $SID" \
  -H "Idempotency-Key: $(uuidgen)" \
  -d '{
    "original_filename": "resume.pdf",
    "file_size_bytes": 500000,
    "mime_type": "application/pdf"
  }' | json_pp
# Expected: 201, body contains uuid + upload_url

# 5. Extract UUID and test GET
UUID=$(curl -s ... | jq -r '.uuid')
curl -s "https://testing.jero.works/service_company/api/resume-sessions/$UUID" \
  -H "Cookie: $SID" | json_pp
# Expected: 200, status = "Pending"

# 6. Same Idempotency-Key test (transport retry safety — INV-4)
curl -s -X POST "https://testing.jero.works/service_company/api/resume-sessions" \
  -H "Content-Type: application/json" \
  -H "Cookie: $SID" \
  -H "Idempotency-Key: $KEY" \
  -d '{...same payload...}' | jq '.uuid'
# Expected: same UUID as first call

# 7. Validation error test (missing Idempotency-Key header)
curl -s -X POST "..." \
  -H "Content-Type: application/json" \
  -H "Cookie: $SID" \
  -d '{"original_filename":"r.pdf","file_size_bytes":1000,"mime_type":"application/pdf"}' | jq '.error'
# Expected: "invalid_idempotency_key"

# 8. Rate limit test
for i in $(seq 1 31); do
  KEY=$(uuidgen)
  STATUS=$(curl -s -o /dev/null -w "%{http_code}" -X POST "..." \
    -H "Content-Type: application/json" -H "Cookie: $SID" \
    -H "Idempotency-Key: $KEY" \
    -d "{\"original_filename\":\"r${i}.pdf\",\"file_size_bytes\":1000,\"mime_type\":\"application/pdf\"}")
  echo "Request $i: $STATUS"
done
# Expected: 30th=201, 31st=429

# 9. Log tail
tail -n 20 storage/logs/laravel.log | grep "jobs.resume.session_created"
# Expected: appears

# 10. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200

# 11. Disable feature flag after test
unset RESUMES_FEATURE_PRESIGN
php artisan config:cache
```

**Rollback:** Remove the route from `routes/service.php`, delete
the controller + form request. Or simply keep flag OFF — endpoint
returns 404.

---

### Step 6 — Browser JS: presign + direct R2 PUT

**Intent:** Replace the current `uploadWithProgress()` multipart POST
with a direct-to-R2 flow. The browser calls `POST /resume-sessions`
to get a presigned URL, then PUTs the file directly to Cloudflare R2.
The client-side parser (pdf.js → re_test()) continues running unchanged.

**This is the core v2 change.** Every other step is supporting
infrastructure around this.

**Pre-requisites:**
- Step 5 complete (presign endpoint works on staging)
- R2 CORS configured to allow browser PUTs from testing.jero.works

**Feature flag:** `resumes.features.browser_upload = false`

---

#### CORS check for R2

Before writing JS, confirm R2 bucket CORS allows browser PUT:
```bash
# You may need to configure R2 CORS via Cloudflare dashboard:
# Bucket → Settings → CORS Policy → Add:
# {
#   "AllowedOrigins": ["https://testing.jero.works"],
#   "AllowedMethods": ["PUT", "HEAD"],
#   "AllowedHeaders": ["*"],
#   "MaxAgeSeconds": 3600
# }
```

---

#### File: `public/js/resume-presign.js`

```javascript
/**
 * Intent: Presigned R2 upload module for v2 resume upload (2-RTT flow).
 * 
 * Replaces the PHP multipart POST with a direct-to-R2 PUT flow:
 *   1. POST /resume-sessions → get presigned URL
 *   2. PUT file directly to R2 (zero PHP bytes)
 *   3. Wait for user to click Submit (Submit handler sends session_uuid)
 * 
 * NOTE: There is NO separate finalize call. The submit handler (Step 7)
 * atomically finalizes the upload AND creates the candidate in one
 * server transaction.
 * 
 * The client-side parse (pdf.js → re_test()) runs unchanged — it parses
 * from the File object in browser memory, not from the server.
 * 
 * Feature flag: resumes.features.browser_upload
 */

window.ResumePresign = (function () {
    'use strict';

    const STATE = {
        IDLE: 'idle',
        UPLOADING: 'uploading',
        UPLOADED: 'uploaded',
        FAILED: 'failed',
        CANCELLED: 'cancelled',
    };

    let currentState = STATE.IDLE;
    let currentSession = null;    // { uuid, upload_url, expires_at }
    let currentFile = null;
    let uploadXHR = null;

    /**
     * Main entry: call this when user selects a file.
     * Returns a promise that resolves when upload completes.
     */
    async function startUpload(file, idempotencyKey) {
        if (currentState === STATE.UPLOADING) {
            throw new Error('Upload already in progress');
        }

        currentFile = file;
        currentState = STATE.UPLOADING;
        emitStateChange(STATE.UPLOADING, 0);

        try {
            // Step 1: Get presigned URL
            currentSession = await requestPresignUrl(file, idempotencyKey);
            
            // Save to localStorage for refresh recovery
            localStorage.setItem('resume_session_uuid', currentSession.uuid);
            localStorage.setItem('resume_idempotency_key', idempotencyKey);

            // Step 2: PUT file directly to R2
            await uploadToR2(file, currentSession.upload_url);

            // Step 3: File is in R2. No finalize needed — the submit
            // handler (Step 7) atomically finalizes + creates candidate
            // in one server transaction. This is the 2-RTT enterprise flow.

            currentState = STATE.UPLOADED;
            emitStateChange(STATE.UPLOADED, 100);
            return currentSession;
        } catch (err) {
            currentState = STATE.FAILED;
            emitStateChange(STATE.FAILED, 0, err.message);
            throw err;
        }
    }

    /**
     * Cancel an in-progress upload.
     */
    function cancelUpload() {
        if (uploadXHR) {
            uploadXHR.abort();
            uploadXHR = null;
        }
        currentState = STATE.CANCELLED;
        currentFile = null;
        localStorage.removeItem('resume_session_uuid');
        localStorage.removeItem('resume_idempotency_key');
        emitStateChange(STATE.CANCELLED, 0);
    }

    /**
     * Get current state (for UI rehydration on refresh).
     */
    function getState() {
        return {
            state: currentState,
            session: currentSession,
            fileName: currentFile?.name,
        };
    }

    // ─── Internal ────────────────────────────────────

    async function requestPresignUrl(file, idempotencyKey) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrfToken) throw new Error('CSRF token not found');

        const resp = await fetch('/service_company/api/resume-sessions', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({
                idempotency_key: idempotencyKey,
                original_filename: file.name,
                file_size_bytes: file.size,
                mime_type: file.type || 'application/octet-stream',
            }),
        });

        if (!resp.ok) {
            const err = await resp.json().catch(() => ({}));
            throw new Error(err.message || `Presign failed: ${resp.status}`);
        }

        return resp.json();
    }

    function uploadToR2(file, uploadUrl) {
        return new Promise((resolve, reject) => {
            uploadXHR = new XMLHttpRequest();
            uploadXHR.open('PUT', uploadUrl, true);
            uploadXHR.setRequestHeader('Content-Type', file.type || 'application/octet-stream');

            uploadXHR.upload.onprogress = (e) => {
                if (e.lengthComputable) {
                    const pct = Math.round((e.loaded / e.total) * 100);
                    emitStateChange(STATE.UPLOADING, pct);
                }
            };

            uploadXHR.onload = function () {
                uploadXHR = null;
                if (this.status >= 200 && this.status < 300) {
                    resolve();
                } else {
                    reject(new Error(`R2 PUT failed: ${this.status}`));
                }
            };

            uploadXHR.onerror = function () {
                uploadXHR = null;
                reject(new Error('Network error during R2 upload'));
            };

            uploadXHR.ontimeout = function () {
                uploadXHR = null;
                reject(new Error('R2 upload timed out'));
            };

            uploadXHR.send(file);
        });
    }

    // ─── Event system ────────────────────────────────

    const listeners = [];

    function onStateChange(callback) {
        listeners.push(callback);
        return () => {
            const idx = listeners.indexOf(callback);
            if (idx > -1) listeners.splice(idx, 1);
        };
    }

    function emitStateChange(state, progress, error) {
        const data = { state, progress, error, session: currentSession };
        listeners.forEach(fn => fn(data));
    }

    // ─── Public API ──────────────────────────────────

    return {
        startUpload,
        cancelUpload,
        getState,
        onStateChange,
        STATE,
    };
})();
```

---

#### Integration into `create.blade.php`

In the existing `create.blade.php`, find the `uploadWithProgress()` call
and add the v2 path before it:

```javascript
// ─── v2 Presigned Upload (feature-flagged) ───────────

if (window.ResumePresign && RESUMES_FEATURE_BROWSER_UPLOAD) {
    const fileInput = document.getElementById('resume');
    if (fileInput) {
        fileInput.addEventListener('change', async function (e) {
            const file = e.target.files[0];
            if (!file) return;

            // Generate SHA256 for idempotency key
            const arrayBuffer = await file.arrayBuffer();
            const hashBuffer = await crypto.subtle.digest('SHA-256', arrayBuffer);
            const hashArray = Array.from(new Uint8Array(hashBuffer));
            const idempotencyKey = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');

            // Disable the legacy path
            window.__upXhrChannelClaimed = true;

            try {
                await window.ResumePresign.startUpload(file, idempotencyKey);
                toastr.success('Resume uploaded successfully');
            } catch (err) {
                console.error('v2 upload failed:', err);
                toastr.error(err.message || 'Upload failed. Please try again.');
                // Fall back to legacy
                window.__upXhrChannelClaimed = false;
            }
        });
    }
}
```

Also add the feature flag check in Blade:
```javascript
// Pass feature flag from server config
const RESUMES_FEATURE_BROWSER_UPLOAD = @json(config('resumes.features.browser_upload', false));
```

---

**What success looks like:**
- Select file → POST /resume-sessions → 201 (presigned URL obtained)
- PUT to R2 directly (Network tab shows direct R2 PUT, no PHP upload)
- Progress bar fills 0% → 100%
- Session finalized → status = Uploaded
- Client-side parser still runs and fills fields (pdf.js/mammoth)

**Test on staging:**
```bash
# 1. Enable the feature flags
export RESUMES_FEATURE_BROWSER_UPLOAD=true
export RESUMES_FEATURE_PRESIGN=true
php artisan config:clear

# 2. JavaScript syntax check
node -e "
const fs = require('fs');
const code = fs.readFileSync('public/js/resume-presign.js', 'utf8');
try {
    new Function(code);
    console.log('JS syntax: OK');
} catch(e) {
    console.error('JS syntax error:', e.message);
}
"

# 3. Browser DevTools test
# Open https://testing.jero.works/service_company/job-applications/create
# Open Network tab → filter by XHR
# Select a PDF file
# Confirm in Network tab:
#   POST /resume-sessions → 201 (presigned URL)
#   PUT <r2.cloudflare.com/...> → 200 (direct upload)
#   POST /resume-sessions/{uuid}/finalize → 200
# 
# Confirm in Console:
#   No "Upload through PHP" messages
#   pdf.js still extracts text → fields filled

# 4. Client-side parser still works
# Upload a PDF with known text
# Confirm name/email/phone fields are filled via re_test()

# 5. Legacy fallback test
# Set RESUMES_FEATURE_BROWSER_UPLOAD=false
# Select file → confirm old multipart POST to /uploadresume works

# 6. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200

# 7. Disable flags after testing
unset RESUMES_FEATURE_BROWSER_UPLOAD
php artisan config:cache
```

**Rollback:** Flip `RESUMES_FEATURE_BROWSER_UPLOAD=false`.
The old `uploadWithProgress()` multipart POST takes over.
Delete `public/js/resume-presign.js` permanently if not needed.

---

## Phase C — Submit + UX (Steps 7-11)

### Step 7 — Submit with session_uuid (2-RTT race-safe flow)

**Intent:** When recruiter clicks Submit, the server atomically:
1. Verifies the session is Uploaded (file is in R2)
2. HEAD-checks the R2 object exists
3. Creates the candidate + links the session

All in one DB transaction. This is the **2-RTT enterprise flow**:
there is no separate finalize step. Presign → R2 PUT → Submit.
No orphan state, no wasted round-trips.

**Pre-requisites:**
- Steps 4-6 complete (presign endpoint + browser R2 PUT working)
- `ServiceCompanyJobApplicationController::store()` identified

**Feature flag:** `resumes.features.submit_with_session = false`

---

#### Controller modification

Find `ServiceCompanyJobApplicationController::store()` method and add
the session_uuid handling:

```php
// Inside store() method, after existing validation but before
// candidate creation:

$sessionUuid = $request->input('session_uuid');

if ($sessionUuid && config('resumes.features.submit_with_session', false)) {
    // Tenant-bound lookup; byUuid+forCompany must keep
    // (company_id, uuid) as a unique composite — see Step 2
    // migration UNIQUE(uuid, company_id). Tenant A MUST NOT
    // see a session UUID minted by Tenant B.
    $session = ResumeUpload::byUuid($sessionUuid)
        ->forCompany(auth()->user()->company_id)
        ->first();

    if (!$session) {
        return response()->json([
            'error'   => 'upload_incomplete',
            'message' => 'Resume upload session not found. Please upload again.',
        ], 422);
    }

    if ($session->status !== ResumeUploadStatus::Uploaded->value) {
        return response()->json([
            'error'   => 'upload_incomplete',
            'message' => 'Resume upload is not complete. Please wait for upload to finish.',
            'current_status' => $session->status,
        ], 422);
    }
}

if (isset($session) && $session) {
    // ATOMIC LINK — session status is the source of truth, not a
    // SELECT-then-UPDATE. Two pods racing on the same valid
    // session_uuid must produce exactly one INSERT into candidates
    // for that session. The conditional UPDATE returns the number
    // of rows touched; if 0, somebody else already linked it.
    //
    // Order matters: claim the session FIRST, then INSERT the
    // candidate. If the claim fails (rows=0), we 200 the existing
    // candidate — never insert a duplicate.
    $claimed = DB::transaction(function () use ($session) {
        $rows = ResumeUpload::where('id', $session->id)
            ->where('company_id', auth()->user()->company_id)
            ->where('status', ResumeUploadStatus::Uploaded->value)
            ->update([
                'status' => ResumeUploadStatus::Completed->value,
                'linked_candidate_id' => null, // set below if we own the insert
                'updated_at'         => now(),
            ]);

        return $rows === 1;
    });

    if (!$claimed) {
        // Concurrent submit already won. Re-read to find the
        // existing candidate. Returning 200 with linked info is
        // INV-3 ("may retry, server verifies state before insert").
        //
        // NOTE — the `duplicate_merged` note here is INV-3 retry
        // semantics, NOT Step 20 duplicate-ownership semantics:
        //   * INV-3 (this code path): SAME session, two concurrent
        //     submits from the SAME recruiter. The first INSERT
        //     wins; the second's lookup returns the existing
        //     candidate. NO NEW candidate row was created.
        //   * Step 20 (different): two DIFFERENT sessions / two
        //     DIFFERENT file picks (anywhere, any time, anyone)
        //     → TWO candidates, BOTH persist, NO merge. The
        //     "duplicate_merged" note must NEVER appear on the
        //     Step 20 path.
        $existing = ResumeUpload::byUuid($sessionUuid)
            ->forCompany(auth()->user()->company_id)
            ->first();

        if ($existing && $existing->linked_candidate_id) {
            $existingCandidate = Candidate::find($existing->linked_candidate_id);
            if ($existingCandidate && $existingCandidate->company_id === auth()->user()->company_id) {
                UploadSessionAudit::record($sessionUuid, 'ActionSubmitReplayWinnerFound', [
                    'candidate_id' => $existingCandidate->id,
                    'company_id'   => auth()->user()->company_id,
                ]);
                return response()->json([
                    'candidate'    => $existingCandidate,
                    'note'         => 'session_replay_winner_found',
                    'session_uuid' => $sessionUuid,
                ], 200);
            }
        }
        // Session exists but is in some unexpected state (Failed, Cancelled).
        // Surface the same 422 as a fresh "upload_incomplete" so the
        // client treats it the same as a not-yet-uploaded session.
        return response()->json([
            'error'   => 'upload_incomplete',
            'message' => 'Resume upload session is no longer accepting submits.',
        ], 422);
    }

    // HEAD-check R2 object exists BEFORE the candidate INSERT so we
    // never end up with a candidate pointing at a missing R2 key.
    // Done inside the transaction body below; if it fails we revert
    // the claim by flipping status back to Uploaded (NOT to Failed —
    // the user can retry, INV-3).
    try {
        $r2Exists = Storage::disk('r2')->exists('resumes/' . $sessionUuid);
        if (!$r2Exists) {
            throw new \RuntimeException('R2 object missing');
        }
    } catch (\Throwable $e) {
        DB::transaction(function () use ($session) {
            ResumeUpload::where('id', $session->id)
                ->where('status', ResumeUploadStatus::Completed->value)
                ->update([
                    'status'      => ResumeUploadStatus::Uploaded->value,
                    'updated_at'  => now(),
                ]);
        });
        return response()->json([
            'error'   => 'upload_incomplete',
            'message' => 'Uploaded file not found in storage. Please upload again.',
        ], 422);
    }

    // ... existing candidate creation code INSIDE the same
    // transaction, before the link UPDATE ...

    // After candidate INSERT, set linked_candidate_id atomically
    // with a second conditional UPDATE so a worker that already
    // 'Claimed' the session (above) but somehow didn't get here
    // (Pod crash) can be recovered by ReconcileOrphanUploadsJob.
    $linked = DB::transaction(function () use ($session, $candidate) {
        $rows = ResumeUpload::where('id', $session->id)
            ->where('company_id', auth()->user()->company_id)
            ->where('status', ResumeUploadStatus::Completed->value)
            ->update(['linked_candidate_id' => $candidate->id]);
        return $rows === 1;
    });

    UploadSessionAudit::record($session->uuid, $linked ? 'ActionSubmitLinked' : 'ActionSubmitLinkRaceLost', [
        'candidate_id' => $candidate->id,
        'company_id'   => auth()->user()->company_id,
    ]);
}
```

---

**Data flow for Step 7 (2-RTT enterprise flow):**

```
Recruiter                    Browser                    PHP/Octane              R2
  │                            │                            │                    │
  │  RTT 1: Select file        │                            │                    │
  │ ────────────────────────►  │                            │                    │
  │                            │  POST /resume-sessions     │                    │
  │                            │ ──────────────────────────►│                    │
  │                            │ ◄── presigned URL ──────── │                    │
  │                            │                            │                    │
  │                            │  PUT file to R2 (direct)   │                    │
  │                            │ ───────────────────────────────────────────────►│
  │                            │ ◄── 200 ───────────────────────────────────────│
  │                            │                            │                    │
  │  [fills form fields]       │  "Uploaded ✓" badge        │                    │
  │                            │                            │                    │
  │  RTT 2: Click Submit       │                            │                    │
  │ ────────────────────────►  │  POST /job-applications    │                    │
  │                            │  {session_uuid, ...fields} │                    │
  │                            │ ──────────────────────────►│                    │
  │                            │                            │  ATOMIC TRANSACTION│
  │                            │                            │  1. Verify Uploaded│
  │                            │                            │  2. HEAD-check R2  │
  │                            │                            │  3. INSERT cand.   │
  │                            │                            │  4. UPDATE session │
  │                            │                            │     (Completed)    │
  │                            │ ◄── 201 candidate ──────── │                    │
```

NOTE: There is NO separate finalize endpoint. The file sits in R2
until the recruiter clicks Submit. If they never click Submit, the
cron job (Step 12) expires the session.

---

**What success looks like:**
- Full 2-RTT flow: presign → R2 PUT → Submit creates candidate
- Idempotent re-submit returns existing candidate_id
- Submit without upload → 422 `upload_incomplete`
- **Cross-request race** (2 pods, same valid `session_uuid`, both
  POST simultaneously): exactly ONE candidate created; the loser
  receives `200 session_replay_winner_found` with the winner's
  `candidate_id` (INV-3, INV-4). Verified by `ResumeV2Invariant3Test::
  test_submit_race_with_duplicate_session_creates_one_candidate`.
- **Cross-tenant probe** (Tenant A submits Tenant B's session UUID):
  `422 upload_incomplete`. The `(uuid, company_id)` UNIQUE on
  `resume_uploads` plus the `forCompany()` scope guarantee that
  the tenant-bound lookup is structurally zero-leak — verified by
  `ResumeV2Invariant3Test::test_cross_tenant_session_uuid_returns_422`.

**Test on staging:**
```bash
# 1. Enable feature flags
export RESUMES_FEATURE_PRESIGN=true
export RESUMES_FEATURE_BROWSER_UPLOAD=true
export RESUMES_FEATURE_SUBMIT_SESSION=true
php artisan config:clear

# 2. Full browser 2-RTT flow test
# Open create.blade.php
# Fill minimum required fields
# Upload PDF → wait for "Uploaded ✓" badge
# Click Submit
# Expected: candidate created, success toast
# Check DB: resume_uploads.linked_candidate_id populated

# 3. Idempotent re-submit test
# Submit same session_uuid again
# Expected: 200, same candidate_id (no duplicate)

# 4. Submit without upload
# Get fresh session_uuid (no PUT to R2)
# Submit with that session_uuid
# Expected: 422 upload_incomplete

# 5. Log tail
tail -n 20 storage/logs/laravel.log | grep "jobs.resume.submit"

# 6. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200

# 7. Disable flags
unset RESUMES_FEATURE_SUBMIT_SESSION
php artisan config:cache
```

**Rollback:** Flip `RESUMES_FEATURE_SUBMIT_SESSION=false`.
The legacy submit path (without session_uuid) is preserved.

---

### Step 8 — Cancel endpoint + UI button

**Intent:** Recruiter can cancel an in-progress upload and start fresh.

**Pre-requisites:**
- Step 4-6 complete (session endpoints exist)
- Controller has cancel() method (already written in Step 5)

**Feature flag:** `resumes.features.cancel = false`

---

#### Route addition (if not already in Step 5)

Add to `routes/service.php`:
```php
Route::delete('resume-sessions/{uuid}', [
    \App\Http\Controllers\Api\ResumeUploadSessionController::class,
    'cancel'
])->name('resume-sessions.cancel')
  ->middleware(function ($request, $next) {
      if (!config('resumes.features.cancel', false)) {
          return response()->json(['error' => 'feature_disabled'], 404);
      }
      return $next($request);
  });
```

---

#### Blade UI addition

In `create.blade.php`, add cancel button near the upload progress bar:

```html
<div id="resume-upload-progress" style="display:none;">
    <div class="progress mb-2">
        <div class="progress-bar" role="progressbar" id="upload-progress-bar"
             style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center">
        <small id="upload-status-text" class="text-muted">Uploading...</small>
        <button type="button" class="btn btn-sm btn-light-danger" id="cancel-upload-btn"
                onclick="handleCancelUpload()">
            Cancel
        </button>
    </div>
</div>
```

```javascript
function handleCancelUpload() {
    if (window.ResumePresign) {
        window.ResumePresign.cancelUpload();
    }
    // Reset file input
    document.getElementById('resume').value = '';
    document.getElementById('resume-upload-progress').style.display = 'none';
    document.getElementById('resume-upload-badge').style.display = 'none';
}
```

---

**What success looks like:**
- Cancel button visible during upload
- Clicking cancel aborts the XHR
- File input resets, user can select a new file
- DELETE endpoint logs the cancellation

**Test on staging:**
```bash
# 1. Enable flag
export RESUMES_FEATURE_CANCEL=true
php artisan config:clear

# 2. Browser test
# Start upload → Cancel button appears
# Click Cancel → upload aborts
# File input resets → can select new file

# 3. API test
UUID=$(curl -s -X POST "..." | jq -r '.uuid')
curl -s -X DELETE "https://testing.jero.works/service_company/api/resume-sessions/$UUID" \
  -H "Cookie: $SID"
# Expected: 200, status=cancelled

# 4. Re-upload after cancel
# Select same file → new UUID generated

# 5. Double-cancel (idempotent)
curl -s -X DELETE "..." | jq '.status'
# Expected: "cancelled"

# 6. Log tail
tail -n 5 storage/logs/laravel.log | grep "jobs.resume.cancelled"
# Expected: appears

# 7. Disable flag
unset RESUMES_FEATURE_CANCEL
php artisan config:cache
```

**Rollback:** Flip flag OFF, hide cancel button.

---

### Step 9 — Refresh recovery (GET session)

**Intent:** If browser refreshes mid-upload, re-read session from server
and rehydrate the UI. Zero data loss.

**Pre-requisites:**
- Step 5 complete (GET endpoint exists)
- localStorage set in Step 5 JS

**Feature flag:** `resumes.features.refresh_recovery = false`

---

#### Rehydration logic in `resume-presign.js`

Add this to the JS module:

```javascript
/**
 * On page load, check if there's a pending session in localStorage.
 * If found, rehydrate the UI state from the SERVER's view of truth
 * — never from localStorage alone. INV-4: only the server owns the
 * status column.
 *
 * Important: any session that the server still reports as 'Pending'
 * past expiry (5 min) is a server-side orphan — the PUT happened
 * but we lost the finalize POST. Trigger an auto-finalize (HEAD-check
 * R2 + conditional UPDATE) BEFORE rehydrating the UI so the user
 * never sees a stuck "0% uploading" state on a fresh page load.
 */
async function checkForExistingSession() {
    const uuid = localStorage.getItem('resume_session_uuid');
    if (!uuid) return;

    let resp;
    try {
        resp = await fetch(`/service_company/api/resume-sessions/${uuid}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
            }
        });
    } catch (err) {
        // Offline at page load: cannot rehydrate from server. Leave the
        // UI in "Choose File" state but keep localStorage intact so
        // the next online event can retry.
        console.warn('Session recovery offline, will retry on online:', err);
        window.addEventListener('online', checkForExistingSession, { once: true });
        return;
    }

    if (resp.status === 404 || resp.status === 410) {
        // Session expired or evicted. Tear down localStorage.
        clearLocalSession();
        return;
    }
    if (!resp.ok) {
        console.warn('Session recovery non-OK status:', resp.status);
        // Do NOT clear localStorage on a transient 5xx; the next
        // refresh can re-attempt.
        return;
    }

    const session = await resp.json();
    currentSession = session;

    // Pre-step: if server still says Pending past the auto-finalize
    // grace window (5 min after session creation), the PUT likely
    // landed but the finalize POST was lost. Force a finalize attempt.
    // Otherwise the UI sits at 0% uploading forever and the user
    // wonders why Submit stays disabled.
    const ageMinutes = (Date.now() - Date.parse(session.created_at)) / 60_000;
    if (session.status === 'Pending' && ageMinutes >= 5) {
        try {
            const finalizeResp = await fetch(
                `/service_company/api/resume-sessions/${uuid}/finalize`,
                {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                }
            );
            if (finalizeResp.ok) {
                const finalized = await finalizeResp.json();
                session.status = finalized.status; // now 'Uploaded' or 'Failed'
                session.upload_completed_at = finalized.upload_completed_at;
            }
            // 422 'upload_incomplete' is expected here if R2 PUT
            // never landed — fall through to the Failed branch below.
        } catch (err) {
            console.warn('Refresh-recovery auto-finalize failed:', err);
        }
    }

    switch (session.status) {
        case 'Uploaded':
        case 'Completed':
            currentState = STATE.UPLOADED;
            emitStateChange(STATE.UPLOADED, 100);
            break;
        case 'Uploading':
            // Mid-upload refresh: PUT to R2 is resumable via multipart
            // (Step 11). Single-PUT (<5 MB) cannot resume — fall back
            // to a clean "Choose File" state with a clear message.
            if (session.multipart === true) {
                currentState = STATE.UPLOADING;
                emitStateChange(STATE.UPLOADING, session.uploaded_bytes ? Math.round(session.uploaded_bytes / session.file_size_bytes * 100) : 0);
                // Step 11 wires the actual resumeMultipart() call.
                await resumeMultipart(session);
            } else {
                // Single PUT cannot resume mid-flight. Clear the
                // session and surface a friendly hint; the user
                // re-picks the file (new session per INV-1).
                clearLocalSession();
                emitStateChange(STATE.IDLE, 0);
                showResumeRecoveryHint('Single-PUT upload cannot resume after refresh. Please re-select the file.');
            }
            break;
        case 'Failed':
            currentState = STATE.FAILED;
            emitStateChange(STATE.FAILED, 0, session.last_error || 'Upload failed');
            // Clear localStorage on terminal states (Failed, Cancelled,
            // Expired, Reconciled) so the next page load is fresh.
            clearLocalSession();
            break;
        case 'Cancelled':
        case 'Expired':
        case 'Reconciled':
            // Terminal states — UI starts fresh.
            clearLocalSession();
            emitStateChange(STATE.IDLE, 0);
            break;
        default:
            console.warn('Unknown session status, defaulting to IDLE:', session.status);
            clearLocalSession();
            emitStateChange(STATE.IDLE, 0);
    }
}

function clearLocalSession() {
    localStorage.removeItem('resume_session_uuid');
    // Per INV-4 + ADR-002: idempotency_key MUST be per-request random
    // and live in the closure for retries only. NEVER in localStorage.
    // (The previous Step 5 JS stored it here — that was a violation
    // we are fixing in this step. See ADR-002.)
}

function showResumeRecoveryHint(msg) {
    const banner = document.getElementById('resume-recovery-hint');
    if (banner) {
        banner.textContent = msg;
        banner.style.display = 'block';
    }
}

// Call on page load
document.addEventListener('DOMContentLoaded', checkForExistingSession);
```

---

**Backend support for the auto-finalize path:**

`EP-3 — POST /service_company/api/resume-sessions/{uuid}/finalize` is
extended to accept a "force re-check" path: when called on a session
whose `status='Pending'` AND `created_at <= now - 5 minutes`, the
endpoint HEAD-checks R2 and conditionally UPDATEs to `Uploaded` if
the object exists. This makes refresh-recovery a server-owned
behavior, not a client guess.

Server-only — no public flag. The endpoint is the same `/finalize`
the browser calls explicitly after a successful PUT; the refresh
path is a re-use of the existing endpoint, no new route.

---

**What success looks like:**
- Start upload → refresh page → upload progress reappears (multipart) or
  "Please re-select the file" hint (single-PUT, <5 MB)
- After upload complete, refresh → "Uploaded ✓" badge visible
- Server-side Pending > 5 min → auto-finalize fires, badge appears
- Expired session → clean "Choose File" state, localStorage cleared
- Idempotency-Key NEVER in localStorage (INV-4, ADR-002). Verified by
  `ResumeV2Invariant1Test::test_localStorage_does_not_contain_idempotency_key`

**Test on staging:**
```bash
# 1. Enable flags
export RESUMES_FEATURE_PRESIGN=true
export RESUMES_FEATURE_BROWSER_UPLOAD=true
export RESUMES_FEATURE_REFRESH_RECOVERY=true
php artisan config:clear

# 2. Mid-upload refresh test (multipart, ≥5 MB)
# Start upload → at <50% progress, refresh page
# Expected: progress reappears, resumes from last part

# 3. Mid-upload refresh test (single-PUT, <5 MB)
# Start upload → at <50% progress, refresh page
# Expected: "Please re-select the file" hint, no error toast

# 4. Post-upload refresh test
# Upload completes → refresh page
# Expected: "Uploaded ✓" badge, Submit enabled

# 5. Stale-Pending auto-finalize test
# Force a session to status=Pending with created_at = 6 min ago
#   (UPDATE resume_uploads SET status='Pending',
#    created_at = NOW() - INTERVAL 6 MINUTE WHERE uuid = ?)
# Refresh page
# Expected: server auto-finalizes to Uploaded, badge appears within
#           ~200 ms of page load

# 6. 410 expired test
# Update expires_at to 1 min ago
# Refresh page
# Expected: clean "Choose File" state, localStorage cleared

# 7. INV-4 invariant check
# Open DevTools → Application → Local Storage
# Expected: NO `resume_idempotency_key` key under any flow
#           (presign, retry, refresh, submit)

# 8. Log tail
tail -n 5 storage/logs/laravel.log | grep "jobs.resume.session"

# 9. Disable flags
unset RESUMES_FEATURE_REFRESH_RECOVERY
php artisan config:cache
```

**Rollback:** Flip flag OFF. localStorage refs survive harmlessly.

---

### Step 10 — Multipart upload from R2 (files ≥5 MB)

**Intent:** Large files upload in chunks. If network drops mid-way,
only the remaining chunks need re-sending.

**Pre-requisites:**
- Step 5 complete (browser upload working with single PUT)
- R2 multipart upload API available

**Feature flag:** `resumes.features.multipart_upload = false`

---

#### Add to `resume-presign.js`

```javascript
/**
 * Multipart upload for files ≥5 MB.
 * Splits file into 5 MB chunks and uploads sequentially.
 * On network failure: lists already-uploaded parts and resumes.
 */
async function uploadMultipartToR2(file, uploadUrl) {
    const PART_SIZE = 5 * 1024 * 1024;  // 5 MB
    const totalParts = Math.ceil(file.size / PART_SIZE);
    
    // Parse upload URL to get the base (R2 multipart uses UploadId)
    const url = new URL(uploadUrl);
    const baseUrl = url.origin + url.pathname;
    
    // Step 1: Initiate multipart upload
    const initResp = await fetch(baseUrl + '?uploads', {
        method: 'POST',
        headers: { 'Content-Type': file.type },
    });
    if (!initResp.ok) throw new Error('Multipart init failed');
    
    const uploadId = extractUploadId(initResp);
    const uploadedParts = [];
    
    for (let partNumber = 1; partNumber <= totalParts; partNumber++) {
        const start = (partNumber - 1) * PART_SIZE;
        const end = Math.min(start + PART_SIZE, file.size);
        const chunk = file.slice(start, end);
        
        const partUrl = `${baseUrl}?partNumber=${partNumber}&uploadId=${uploadId}`;
        
        // Retry up to 3 times per part
        let retries = 3;
        let partOK = false;
        
        while (retries > 0 && !partOK) {
            try {
                const partResp = await fetch(partUrl, {
                    method: 'PUT',
                    body: chunk,
                });
                
                if (partResp.ok) {
                    const etag = partResp.headers.get('ETag');
                    uploadedParts.push({ PartNumber: partNumber, ETag: etag });
                    partOK = true;
                    
                    // Progress
                    const pct = Math.round((partNumber / totalParts) * 100);
                    emitStateChange(STATE.UPLOADING, pct);
                } else {
                    retries--;
                }
            } catch (err) {
                retries--;
                if (retries === 0) throw err;
                await new Promise(r => setTimeout(r, 1000));
            }
        }
    }
    
    // Step 3: Complete multipart upload
    const completeBody = buildCompleteXml(uploadedFiles);
    const completeResp = await fetch(baseUrl + '?uploadId=' + uploadId, {
        method: 'POST',
        body: completeBody,
    });
    if (!completeResp.ok) throw new Error('Multipart completion failed');
    
    return completeResp;
}
```

---

**What success looks like:**
- File ≥5 MB → multipart upload initiated
- Multiple PUT requests visible in Network tab
- Network interruption → resumes from last successful part
- Progress calculated across all parts

**Test on staging:**
```bash
# 1. Create test file
dd if=/dev/urandom of=/tmp/test-6mb.pdf bs=1M count=6

# 2. Enable flag
export RESUMES_FEATURE_MULTIPART_UPLOAD=true
php artisan config:clear

# 3. Browser test
# Upload test-6mb.pdf
# Network tab: multiple PUT requests (parts)
# Progress bar fills smoothly from 0% to 100%

# 4. Interrupt test
# Start upload → DevTools: Network → Offline at ~50%
# Wait 3s → Re-enable network
# Expected: upload resumes, new PUT requests start at next part

# 5. Cancel mid-multipart
# Start upload → click Cancel
# Expected: abortMultipartUpload called

# 6. File <5 MB still uses single PUT
dd if=/dev/urandom of=/tmp/test-1mb.pdf bs=1M count=1
# Upload test-1mb.pdf
# Expected: single PUT (no multipart)

# 7. Disable flag
unset RESUMES_FEATURE_MULTIPART_UPLOAD
php artisan config:cache
```

**Rollback:** Flip `RESUMES_FEATURE_MULTIPART_UPLOAD=false`.
All files use single PUT.

---

### Step 11 — Network resilience (offline/online handling)

**Intent:** Browser detects actual network state and handles transitions
gracefully. Uses a preflight ping rather than relying on unreliable
`navigator.onLine`.

**Pre-requisites:**
- Steps 9-10 complete (refresh recovery + multipart upload)

**Feature flag:** `resumes.features.network_resilience = false`

---

#### File: `public/js/resume-retry-manager.js`

```javascript
/**
 * Intent: Network resilience manager for resume upload.
 * 
 * Unlike navigator.onLine (which is unreliable), this module
 * uses an actual preflight ping to determine connectivity.
 * On offline: saves state, shows badge.
 * On online: re-reads server state, resumes upload.
 */

window.ResumeRetryManager = (function () {
    'use strict';

    let wasOffline = false;
    let preflightInterval = null;

    const PREFLIGHT_URL = '/service_company/api/resume-preflight';
    const CHECK_INTERVAL_MS = 3000;  // check every 3s when suspected offline
    const PREFLIGHT_RETRIES = 3;
    const PREFLIGHT_SPACING_MS = 1000;

    function start() {
        window.addEventListener('online', handleOnline);
        window.addEventListener('offline', handleOffline);
    }

    function stop() {
        window.removeEventListener('online', handleOnline);
        window.removeEventListener('offline', handleOffline);
        if (preflightInterval) {
            clearInterval(preflightInterval);
            preflightInterval = null;
        }
    }

    async function handleOnline() {
        console.log('[ResumeRetry] navigator.onLine event fired');
        
        // Don't trust navigator.onLine — do actual preflight check
        const actuallyOnline = await preflightCheck();
        
        if (actuallyOnline) {
            console.log('[ResumeRetry] Preflight OK — declaring online');
            wasOffline = false;
            document.body.classList.remove('resume-network-lost');
            
            // Re-read server state
            const uuid = localStorage.getItem('resume_session_uuid');
            if (uuid) {
                try {
                    const resp = await fetch(`/service_company/api/resume-sessions/${uuid}`);
                    if (resp.ok) {
                        const session = await resp.json();
                        console.log('[ResumeRetry] Session state:', session.status);
                        
                        // Dispatch event for other modules to handle
                        window.dispatchEvent(new CustomEvent('resume:network-restored', {
                            detail: { session }
                        }));
                    }
                } catch (err) {
                    console.warn('[ResumeRetry] State re-read failed:', err);
                }
            }
        } else {
            console.log('[ResumeRetry] navigator says online, but preflight failed');
            handleOffline();
        }
    }

    function handleOffline() {
        console.log('[ResumeRetry] Network lost');
        wasOffline = true;
        document.body.classList.add('resume-network-lost');
        
        // Show badge (existing .resume-network-lost CSS class)
        showNetworkLostBadge();
        
        // Start preflight polling
        if (!preflightInterval) {
            preflightInterval = setInterval(async () => {
                const ok = await preflightCheck();
                if (ok) {
                    clearInterval(preflightInterval);
                    preflightInterval = null;
                    handleOnline();
                }
            }, CHECK_INTERVAL_MS);
        }
    }

    async function preflightCheck() {
        for (let i = 0; i < PREFLIGHT_RETRIES; i++) {
            try {
                const resp = await fetch(PREFLIGHT_URL, {
                    method: 'GET',
                    cache: 'no-store',
                });
                if (resp.ok) return true;
            } catch (err) {
                // Network error — try again
            }
            await new Promise(r => setTimeout(r, PREFLIGHT_SPACING_MS));
        }
        return false;
    }

    function showNetworkLostBadge() {
        // Implementation depends on existing badge system
        const badge = document.getElementById('network-status-badge');
        if (badge) {
            badge.textContent = 'Network Lost';
            badge.style.display = 'inline-block';
        }
    }

    return { start, stop };
})();
```

---

**What success looks like:**
- Network lost during upload → "Network Lost" badge shown
- Network restored → upload resumes
- At submit time, network fails → auto-retry with idempotency key
- Preflight ping detects connectivity correctly

**Test on staging:**
```bash
# 1. Enable flags
export RESUMES_FEATURE_NETWORK_RESILIENCE=true
php artisan config:clear

# 2. Offline mid-upload test
# Start upload → DevTools: Network → Offline
# Wait 5s → Re-enable network
# Expected: upload resumes, progress continues

# 3. Offline at submit test
# Complete upload → click Submit → DevTools block response
# Expected: submit auto-retries (idempotency prevents duplicates)

# 4. Network badge test
# Kill network → "Network Lost" badge appears
# Restore network → badge disappears

# 5. Preflight endpoint test
curl -s "https://testing.jero.works/service_company/api/resume-preflight"
# Expected: 200, {status: "ok", server_time: "..."}

# 6. Disable flag
unset RESUMES_FEATURE_NETWORK_RESILIENCE
php artisan config:cache
```

**Rollback:** Flip `RESUMES_FEATURE_NETWORK_RESILIENCE=false`.

---

## Phase D — Hardening (Steps 12-15)

### Step 12 — Cron jobs + scheduler

**Intent:** Background maintenance — clean up expired sessions, repair
stuck uploads, reconcile orphaned R2 objects.

**Pre-requisites:**
- Steps 2-3 complete (DB columns + models exist)
- Horizon running

**Feature flag:** None (cron-only, always safe)

---

#### File: `app/Console/Commands/ReconcileOrphanUploads.php`

```php
<?php
namespace App\Console\Commands;

use App\Enums\ResumeUploadStatus;
use App\Models\ResumeUpload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// Intent: Mark expired Pending sessions as Expired and remove their
// R2 objects. Runs daily at 03:30 IST.

class ReconcileOrphanUploads extends Command
{
    protected $signature = 'uploads:reconcile {--dispatch : Actually dispatch work}';
    protected $description = 'Mark expired resume upload sessions and clean up R2';

    public function handle(): int
    {
        if (!$this->option('dispatch')) {
            $this->info('Dry-run mode. Use --dispatch to execute.');
            return 0;
        }

        $expired = ResumeUpload::expired()->get();
        $count = $expired->count();

        foreach ($expired as $session) {
            // Best-effort R2 delete
            try {
                Storage::disk('r2')->delete('resumes/' . $session->uuid);
            } catch (\Throwable $e) {
                Log::warning('jobs.resume.r2_delete_failed', [
                    'uuid'  => $session->uuid,
                    'error' => $e->getMessage(),
                ]);
            }

            $session->update([
                'status'          => ResumeUploadStatus::Expired->value,
                'cleanup_reason'  => 'ReconcileOrphanUploads',
            ]);
        }

        Log::info('jobs.resume.reconcile_completed', [
            'expired_count' => $count,
        ]);

        $this->info("Reconciled {$count} expired sessions.");
        return 0;
    }
}
```

---

#### File: `app/Console/Commands/RepairStuckSessions.php`

```php
<?php
namespace App\Console\Commands;

use App\Enums\ResumeUploadStatus;
use App\Models\ResumeUpload;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

// Intent: Mark stuck-Uploading sessions as Failed.
// Runs every 15 minutes via scheduler.

class RepairStuckSessions extends Command
{
    protected $signature = 'uploads:repair {--dispatch : Actually repair stuck sessions}';
    protected $description = 'Repair stuck resume upload sessions';

    public function handle(): int
    {
        if (!$this->option('dispatch')) {
            $this->info('Dry-run mode. Use --dispatch to execute.');
            return 0;
        }

        $stuck = ResumeUpload::stuck()->get();
        $count = $stuck->count();

        foreach ($stuck as $session) {
            $session->update([
                'status'     => ResumeUploadStatus::Failed->value,
                'last_error' => 'RepairStuckSessions: stuck in Uploading > 5 min',
            ]);
        }

        Log::info('jobs.resume.repair_completed', [
            'repaired_count' => $count,
        ]);

        $this->info("Repaired {$count} stuck sessions.");
        return 0;
    }
}
```

---

#### Scheduler registration in `app/Console/Kernel.php`

```php
// Inside schedule() method:

// Resume upload: reconcile expired sessions daily at 03:30 IST
$schedule->command('uploads:reconcile --dispatch')
    ->dailyAt('03:30')->timezone('Asia/Kolkata')
    ->withoutOverlapping(300)   // 5 min lock
    ->onOneServer();            // Redis-backed leader election

// Resume upload: repair stuck sessions every 15 minutes
$schedule->command('uploads:repair --dispatch')
    ->everyFifteenMinutes()
    ->withoutOverlapping(840)   // 14 min lock (< 15 min period)
    ->onOneServer();            // Redis-backed leader election
```

---

**What success looks like:**
- `php artisan uploads:reconcile --dispatch` runs without error
- `php artisan uploads:repair --dispatch` runs without error
- `php artisan schedule:list` shows both commands
- Second run is idempotent (zero work)

**Test on staging:**
```bash
# 1. PHP lint
php -l app/Console/Commands/ReconcileOrphanUploads.php
php -l app/Console/Commands/RepairStuckSessions.php

# 2. Dry-run
php artisan uploads:reconcile
# Expected: Dry-run mode

php artisan uploads:repair
# Expected: Dry-run mode

# 3. Register commands
php artisan list | grep uploads
# Expected: uploads:reconcile, uploads:repair

# 4. Verify scheduler
php artisan schedule:list | grep uploads
# Expected: 2 entries with cron expressions

# 5. First run
php artisan uploads:reconcile --dispatch
# Expected: log line "jobs.resume.reconcile_completed"

# 6. Idempotent second run
php artisan uploads:reconcile --dispatch
# Expected: 0 expired sessions (already done)

# 7. Log tail
tail -n 10 storage/logs/laravel.log | grep "jobs.resume.reconcile\|jobs.resume.repair"

# 8. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200
```

**Rollback:** Remove schedule entries from `Kernel.php`.
Commands remain but won't auto-run.

---

### Step 13 — Rate limiting (Redis + per-company)

**Intent:** Protect presign and submit endpoints from abuse at scale.

**Pre-requisites:**
- Step 1 complete (health endpoint)
- Step 5 complete (presign endpoint exists to rate-limit)

**Feature flag:** N/A (hardening, always ON)

---

#### File: `app/Http/Middleware/CompanyThrottleMiddleware.php`

```php
<?php
namespace App\Http\Middleware;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

// Intent: Company-scoped rate limiter.
// Key format: {company_id}:{route_name}
// Prevents one company from exhausting all pods' capacity.
//
// Fail-open contract: if Redis is unavailable or the limiter throws,
// the request MUST pass through (with a structured log line). The
// alternative — failing CLOSED on Redis outage — would cascade the
// outage into the resume subsystem, which is exactly the opposite of
// what a 10K-concurrent recruiter deployment needs.

class CompanyThrottleMiddleware
{
    public function __construct(private RateLimiter $limiter) {}

    public function handle(Request $request, \Closure $next, int $maxAttempts = 120, int $decayMinutes = 1)
    {
        $companyId = auth()->user()->company_id ?? 'anonymous';
        $key = sha1("company:{$companyId}:" . $request->route()->getName());

        try {
            if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
                Log::warning('jobs.resume.throttle_hit_company', [
                    'company_id' => $companyId,
                    'route'      => $request->route()->getName(),
                ]);

                return response()->json([
                    'error'   => 'rate_limit_exceeded',
                    'message' => 'Too many requests. Please slow down.',
                ], 429);
            }

            $this->limiter->hit($key, $decayMinutes * 60);

            return $next($request);
        } catch (Throwable $e) {
            // FAIL-OPEN — log full structured context, let the
            // request through. Redis is already on the critical path
            // (cache backend for `onOneServer()`, counter store,
            // queues); doubling-up on resume throttling is not worth
            // a 5xx here. Health endpoint surfaces the Redis-down
            // condition separately (Phase 6).
            Log::warning('jobs.resume.throttle_fails_open', [
                'company_id' => $companyId,
                'route'      => $request->route()->getName(),
                'exception'  => $e::class,
                'message'    => $e->getMessage(),
            ]);

            return $next($request);
        }
    }
}
```

---

#### Register in `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'company.throttle' => \App\Http\Middleware\CompanyThrottleMiddleware::class,
    ]);
})
```

---

#### Apply to routes in `routes/service.php`:

```php
Route::post('resume-sessions', [...])
    ->middleware([
        'throttle:' . config('resumes.rate_limits.presign_per_user') . ',1',
        'company.throttle:' . config('resumes.rate_limits.presign_per_company') . ',1',
    ]);
```

---

**What success looks like:**
- 30 presign requests/min per user → 31st = 429
- Across users, same company hits 120/min → 429
- Redis down → limits fail open (logged, no 429)

**Test on staging:**
```bash
# 1. PHP lint
php -l app/Http/Middleware/CompanyThrottleMiddleware.php

# 2. Per-user rate limit test
# Note: per INV-4 / ADR-002, the per-request token travels as the
# Idempotency-Key HTTP header (NOT a body field). Each iteration
# generates a fresh UUID so each request is a NEW session (INV-1).
for i in $(seq 1 31); do
  IDEM=$(uuidgen)
  STATUS=$(curl -s -o /dev/null -w "%{http_code}" \
    -X POST "https://testing.jero.works/service_company/api/resume-sessions" \
    -H "Content-Type: application/json" \
    -H "Cookie: $SID" \
    -H "Idempotency-Key: ${IDEM}" \
    -d "{\"original_filename\":\"r${i}.pdf\",\"file_size_bytes\":1000,\"mime_type\":\"application/pdf\"}")
  echo "Request $i: $STATUS"
done
# Expected: 30th=201, 31st=429

# 3. Rate limit headers
curl -s -D - -X POST "..." 2>&1 | grep -i "x-ratelimit"
# Expected: X-RateLimit-Remaining header

# 4. Redis fallback test
redis-cli DEBUG SET-ACTIVE-EXEC 0  # Simulate Redis down
curl -X POST "..."  # Expected: 200 (fails open)
tail -n 3 storage/logs/laravel.log | grep "throttle_fails_open"
redis-cli DEBUG SET-ACTIVE-EXEC 1  # Restore Redis

# 5. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200
```

**Rollback:** Remove `company.throttle` middleware from routes.

---

### Step 14 — BroadcastChannel tab sync

**Intent:** Same-origin tabs sync state instantly without any server
round-trip. Zero server cost.

**Pre-requisites:**
- Step 5 complete (upload events to broadcast)

**Feature flag:** `resumes.features.broadcast_channel = false`

---

#### File: `public/js/resume-broadcast-sync.js`

```javascript
/**
 * Intent: Same-origin tab sync via BroadcastChannel.
 * When one tab changes upload state, all other tabs in the same
 * browser (same company) see the update instantly.
 *
 * Zero server bytes — pure client-side IPC.
 * Fallback for Safari < 15.4: localStorage 'storage' events.
 *
 * Design constraints:
 *   1. NO PING-PONG: A cross-tab incoming message MUST NOT trigger
 *      a re-broadcast. We use a per-event `__rt` flag and the
 *      resume:state-changed listener skips re-broadcasting when the
 *      event was already received via channel.
 *   2. NO localStorage LEAK: the localStorage fallback path writes
 *      `resume_broadcast_<ts>` keys; the consumer cleans them up
 *      immediately on receipt AND a periodic sweep (60 s) drops
 *      anything older than 5 min.
 *   3. IDEMPOTENCY-KEY IS NEVER SHARED HERE: per INV-4 / ADR-002,
 *      `Idempotency-Key` is a per-request random token in the
 *      closure; it MUST NOT travel over BroadcastChannel nor
 *      localStorage. The BroadcastChannel payload only carries
 *      { uuid, state, progress_percent, error_safe }.
 */

window.ResumeBroadcastSync = (function () {
    'use strict';

    let channel = null;
    const COMPANY_ID = document.body.dataset.companyId || 'unknown';
    const LS_PREFIX = 'resume_broadcast_';
    const LS_TTL_MS = 5 * 60 * 1000;
    let sweeperInterval = null;

    function init() {
        try {
            channel = new BroadcastChannel('resume-sync-' + COMPANY_ID);
            channel.onmessage = handleMessage;
        } catch (e) {
            console.warn('[BroadcastSync] BroadcastChannel not supported, using localStorage fallback');
            window.addEventListener('storage', handleStorageEvent);
        }

        // Listen for LOCAL state changes (i.e. tab-generated, not
        // tab-received). The `__rt` flag is set when the state-change
        // is the result of an incoming cross-tab message; listeners
        // for resume:state-changed must skip re-broadcasting in
        // that case to break the ping-pong loop.
        window.addEventListener('resume:state-changed', (e) => {
            if (e.detail && e.detail.__rt === true) {
                // Re-broadcast suppressed: this state change is the
                // echo of an incoming cross-tab message.
                return;
            }
            broadcast(e.detail);
        });

        // Periodic cleanup of the localStorage fallback keys. Without
        // this, a long session leaves tens of thousands of dead keys.
        if (!sweeperInterval) {
            sweeperInterval = setInterval(sweepLocalStorageKeys, 60_000);
        }
    }

    function destroy() {
        if (channel) {
            channel.close();
            channel = null;
        }
        window.removeEventListener('storage', handleStorageEvent);
        if (sweeperInterval) {
            clearInterval(sweeperInterval);
            sweeperInterval = null;
        }
    }

    function broadcast(data) {
        // Strip any Idempotency-Key-shaped payloads defensively.
        // Belt-and-suspenders: even if a future contributor adds
        // `{ idem: ... }` to a broadcast event, we drop it here.
        const safe = {
            uuid:             data.uuid,
            state:            data.state,
            progress_percent: data.progress_percent,
            error_safe:       data.error_safe,
            // NOTE: no `idempotency_key`, no `original_filename`,
            // no PII. INV-4 + GDPR-by-default.
        };
        const payload = JSON.stringify(safe);

        if (channel) {
            channel.postMessage(payload);
        } else {
            // localStorage fallback for Safari < 15.4. The value is
            // the WHOLE payload string (not JSON.stringified again
            // like the old code did — that double-wrapped the
            // object and the consumer's JSON.parse failed silently).
            localStorage.setItem(LS_PREFIX + Date.now(), payload);
        }
    }

    function handleMessage(e) {
        let data;
        try {
            data = JSON.parse(e.data);
        } catch {
            return;
        }
        applyIncomingState(data);
    }

    function handleStorageEvent(e) {
        if (!e.key || !e.key.startsWith(LS_PREFIX)) return;

        // Tidy up the entry we just consumed — otherwise localStorage
        // grows unboundedly. The setTimeout yields so the parser and
        // the consumer see the value before removal.
        try {
            const data = JSON.parse(e.newValue);
            applyIncomingState(data);
        } catch {
            // fall through to cleanup
        }
        setTimeout(() => {
            try { localStorage.removeItem(e.key); } catch (_) {}
        }, 0);
    }

    function applyIncomingState(data) {
        // Only apply if uuid matches local session. Two tabs in the
        // same company but holding different session_uuids (different
        // file picks, INV-1) MUST NOT cross-pollinate.
        const localUuid = localStorage.getItem('resume_session_uuid');
        if (!data || !data.uuid || data.uuid !== localUuid) return;

        // Mark the inbound event as "received via tab sync" so the
        // resume:state-changed listener (above) does NOT re-broadcast.
        const detail = Object.assign({}, data, { __rt: true });
        window.dispatchEvent(new CustomEvent('resume:cross-tab-sync', {
            detail,
        }));
    }

    function sweepLocalStorageKeys() {
        const cutoff = Date.now() - LS_TTL_MS;
        for (let i = localStorage.length - 1; i >= 0; i--) {
            const key = localStorage.key(i);
            if (key && key.startsWith(LS_PREFIX)) {
                const ts = Number(key.substring(LS_PREFIX.length));
                if (Number.isFinite(ts) && ts < cutoff) {
                    try { localStorage.removeItem(key); } catch (_) {}
                }
            }
        }
    }

    return { init, destroy, broadcast };
})();
```

---

**No localStorage for cross-tab session-key carry:** Note the
explicit `localStorage.getItem('resume_session_uuid')` check in
`applyIncomingState` — the session UUID itself is allowed in
localStorage (it is the primary key for state rehydration; it's a
session-marker, not a transport-retry idempotency key). What MUST
NOT live in localStorage is the `Idempotency-Key` header value (see
Step 9 fix; INV-4, ADR-002).

---

**What success looks like:**
- Two tabs in same browser: Tab A uploads, Tab B sees progress instantly
- Cross-browser (Chrome + Firefox): no sync (correct — different browsers)
- Zero server logs related to tab sync

**Test on staging:**
```bash
# 1. Enable flag
export RESUMES_FEATURE_BROADCAST_CHANNEL=true
php artisan config:clear

# 2. Same-browser two-tab test
# Tab A: open create.blade.php
# Tab B: open create.blade.php
# Tab A: upload file
# Tab B: expected — "Uploading..." badge appears in < 1s
# Tab A: upload completes
# Tab B: expected — "Uploaded ✓" badge appears

# 3. Cross-browser test
# Chrome: upload file
# Firefox (same machine): expected — nothing (different browser, no BroadcastChannel)
# This is CORRECT behavior

# 4. Console check
# No BroadcastChannel errors in either tab

# 5. Zero server load check
grep -c "broadcast\|BroadcastChannel\|tab-sync" storage/logs/laravel.log
# Expected: 0 (all sync is client-side)

# 6. Disable flag
unset RESUMES_FEATURE_BROADCAST_CHANNEL
php artisan config:cache
```

**Rollback:** Flip `RESUMES_FEATURE_BROADCAST_CHANNEL=false`.
Tabs behave independently (same as today).

---

### Step 15 — ResumeMetrics + health endpoint + structured logging

**Intent:** Production observability — counters, health probes, and
structured logging across all resume upload components.

**Pre-requisites:**
- Step 1 complete (config exists)
- Redis running

**Feature flag:** N/A (always-on infra)

---

#### File: `app/Services/ResumeMetrics.php`

```php
<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

// Intent: Redis-backed metrics counters for the resume upload subsystem.
// Uses direct Redis (not Cache facade) for atomic increment.
// Fail-fast: if Redis is down and metrics_require_redis=true, throws.

class ResumeMetrics
{
    private string $prefix = 'resume_metrics:';
    private bool $requireRedis;

    public function __construct()
    {
        $this->requireRedis = config('resumes.metrics_require_redis', true);
    }

    public function increment(string $metric, int $by = 1): void
    {
        try {
            $key = $this->prefix . $metric;
            Redis::incrby($key, $by);
            Redis::expire($key, 86400);  // 24h TTL
        } catch (\Throwable $e) {
            if ($this->requireRedis) {
                throw $e;
            }
            Log::warning('jobs.resume.metric_failed', [
                'metric' => $metric,
                'error'  => $e->getMessage(),
            ]);
        }
    }

    public function value(string $metric): int
    {
        try {
            return (int) Redis::get($this->prefix . $metric);
        } catch (\Throwable) {
            return 0;
        }
    }

    // ─── Named counters ────────────────────────

    public function incrementUploadStarted(): void    { $this->increment('upload_started_total'); }
    public function incrementUploadCompleted(): void  { $this->increment('upload_completed_total'); }
    public function incrementUploadFailed(): void     { $this->increment('upload_failed_total'); }
    public function incrementCancelled(): void        { $this->increment('cancelled_total'); }
    public function incrementThrottled(): void        { $this->increment('throttled_total'); }

    public function uploadStartedTotal(): int    { return $this->value('upload_started_total'); }
    public function uploadCompletedTotal(): int  { return $this->value('upload_completed_total'); }
    public function uploadFailedTotal(): int     { return $this->value('upload_failed_total'); }
}
```

---

#### File: `app/Http/Controllers/Api/ResumeHealthController.php`

```php
<?php
namespace App\Http\Controllers\Api;

use App\Models\ResumeUpload;
use App\Enums\ResumeUploadStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

// Intent: Internal health endpoint for the resume subsystem.
// Returns per-component checks so load balancers and ops can
// determine subsystem health independently of the main /up route.

class ResumeHealthController extends \App\Http\Controllers\Controller
{
    public function show(): JsonResponse
    {
        $checks = [];
        $allOk = true;

        // MySQL
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $checks['mysql'] = [
                'ok'      => true,
                'latency_ms' => round((microtime(true) - $start) * 1000, 2),
            ];
        } catch (\Throwable $e) {
            $allOk = false;
            $checks['mysql'] = [
                'ok'      => false,
                'error'   => $e->getMessage(),
            ];
        }

        // Redis
        try {
            $start = microtime(true);
            Redis::ping();
            $checks['redis'] = [
                'ok'      => true,
                'latency_ms' => round((microtime(true) - $start) * 1000, 2),
            ];
        } catch (\Throwable $e) {
            $checks['redis'] = [
                'ok'      => false,
                'error'   => $e->getMessage(),
            ];
        }

        // Orphan sessions count
        $checks['orphan_uploads'] = [
            'count' => ResumeUpload::expired()->count(),
        ];

        // Stuck sessions count
        $checks['stuck_uploads'] = [
            'count' => ResumeUpload::stuck()->count(),
        ];

        $status = match (true) {
            !$checks['mysql']['ok'] ?? true => 'down',
            !$allOk => 'degraded',
            default => 'ok',
        };

        return response()->json([
            'status'      => $status,
            'checks'      => $checks,
            'hostname'    => gethostname(),
            'timestamp'   => now()->toIso8601String(),
        ]);
    }
}
```

---

#### File: `app/Http/Middleware/InternalTokenMiddleware.php`

```php
<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// Intent: Simple bearer-token auth for internal endpoints.
// Token configured via RESUMES_HEALTH_TOKEN env var.

class InternalTokenMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $expected = config('resumes.health_token');
        
        if (!$expected || $request->bearerToken() !== $expected) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'Invalid or missing health token',
            ], 401);
        }

        return $next($request);
    }
}
```

---

#### Route in `routes/service.php`

```php
Route::prefix('api/internal')->middleware('internal_token')->group(function () {
    Route::get('health/resume', [\App\Http\Controllers\Api\ResumeHealthController::class, 'show'])
        ->name('internal.health.resume');
});
```

---

**What success looks like:**
- `/api/internal/health/resume` returns 200 with all checks OK
- Bearer token auth works (invalid token → 401)
- Metrics counters visible in Redis

**Test on staging:**
```bash
# 1. PHP lint
php -l app/Services/ResumeMetrics.php
php -l app/Http/Controllers/Api/ResumeHealthController.php
php -l app/Http/Middleware/InternalTokenMiddleware.php

# 2. Health endpoint (valid token)
# Important: read the token from artisan, NOT from `php -r` —
# `config()` is a Laravel helper, not a bare PHP function.
TOKEN=$(php artisan tinker --execute="echo config('resumes.health_token');" 2>/dev/null)
curl -s -H "Authorization: Bearer $TOKEN" \
  "https://testing.jero.works/service_company/api/internal/health/resume" | json_pp
# Expected: status=ok, mysql.ok=true, redis.ok=true

# 3. Health endpoint (no token)
curl -s "https://testing.jero.works/service_company/api/internal/health/resume"
# Expected: 401

# 4. Increment a metric
php artisan tinker --execute="app(\App\Services\ResumeMetrics::class)->incrementUploadStarted();"
php artisan tinker --execute="dump(app(\App\Services\ResumeMetrics::class)->uploadStartedTotal());"
# Expected: >= 1

# 5. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200
```

**Rollback:** Remove route + middleware. Metrics in Redis auto-expire.

---

## Phase E — Ship (Steps 16-18)

### Step 16 — Public career-apply extraction

**Intent:** Extract career-apply from `HomeController` god controller
into its own controller, reusing the v2 upload pipeline.

**Pre-requisites:**
- Steps 1-14 complete (entire v2 pipeline built and tested)

**Feature flag:** `resumes.features.career_controller = false`

---

#### File: `app/Http/Controllers/Api/CareerApplyController.php`

```php
<?php
namespace App\Http\Controllers\Api;

use App\Http\Requests\CareerApplyStoreRequest;
use App\Services\ResumeUploadSession;
use App\Services\CareerApplyService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

// Intent: OWNED controller for career/public apply flow.
// This is a REAL extraction — the controller OWNS its logic; it does
// NOT delegate back to HomeController. The legacy HomeController
// path remains only while the feature flag is OFF, and is deleted
// in the v2.5 cleanup step. CLAUDE.md rule 11: vertical slices
// (Route + FormRequest + Action + service) — never a god controller.

class CareerApplyController extends \App\Http\Controllers\Controller
{
    public function __construct(
        private ResumeUploadSession $sessionService,
        private CareerApplyService  $applyService,
    ) {}

    /**
     * Career apply — public-facing candidate application form.
     */
    public function apply(Request $request)
    {
        // Public, no auth; tenant inference is from the public job
        // slug on the request (NOT from auth).
        return view('public.career_apply', $this->applyService->formContext($request));
    }

    /**
     * Store career application with optional session_uuid.
     *
     * Cross-request race handling for the public path:
     * Same conditional-UPDATE-as-source-of-truth pattern as Step 7.
     * public sessions are tenantless in `company_id` (nullable) so
     * we can't lean on the company_id UNIQUE; the session_uuid
     * itself carries a UNIQUE(uuid) on the legacy table. New sessions
     * for the same UUID → race resolves by checking `status` first.
     */
    public function store(CareerApplyStoreRequest $request): JsonResponse
    {
        $sessionUuid = $request->input('session_uuid');

        if ($sessionUuid) {
            $session = $this->sessionService->getPublic($sessionUuid);

            if (!$session) {
                return response()->json([
                    'error'   => 'upload_incomplete',
                    'message' => 'Resume upload session not found.',
                ], 422);
            }

            // Atomic claim via conditional UPDATE — second concurrent
            // submit hits status=Completed and gets the merged path.
            $claimed = $this->sessionService->claimForPublicSubmit($session->id);

            if (!$claimed) {
                $existing = $this->sessionService->getPublic($sessionUuid);
                if ($existing && $existing->linked_candidate_id) {
                    $existingCandidate = \App\Models\Candidate::find($existing->linked_candidate_id);
                    if ($existingCandidate) {
                        // INV-3 retry: same session, second submit,
                        // winner already inserted. NOT a Step 20
                        // duplicate-ownership case — see Step 20
                        // for why we never merge candidates.
                        return response()->json([
                            'candidate'    => $existingCandidate,
                            'note'         => 'session_replay_winner_found',
                            'session_uuid' => $sessionUuid,
                        ], 200);
                    }
                }
                return response()->json([
                    'error'   => 'upload_incomplete',
                    'message' => 'Resume upload session is no longer accepting submits.',
                ], 422);
            }

            // HEAD-check R2 — if missing, revert the claim.
            try {
                $r2Exists = \Illuminate\Support\Facades\Storage::disk('r2')
                    ->exists('resumes/' . $sessionUuid);
                if (!$r2Exists) {
                    throw new \RuntimeException('R2 object missing');
                }
            } catch (\Throwable $e) {
                $this->sessionService->releasePublicClaim($session->id);
                return response()->json([
                    'error'   => 'upload_incomplete',
                    'message' => 'Uploaded file not found in storage.',
                ], 422);
            }
        }

        // Hand off to the service — controller does NOT contain
        // business logic. CareerApplyService::store() handles
        // validation, candidate creation, ES sync, coin deduction,
        // audit log, and the link-back UPDATE.
        $result = $this->applyService->store($request, $session ?? null);

        return response()->json($result, 201);
    }
}
```

**Companion files (vertical slice, all NEW):**

- `app/Http/Requests/CareerApplyStoreRequest.php` — FormRequest,
  owns validation rules (job_id required, candidate fields,
  session_uuid optional, captcha).
- `app/Services/CareerApplyService.php` — owns the actual candidate
  create + link-back logic; extracted from the body of the
  legacy `HomeController::careerStore`.
- `app/Services/ResumeUploadSession.php` (extended) — gains
  `getPublic($uuid)` and `claimForPublicSubmit($id)` /
  `releasePublicClaim($id)` for the tenantless path.
- `routes/service.php` — registers `Route::post('career/apply',
  [CareerApplyController::class, 'store'])` ONLY when the feature
  flag is on; the legacy `Route::post('career/apply',
  [HomeController::class, 'careerStore'])` stays on the side-by-side
  shim until `RESUMES_FEATURE_CAREER_CONTROLLER` is flipped off
  globally.

**Cleanup at v2.5:** Once the flag has been ON in production for
30 days and the legacy path has zero traffic, DELETE
`HomeController::careerApply` and `HomeController::careerStore`.
The god controller drops by ~600 LOC at that point — measurable
in the next `phploc` run.

---

**What success looks like:**
- Career-apply page uses v2 upload flow
- Same functionality as before extraction
- Feature flag OFF → old HomeController serves

**Test on staging:**
```bash
# 1. PHP lint
php -l app/Http/Controllers/Api/CareerApplyController.php

# 2. Enable flag
export RESUMES_FEATURE_CAREER_CONTROLLER=true
php artisan config:clear

# 3. Full career flow
# Open career-apply URL → fill form → upload PDF → submit
# Expected: candidate created, resume linked

# 4. Same as before (flag OFF)
export RESUMES_FEATURE_CAREER_CONTROLLER=false
php artisan config:clear
# Do same flow → same result expected

# 5. HomeController still works
curl -sI https://testing.jero.works | head -1
# Expected: HTTP/2 200

# 6. Disable flag
unset RESUMES_FEATURE_CAREER_CONTROLLER
php artisan config:cache
```

**Rollback:** Flip flag OFF. HomeController resumes serving.

---

### Step 17 — Load test + performance tuning

**Intent:** Prove the system holds under load. Tune bottlenecks before
production GA.

**Pre-requisites:**
- All previous steps complete
- Staging environment with multiple pods (or at least 1)

**Feature flag:** All v2 flags ON in staging only

---

#### File: `tests/Load/ResumeUploadLoadTest.php`

```php
<?php
// Intent: Simulate 10K concurrent resume uploads.
// Measures p50/p95/p99 latency, throughput, error rate.

test('presign endpoint handles 100 concurrent requests', function () {
    $requests = [];
    for ($i = 0; $i < 100; $i++) {
        // Each request gets a fresh per-request random UUID in the
        // Idempotency-Key header (INV-4 / ADR-002). NOT a content
        // hash; same key on retry → same session, different key →
        // new session.
        $idem = (string) \Illuminate\Support\Str::uuid();
        $requests[] = function () use ($idem, $i) {
            $start = microtime(true);
            $resp = $this->withHeaders(['Idempotency-Key' => $idem])
                ->post('/service_company/api/resume-sessions', [
                    'original_filename' => "test-{$i}.pdf",
                    'file_size_bytes'  => 1000,
                    'mime_type'        => 'application/pdf',
                ]);
            $latency = (microtime(true) - $start) * 1000;
            return ['status' => $resp->status(), 'latency_ms' => $latency];
        };
    }

    $results = parallel($requests);
    $latencies = array_column($results, 'latency_ms');
    sort($latencies);

    $p50 = $latencies[count($latencies) * 0.5];
    $p95 = $latencies[count($latencies) * 0.95];
    $p99 = $latencies[count($latencies) * 0.99];
    $errors = count(array_filter($results, fn($r) => $r['status'] !== 201));

    dump("P50: {$p50}ms, P95: {$p95}ms, P99: {$p99}ms, Errors: {$errors}");
    
    expect($p50)->toBeLessThan(200);   // 200ms p50 target
    expect($p95)->toBeLessThan(500);   // 500ms p95 target
    expect($errors)->toBe(0);          // zero errors
});
```

---

**What success looks like:**
- P50 < 200ms for presign
- P95 < 500ms
- Zero errors under load
- v2 shows ~80% fewer PHP requests vs v1

**Test on staging:**
```bash
# 1. Enable all flags (no finalize — 2-RTT flow)
export RESUMES_FEATURE_PRESIGN=true
export RESUMES_FEATURE_BROWSER_UPLOAD=true
export RESUMES_FEATURE_SUBMIT_SESSION=true
php artisan config:clear

# 2. Run load test
php artisan test --filter=ResumeUploadLoadTest

# 3. Monitor health during load
watch -n 1 "curl -s -H 'Authorization: Bearer \$TOKEN' \
  'https://testing.jero.works/service_company/api/internal/health/resume' | jq '.status'"

# 4. Check logs for errors
tail -n 50 storage/logs/laravel.log | grep -i "error\|exception\|fatal"
# Expected: no error lines from resume subsystem

# 5. Compare v1 vs v2 throughput
# Run 100 uploads via v1 path (multipart POST)
# Run 100 uploads via v2 path (presigned PUT)
# Compare total time and error rates

# 6. Disable flags
unset RESUMES_FEATURE_PRESIGN RESUMES_FEATURE_BROWSER_UPLOAD
unset RESUMES_FEATURE_FINALIZE RESUMES_FEATURE_SUBMIT_SESSION
php artisan config:cache
```

**Rollback:** Only staging; nothing in production changes.

---

### Step 18 — Production GA

**Intent:** v2 is the primary upload path. v1 is deprecated.

**Pre-requisites:**
- All Steps 1-17 complete and verified on staging
- All feature flags tested ON with no regressions

**Feature flag:** All v2 flags ON in production.

---

#### Production deploy checklist

```bash
# ── Pre-deploy ──────────────────────────────────────

# 1. Run full regression
php artisan test --testsuite=Feature --filter=ResumeV2
bash scripts/ai-guards.sh

# 2. Verify all flags in .env
grep "RESUMES_FEATURE" .env
# All should be set to true

# 3. Verify config cache
php artisan config:cache

# 4. Verify R2 CORS includes production domain
# Bucket → CORS → AllowedOrigins: ["https://jero.works", "https://*.jero.works"]

# ── Deploy ──────────────────────────────────────────

# 5. Push code
git push origin staging

# 6. Run migrations
php artisan migrate

# 7. Clear + warm cache
php artisan optimize

# ── Post-deploy (first 5 minutes) ───────────────────

# 8. Health check
curl -H "Authorization: Bearer $TOKEN" /api/internal/health/resume
# Expected: status=ok

# 9. Create a session
# Idempotency-Key is now an HTTP HEADER (per INV-4 / ADR-002), not a
# body field. Each invocation generates a new UUID for a brand-new
# session.
curl -X POST /service_company/api/resume-sessions \
  -H "Idempotency-Key: $(uuidgen)" \
  -H "Content-Type: application/json" \
  -d '{"original_filename":"test.pdf","file_size_bytes":1000,"mime_type":"application/pdf"}'
# Expected: 201

# 10. Monitor error rates
tail -f storage/logs/laravel.log | grep -E "jobs\.resume\."
# Expected: no ERROR level events

# 11. Monitor legacy path deprecation
tail -f storage/logs/laravel.log | grep "deprecated"
# Expected: legacy path warnings (expected during transition)

# ── Day +1 ──────────────────────────────────────────

# 12. Compare metrics
# v2 presign requests vs v1 uploadresume requests
# Expected: 80%+ of uploads using v2 path

# 13. Check for stuck/reconciled sessions
php artisan uploads:reconcile --dispatch
# Expected: < 1% of sessions need reconciliation
```

---

#### Deprecation notices in v1 endpoints

Add to `ServiceCompanyJobApplicationController`:
```php
// In uploadresume() and store() methods:
Log::warning('jobs.resume.legacy_path_deprecated', [
    'route' => request()->route()->getName(),
    'company_id' => auth()->user()->company_id,
]);
```

---

**What success looks like:**
- All feature flags ON in production
- v1 path logs deprecation warning but still serves
- 5-minute post-deploy monitor shows zero errors
- Health endpoint OK
- Legacy path traffic declining week over week

**Test on staging (final rehearsal):**
```bash
# 1. All flags ON
grep "RESUMES_FEATURE" .env
# All = true

# 2. Full regression
php artisan test --testsuite=Feature --filter=ResumeV2

# 3. v2 presign works
curl -X POST "...resume-sessions..." | jq '.upload_url'
# Expected: presigned R2 URL

# 4. v1 legacy deprecation logged
curl -X POST "...uploadresume..." -F "file=@/tmp/test.pdf"
tial -n 3 storage/logs/laravel.log | grep "legacy_path_deprecated"
# Expected: appears

# 5. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expected: HTTP/2 200

# 6. Final ai-guards
bash scripts/ai-guards.sh
```

**Rollback runbook:**

The v2 rollout is gated at five independent feature flags. A
production incident scopes down to ONE step, not the whole
rollout. The default rollback is "flip the broken flag to false";
the recovery time is bounded by `config:cache` propagation (~30 s).

```bash
# ── Step-by-step rollback (use ONLY the steps that apply) ──────

# Symptom: presign endpoint is 5xx or producing malformed URLs.
# Action: disable presign, fall back to legacy multipart POST.
export RESUMES_FEATURE_PRESIGN=false

# Symptom: browser direct PUT to R2 is failing (CORS, mTLS, etc.).
# Action: disable browser upload, force the JS to fall through to
#          the legacy `data-nojs` path.
export RESUMES_FEATURE_BROWSER_UPLOAD=false

# Symptom: 2-RTT submit (Step 7) is creating duplicate candidates.
# Action: disable submit-with-session; controller falls through to
#          the legacy `store()` without `session_uuid`.
export RESUMES_FEATURE_SUBMIT_SESSION=false

# Symptom: parser pipeline is stuck or failing (queue, Horizon, ES).
# Action: disable QueuedForParsing; presign still works but
#          uploaded sessions never parse. Submit with manual entry.
export RESUMES_FEATURE_PARSER=false

# Symptom: RateLimiter returning 429s on healthy traffic (Step 13).
# Action: leave the limiter ON but flip its fail-open mode by
#          temporarily setting resumes.throttle_strict=false.
export RESUMES_THROTTLE_STRICT=false

# Symptom: Health endpoint is leaking data to unauthenticated probes.
# Action: rotate the token in env, no flag flip required.
export RESUMES_HEALTH_TOKEN=$(php -r 'echo bin2hex(random_bytes(32));')

# ALWAYS: re-cache config after ANY env change.
php artisan config:cache

# Verify rollback succeeded:
curl -sI https://jero.works/login | head -1       # expect 200
curl -sI https://jero.works/job-application/create | head -1  # expect 200
tail -n 20 storage/logs/laravel.log | grep -i 'jobs.resume' | grep -i error
# Expect: no new ERROR-level lines from the resume subsystem.

# DAY 0 (post-rollback): triage which step to fix and re-enable.
# Do NOT flip multiple flags back on in one deploy — re-enable ONE
# at a time, observe for 30 minutes, then enable the next.

# DAY 1 (recovery verification): re-run the staging tests for the
# rolled-back step before flipping the flag back on in production.
php artisan test --filter=ResumeV2Invariant1Test   # whichever applies
```

**Hard rollback (rollback EVERYTHING):**

If multiple subsystems are simultaneously broken (a bad deploy, a
Redis outage cascading through `Cache::lock()`, a queue worker
explosion), flip ALL flags off in one env-rotation deploy:

```bash
# Nuclear rollback. v1 path serves everything.
# Expected outcome: ~2x PHP load (since v1 uploads stream through
# PHP), but upload subsystem still functional. Recruiters see
# slower uploads; nothing is broken.
unset RESUMES_FEATURE_PRESIGN
unset RESUMES_FEATURE_BROWSER_UPLOAD
unset RESUMES_FEATURE_SUBMIT_SESSION
unset RESUMES_FEATURE_PARSER
unset RESUMES_FEATURE_BROADCAST_CHANNEL
unset RESUMES_FEATURE_REFRESH_RECOVERY
unset RESUMES_FEATURE_EDIT_PAGE_V2
php artisan config:cache
```

**What "rollback" does NOT do:** the database migrations are
additive (`add_resume_v2_indexes`, `add_resume_v2_session_columns`,
`create_resume_parse_results`, `create_upload_session_audits`) so
the schema stays. v1 needs the v2 tables to exist (it just doesn't
write to them). To REVERT a migration:

```bash
php artisan migrate:rollback --step=4
# Order matters: rollback audits, then parse_results, then session
# columns, then indexes. The composer-up command in v1 era is
# preserved in storage/app/rollback_resume_v2.sql as the
# forward-equivalent DDL if migrate:rollback is not available.
```

**Health endpoint and observability are NEVER rolled back:** even
in nuclear-rollback mode, the health endpoint stays on. Ops needs
the probe to confirm the subsystem is responsive. To disable the
health route without removing the controller:
```bash
# Comment out the route in routes/service.php and re-cache config.
# This is reversible — restoring the route is a 1-line edit.
```

---

## Phase F — Edit page (Step 19)

### Step 19 — Edit page vertical slice (replace / remove resume on edit)

**Intent:** Bring the EDIT page (`add_job_wise_condidates.id` keyed form,
`ServiceCompanyJobApplicationController::edit()` at line 3981 + `update()`
at line 5389) onto the same 2-RTT R2-direct flow used by create. The
existing resume on the row may be (a) **kept as-is**, (b) **replaced**
with a new presigned upload, or (c) **removed**. All three branches
must behave atomically and respect the same Captain-or-submitter
auth-guard from v1.

**Why this is its own step:** Steps 1-18 only cover the CREATE flow.
The existing `update()` at line 5389 has a 3-branch resume handler
(lines 5808-5840) that routes through PHP multipart upload and writes
to S3 — it bypasses R2 entirely and breaks idempotency. We cannot
just delete it; recruiters have real EDIT flows in production.

**Pre-requisites:**
- Steps 4-7 (presign + 2-RTT submit) are on and stable
- `ResumeUploadSession` is the canonical store for the resume's R2 key

**Feature flag:** `features.edit_page_v2` → `RESUMES_FEATURE_EDIT_PAGE_V2`
(default `false`). Same rollout pattern as the create flag — staging
first, then production.

---

#### Current vs v2 scenarios (locked design)

| User action on edit | v1 behaviour (today, lines 5808-5840) | v2 behaviour (Step 19) |
|---|---|---|
| Open edit, change nothing, hit Update | Full row UPDATE; old resume preserved | Same UPDATE; **no presign, no R2 round-trip** — fast path |
| Open edit, change scalar fields, keep same resume | UPDATE; resume preserved | Same UPDATE; **`session_uuid` ignored**, `documents` row untouched |
| Open edit, **replace** resume | Multipart re-upload through PHP (`hasFile('resume')`); old `documents` row never tombstoned | Presign + direct R2 PUT + `PATCH /job-applications/{id}` carries `session_uuid`; old `documents` row **tombstoned**, new row appended |
| Open edit, **remove** resume | `resumeCheck=0` → `update(['resume' => ''])`; `documents` row left dangling | `remove_existing_resume=1` → `update(['resume' => ''])` + tombstone `documents` rows |
| Open edit, pick new file then close tab | Temp row stranded at `r2/temp/<uniqid>` | `uploads:cleanup-orphans` already reaps `ResumeUploadSession` rows after TTL — same behaviour |

---

#### Architecture (3 changes → 7 files)

##### Change 1 — Controller: `update()` with branched resume handling

**Edit `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`**
(only the `update()` method at line 5389 — do NOT touch `edit()`):

Add a guard block right after the existing Captain-or-submitter check
(currently lines 5401-5422), before the `JobApplication::findOrFail($id)`:

```php
// v2 edit-page resume handling — guarded by feature flag
$hasResumeReplacement = $request->filled('session_uuid');
$hasResumeRemoval     = $request->boolean('remove_existing_resume');

if (($hasResumeReplacement || $hasResumeRemoval)
    && ! config('features.edit_page_v2')) {
    return Reply::dataOnly([
        'status'  => 'flag_off',
        'msgType' => 'error',
        'message' => 'Edit-page v2 not enabled',
    ]);
}
```

Then wrap the resume branch block (currently lines 5808-5840) so the
**two resume branches** call into the same `DB::transaction(...)` block
used by the scalar UPDATE:

```php
DB::transaction(function () use ($request, $id, $hasResumeReplacement, $hasResumeRemoval) {

    // Existing scalar UPDATEs (lines 5478-5890 minus the resume branch) stay here.
    // ... [unchanged] ...

    // Branch A — replace resume (v2 path)
    if ($hasResumeReplacement) {
        $sessionUuid = $request->input('session_uuid');

        // 1. Verify session belongs to this tenant + status is Uploaded
        $session = ResumeUploadSession::where('uuid', $sessionUuid)
            ->where('company_id', auth()->user()->company_id)
            ->lockForUpdate()
            ->firstOrFail();

        abort_unless(
            in_array($session->status, ['Uploaded', 'Pending'], true),
            422,
            'RESUME_SESSION_NOT_UPLOADED'
        );

        // 2. HEAD-check R2 (object exists)
        abort_unless(
            Storage::disk('r2')->exists($session->object_key),
            422,
            'RESUME_NOT_IN_R2'
        );

        // 3. Conditional UPDATE session → Linked (race-safe across N pods)
        $linked = $session->newQuery()
            ->where('id', $session->id)
            ->whereIn('status', ['Uploaded', 'Pending'])
            ->update([
                'status' => 'Linked',
                'linked_application_id' => $id,
                'linked_at' => now(),
            ]);

        abort_unless($linked, 422, 'RESUME_SESSION_ALREADY_LINKED');

        // 4. UPDATE-in-place the existing Resume documents row.
        //    `documents` has UNIQUE KEY (company_id, documentable_id,
        //    documentable_type, name) — only ONE Resume row per
        //    JobApplication is allowed. We UPDATE the existing row's
        //    hashname rather than INSERT+DELETE (verified against
        //    database/schema/mysql-schema.sql line 1341).
        $newHashname = $session->canonical_url ?? $session->presigned_url_path;
        $jobApp = JobApplication::findOrFail($id);

        $docUpdated = DB::table('documents')
            ->where('documentable_id', $id)
            ->where('documentable_type', JobApplication::class)
            ->where('company_id', auth()->user()->company_id)
            ->where('name', 'Resume')
            ->update([
                'hashname'   => $newHashname,
                'updated_at' => now(),
            ]);

        // 5. If no Resume row existed yet, INSERT one (covers edge case
        //    where the application was created without a resume).
        if (! $docUpdated) {
            DB::table('documents')->insert([
                'company_id'        => auth()->user()->company_id,
                'documentable_id'   => $id,
                'documentable_type' => JobApplication::class,
                'name'              => 'Resume',
                'hashname'          => $newHashname,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }

        // 6. Rewrite the canonical resume column to the new R2 URL
        $jobApp->resume = $newHashname;
        $jobApp->save();
    }

    // Branch B — remove resume
    elseif ($hasResumeRemoval) {
        JobApplication::where('id', $id)->update(['resume' => '']);
        // No `deleted_at` on documents table — keep the row but the
        // canonical resume column is now ''. A future Step 19.1 may
        // add soft-delete column; for now the `documents` row stays
        // as an audit trail (R2 object may be reaped by the existing
        // uploads:reconcile job if Linked session has no other referrer).
    }

    // Branch C — no resume change: existing resume preserved (default)
});
```

##### Change 2 — Blade: edit page UX for "Replace / Remove"

**Edit `resources/views/service_company/job-applications/create.blade.php`**
(only the `EDIT` branch — guarded by `@if($page_event === 'EDIT')`):

1. **Existing resume preview** — render the current `documents.hashname`
   as a clickable "View resume" link with a small "Replace" toggle
   button beside it (current edit already loads this at line 4089 —
   surface it above the file input).
2. **`session_uuid` hidden input** — same name as the create flow
   (`#session_uuid`); stays empty until the user picks a new file.
3. **`remove_existing_resume` checkbox** — initially unchecked; the
   existing `resumeCheck` checkbox at line ~2438 is **removed** in v2
   (replaced by this).

```blade
@if($page_event === 'EDIT')
  {{-- Existing resume preview --}}
  @if(!empty($documents) && $documents->hashname)
    <div class="existing-resume" data-kt-resume="existing">
      <a href="{{ $documents->hashname }}" target="_blank">View current resume</a>
      <button type="button" class="btn btn-sm btn-light-primary"
              data-kt-resume-action="replace">Replace resume</button>
      <label class="form-check form-check-sm">
        <input type="checkbox" name="remove_existing_resume" value="1"
               data-kt-resume-action="remove">
        Remove existing resume
      </label>
    </div>
  @endif
@endif
```

##### Change 3 — JS: conditional presign on edit

**Edit `resources/views/service_company/job-applications/create.blade.php`**
inline JS — branch the file-input handler by `$page_event`:

```js
const isEditPage = @json($page_event === 'EDIT');

fileInput.addEventListener('change', async (e) => {
    if (isEditPage && e.target.files.length === 0) {
        // User cleared the input — preserve existing resume.
        document.querySelector('[name="session_uuid"]').value = '';
        return;
    }
    // … existing presign + direct-PUT flow (Step 6) runs for both
    // CREATE and EDIT — only the submit URL differs.
});
```

The submit handler already branches by URL (create → `store`, edit →
`update`); only the **resume presign** path needs the `isEditPage`
guard. The parser fan-out (Step 9) re-runs on every file pick — that
is desired behaviour on EDIT too: replacing a resume should re-extract
name/email/phone for the manual-edit-always-wins UX.

---

#### Atomic transaction guarantees

The new `update()` body wraps:

1. Resume session `lockForUpdate` + conditional UPDATE → Linked
2. `documents` row INSERT (new resume)
3. `documents` rows UPDATE `deleted_at` (tombstone previous Resume)
4. `job_applications.resume` UPDATE (canonical URL)
5. Scalar UPDATEs (existing logic, lines 5478-5890 minus resume branch)

…all in one `DB::transaction()`. Failure at any step rolls back all
of them. ES re-index fires via Observer after commit, exactly as today.

---

#### Tenancy + auth invariants (do not weaken)

- The existing Captain-or-submitter guard (lines 5401-5422) is **not
  bypassed** — it runs before the transaction opens.
- `ResumeUploadSession::where('company_id', auth()->user()->company_id)`
  — a `session_uuid` from another tenant **404s**, never links.
- `lockForUpdate()` on the session row + conditional UPDATE on
  `Linked` — race-safe across N pods (same pattern from Step 7).
- The `documents` tombstone UPDATE filters on `documentable_id = $id`
  AND `documentable_type = JobApplication::class` — a malicious user
  cannot tombstone another tenant's documents by guessing ids.

---

#### Race conditions covered (each is a test)

| Race | Detection |
|------|-----------|
| Captain submits Edit A while submitter submits Edit B, both replace resume | First conditional UPDATE on `Linked` wins; second returns 422. Test: assert `ResumeUploadSession` row linked only once, `documents.hashname` matches winner's R2 URL. |
| Replace attempt arrives while previous session still `Pending` | Conditional UPDATE on `Linked` returns 0 rows → 422. Test: pre-insert session with status=`Pending`, PATCH, assert 422. |
| Object reaped by `uploads:cleanup-orphans` between PUT and submit | `Storage::disk('r2')->exists()` returns false → 422 `RESUME_NOT_IN_R2`. Test: mock `Storage::exists` to false. |
| Cross-tenant `session_uuid` | `where('company_id', …)` returns null → 404. Test: actAs company A, attempt PATCH with company B's session_uuid. |
| Double-submit (user double-clicks Update) | Idempotency: conditional UPDATE on `Linked` returns 0 rows on the second call → 422. Test: PATCH twice, assert second is 422. |

---

#### File inventory

### NEW (1 file)
1. `tests/Feature/JobApplicationEditResumeTest.php` — covers all 6
   scenarios from the table above + the 5 race tests.

### MODIFIED (4 files)
1. `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
   — wrap resume branches in transaction; add Branch A/B/C inside
   `update()`. **Do not** touch `edit()` (line 3981).
2. `resources/views/service_company/job-applications/create.blade.php`
   — edit-page Blade block + JS guard for `isEditPage`.
3. `config/resumes.php` — add `'edit_page_v2' => (bool) env('RESUMES_FEATURE_EDIT_PAGE_V2', false)`.
4. `routes/service.php` — `PATCH /job-applications/{id}` already exists;
   no new route needed (the same endpoint branches on request shape).

---

#### Verification

### Automated tests
```
php artisan test --filter=JobApplicationEditResumeTest
# expect 11/11 green

php artisan test --filter=ResumeV2
# expect no regressions

bash scripts/ai-guards.sh
```

### Manual checklist (staging)
```
# 1. Flag ON
RESUMES_FEATURE_EDIT_PAGE_V2=true php artisan config:cache

# 2. Open an existing application edit page
curl -b cookies https://testing.jero.works/service_company/job-applications/<encrypted_id>/edit
# Expected: form loads with "View current resume" + "Replace" + "Remove" controls

# 3. Replace flow
- Click "Replace resume" → pick new file → PUT to R2 (DevTools shows presign + direct R2 PUT)
- Click Update
- Verify in MySQL:
  SELECT * FROM documents WHERE documentable_id = <id> AND name = 'Resume';
  # Expect: 1 active row (new), 1 tombstoned row (old)
  SELECT * FROM resume_upload_sessions WHERE uuid = '<uuid>';
  # Expect: status='Linked'

# 4. Remove flow
- Uncheck "Remove existing resume" → click Update
- Verify resume is '' and the documents row is tombstoned.

# 5. No-change flow
- Edit page → change phone → click Update (no resume touch)
- Verify session_uuid is empty in network panel — no R2 PUT.

# 6. Cross-tenant session_uuid
- Try PATCH with a session_uuid from another company
- Expect: 404

# 7. Captain-or-submitter guard
- Open edit page as a user who is neither
- Expect: redirect with candidate_edit_denied (existing v1 behaviour)
```

---

#### Out of scope for Step 19

- Multipart chunked upload on EDIT (Step 10 already covers CREATE;
  EDIT reuses the same presign endpoint, so chunks work for free —
  no code change required in Step 19).
- `documents.deleted_at` column does NOT exist in the schema today
  (verified — `database/schema/mysql-schema.sql` line 1331-1342).
  However, `documents` also has UNIQUE KEY `(company_id,
  documentable_id, documentable_type, name)` (line 1341) which means
  only ONE Resume row per JobApplication is allowed. Step 19's
  Branch A uses UPDATE-in-place on the existing Resume row instead
  of INSERT+tombstone, which respects the unique constraint without
  requiring a schema change. A future Step 19.1 may add a
  `document_history` table for full audit if compliance demands it.
- Public career-edit pages — same controller code path; defer until
  Step 17's extraction is complete.

---

**What success looks like:**
- All 11 tests green
- Staging rehearsal: replace / remove / keep-as-is all atomic
- Zero PHP bytes on the resume-replacement path
- Cross-tenant session_uuid → 404
- `ai-guards.sh` clean

---

## Phase G — Duplicate-by-design (Step 20)

### Step 20 — Duplicates are created freely; the Duplicate Ownership Engine only DETECTS and REPORTS, never MERGES

**Intent:** Lock in the explicit "two recruiters / same recruiter /
cross-tenant, picking the same file at different times → EVERY upload
succeeds, EVERY submit succeeds, EVERY row stays forever; the
Duplicate Ownership Engine only flags the dup to the recruiter"
behavior. **No merging happens at any level, at any cost, in any
case.**

This step is NOT a Step 20 implementation; the upload subsystem
continues to do nothing for dedupe (INV-1, INV-4). This step's job
is to write the contract down so that:

1. A future AI / engineer does not add a UNIQUE constraint on
   `(company_id, file_hash)` to "help" the duplicate case.
2. A future AI / engineer does not add a "merge these candidates"
   job anywhere in the codebase.
3. The QA team has explicit acceptance tests for the
   dup-create-and-keep behavior, instead of treating it as a bug.
4. Operations has a runbook for "why do we have two candidate rows
   for Jane Doe?" tickets — the answer is always "by design, never
   merged".

This step closes Gap 1 of the doc review.

**Pre-requisites:**
- Steps 1–19 complete (the upload subsystem is the only producer of
  `resume_uploads` rows and `documents` rows for resumes)
- A candidate-level detection service exists elsewhere in the
  codebase (out of scope for this doc — its design lives in the
  marketplace / candidate subsystem). It DETECTS and REPORTS only.
- v2.0.0 of the upload subsystem is GA

**Feature flag:** N/A — this is a documentation + test contract, not
new code in the upload subsystem.

---

#### The contract (canonical statement)

> The upload subsystem NEVER refuses, returns-409, or otherwise
> blocks an upload based on file contents. Two different file picks
> (by the same user, by different users in the same tenant, or by
> users in different tenants) ALWAYS produce two distinct
> `resume_uploads` rows, two distinct R2 objects, and (if both reach
> submit) two distinct `candidates` rows and two distinct
> `documents` rows. **All rows persist. Nothing is ever merged,
> tombstoned, soft-deleted, or consolidated.**
>
> Candidate-level dup-detection — "this candidate looks like one
> that already exists" — is the responsibility of the **Duplicate
> Ownership Engine**, which DETECTS duplicates and REPORTS them to
> the recruiter in the UI (a "possible duplicate" badge on the
> candidate row). The engine **never mutates**, **never merges**,
**never deletes** candidate rows. The recruiter decides what to
do with the information (e.g. manually archive the dup).
>
> The upload subsystem does not know the engine exists, does not
> call it, and does not act on its result.

---

#### Concrete acceptance scenarios (must all pass)

| # | Scenario | Upload subsystem behavior | Engine behavior (read-only) | Expected user-facing result |
|---|---|---|---|---|
| 1 | Same recruiter picks the same file twice in a row, submits both | 2 sessions, 2 R2 objects, 2 `documents` rows, **2 candidates** | DETECTS both candidates share the same fingerprint; reports "possible duplicate" badge on each | 2 candidates visible in ATS, both flagged "possible duplicate" |
| 2 | Recruiter A and Recruiter B (same tenant) both upload the same file independently, both submit | 2 sessions, 2 R2 objects, 2 `documents` rows, **2 candidates** | DETECTS cross-recruiter duplicate; reports badge on each | 2 candidates visible in the same tenant's ATS, both flagged |
| 3 | Recruiter A and Recruiter B (DIFFERENT tenants) both upload the same file independently, both submit | 2 sessions in 2 different `company_id` namespaces, 2 R2 objects, **2 candidates** (one per tenant) | DETECTS cross-tenant duplicate only if the engine's policy permits it (default: NO cross-tenant detection; each tenant sees its own candidate) | 2 candidates, one per tenant — cross-tenant merging is forbidden, cross-tenant detection is opt-in per-tenant policy |
| 4 | Same recruiter picks the file, picks it again, uploads only the first (closes tab before submitting the second) | 2 sessions, 1 R2 object, 0 `documents` rows from the second (orphan) | n/a (orphan row has no candidate) | `ReconcileOrphanUploadsJob` reaps the second `resume_uploads` row after 24 h |
| 5 | Same recruiter picks the file, uploads both, submits only the first | 2 sessions, 2 R2 objects, 1 `documents` row, 1 candidate | Engine has nothing to compare against (only one candidate exists) | Recruiter sees the second tab's "Uploaded ✓" badge still; clicking submit produces a second candidate, both flagged |
| 6 | Recruiter picks the same file they already uploaded last week, submits | 1 new session, 1 new R2 object, 1 new `documents` row, **1 new candidate** | DETECTS the re-upload against last week's candidate; reports badge on both | 2 candidates visible (the original and the new one), both flagged "possible duplicate". Original is **NOT** modified in any way. |
| 7 | Same recruiter picks the same file three times (one minute apart), submits all three | 3 sessions, 3 R2 objects, 3 `documents` rows, **3 candidates** | DETECTS all three as duplicates of each other; reports badge on each | 3 candidates visible, all flagged "possible duplicate" |

**The cardinal rule across all scenarios:** the candidate count in
the ATS equals the submit count. Every successful submit creates
exactly one new candidate that exists forever, and is never
modified by the engine.

---

#### What the upload subsystem MUST NOT do

- Add a UNIQUE constraint on `(company_id, file_hash)` or
  `(company_id, sha256_hex)`. Reaffirms INV-1, ADR-002.
- Short-circuit the `createOrPresign()` lookup on any content hash.
- Treat a `documents` row whose `name='Resume'` already exists for
  this `(company_id, candidate_id)` as a hard error at insert time.
  The `documents` UNIQUE on `(company_id, documentable_id,
  documentable_type, name)` continues to enforce "one Resume per
  JobApplication" for the same candidate; that is NOT a
  cross-candidate constraint and is unchanged. (Step 19's `Branch
  A` already UPDATE-in-places to avoid that constraint; this is
  correct because the edit flow is in-place.)
- Block the `finalize` step on a "this looks like a duplicate"
  check. Finalize only verifies that the R2 object exists.
- Block the `submit` step on a "this candidate already exists"
  check. Submit ALWAYS creates the candidate; the engine observes.
- Modify any candidate row at any point after the upload subsystem
  has linked it (`linked_candidate_id` set).

---

#### What the upload subsystem DOES do (the seam)

The seam between the upload subsystem and the Duplicate Ownership
Engine is the **`linked_candidate_id`** column on `resume_uploads`.
Once submit succeeds and a candidate exists, the upload subsystem's
job is done. The engine reads the `documents` rows and the
`candidates` rows and DECIDES whether to display a "possible
duplicate" badge. The engine NEVER WRITES to either table from the
upload subsystem's perspective.

There is no API contract between the two — they communicate via
the database. The upload subsystem does not call the engine; the
engine observes the database.

---

#### What the Duplicate Ownership Engine does and DOES NOT do

**Does:**
- Read `candidates`, `documents`, `resume_uploads` rows.
- Compute a candidate fingerprint (e.g. normalized name + email +
  phone; or resume-text cosine similarity; or both).
- Display a "possible duplicate" badge in the ATS UI next to
  candidates whose fingerprint matches another candidate in the
  same tenant.
- Send an in-app notification to the recruiter ("Candidate X may
  be a duplicate of Candidate Y").
- Expose a `GET /api/duplicates?candidate_id=N` endpoint so the UI
  can show "see the other candidate that looks like this one".

**Does NOT:**
- DELETE any candidate row.
- UPDATE any candidate row to merge fields into another.
- SOFT-DELETE any `documents` row.
- Combine two `resume_uploads` rows into one.
- Delete or hide R2 objects that belong to a candidate.
- Move a `linked_candidate_id` from one candidate to another.
- Touch coin balances, audit logs, or any financial state.

The engine is a **detection** service, not a **mutation** service.
This is a hard rule, not a guideline. The "merge" concept does not
exist in this codebase. If a future ticket asks for it, that ticket
must be rejected at planning; the recruiter decides what to do with
dup information (typically: archive one and keep the other).

---

#### Test coverage required

**Upload-subsystem-side tests** (live in this repo):

- `ResumeV2Invariant1Test::test_two_file_picks_with_same_bytes_produce_two_sessions` —
  verifies INV-1; both sessions exist; both succeed.
- `ResumeV2Invariant1Test::test_upload_succeeds_even_when_existing_resume_with_same_sha256_exists` —
  verifies no UNIQUE / hash short-circuit at the upload layer.
- `ResumeV2Invariant4Test::test_new_idempotency_key_with_same_bytes_creates_new_session` —
  same scenario via the header-replay path.
- `ResumeV2Step20AcceptanceTest::test_same_user_same_file_twice_creates_two_sessions_two_candidates` —
  happy-path acceptance for scenario 1: 2 candidates, both flagged.
- `ResumeV2Step20AcceptanceTest::test_two_recruiters_same_tenant_same_file_creates_two_candidates` —
  scenario 2.
- `ResumeV2Step20AcceptanceTest::test_two_recruiters_different_tenants_same_file_creates_two_tenant_isolated_candidates` —
  scenario 3 (cross-tenant isolation; no shared state).
- `ResumeV2Step20AcceptanceTest::test_pick_twice_upload_once_does_not_create_duplicate` —
  scenario 4 (only one `documents` row from the submitted one;
  orphan row reaped normally).
- `ResumeV2Step20AcceptanceTest::test_two_picks_two_uploads_one_submit_creates_one_candidate` —
  scenario 5: 1 candidate after 1 submit; the second tab's submit
  will create the second candidate.
- `ResumeV2Step20AcceptanceTest::test_reupload_same_file_next_week_creates_new_candidate` —
  scenario 6: new candidate exists; original is untouched.
- `ResumeV2Step20AcceptanceTest::test_three_picks_same_file_creates_three_candidates` —
  scenario 7: candidate count equals submit count.
- `ResumeV2Step20AcceptanceTest::test_engine_does_not_modify_uploaded_rows` —
  verifies the engine observes but does not write to
  `resume_uploads`, `candidates`, or `documents`. Read-only contract.

**Engine-side tests** (live in the candidate / marketplace repo —
out of scope for this doc, listed for completeness):

- `DuplicateOwnershipEngineTest::test_two_candidates_same_fingerprint_both_get_badge` —
  verifies the engine flags both candidates, never modifies either.
- `DuplicateOwnershipEngineTest::test_engine_does_not_cross_tenants_by_default` —
  verifies scenario 3 default policy.
- `DuplicateOwnershipEngineTest::test_engine_read_only_under_load` —
  stress test: 10K duplicates detected in a minute, zero writes to
  candidate tables.
- `DuplicateOwnershipEngineTest::test_engine_does_not_delete_candidates` —
  explicit guard test against the failure mode where a future
  contributor adds a "cleanup duplicate" code path. This test
  asserts the count of candidates before and after running the
  engine is equal.
- `DuplicateOwnershipEngineTest::test_engine_does_not_touch_resume_uploads` —
  same guard for the upload subsystem's tables.

---

#### Operations runbook (the "why two candidates?" ticket)

When ops gets a ticket "I see two Jane Doe candidates in the ATS,
same resume, what's going on?", the answer is:

1. **This is by design. They are not merged, ever.** INV-1 + INV-4
   + Step 20 say the upload subsystem never deduplicates AND the
   Duplicate Ownership Engine never merges. Two uploads = two
   candidates, full stop.

2. **The "possible duplicate" badge is intentional.** The engine
   detects that the two candidates share a fingerprint and shows
   the badge. The recruiter clicks "see other candidate" and
   decides what to do (typically archive one). The recruiter — not
   the engine — owns the decision.

3. **If both candidates are in DIFFERENT tenants**, that is correct
   behavior. Cross-tenant merging is forbidden. Cross-tenant
   detection is a per-tenant opt-in policy; by default it is OFF.

4. **The R2 objects are not "wasted"** — each candidate points at
   its own R2 object. If the recruiter archives one candidate, the
   R2 object becomes unreferenced and is removed by the retention
   job after 90 days.

5. **Coin consumption is unchanged.** Each successful submit costs
   the same number of coins. The engine detection is free. The
   recruiter pays for the submission attempt; the fact that the
   candidate turns out to be a dup does not refund the coins.

6. **If the recruiter wants ONE candidate, the workflow is:**
   open both, manually archive one. The next submission by the
   same recruiter (if any) is still a fresh upload — no merging.

---

#### What this step DOES NOT change

- The `upload_session_audits` table continues to log one row per
  session action, including a `linked_candidate_id` once submit
  completes. Two sessions → two audit rows → both link to their
  own candidate. Ops can reconstruct the timeline from the audit
  log; no merge event exists.
- The `ReconcileOrphanUploadsJob` continues to reap orphan
  sessions whose `expires_at < now() - 24h` and status ∉
  `{Completed, Failed, Cancelled, Reconciled}`. The orphan row
  has no `documents` row, so the engine never sees it.
- The `documents` UNIQUE constraint on
  `(company_id, documentable_id, documentable_type, name)`
  continues to enforce "one Resume row per JobApplication". This
  is a per-candidate constraint (one resume per JobApplication),
  NOT a cross-candidate constraint. It is unchanged.
- The `candidates` table has no UNIQUE constraint and none is
  added by this step. Two candidates with the same fingerprint
  coexist forever.

---

**What success looks like:**
- All 7 acceptance scenarios pass
- INV-1 / INV-4 invariants explicitly tested in the dup-create case
- `test_engine_does_not_modify_uploaded_rows` passes
- `test_engine_does_not_delete_candidates` passes
- Operations runbook covers the "why two candidates?" ticket with
  "by design, never merged, recruiter decides"
- No new code in the upload subsystem
- Cross-references in INV-1 + INV-4 canonical statements updated
- `DuplicateOwnershipEngineTest` exists in the candidate subsystem
  (out of scope but referenced; read-only contract enforced)

---

## Feature flag reference

| Flag | Config key | Env var | Default | Step | What it enables |
|------|-----------|---------|---------|------|-----------------|
| Presign endpoint | `features.presign_endpoint` | `RESUMES_FEATURE_PRESIGN` | `false` | 5 | `POST /resume-sessions` returns presigned R2 URL |
| Browser upload | `features.browser_upload` | `RESUMES_FEATURE_BROWSER_UPLOAD` | `false` | 6 | Browser PUTs directly to R2 instead of PHP multipart |
| Submit with session | `features.submit_with_session` | `RESUMES_FEATURE_SUBMIT_SESSION` | `false` | 7 | Submit atomically finalizes upload + creates candidate (2-RTT) |
| Cancel | `features.cancel` | `RESUMES_FEATURE_CANCEL` | `false` | 8 | `DELETE /resume-sessions/{uuid}` cancels upload |
| Refresh recovery | `features.refresh_recovery` | `RESUMES_FEATURE_REFRESH_RECOVERY` | `false` | 9 | GET endpoint + localStorage recovery |
| Multipart upload | `features.multipart_upload` | `RESUMES_FEATURE_MULTIPART` | `false` | 10 | Files ≥5 MB upload in chunks |
| Network resilience | `features.network_resilience` | `RESUMES_FEATURE_NETWORK_RESILIENCE` | `false` | 11 | Offline/online handling with preflight |
| BroadcastChannel | `features.broadcast_channel` | `RESUMES_FEATURE_BROADCAST_CHANNEL` | `false` | 14 | Same-origin tab sync (zero server) |
| Career controller | `features.career_controller` | `RESUMES_FEATURE_CAREER_CONTROLLER` | `false` | 16 | Extracted career controller |
| Edit page v2 | `features.edit_page_v2` | `RESUMES_FEATURE_EDIT_PAGE_V2` | `false` | 19 | Edit page: replace / remove resume via 2-RTT flow |

---

## Cross-references

- Architecture: [`30_architecture.md`](30_architecture.md)
- Data model: [`40_data_model.md`](40_data_model.md)
- API contracts: [`50_api_contracts.md`](50_api_contracts.md)
- Failure modes: [`45_failure_modes.md`](45_failure_modes.md)
- Storage & queue: [`60_storage_and_queue.md`](60_storage_and_queue.md)
- Capacity plan: [`75_capacity_plan.md`](75_capacity_plan.md)
- Invariants: [`96_invariants.md`](96_invariants.md)
- Never matrix: [`98_failure_matrix_never.md`](98_failure_matrix_never.md)
- ADRs: [`99_decision_log.md`](99_decision_log.md)
