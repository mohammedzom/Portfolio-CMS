# Portfolio CMS — Hestia release deployment bootstrap

This is a **one-time server administrator task**, not a GitHub Actions command. The new SSH key is assigned to the unprivileged `portfolio-deploy` account with `restrict` in `authorized_keys`. GitHub Actions does not need a PTY or sudo.

## Safety / assumptions

- Existing live Laravel checkout: `/home/moh/web/mohammedzomlot.dev/private`
- Existing Hestia user and PHP-FPM pool owner: **verify it is `moh`** before changing ACLs.
- Hestia currently serves `public_html -> private/public`.
- Old `private` checkout, `.env`, database and uploaded media must be preserved.
- Take a **tested off-server DB backup / VPS snapshot**, and inspect Hestia Nginx/PHP-FPM symlink and `open_basedir` behavior before the cutover.
- Production deploys are **manual by default**; no push will deploy unless you explicitly set the repository variable `PORTFOLIO_AUTODEPLOY=true`.

## 1. Verify Hestia and ownership first

Run from an administrative SSH session **on the VPS**:

```bash
BASE=/home/moh/web/mohammedzomlot.dev
namei -l "$BASE/public_html"
readlink -f "$BASE/public_html"
ps -eo user,group,args | grep '[p]hp-fpm: pool'
sudo getfacl -p "$BASE/private/.env"
```

Do not proceed until PHP-FPM for this domain can access release directories and write `shared/storage`. Hestia may overwrite manual web-template paths during a rebuild, so a persistent web template may be required.

## 2. Create only the release area and shared writable storage

Run as an administrator, after backups, once:

```bash
BASE=/home/moh/web/mohammedzomlot.dev
sudo install -d -o portfolio-deploy -g portfolio-deploy -m 0755 "$BASE/releases"
sudo install -d -o portfolio-deploy -g portfolio-deploy -m 0755 "$BASE/shared"

# Copy media, logs and Laravel storage to a shared location.
# Only run this when shared/storage does not already exist.
sudo test ! -e "$BASE/shared/storage"
sudo cp -a "$BASE/private/storage" "$BASE/shared/storage"
sudo chown -R portfolio-deploy:portfolio-deploy "$BASE/shared/storage"

# Allow the Hestia PHP-FPM user to read and write shared storage.
sudo setfacl -R -m u:moh:rwX "$BASE/shared/storage"
sudo find "$BASE/shared/storage" -type d -exec   setfacl -m d:u:moh:rwx,d:u:portfolio-deploy:rwx {} +

# Laravel CLI runs as portfolio-deploy; it needs only read access to the existing .env.
# No GitHub secret or database credential is copied into the repository.
sudo chmod 600 "$BASE/private/.env"
sudo setfacl -m u:portfolio-deploy:r-- "$BASE/private/.env"

# Create a pointer to the EXISTING live code; do not delete or copy it.
sudo -u portfolio-deploy ln -s ../private "$BASE/releases/current"
```

Check that the only writable application areas for `portfolio-deploy` are `releases` and `shared/storage`. Its read access to `.env` necessarily provides the application DB credentials; treat this SSH key as a privileged **application deployment** credential despite its limited Linux permissions.

## 3. Point Hestia to the release pointer (one-time)

**First** verify that `$BASE/releases/current/public` resolves to the existing `private/public`. Only after verifying the web server config, atomically replace the current `public_html` symlink without deleting the old checkout:

```bash
BASE=/home/moh/web/mohammedzomlot.dev
readlink -f "$BASE/releases/current/public"
readlink -f "$BASE/private/public"

sudo ln -s "$BASE/releases/current/public" "$BASE/public_html.next"
sudo mv -Tf "$BASE/public_html.next" "$BASE/public_html"

# Should still point to the exact same old public directory before first deployment:
readlink -f "$BASE/public_html"
curl -fsSI https://mohammedzomlot.dev/up
```

If Hestia regenerates `public_html` or PHP-FPM rules, use a **custom Hestia web template** that preserves the same target. Never run `chmod -R 777` or give this user sudo.

## 4. GitHub Secrets and first deployment

Keep these **production Environment secrets**:

| Secret | Value |
| --- | --- |
| `SERVER_HOST` | VPS public address |
| `SERVER_SSH_PORT` | SSH port |
| `SERVER_USERNAME` | `portfolio-deploy` |
| `SERVER_SSH_KEY` | full private Ed25519 key for that restricted account |

`PROJECT_PATH` is not used by this CD. The paths are deliberately fixed in the release script.

Go to **Actions → Laravel CI/CD → Run workflow**, select `main`, enable **deploy_to_production**. It runs CI, packages the exact tested commit, and uploads it to `/home/portfolio-deploy/.portfolio-artifacts`. The deploy user creates a new release, links `.env` and shared storage, installs production Composer dependencies, checks for **pending** migrations, swaps `releases/current`, performs a local HTTPS `/up` probe, and restarts queue workers via Laravel.

The script **refuses deployment before cutover** if ACLs, shared storage, `public_html`, or the legacy release pointer are missing. If a health probe fails after cutover, it restores the previous symlink. It **never** runs `migrate:fresh`, automatically reverses DB migrations, or uses `git reset --hard` against the old checkout.

**Pending migrations:** the deployment refuses to activate. Take and verify a database backup, review backward compatibility, and apply schema updates through an approved separate maintenance operation.

**Workers and scheduler:** configure the production queue worker and scheduler to use `releases/current`, rather than a hardcoded `private` path. Their processes must run under the correct Hestia/PHP user and have write access to shared storage.

**PHP-FPM:** verify that OPcache and the Hestia pool observe a new release after switching the symlink. If the pool needs an explicit reload, implement it via a narrowly scoped administrator-managed mechanism, not general sudo for `portfolio-deploy`.

## 5. Optional automatic deployment

Only after the manual release, web routing, worker, and rollback tests succeed, set the GitHub repository variable `PORTFOLIO_AUTODEPLOY` to `true`. A successful CI push to `main` can then deploy automatically. Do **not** enable it before bootstrap.

## Future Phase 2 merge

The `dev` branch has a separate, newer workflow for Web assets. When promoting Phase 2, resolve the workflow merge conflict by retaining **both** its PHP 8.4/8.5 tests and frontend build checks **and** this restricted-user, artifact-based CD. Do not reintroduce the original `git merge` / `git reset --hard` deploy inside `private`.
