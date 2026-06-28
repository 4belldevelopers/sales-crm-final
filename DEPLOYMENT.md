# Deployment Guide — testing.jero.works (staging) & hire.jero.works (production)

Pipeline: **push to `staging` → auto-deploys to testing → merge to `main` → manually run the
Production workflow → deploys to hire.** Both servers are self-managed Hostinger VPS running
Laravel Octane on FrankenPHP. Deploys happen via GitHub Actions over SSH.

---

## A. One-time: prepare each VPS (do on BOTH testing and hire)

Currently code reaches the servers by FTP/manual copy. Convert each app folder into a git
checkout of this repo. **`.env`, `storage/`, `user-uploads/`, `vendor/`, `node_modules/` are
git-ignored, so they are NOT touched.**

> ⚠️ Back up the server folder first: `tar czf ~/app-backup-$(date +%F).tar.gz .`

```bash
cd /path/to/the/app          # the web root that currently holds the site
git init
git remote add origin https://github.com/Jeroworks-Technology/JWMaincode28June2026.git
git fetch origin

# TESTING server uses the staging branch:
git reset --hard origin/staging
git checkout -B staging origin/staging

# PRODUCTION server uses the main branch instead:
#   git reset --hard origin/main
#   git checkout -B main origin/main
```

Requirements on each VPS (install if missing): **git, PHP 8.2+, Composer, Node.js + npm**.
Octane/FrankenPHP should already be running as a systemd/supervisor service so that
`php artisan octane:reload` gracefully restarts the workers.

### Let GitHub authenticate `git fetch` on the server
Either make the repo readable via a deploy key, or run `gh auth login` on the server, or use a
read-only Personal Access Token in the remote URL. (Deploy key is cleanest — one per server.)

---

## B. One-time: create an SSH key so GitHub Actions can log into each VPS

On your PC (or each server), generate a dedicated key per environment:

```bash
ssh-keygen -t ed25519 -C "github-actions-staging" -f staging_deploy_key
ssh-keygen -t ed25519 -C "github-actions-prod"    -f prod_deploy_key
```

For each: append the **public** key (`*.pub`) to the server's `~/.ssh/authorized_keys`,
and paste the **private** key into the matching GitHub secret below.

---

## C. One-time: add GitHub repo secrets

Repo → **Settings → Secrets and variables → Actions → New repository secret**:

| Secret | Value |
|--------|-------|
| `STAGING_HOST` | testing VPS IP / hostname |
| `STAGING_USER` | SSH user (e.g. `root` or a deploy user) |
| `STAGING_SSH_KEY` | the **private** staging key (full file contents) |
| `STAGING_PATH` | absolute path to the app on the testing VPS |
| `STAGING_PORT` | SSH port (omit if 22) |
| `PROD_HOST` | hire VPS IP / hostname |
| `PROD_USER` | SSH user |
| `PROD_SSH_KEY` | the **private** production key |
| `PROD_PATH` | absolute path to the app on the production VPS |
| `PROD_PORT` | SSH port (omit if 22) |

---

## D. Everyday workflow

1. Edit code locally, commit, **push to `staging`** (GitHub Desktop).
2. The **Deploy to Staging** action runs automatically → check **testing.jero.works**.
3. When happy, **merge `staging` into `main`** (PR or in GitHub Desktop).
4. Go to **Actions → Deploy to Production → Run workflow** → it deploys to **hire.jero.works**.

---

## E. Important notes

- **Never commit `.env`** — each server keeps its own (different DB, APP_KEY, Stripe/Razorpay,
  Redis, Elasticsearch). Already git-ignored.
- **Back up the production DB before each prod deploy** — migrations run with `--force`.
- The `storage/*.json` Google credential files are currently committed and will deploy to every
  server. Consider removing them from git and rotating the keys.
- `frankenphp` and `storage/logs/*.log` are git-ignored (too large for GitHub) — the server keeps
  its own copies.
