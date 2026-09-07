# Candidate Details Tabs V2 — Zero-Downtime Deployment Runbook

**Module:** Candidate Details Drawer V2 (`ServiceCompanyJobApplicationController`)  
**Target Tabs:** Notes, Timeline, Resume / CV  
**Rollout Strategy:** Zero-Downtime Octane/FrankenPHP Atomic Hot-Reload  

---

## 1. Pre-Deployment Verification Checklist

Execute these checks prior to triggering production deployment:

```bash
# 1. Verify PHP syntax on modified controllers & routes
php -l app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php
php -l app/Http/Controllers/ServiceCompany/ApplicantNoteController.php
php -l routes/service.php

# 2. Run automated security & regression test suite
php artisan test --filter=CandidateDetailsTabsV2Test
php artisan test --filter=Resume
```

---

## 2. Production Deployment Steps (Zero-Downtime Sequence)

Execute the steps in order:

### Step 1: Code Pull
Pull code changes to production server workspace:
```bash
git pull origin main
```

### Step 2: Route & View Cache Refresh
Refresh Laravel route & view compilation caches:
```bash
php artisan route:clear
php artisan view:clear
php artisan route:cache
php artisan view:cache
```

### Step 3: Octane / FrankenPHP Hot-Reload
Trigger worker process hot-reload without dropping active HTTP client connections:
```bash
php artisan octane:reload
```

---

## 3. Verification & Post-Deployment Monitoring

1. **APM Telemetry Inspection**:
   Monitor storage logs for `'candidate_details_shell_rendered'` events:
   - Target rendering duration: **< 50 ms**.
   - Target payload size: **< 40 KB**.
2. **Rollback Trigger Protocol**:
   If APM error rate exceeds 0.01% or latency exceeds 200 ms:
   ```bash
   git checkout HEAD~1
   php artisan octane:reload
   ```
