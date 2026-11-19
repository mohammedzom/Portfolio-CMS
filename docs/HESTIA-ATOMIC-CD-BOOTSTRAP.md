# Portfolio CMS — Hestia atomic release deployment (runbook)

Production: `mohammedzomlot.dev` on a Hestia VPS (Nginx → Apache → PHP-FPM 8.5, MariaDB 11.4).
Deploys run as the unprivileged `portfolio-deploy` account (SSH key with `restrict`: no PTY, no
forwarding, no sudo). Everything below was rehearsed end-to-end on an isolated Hestia test domain.

## Layout

```
/home/moh/web/mohammedzomlot.dev/
├── private/                 legacy checkout — kept untouched as a fallback
├── releases/                portfolio-deploy:portfolio-deploy 0750 (+ACL moh r-x, www-data --x)
│   ├── <sha>/               one immutable release per commit (code read-only for moh)
│   ├── current -> <sha>     the live release (initially ../private)
│   ├── .previous            rollback target of the last deploy
│   └── .history             deploy / rollback / failure log
├── shared/                  root:root 0750 (+ACL moh, portfolio-deploy r-x; www-data --x)
│   ├── .env                 moh 0600 + ACL portfolio-deploy:r (single source of truth)
│   └── storage/             moh-owned; ACL+default ACL rwX for moh and portfolio-deploy;
│                            www-data may read only storage/app/public
└── public_html -> /home/moh/web/mohammedzomlot.dev/releases/current/public
```

Inside a release: `storage -> shared/storage`, `.env -> shared/.env`,
`public/storage -> ../storage/app/public`.

### Why Hestia needs a custom PHP-FPM template

Hestia's default pool sets `open_basedir` to `public_html:private:…`. PHP checks the *resolved*
path, so code under `releases/` and `shared/` would be refused. The backend template
`portfolio-releases-PHP-8_5` (`.github/deploy/hestia/portfolio-releases-PHP-8_5.tpl`) is the stock
template plus those two paths. Because it is a Hestia template (not an edit of the generated pool
file) it survives `v-rebuild-web-domain` and Hestia upgrades. The `-PHP-8_5` suffix pins the PHP version.

Hestia's rebuild uses `chown --no-dereference` and `mkdir -p` on `public_html`, so the
`public_html` symlink and the `releases/`/`shared/` directories are preserved (verified in
`/usr/local/hestia/func/rebuild.sh`). No custom Nginx/Apache template is needed.

### Why PHP-FPM is reloaded on every switch

FPM workers cache resolved paths (`realpath_cache_ttl=120`) and OPcache entries, so after a
symlink switch a worker could keep executing the previous release for up to two minutes. A root
systemd unit pair `portfolio-release-<domain>.path/.service` watches `releases/` and, when the
resolved `current` target changes, runs `/usr/local/sbin/portfolio-fpm-reload` (graceful
`systemctl reload php8.5-fpm`) and writes the target to
`/var/lib/portfolio-release/<domain>.active`. The deploy script waits for that stamp before it
runs any HTTP check; if it does not appear within 30 s the release is rolled back. No sudo is
granted to `portfolio-deploy`.

## One-time server bootstrap (root)

```bash
# 1. Verified DB backup first (see "Backups").
cd /path/to/Portfolio-CMS/.github/deploy/hestia
./hestia-bootstrap-releases.sh prepare moh mohammedzomlot.dev \
    --seed-media /path/to/storage/app/public      # optional, never overwrites
# 2. Only after checking that releases/current/public resolves to what public_html serves now:
./hestia-bootstrap-releases.sh switch-public-html moh mohammedzomlot.dev
```

`prepare` is idempotent. It does not touch `private/`, the database or `public_html`. It
switches the domain's backend template (Hestia reloads PHP-FPM) and sets `private/.env` to 0600.
`switch-public-html` refuses to run unless the new path resolves to the directory already being
served, so the switch itself changes nothing visible.

Emergency exit from the whole layout: `./hestia-bootstrap-releases.sh revert-public-html moh mohammedzomlot.dev`
(points `public_html` back to `private/public`).

## GitHub configuration

Environment `production` secrets: `SERVER_HOST`, `SERVER_SSH_PORT`, `SERVER_USERNAME`
(must be `portfolio-deploy`), `SERVER_SSH_KEY`. `PROJECT_PATH` is no longer used.
The server's host key is pinned in `.github/deploy/known_hosts` (alias `portfolio-production`);
update that file if the VPS host key ever changes.

## Deploying

Actions → **Laravel CI/CD** → Run workflow on `main`:

| Input | Effect |
| --- | --- |
| `deploy_to_production` | must be checked, otherwise only CI runs |
| `mode = dry-run` (default) | builds, uploads, extracts, caches, checks DB/migrations and smoke-tests the release in-process, then discards it. The site is not touched. |
| `mode = deploy` | the same, then switches `current`, waits for the FPM reload and checks the real web stack; rolls back automatically on any failure |

