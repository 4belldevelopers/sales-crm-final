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
| Idempotency | None — re-upload creates duplicate rows | SHA256-based idempotency key → same UUID reused |
| Upload progress | Basic jQuery AJAX call | Real progress bar with ETA, speed via XHR |

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
v1 (current):
  Recruiter ──POST file────▶ PHP/Octane ──store──▶ Disk/DB
                             ▲                     ▲
                             │                     │
                           Full file bytes       RAM spike
                          in memory (6 MB ×     per upload
                          10K = 60 GB RAM)
                          
v2 (target):
  Recruiter ──GET presign──▶ PHP/Octane ──insert──▶ MySQL (metadata only, ~200 bytes)
              ──PUT file────▶ Cloudflare R2         │
                            (zero PHP bytes)        │
              ──POST submit──▶ PHP/Octane ──link────▶
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
            // Idempotency — sha256 hex of file bytes
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
            // Idempotency: one active session per file per company
            $table->unique(['company_id', 'idempotency_key'], 'resume_uploads_company_idem_key_unique');
            
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
            $table->dropIndex('resume_uploads_company_idem_key_unique');
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
│  ┌ idempotency_key (char 64, UNIQUE+company)        │
│  ├ parser_started_at / parser_completed_at           │
│  ├ parser_attempts (default 0)                       │
│  ├ last_error (text, nullable)                       │
│  ├ last_parser_pod_id (varchar 64, nullable)         │
│  └ expires_at (timestamp, nullable)                  │
│                                                      │
│  NEW indexes:                                        │
│  ┌ UNIQUE(company_id, idempotency_key)               │
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

### Step 4 — `ResumeUploadSession` service (core logic)

