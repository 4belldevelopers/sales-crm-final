# Verification Guide

Commands to validate changes safely. Run from repo root in `testing.jero.works`.
Windows host (XAMPP/PowerShell) + this is an **Octane/FrankenPHP** app talking to
**remote** MySQL + ES (`76.13.243.68`) and local Redis. Adjust for your shell.

> ⚠️ Verification touches shared remote services (DB/ES). Prefer a local/staging DB.
> **Never run migrations or destructive artisan commands against the production
> `dbmaster` DB.**

## Safe verification sequence (fast → slow)

### 1. PHP syntax / static
```bash
php -l path/to/ChangedFile.php                      # lint a single file
./vendor/bin/pint --test                            # code style check (no changes)
./vendor/bin/pint path/to/ChangedFile.php           # autofix style for your file
# (recommended, not yet installed) ./vendor/bin/phpstan analyse app
```

### 2. Routes (catch broken route/controller references)
```bash
php artisan route:list --path=<fragment>            # verify your route resolves
php artisan route:list > /dev/null && echo OK       # whole table compiles
```

### 3. Config / autoload sanity
```bash
composer dump-autoload
php artisan config:clear && php artisan route:clear && php artisan view:clear
php artisan about                                    # env/driver overview
```

### 4. Schema check (READ-ONLY — never migrate prod)
```bash
php artisan db:table <table>                         # inspect live columns/indexes
php artisan db:show                                  # DB overview
# Do NOT run: php artisan migrate (schema lives in live DB — see ADR 0005)
```

### 5. Tests
```bash
php artisan test                                     # full suite (mostly scaffolding)
php artisan test --filter=<TestName>                 # targeted
# NOTE: real ATS business logic is largely untested — passing tests ≠ feature works.
```

### 6. Queues / jobs (if you touched a Job/Observer)
```bash
php artisan queue:work --once                        # process one job locally
php artisan horizon                                  # dashboard (needs Redis)
php artisan horizon:status
```

### 7. Elasticsearch sync (if you touched indexing)
```bash
# Backfill / rebuild the affected index from MySQL:
php artisan list | grep -i index                     # see Index*ToElasticsearch commands
php artisan <command:name>                            # e.g. candidates index rebuild
# Verify a doc landed (replace index/host from .env):
curl -u elastic:*** "http://<ES_HOST>:9200/<index>/_count"
```

### 8. Frontend build (if you touched Blade/JS/CSS assets)
```bash
npm install
npm run dev          # local
npm run build        # production assets (Vite)
```

### 9. Run the app to smoke-test
```bash
php artisan octane:start --server=frankenphp         # production-like runtime
# or for quick checks:
php artisan serve
```

## Recommended order for a typical change
1. `php -l` the file → 2. `pint --test` → 3. `route:list --path=` →
4. `config:clear` → 5. `php artisan test --filter=` → 6. (jobs/ES) targeted checks →
7. manual smoke test via Octane.

## Deployment checks (pre-release)
- [ ] `composer install --no-dev --optimize-autoloader`
- [ ] `php artisan config:cache route:cache view:cache` (Octane benefits)
- [ ] Horizon worker + scheduler (`schedule:run`) running
- [ ] Reverb websocket server running
- [ ] ES indices reachable; reindex commands run if mappings changed
- [ ] No debug routes (`/mail-test`, `/whatsapp/connect`) exposed
- [ ] `.env` secrets present & not committed; exposed secrets rotated

## Honesty rule
Report exactly what you ran and its result. If a check was skipped (e.g. couldn't
reach remote DB/ES), say so — don't claim verification you didn't perform.
