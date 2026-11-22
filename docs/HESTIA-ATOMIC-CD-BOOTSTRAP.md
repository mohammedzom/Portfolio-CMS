# Portfolio CMS — Hestia atomic release deployment (runbook)

Production: `mohammedzomlot.dev` on a Hestia VPS (Nginx → Apache → PHP-FPM 8.5, MariaDB 11.4).
Deploys run as the unprivileged `portfolio-deploy` account (SSH key with `restrict`: no PTY, no
forwarding, no sudo). The commit that introduced this layout (#28) reports an end-to-end rehearsal
on an isolated Hestia test domain; that rehearsal cannot be reproduced from this repository. See
[What has and has not been verified](#what-has-and-has-not-been-verified) before relying on any step.

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

Recommended repository settings (observed on 2026-11-22; none of them changed by this repository):

- **Deploy secrets are also defined at repository level.** The same `SERVER_*` secrets exist at
  repository level as well as in the `production` environment. Repository secrets are readable by
  any job in any workflow, including one changed in a pull request from a branch of this
  repository. Delete the repository-level copies; `PROJECT_PATH` is unused everywhere.
- **The `production` environment has no protection rules.** Add **required reviewers** (an
  explicit approval before every production job) and a **deployment branch policy** that allows
  only `main`. Without that policy, a workflow on another branch can reference
  `environment: production` and read its secrets.
- **Force pushes are allowed on `main`.** Disallow them; `dev` already does. A rewritten `main`
  breaks the link between a deployed `.release-manifest` SHA and the history.
- Leave the repository variable `PORTFOLIO_AUTODEPLOY` unset (it is unset today), so deploys stay
  manual.

## Deploying

Actions → **Laravel CI/CD** → Run workflow on `main`:

| Input | Effect |
| --- | --- |
| `deploy_to_production` | must be checked, otherwise only CI runs |
| `mode = dry-run` (default) | builds, uploads, extracts, caches, checks DB/migrations and smoke-tests the release in-process, then discards it and its uploaded archive. The site is not switched (see "What a dry-run writes"). |
| `mode = deploy` | the same, then switches `current`, waits for the FPM reload and checks the real web stack; rolls back automatically on any failure |
| `api_health = auto` (default) | checks the legacy `GET /api/v1/portfolio` only when the release registers that route **and** `API_KEY` is configured: exactly today's behaviour for the current code |
| `api_health = require` | the release fails if the legacy API cannot be checked (route or `API_KEY` missing) |
| `api_health = skip` | never checks the legacy API (for a Blade-only site; see "Health checks") |

Pipeline: `test` (Pint, Pest, ShellCheck, hermetic deploy-script tests, Vite build when present) → `release`
(`.github/scripts/build-release.sh`: `git archive` of the tested SHA + `composer install --no-dev`
+ `npm run build` + `.release-manifest` + SHA-256; it refuses secret-like files) → `deploy` (scp to
`~/.portfolio-artifacts`, then `deploy-release.sh <mode> <sha>` taken from the same artifact).
The server never runs `composer install` or `npm`.

The job summary records the commit, the mode and the API check mode of every run.

### Expected GitHub Actions results

| Run | `test` | `Build immutable release artifact` | `Deploy release through restricted SSH` |
| --- | --- | --- | --- |
| push / pull request | ✅ | skipped | skipped |
| manual, `deploy_to_production` unchecked | ✅ | skipped | skipped |
| manual dry-run | ✅ | ✅ | ✅ means **only** that the release was prepared and passed every pre-switch check; nothing went live |
| manual deploy | ✅ | ✅ | ✅ means the release is live and passed the post-switch checks; ❌ means it was refused before the switch or rolled back (read the log) |

A green CI run is never a production activation. Only a `mode = deploy` run whose deploy job ends
with `Deployment successful: <sha>` activated a release.

### Detached execution and server-side logs

`deploy`, `dry-run` and `rollback` re-run themselves with `setsid`, writing to
`~/.portfolio-artifacts/logs/<UTC time>-<mode>-<sha>-<pid>.log`, and stream that log to the SSH
session. If the connection drops, or GitHub cancels or times out the job, the run still finishes,
including any automatic rollback. The GitHub job then shows a failure or cancellation, but the
server-side log and `status` show the real outcome. The lock (`releases/.deploy.lock`) makes a new
run wait for the old one to end; it refuses to start meanwhile. Archives and scripts are removed
after a successful deploy or dry-run. Leftovers expire after 14 days and logs after 90 days.

`deploy-release.sh` refuses to start (before touching anything) if: the identity is not
`portfolio-deploy`; the layout, `shared/.env` (must be read-only) or `shared/storage` are missing;
the FPM watcher is not active; another deploy holds the lock; the checksum does not match; or the
archive contains absolute paths, `..`, links or special files.

If the same SHA already exists but is not live (for example it is `.previous`), that copy is set
aside, not deleted, and put back unless the new build goes live. A dry-run therefore never
removes a rollback target. A run that was killed outright leaves `.stage-*`, `.current.*` or
`.replaced-*` entries; the next run removes them, or restores them, under the lock.

### Health checks

| Phase | Check | Passes when |
| --- | --- | --- |
| before the switch | release manifest | `sha=` equals the deployed SHA |
| | platform | `composer check-platform-reqs --no-dev` passes (if Composer is not installed on the server, only the PHP version is enforced, by the autoloader, and a warning is logged) |
| | Laravel caches | `package:discover`, `config:cache`, `route:cache`, `event:cache`, `view:cache` succeed; at least one route |
| | database | `migrate:status` works and lists **no pending migration** |
| | in-process (`.github/scripts/smoke-release.php`) | cache store accepts a throwaway key · `GET /up` = 200 · `select 1` on the default connection · `GET /` ends in 200 on the domain (same-host redirects followed, ≤ 5; a loop or a redirect to another host fails) · legacy API per `api_health` |
| after the switch | PHP-FPM | the root watcher confirms the reload for the new target within 30 s |
| | real web stack (Nginx → Apache → PHP-FPM on the local IP, bypassing Cloudflare; 5 attempts, 10 s each) | `/up` = 200 · `/` = 200 after ≤ 5 redirects that stay on the domain · legacy API per `api_health` · one Vite asset from the manifest (or `/robots.txt`) · one media file from `storage/app/public` |

On a post-switch failure it switches back, waits for the reload, re-checks the previous release
and deletes the failed release. It keeps the 5 newest other releases plus current and previous.

**Transition away from the legacy API.** `auto` keeps the API check for as long as a release
registers `GET api/v1/portfolio` and `API_KEY` is set. That is the case for the current code, so
production behaviour is unchanged. A Blade-only release that no longer registers the route is
checked through `/up`, `/`, the database, the assets and the media, without the API. `skip` is
only needed while the route still exists but should no longer gate deploys. Use `require` when you
want a deploy to fail if the API check silently stops running.

**What a dry-run writes.** A dry-run never switches the site and deletes its prepared release.
Its requests run with an in-memory cache and sessions, so it never stores application data such
as `portfolio_all` in the live cache. It still writes to shared state:
- compiled Blade views under `shared/storage/framework/views` (keyed by the release path, so never
  read by the live release; they accumulate);
- short-lived rate-limiter counters in the live cache store;
- Laravel log entries, if something fails.

Automatic deploys on push to `main` happen only if the repository variable
`PORTFOLIO_AUTODEPLOY=true` is set. Leave it unset until a manual deploy has succeeded.

## Release identification

- `deploy-release.sh status` shows the current target, `.previous`, the FPM reload stamp, the
  release directories and the last 10 history entries.
- Every release contains `.release-manifest` (`sha`, `built_at`, `frontend`, `github_run`). The
  run ID links back to the GitHub Actions run that built it.
- `releases/.history` lists every `deployed`, `failed` and `rollback`, with UTC time.
- `~/.portfolio-artifacts/logs/` holds the full log of every detached run.

## Rollback

There is no rollback mode in the workflow; rollback is an SSH operation by an operator.

```bash
# as portfolio-deploy (via the deploy SSH key, or sudo -u portfolio-deploy from an admin shell).
# Use the script of a release directory, not of current: after a rollback to ../private, current
# is the legacy checkout, which may not contain deploy-release.sh.
R=/home/moh/web/mohammedzomlot.dev/releases/<any-release-sha>
bash $R/.github/scripts/deploy-release.sh status
bash $R/.github/scripts/deploy-release.sh rollback          # to .previous
bash $R/.github/scripts/deploy-release.sh rollback <sha>    # or ../private
```

Rollback runs detached and logs like a deploy. It waits for the PHP-FPM reload (only a warning if
it is not confirmed) and runs the post-switch health checks. If those fail it reports an error, but
**does not switch again**: decide by hand.

Database migrations are never reversed automatically.

## Recovering from a failed deployment

1. Read the deploy job log and `~/.portfolio-artifacts/logs/<…>-deploy-<sha>-*.log`; run `status`.
2. **Refused before the switch** (checksum, archive safety, manifest, platform, caches, pending
   migrations, smoke test): nothing went live. Fix the cause and run a new dry-run.
3. **Rolled back after the switch** (`failed <sha> (restored: <previous>)` in the history): the
   previous release is live again. Check the warning line "the previous release also fails health
   checks": if it is present, the web stack itself is unhealthy, not the release.
4. **The job was cancelled or timed out:** the detached run kept going. Wait for the log to end
   (`Deployment successful`, or `ERROR: release … was rolled back`), then run `status`.
5. **The run was killed outright** (reboot, `kill -9`): run `status`. If `current` points at a
   release that never passed its checks, roll back explicitly. The next run cleans up any
   temporary entries.
6. **Emergency exit from the release layout:** `hestia-bootstrap-releases.sh revert-public-html`
   (root).

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

`dev`'s workflow still uses the old in-place deploy (`git merge --ff-only` on the server plus an
uploaded asset archive). When merging, keep **this** workflow and these scripts.

Requirements for the Blade/Vite frontend:
- `package.json` and `package-lock.json` must both be committed. `build-release.sh` runs
  `npm ci && npm run build` and fails without `public/build/manifest.json`. `dev`'s self-hosted
  fonts also need `public/build/fonts-manifest.json`; a missing one shows up as a `/` failure in
  the in-process smoke test, not as a separate check.
- `main` still tracks a stale `public/build/` (`manifest.json` plus one JS file) that `dev` already
  removed. `build-release.sh` deletes `public/build` before packaging, so it never ships, but the
  merge should drop those files.
- `/` must answer 200 on `mohammedzomlot.dev`. A redirect to itself, or to any other host, is
  refused before the switch. `dev`'s Blade homepage does answer 200; the old
  `redirect('https://mohammedzomlot.dev')` does not.
- After the post-switch checks pass, one Vite asset from the manifest is fetched through the real
  web stack.
- `dev` keeps `GET api/v1/portfolio`, so `api_health = auto` still checks it. Only switch to `skip`
  once the API is no longer meant to gate deploys.

## What has and has not been verified

| Area | Evidence | Status |
| --- | --- | --- |
| Pre-switch path on the production server (preflight, artifact checks, caches, DB/migrations, in-process smoke) | Dry-run runs #75 (correctly refused a `/` redirect loop) and #79 (passed), on 2026-11-19 and 2026-11-22, using the **previous** script | verified for those commits |
| Switch, post-switch checks, automatic rollback, set-aside, recovery, locking, pruning, archive safety, SSH-drop survival | `.github/tests/deploy-release.test.sh` in CI (fake Hestia layout, real script) and a local rehearsal with a real artifact, real PHP 8.5 and real Laravel caches | verified locally / in CI only |
| A `mode = deploy` run in production | none | **not tested** |
| `rollback` in production | none | **not tested** |
| The root PHP-FPM watcher, the Hestia template, ACLs and the `restrict` SSH key | preflight in run #79 confirmed the watcher unit is active and the stamp is readable; the rest is documented, not observed | partially verified |
| `composer` present on the server (extension check) | not observable from the logs | unknown |
| Queue worker and scheduler cron | documented above, not observed | unverified |
| Backups and restore test | documented above, not observed | unverified |