Pipeline: `test` (Pint, Pest, script lint, Vite build when present) → `release`
(`.github/scripts/build-release.sh`: `git archive` of the tested SHA + `composer install --no-dev`
+ `npm run build` + `.release-manifest` + SHA-256) → `deploy` (scp to
`~/.portfolio-artifacts`, then `deploy-release.sh <mode> <sha>` taken from the same artifact).
The server never runs `composer install` or `npm`.

`deploy-release.sh` refuses to start (before touching anything) if: the identity is not
`portfolio-deploy`; the layout, `shared/.env` (must be read-only) or `shared/storage` are missing;
the FPM watcher is not active; another deploy holds the lock; the checksum does not match; or the
archive contains absolute paths, `..`, links or special files.

Before the switch it checks: release manifest = SHA, PHP platform requirements, Laravel caches
(`package:discover`, `config:cache`, `route:cache`, `event:cache`, `view:cache`), at least one
route, DB reachable, **no pending migrations**, and an in-process smoke test of `/up`, `/` (no 5xx and
**no redirect to its own domain**) and `/api/v1/portfolio` (`success: true`, with the configured API key).

After the switch it checks, through Nginx/Apache/PHP-FPM on the local IP (bypassing Cloudflare):
`/up`, `/` (≤5 redirects, final 200, so loops fail), `/api/v1/portfolio`, one Vite asset (or
`/robots.txt`) and one media file from `storage/app/public`. On failure it switches back, waits
for the reload, re-checks the previous release and deletes the failed release.
It keeps the 5 newest releases plus current and previous.

Automatic deploys on push to `main` happen only if the repository variable
`PORTFOLIO_AUTODEPLOY=true` is set. Leave it unset until a manual deploy has succeeded.

## Rollback

```bash
# as portfolio-deploy (via the deploy SSH key, or sudo -u portfolio-deploy from an admin shell)
bash /home/moh/web/mohammedzomlot.dev/releases/current/.github/scripts/deploy-release.sh status
bash /home/moh/web/mohammedzomlot.dev/releases/current/.github/scripts/deploy-release.sh rollback          # to .previous
bash /home/moh/web/mohammedzomlot.dev/releases/current/.github/scripts/deploy-release.sh rollback <sha>    # or ../private
```

Database migrations are never reversed automatically.

## Migrations

A release with pending migrations is refused. To apply them:

1. Take a database backup and test its restore (see below).
2. Review the migration for backward compatibility with the live release.
3. Run a `dry-run` deploy, then with approval apply from a temporary copy of the new release, e.g.
   `sudo -u portfolio-deploy php <release>/artisan migrate --force`. Then run the normal `deploy`.

Never run `migrate:fresh`, `db:wipe`, `key:generate` or seeders in production.

## Queue worker and scheduler

`QUEUE_CONNECTION=database`. Both run from `releases/current` as `moh` via Hestia cron, so each
run uses the live release and needs no restart:

```bash
v-add-cron-job moh '*' '*' '*' '*' '*' 'cd /home/moh/web/mohammedzomlot.dev/releases/current && /usr/bin/php8.5 artisan schedule:run >/dev/null 2>&1'
v-add-cron-job moh '*' '*' '*' '*' '*' 'cd /home/moh/web/mohammedzomlot.dev/releases/current && /usr/bin/php8.5 artisan queue:work --stop-when-empty --max-time=50 --tries=3 >/dev/null 2>&1'
```

## Backups

```bash
B=/root/portfolio-cd-backups/$(date +%Y%m%dT%H%M%S); install -d -m 700 "$B"
mariadb-dump --single-transaction --routines --triggers --events --hex-blob moh_portfolio_db | gzip -9 > "$B/db.sql.gz"
# restore test into a throw-away database, compare row counts / CHECKSUM TABLE, then drop it
```

Hestia's nightly user backup (`/backup/moh.*.tar`) also contains the database and `releases/`/`shared/`.

## Permission notes

- `portfolio-deploy` can write only `releases/`, `shared/storage` and its home. It can read
  `shared/.env` (so it effectively holds the application's DB credentials). Treat its key as a
  privileged deployment credential.
- Directories that PHP-FPM (`moh`) creates at runtime with mode 0755 are read-only for
  `portfolio-deploy`. That is intended: deploys never modify uploaded media.
- Release code is read-only for `moh`, so a compromised web request cannot modify code.
- `www-data` (Nginx/Apache) can read only `public/` and `storage/app/public`. It cannot read `.env` or the config cache.

## Merging Phase 2 (`dev`)

`dev`'s workflow still uses the old in-place `git reset` deploy. When merging, keep **this**
workflow (it already builds Vite assets in CI when `package-lock.json` exists) and re-add
`dev`'s extra checks if wanted (`test (PHP 8.4)` job, `public/build/fonts-manifest.json` assertion).
Before the first production deploy of `main`, make sure `/` is not
`redirect('https://mohammedzomlot.dev')`. On this domain that is a redirect loop, and the deploy
script will refuse it.