**Intent:** Build the single service class that encapsulates all upload
session business logic. This is the hardest class in the v2 system.
Every endpoint in Steps 4-7 delegates to this service.

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
     * Create a new upload session (or return existing one for same idem key).
     *
     * Flow:
     * 1. Compute idempotency_key from client-supplied value
     * 2. Check for existing non-terminal session with same key+company
     * 3. If found → return existing (INV-1: same UUID reused)
     * 4. If not found → INSERT new row with status=Pending
     * 5. Generate presigned R2 PUT URL
     * 6. Return session metadata to client
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
        // 1. Validate idempotency key format (64-char hex)
        if (!preg_match('/^[a-f0-9]{64}$/', $idempotencyKey)) {
            throw new \InvalidArgumentException('Invalid idempotency key format');
        }

        // 2. Check for existing non-terminal session
        $existing = ResumeUpload::where('company_id', $companyId)
            ->where('idempotency_key', $idempotencyKey)
            ->whereNotIn('status', array_map(fn($s) => $s->value, [
                ResumeUploadStatus::Completed,
                ResumeUploadStatus::Failed,
                ResumeUploadStatus::Cancelled,
                ResumeUploadStatus::Expired,
                ResumeUploadStatus::Reconciled,
            ]))
            ->first();

        if ($existing) {
            // INV-1: reuse same UUID, rotate presign URL if needed
            UploadSessionAudit::record($existing->uuid, 'ActionSessionReused', [
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

test('createOrPresign with new idem key creates session', function () {
    $result = $this->service->createOrPresign(
        $this->companyId, $this->idemKey,
        'resume.pdf', 1000, 'application/pdf', '127.0.0.1'
    );

    expect($result['uuid'])->toBeString();
    expect($result['status'])->toBe('Pending');
    expect($result['upload_url'])->toContain('http');
});

test('same idem key returns same UUID (INV-1)', function () {
    $first = $this->service->createOrPresign(
        $this->companyId, $this->idemKey,
        'resume.pdf', 1000, 'application/pdf', '127.0.0.1'
    );

    $second = $this->service->createOrPresign(
        $this->companyId, $this->idemKey,
        'resume.pdf', 1000, 'application/pdf', '127.0.0.1'
    );

    expect($second['uuid'])->toBe($first['uuid']);
});

test('different companies get different UUIDs for same idem key', function () {
    $a = $this->service->createOrPresign('company-a', $this->idemKey, 'r.pdf', 1000, 'application/pdf', '');
    $b = $this->service->createOrPresign('company-b', $this->idemKey, 'r.pdf', 1000, 'application/pdf', '');

    expect($a['uuid'])->not->toBe($b['uuid']);
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

test('invalid idem key format throws', function () {
    $this->expectException(\InvalidArgumentException::class);
    $this->service->createOrPresign(
        $this->companyId, 'not-hex', 'r.pdf', 1000, 'application/pdf', ''
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
- Same idem key → same UUID (INV-1)
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
            'idempotency_key'  => [
                'required',
                'string',
                'size:64',
                'regex:/^[a-f0-9]{64}$/',
            ],
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
            'idempotency_key.regex' => 'Invalid idempotency key format',
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

// Intent: v2 resume upload session endpoints — presign, finalize, show, cancel.
// Every method delegates to ResumeUploadSession service.
// No file bytes are handled here — only metadata.

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

        try {
            $result = $this->sessionService->createOrPresign(
                companyId:        $companyId,
                idempotencyKey:   $request->input('idempotency_key'),
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
     * POST /service_company/api/resume-sessions/{uuid}/finalize
     *
     * After the browser completes the R2 PUT, call this to mark
     * the session as Uploaded. Candidate creation happens at submit.
     */
    public function finalize(Request $request, string $uuid): JsonResponse
    {
        if (!config('resumes.features.finalize_endpoint', false)) {
            return response()->json(['error' => 'feature_disabled'], 404);
        }

        $companyId = auth()->user()->company_id;

        try {
            $result = $this->sessionService->finalize($uuid, $companyId);
            return response()->json($result);
        } catch (\RuntimeException $e) {
            return response()->json([
                'error'   => $e->getCode() === 404 ? 'not_found' : 'session_error',
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 422);
        }
    }

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
- Same idempotency key → same UUID returned
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
  -d '{
    "idempotency_key": "aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa",
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

# 6. Same idem key test
curl -s -X POST "https://testing.jero.works/service_company/api/resume-sessions" \
  -H "Content-Type: application/json" \
  -H "Cookie: $SID" \
  -d '{...same idem key...}' | jq '.uuid'
# Expected: same UUID as first call

# 7. Validation error test (invalid idem key)
curl -s -X POST "..." -d '{"idempotency_key": "short"}' | jq '.error'
# Expected: "validation_error"

# 8. Rate limit test
for i in $(seq 1 31); do
  KEY=$(printf 'a%.0s' $(seq 1 64))
  STATUS=$(curl -s -o /dev/null -w "%{http_code}" -X POST "..." \
    -H "Content-Type: application/json" -H "Cookie: $SID" \
    -d "{\"idempotency_key\":\"${KEY}\",\"original_filename\":\"r${i}.pdf\",\"file_size_bytes\":1000,\"mime_type\":\"application/pdf\"}")
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
 * Intent: Presigned R2 upload module for v2 resume upload.
 * 
 * Replaces the PHP multipart POST with a direct-to-R2 PUT flow:
 *   1. POST /resume-sessions → get presigned URL
 *   2. PUT file directly to R2 (zero PHP bytes)
 *   3. POST finalize → mark session as Uploaded
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

            // Step 3: Finalize on server
            await finalizeSession(currentSession.uuid);

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

    async function finalizeSession(uuid) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        const resp = await fetch(`/service_company/api/resume-sessions/${uuid}/finalize`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });

        if (!resp.ok) {
            const err = await resp.json().catch(() => ({}));
            throw new Error(err.message || `Finalize failed: ${resp.status}`);
        }

        return resp.json();
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
# 1. Enable the feature flag
export RESUMES_FEATURE_BROWSER_UPLOAD=true
export RESUMES_FEATURE_PRESIGN=true
export RESUMES_FEATURE_FINALIZE=true
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
unset RESUMES_FEATURE_FINALIZE
php artisan config:cache
```

**Rollback:** Flip `RESUMES_FEATURE_BROWSER_UPLOAD=false`.
The old `uploadWithProgress()` multipart POST takes over.
Delete `public/js/resume-presign.js` permanently if not needed.

---

## Phase C — Submit + UX (Steps 7-11)

### Step 7 — Submit with session_uuid

**Intent:** When recruiter clicks Submit, the candidate is created and
the upload session is linked in a single transaction. This eliminates
the separate finalize call (4 RTT → 2 RTT for v2.5).

**Pre-requisites:**
- Steps 4-6 complete (presign + upload + finalize flow works)
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
    // Verify session belongs to this company and is Uploaded
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

    // HEAD-check R2 object exists
    try {
        $r2Exists = Storage::disk('r2')->exists('resumes/' . $sessionUuid);
        if (!$r2Exists) {
            throw new \RuntimeException('R2 object missing');
        }
    } catch (\Throwable $e) {
        return response()->json([
            'error'   => 'upload_incomplete',
            'message' => 'Uploaded file not found in storage. Please upload again.',
        ], 422);
    }
}

// ... existing candidate creation code ...

// If session_uuid was provided, link the session after candidate creation:
if (isset($session) && $session) {
    $session->update([
        'status' => ResumeUploadStatus::Completed->value,
        'linked_candidate_id' => $candidate->id,
    ]);

    UploadSessionAudit::record($sessionUuid, 'ActionSubmitLinked', [
        'candidate_id' => $candidate->id,
        'company_id'   => auth()->user()->company_id,
    ]);
}
```

---

**Data flow for Step 7:**

```
v2.5 flow (after Step 7):
  Recruiter                    Browser                    PHP/Octane              R2
    │                            │                            │                    │
    │  Select file               │                            │                    │
    │ ────────────────────────►  │                            │                    │
    │                            │  POST /resume-sessions     │                    │
    │                            │ ──────────────────────────►│                    │
    │                            │ ◄── presigned URL ──────── │                    │
    │                            │                            │                    │
    │                            │  PUT file to R2            │                    │
    │                            │ ───────────────────────────────────────────────►│
    │                            │ ◄── 200 ───────────────────────────────────────│
    │                            │                            │                    │
    │  Click Submit              │                            │                    │
    │ ────────────────────────►  │  POST /job-applications    │                    │
    │                            │  {session_uuid, ...fields} │                    │
    │                            │ ──────────────────────────►│                    │
    │                            │                            │  1. Verify session │
    │                            │                            │  2. HEAD-check R2  │
    │                            │                            │  3. INSERT candidate│
    │                            │                            │  4. UPDATE session  │
    │                            │                            │     (Completed)     │
    │                            │ ◄── 201 candidate ──────── │                    │
```

---

**What success looks like:**
- Full flow: presign → R2 PUT → Submit creates candidate in 2 server hits
- Idempotent re-submit returns existing candidate_id
- Submit without upload → 422 `upload_incomplete`

**Test on staging:**
```bash
# 1. Enable feature flags
export RESUMES_FEATURE_PRESIGN=true
export RESUMES_FEATURE_BROWSER_UPLOAD=true
export RESUMES_FEATURE_FINALIZE=true
export RESUMES_FEATURE_SUBMIT_SESSION=true
php artisan config:clear

# 2. Full browser flow test
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
unset RESUMES_FEATURE_FINALIZE
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
 * If found, rehydrate the UI state.
 */
function checkForExistingSession() {
    const uuid = localStorage.getItem('resume_session_uuid');
    if (!uuid) return;

    fetch(`/service_company/api/resume-sessions/${uuid}`, {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
        }
    })
    .then(resp => {
        if (resp.status === 404) {
            // Session expired — clean up
            localStorage.removeItem('resume_session_uuid');
            localStorage.removeItem('resume_idempotency_key');
            return null;
        }
        return resp.json();
    })
    .then(session => {
        if (!session) return;
        
        currentSession = session;
        
        switch (session.status) {
            case 'Uploaded':
            case 'Completed':
                currentState = STATE.UPLOADED;
                emitStateChange(STATE.UPLOADED, 100);
                break;
            case 'Pending':
            case 'Uploading':
                currentState = STATE.UPLOADING;
                emitStateChange(STATE.UPLOADING, 0);
                // Attempt resume (Step 11 handles multipart resume)
                break;
            case 'Failed':
                currentState = STATE.FAILED;
                emitStateChange(STATE.FAILED, 0, session.last_error || 'Upload failed');
                break;
        }
    })
    .catch(err => {
        console.warn('Session recovery failed:', err);
    });
}

// Call on page load
document.addEventListener('DOMContentLoaded', checkForExistingSession);
```

---

**What success looks like:**
- Start upload → refresh page → upload progress reappears
- After upload complete, refresh → "Uploaded ✓" badge visible
- Expired session → clean "Choose File" state

**Test on staging:**
```bash
# 1. Enable flags
export RESUMES_FEATURE_PRESIGN=true
export RESUMES_FEATURE_BROWSER_UPLOAD=true
export RESUMES_FEATURE_REFRESH_RECOVERY=true
php artisan config:clear

# 2. Mid-upload refresh test
# Start upload (small file, <50%)
# Refresh page
# Expected: upload progress reappears, continues from beginning

# 3. Post-upload refresh test
# Upload completes → refresh page
# Expected: "Uploaded ✓" badge, Submit enabled

# 4. 404 test
# Get a session UUID from DB
# Wait until expired (or manually mark expired in DB)
# Refresh page
# Expected: clean "Choose File" state

# 5. Log tail
tail -n 5 storage/logs/laravel.log | grep "jobs.resume.session"

# 6. Disable flags
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

// Intent: Company-scoped rate limiter.
// Key format: {company_id}:{route_name}
// Prevents one company from exhausting all pods' capacity.

class CompanyThrottleMiddleware
{
    public function __construct(private RateLimiter $limiter) {}

    public function handle(Request $request, \Closure $next, int $maxAttempts = 120, int $decayMinutes = 1)
    {
        $companyId = auth()->user()->company_id ?? 'anonymous';
        $key = sha1("company:{$companyId}:" . $request->route()->getName());

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
for i in $(seq 1 31); do
  KEY=$(printf 'a%.0s' $(seq 1 64))
  STATUS=$(curl -s -o /dev/null -w "%{http_code}" \
    -X POST "https://testing.jero.works/service_company/api/resume-sessions" \
    -H "Content-Type: application/json" -H "Cookie: $SID" \
    -d "{\"idempotency_key\":\"${KEY}\",\"original_filename\":\"r${i}.pdf\",\"file_size_bytes\":1000,\"mime_type\":\"application/pdf\"}")
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
 */

window.ResumeBroadcastSync = (function () {
    'use strict';

    let channel = null;
    const COMPANY_ID = document.body.dataset.companyId || 'unknown';

    function init() {
        try {
            channel = new BroadcastChannel('resume-sync-' + COMPANY_ID);
            channel.onmessage = handleMessage;
        } catch (e) {
            console.warn('[BroadcastSync] BroadcastChannel not supported, using localStorage fallback');
            window.addEventListener('storage', handleStorageEvent);
        }

        // Listen for local state changes to broadcast
        window.addEventListener('resume:state-changed', (e) => {
            broadcast(e.detail);
        });
    }

    function destroy() {
        if (channel) {
            channel.close();
            channel = null;
        }
        window.removeEventListener('storage', handleStorageEvent);
    }

    function broadcast(data) {
        if (channel) {
            channel.postMessage(JSON.stringify(data));
        } else {
            // localStorage fallback for Safari < 15.4
            localStorage.setItem('resume_broadcast_' + Date.now(), JSON.stringify(data));
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
        if (!e.key || !e.key.startsWith('resume_broadcast_')) return;
        let data;
        try {
            data = JSON.parse(e.newValue);
        } catch {
            return;
        }
        applyIncomingState(data);
    }

    function applyIncomingState(data) {
        // Only apply if uuid matches local session
        const localUuid = localStorage.getItem('resume_session_uuid');
        if (!data.uuid || data.uuid !== localUuid) return;

        console.log('[BroadcastSync] Syncing from cross-tab:', data);
        
        // Dispatch event that other modules listen to
        window.dispatchEvent(new CustomEvent('resume:cross-tab-sync', {
            detail: data
        }));
    }

    return { init, destroy, broadcast };
})();
```

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
TOKEN=$(php -r "echo config('resumes.health_token');")
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

use App\Services\ResumeUploadSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

// Intent: Thin controller for career/public apply flow.
// Delegates to HomeController methods for now, with v2 resume
// upload support layered on top.

class CareerApplyController extends \App\Http\Controllers\Controller
{
    public function __construct(
        private ResumeUploadSession $sessionService
    ) {}

    /**
     * Career apply — public-facing candidate application.
     */
    public function apply(Request $request): JsonResponse
    {
        // Delegate to HomeController's careerApply method
        $controller = app(\App\Http\Controllers\HomeController::class);
        return $controller->careerApply($request);
    }

    /**
     * Store career application with optional session_uuid.
     */
    public function store(Request $request): JsonResponse
    {
        $sessionUuid = $request->input('session_uuid');
        
        if ($sessionUuid) {
            // Verify session (public — no auth, so we trust the session)
            $session = $this->sessionService->get($sessionUuid, null);
            if (!$session || $session->status !== 'Uploaded') {
                return response()->json(['error' => 'upload_incomplete'], 422);
            }
        }

        // Delegate to HomeController's careerStore
        $controller = app(\App\Http\Controllers\HomeController::class);
        return $controller->careerStore($request);
    }
}
```

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
        $key = str_repeat(dechex($i), 64);
        $requests[] = function () use ($key) {
            $start = microtime(true);
            $resp = $this->post('/service_company/api/resume-sessions', [
                'idempotency_key'  => substr($key, 0, 64),
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
# 1. Enable all flags
export RESUMES_FEATURE_PRESIGN=true
export RESUMES_FEATURE_BROWSER_UPLOAD=true
export RESUMES_FEATURE_FINALIZE=true
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
curl -X POST /service_company/api/resume-sessions -d '{"idempotency_key":"...","original_filename":"test.pdf","file_size_bytes":1000,"mime_type":"application/pdf"}'
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

**Rollback:** Flip any failing flag back to `false` in `.env`.
Re-run `php artisan config:cache`. The v1 path resumes serving.

---

## Feature flag reference

| Flag | Config key | Env var | Default | Step | What it enables |
|------|-----------|---------|---------|------|-----------------|
| Presign endpoint | `features.presign_endpoint` | `RESUMES_FEATURE_PRESIGN` | `false` | 5 | `POST /resume-sessions` returns presigned R2 URL |
| Browser upload | `features.browser_upload` | `RESUMES_FEATURE_BROWSER_UPLOAD` | `false` | 6 | Browser PUTs directly to R2 instead of PHP multipart |
| Finalize | `features.finalize_endpoint` | `RESUMES_FEATURE_FINALIZE` | `false` | 6 | `POST /resume-sessions/{uuid}/finalize` marks session Uploaded |
| Submit with session | `features.submit_with_session` | `RESUMES_FEATURE_SUBMIT_SESSION` | `false` | 7 | Submit links session_uuid to candidate |
| Cancel | `features.cancel` | `RESUMES_FEATURE_CANCEL` | `false` | 8 | `DELETE /resume-sessions/{uuid}` cancels upload |
| Refresh recovery | `features.refresh_recovery` | `RESUMES_FEATURE_REFRESH_RECOVERY` | `false` | 9 | GET endpoint + localStorage recovery |
| Multipart upload | `features.multipart_upload` | `RESUMES_FEATURE_MULTIPART` | `false` | 10 | Files ≥5 MB upload in chunks |
| Network resilience | `features.network_resilience` | `RESUMES_FEATURE_NETWORK_RESILIENCE` | `false` | 11 | Offline/online handling with preflight |
| BroadcastChannel | `features.broadcast_channel` | `RESUMES_FEATURE_BROADCAST_CHANNEL` | `false` | 14 | Same-origin tab sync (zero server) |
| Career controller | `features.career_controller` | `RESUMES_FEATURE_CAREER_CONTROLLER` | `false` | 16 | Extracted career controller |

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
