#!/usr/bin/env bash
# Activates a release archive on the Hestia VPS as the restricted
# `portfolio-deploy` user (no sudo, no PTY).
#
# Usage:
#   deploy-release.sh deploy  <sha>   build, verify, switch, health-check, auto-rollback
#   deploy-release.sh dry-run <sha>   everything up to the switch, then discard
#   deploy-release.sh rollback [release]  switch back (default: the previous release)
#   deploy-release.sh status
#
# deploy, dry-run and rollback run detached from the SSH session and log to
# $ARTIFACTS/logs/ (see run_detached), so a dropped connection or a cancelled
# GitHub job cannot interrupt a switch or an automatic rollback halfway.
#
# Environment (all optional; production uses the defaults):
#   PORTFOLIO_HEALTH_API   auto (default): check GET /api/v1/portfolio only when the
#                          release registers that route and API_KEY is configured;
#                          require: fail the release if it cannot be checked;
#                          skip: never check it (for a Blade-only site).
#
# Expected layout (provisioned once by hestia-bootstrap-releases.sh as root):
#   $BASE/releases/<sha>/          immutable releases, owned by portfolio-deploy
#   $BASE/releases/current -> <sha> (or ../private before the first release)
#   $BASE/shared/.env              read-only for portfolio-deploy
#   $BASE/shared/storage/          writable by moh (PHP-FPM) and portfolio-deploy
#   $BASE/public_html -> $BASE/releases/current/public
#   $RELOAD_STAMP                  written by a root systemd path unit after it
#                                  reloads PHP-FPM for a new `current` target
#
# Safety rules: nothing outside $BASE/releases and the artifact directory is
# modified; pending migrations block activation; the site is switched with a
# single rename(2) and switched back automatically if any health check fails.
set -Eeuo pipefail
umask 027

MODE="${1:-}"
SHA="${2:-}"

DOMAIN="${PORTFOLIO_DOMAIN:-mohammedzomlot.dev}"
HESTIA_USER="${PORTFOLIO_HESTIA_USER:-moh}"
DEPLOY_USER="${PORTFOLIO_DEPLOY_USER:-portfolio-deploy}"
WEB_SERVER_USER="${PORTFOLIO_WEB_SERVER_USER:-www-data}"
HEALTH_SCHEME="${PORTFOLIO_HEALTH_SCHEME:-https}"
HEALTH_IP="${PORTFOLIO_HEALTH_IP:-}"
KEEP_RELEASES="${PORTFOLIO_KEEP_RELEASES:-5}"
PHP_BIN="${PORTFOLIO_PHP:-/usr/bin/php8.5}"
ARTIFACTS="${PORTFOLIO_ARTIFACTS:-$HOME/.portfolio-artifacts}"

FPM_RELOAD_TIMEOUT="${PORTFOLIO_FPM_RELOAD_TIMEOUT:-30}"
HEALTH_ATTEMPTS="${PORTFOLIO_HEALTH_ATTEMPTS:-5}"
HEALTH_RETRY_DELAY="${PORTFOLIO_HEALTH_RETRY_DELAY:-2}"
HEALTH_MAX_TIME="${PORTFOLIO_HEALTH_MAX_TIME:-10}"
# Legacy REST API health check: auto (only when the release registers
# GET api/v1/portfolio and API_KEY is configured), require, or skip.
HEALTH_API="${PORTFOLIO_HEALTH_API:-auto}"
ARTIFACT_RETENTION_DAYS="${PORTFOLIO_ARTIFACT_RETENTION_DAYS:-14}"
SELF="$(readlink -f -- "${BASH_SOURCE[0]}")"

# The two path overrides exist for the hermetic tests in .github/tests; production uses the defaults.
BASE="${PORTFOLIO_BASE:-/home/$HESTIA_USER/web/$DOMAIN}"
RELEASES="$BASE/releases"
SHARED="$BASE/shared"
CURRENT="$RELEASES/current"
PUBLIC_LINK="$BASE/public_html"
RELOAD_STAMP="${PORTFOLIO_RELOAD_STAMP:-/var/lib/portfolio-release/$DOMAIN.active}"
HISTORY="$RELEASES/.history"

STAGE=""
TARGET=""
TARGET_CREATED=0
ACTIVATED=0
TMPDIR_PRIVATE=""
SET_ASIDE=""
API_ROUTE=0

log()  { printf '[%s] %s\n' "$(date -u +%H:%M:%S)" "$*"; }
fail() { printf '[%s] ERROR: %s\n' "$(date -u +%H:%M:%S)" "$*" >&2; exit 1; }

cleanup() {
  local status=$?
  if [[ -n "$STAGE" && -d "$STAGE" ]]; then rm -rf -- "$STAGE"; fi
  # A release that never became active is never left behind half-built.
  if [[ "$TARGET_CREATED" == 1 && "$ACTIVATED" == 0 && -n "$TARGET" && -d "$TARGET" ]]; then
    if [[ "$(readlink -f "$CURRENT")" != "$(readlink -f "$TARGET")" ]]; then
      rm -rf -- "$TARGET"
    fi
  fi
  # An inactive copy of this SHA that existed before the run (possibly the
  # rollback target) is put back unless the new build went live.
  if [[ -n "$SET_ASIDE" && -d "$SET_ASIDE" ]]; then
    if [[ "$ACTIVATED" == 0 && ! -e "$TARGET" ]]; then
      mv -T -- "$SET_ASIDE" "$TARGET"
    else
      rm -rf -- "$SET_ASIDE"
    fi
  fi
  if [[ -n "$TMPDIR_PRIVATE" ]]; then rm -rf -- "$TMPDIR_PRIVATE"; fi
  exit "$status"
}
trap cleanup EXIT
trap 'printf "[%s] ERROR: command failed (line %s): %s\n" "$(date -u +%H:%M:%S)" "$LINENO" "$BASH_COMMAND" >&2' ERR

artisan() { "$PHP_BIN" "$TARGET/artisan" --no-interaction --no-ansi "$@"; }

# Runs an artisan command quietly, but shows its output when it fails.
artisan_step() {
  local out
  if ! out="$(artisan "$@" 2>&1)"; then
    printf '%s\n' "$out" >&2
    fail "php artisan $* failed"
  fi
}

# ---------------------------------------------------------------- preflight
preflight() {
  [[ "$(id -un)" == "$DEPLOY_USER" ]] || fail "must run as $DEPLOY_USER, not $(id -un)"
  local cmd
  for cmd in "$PHP_BIN" curl tar sha256sum setfacl flock readlink; do
    command -v "$cmd" >/dev/null || fail "required command missing: $cmd"
  done
  [[ -d "$RELEASES" && -w "$RELEASES" ]] || fail "$RELEASES is not provisioned or not writable"
  [[ -L "$CURRENT" ]] || fail "$CURRENT symlink is missing (bootstrap not run)"
  [[ -d "$CURRENT/public" ]] || fail "$CURRENT does not point at a valid release"
  [[ -L "$PUBLIC_LINK" && "$(readlink "$PUBLIC_LINK")" == "$CURRENT/public" ]] ||
    fail "public_html must be a symlink to $CURRENT/public"
  [[ -r "$SHARED/.env" ]] || fail "$SHARED/.env is missing or not readable"
  [[ ! -w "$SHARED/.env" ]] || fail "$SHARED/.env must be read-only for $DEPLOY_USER"
  [[ -d "$SHARED/storage" && -w "$SHARED/storage" && -w "$SHARED/storage/logs" ]] ||
    fail "$SHARED/storage is missing or not writable"
  [[ "$HEALTH_API" =~ ^(auto|require|skip)$ ]] || fail "PORTFOLIO_HEALTH_API must be auto, require or skip (got: $HEALTH_API)"
  [[ -r "$RELOAD_STAMP" ]] || fail "PHP-FPM reload watcher is not installed ($RELOAD_STAMP)"
  systemctl is-active --quiet "portfolio-release-${DOMAIN//./-}.path" ||
    fail "PHP-FPM reload watcher portfolio-release-${DOMAIN//./-}.path is not active"
  if [[ -z "$HEALTH_IP" ]]; then
    HEALTH_IP="$(ip -4 route get 1.1.1.1 2>/dev/null | sed -nE 's/.* src ([0-9.]+).*/\1/p' | head -n 1)"
  fi
  [[ "$HEALTH_IP" =~ ^[0-9.]+$ ]] || fail "could not determine the local web server IP"
  TMPDIR_PRIVATE="$(mktemp -d)"
  exec 9>"$RELEASES/.deploy.lock"
  flock -n 9 || fail "another deployment is running"
  recover_interrupted
  prune_artifacts
}

# A run that was killed (OOM, reboot, kill -9) cannot clean up after itself.
# Under the lock nothing else is running, so its temporary entries can go, and
# a release it had set aside is put back if nothing replaced it.
recover_interrupted() {
  local entry name sha
  for entry in "$RELEASES"/.stage-* "$RELEASES"/.current.*; do
    [[ -e "$entry" || -L "$entry" ]] || continue
    log "Removing $(basename "$entry") left by an interrupted run"
    rm -rf -- "$entry"
  done
  for entry in "$RELEASES"/.replaced-*; do
    [[ -d "$entry" ]] || continue
    name="$(basename "$entry")"; sha="${name#.replaced-}"; sha="${sha%%-*}"
    if [[ "$sha" =~ ^[0-9a-f]{40}$ && ! -e "$RELEASES/$sha" ]]; then
      log "Restoring release $sha set aside by an interrupted run"
      mv -T -- "$entry" "$RELEASES/$sha"
    else
      rm -rf -- "$entry"
    fi
  done
}

# Uploaded archives and run logs are kept for diagnosis, but not forever.
prune_artifacts() {
  [[ -d "$ARTIFACTS" ]] || return 0
  find "$ARTIFACTS" -maxdepth 1 -type f \( -name 'release-*.tar.gz' -o -name 'release-*.sha256' -o -name 'deploy-release-*.sh' \) \
    ! -name "release-${SHA:-none}.*" -mtime +"$ARTIFACT_RETENTION_DAYS" -delete
  if [[ -d "$ARTIFACTS/logs" ]]; then
    find "$ARTIFACTS/logs" -maxdepth 1 -type f -name '*.log' -mtime +90 -delete
  fi
}

# ----------------------------------------------------------- archive checks
verify_archive() {
  local archive="$ARTIFACTS/release-$SHA.tar.gz"
  local sums="$ARTIFACTS/release-$SHA.sha256"
  [[ -f "$archive" && -f "$sums" ]] || fail "artifact or checksum missing in $ARTIFACTS"
  (cd "$ARTIFACTS" && sha256sum --quiet --check "release-$SHA.sha256") || fail "artifact checksum mismatch"

  # Only "./"-relative names, no "..", no escapes, only files and directories.
  local names types
  names="$(tar --quoting-style=escape -tzf "$archive")" || fail "artifact is not a readable tar.gz"
  if grep -qvE '^\./' <<<"$names"; then fail "artifact has entries outside ./"; fi
  if grep -qE '(^|/)\.\.(/|$)' <<<"$names"; then fail "artifact has '..' path components"; fi
  if grep -qF '\' <<<"$names"; then fail "artifact has names with escaped characters"; fi
  types="$(tar --quoting-style=escape -tvzf "$archive" | cut -c1 | sort -u | tr -d '\n')"
  [[ "$types" =~ ^[-d]+$ ]] || fail "artifact contains links or special files (types: $types)"
  ARCHIVE="$archive"
}

# ----------------------------------------------------------- health checks
read_api_key() {
  # Read from the release's cached config; never printed.
  "$PHP_BIN" -r '$c = require $argv[1]; echo (string) ($c["app"]["api_key"] ?? "");' \
    "$1/bootstrap/cache/config.php"
}

# In-process requests through the release's HTTP kernel, before the switch
# (see .github/scripts/smoke-release.php for what is checked).
smoke_in_process() {
  log "Smoke-testing the release in-process (/up, database, /, legacy API: $HEALTH_API)"
  "$PHP_BIN" -d display_errors=stderr "$TARGET/.github/scripts/smoke-release.php" \
    "$TARGET" "$DOMAIN" "$HEALTH_SCHEME" "$HEALTH_API"
}

# Prints 1 when the release registers GET api/v1/portfolio, else 0.
release_api_route() { # <release-dir>
  local routes
  routes="$("$PHP_BIN" "$1/artisan" --no-interaction --no-ansi route:list --json 2>/dev/null)" || { echo 0; return 0; }
  "$PHP_BIN" -r '$r = json_decode(stream_get_contents(STDIN), true) ?: [];
    foreach ($r as $x) { if (($x["uri"] ?? "") === "api/v1/portfolio" && str_contains($x["method"] ?? "", "GET")) { echo 1; exit; } }
    echo 0;' <<<"$routes"
}

# Real requests through Nginx -> Apache -> PHP-FPM on this server. Redirects
# are followed (at most 5, so loops fail) but must stay on $DOMAIN.
http_check() { # <label> <path> <expect: html|json> [header-file]
  local label="$1" path="$2" expect="$3" headers="${4:-}" port out code redirects effective host
  port=$([[ "$HEALTH_SCHEME" == https ]] && echo 443 || echo 80)
  local args=(--silent --show-error --max-time "$HEALTH_MAX_TIME" --location --max-redirs 5
    --resolve "$DOMAIN:$port:$HEALTH_IP" -A 'portfolio-deploy-healthcheck curl'
    -o "$TMPDIR_PRIVATE/body" -w '%{http_code} %{num_redirects} %{url_effective}')
  [[ -n "$headers" ]] && args+=(-H "@$headers")
  local _
  for _ in $(seq 1 "$HEALTH_ATTEMPTS"); do
    if out="$(curl "${args[@]}" "$HEALTH_SCHEME://$DOMAIN$path" 2>"$TMPDIR_PRIVATE/err")"; then
      read -r code redirects effective <<<"$out"
      host=""; [[ "$effective" =~ ^https?://([^/:?#]+) ]] && host="${BASH_REMATCH[1]}"
      if [[ "$host" != "$DOMAIN" ]]; then
        code="$code, redirected away from $DOMAIN to ${host:-an unknown host}"
      elif [[ "$code" == 200 ]]; then
        if [[ "$expect" != json ]] || grep -q '"success":true' "$TMPDIR_PRIVATE/body"; then
          log "  $label $path -> 200 (redirects: $redirects)"
          return 0
        fi
      fi
    else
      code="curl: $(head -c 200 "$TMPDIR_PRIVATE/err")"
    fi
    sleep "$HEALTH_RETRY_DELAY"
  done
  log "  $label $path FAILED -> $code"
  return 1
}

http_checks() { # <release-dir>
  local release="$1" ok=0 key="" route asset image
  log "HTTP checks via the real web stack ($HEALTH_SCHEME://$DOMAIN @ $HEALTH_IP)"
  http_check health /up html || ok=1
  http_check web / html || ok=1
  if [[ "$HEALTH_API" == skip ]]; then
    log "  api /api/v1/portfolio skipped (PORTFOLIO_HEALTH_API=skip)"
  else
    route="$(release_api_route "$release")"
    [[ -f "$release/bootstrap/cache/config.php" ]] && key="$(read_api_key "$release")"
    if [[ "$route" == 1 && -n "$key" ]]; then
      printf 'X-Api-Key: %s\nAccept: application/json\n' "$key" > "$TMPDIR_PRIVATE/api-headers"
      http_check api /api/v1/portfolio json "$TMPDIR_PRIVATE/api-headers" || ok=1
      rm -f -- "$TMPDIR_PRIVATE/api-headers"
    elif [[ "$HEALTH_API" == require ]]; then
      log "  api /api/v1/portfolio FAILED -> required, but the release has $([[ "$route" == 1 ]] && echo 'no API_KEY' || echo 'no such route')"
      ok=1
    else
      log "  api /api/v1/portfolio skipped ($([[ "$route" == 1 ]] && echo 'API_KEY is not configured' || echo 'route not registered by this release'))"
    fi
  fi
  if [[ -f "$release/public/build/manifest.json" ]]; then
    asset="$("$PHP_BIN" -r '$m = json_decode(file_get_contents($argv[1]), true); echo current($m)["file"] ?? "";' "$release/public/build/manifest.json")"
    [[ -n "$asset" ]] && { http_check asset "/build/$asset" html || ok=1; }
  else
    http_check static /robots.txt html || ok=1
  fi
  image="$(cd "$SHARED/storage/app/public" && find . -type f \( -name '*.jpg' -o -name '*.png' -o -name '*.webp' \) |
    sed 's#^\./##' | grep -E '^[A-Za-z0-9._/-]+$' | sort | head -n 1 || true)"
  [[ -n "$image" ]] && { http_check media "/storage/$image" html || ok=1; }
  return "$ok"
}

# Waits until the root watcher reloaded PHP-FPM for the given release, so no
# worker keeps serving a stale realpath/OPcache view of `current`.
wait_for_fpm_reload() {
  local want="$1" i
  for i in $(seq 1 "$FPM_RELOAD_TIMEOUT"); do
    [[ "$(cat "$RELOAD_STAMP" 2>/dev/null)" == "$want" ]] && { log "PHP-FPM reloaded for $want"; return 0; }
    # Re-notify the root watcher in case an event was coalesced.
    (( i % 5 == 0 )) && touch "$RELEASES/.reload-request"
    sleep 1
  done
  log "PHP-FPM reload was not confirmed within ${FPM_RELOAD_TIMEOUT}s"
  return 1
}

switch_current() { # <link text, relative to releases/>
  local tmp="$RELEASES/.current.$$"
  ln -sfn -- "$1" "$tmp"
  mv -Tf -- "$tmp" "$CURRENT"
}

record() { printf '%s %s %s\n' "$(date -u +%Y-%m-%dT%H:%M:%SZ)" "$1" "$2" >> "$HISTORY"; }

# ------------------------------------------------------------------- modes
build_release() {
  verify_archive
  TARGET="$RELEASES/$SHA"
  if [[ -e "$TARGET" ]]; then
    if [[ "$(readlink -f "$CURRENT")" == "$(readlink -f "$TARGET")" ]]; then
      log "Release $SHA is already active; re-running health checks only"
      wait_for_fpm_reload "$(readlink -f "$TARGET")"
      http_checks "$TARGET" || fail "active release fails health checks"
      rm -f -- "$ARCHIVE" "$ARTIFACTS/release-$SHA.sha256"
      exit 0
    fi
    # It may be the rollback target (.previous): keep it until this run is
    # live; cleanup() puts it back if the run fails or is a dry-run.
    SET_ASIDE="$RELEASES/.replaced-$SHA-$$"
    log "Setting aside the existing inactive copy of $SHA until this run finishes"
    mv -T -- "$TARGET" "$SET_ASIDE"
  fi

  STAGE="$(mktemp -d "$RELEASES/.stage-$SHA-XXXXXX")"
  log "Extracting release $SHA"
  tar -xzf "$ARCHIVE" -C "$STAGE" --no-same-owner --no-same-permissions --no-overwrite-dir
  grep -qx "sha=$SHA" "$STAGE/.release-manifest" || fail "release manifest does not match $SHA"
  local f
  for f in artisan composer.lock vendor/autoload.php public/index.php bootstrap/app.php .github/scripts/smoke-release.php; do
    [[ -f "$STAGE/$f" ]] || fail "release is missing $f"
  done
  if [[ -f "$STAGE/package.json" && ! -f "$STAGE/public/build/manifest.json" ]]; then
    fail "frontend release has no Vite manifest"
  fi

  # Caches embed absolute paths, so the release is moved to its final path first.
  mv -T -- "$STAGE" "$TARGET"
  STAGE=""
  TARGET_CREATED=1

  # Permissions: owner rwX, PHP-FPM user read-only, web server only public/.
  chmod -R u=rwX,g=rX,o= -- "$TARGET"
  setfacl -R -m "u:$HESTIA_USER:rX,m::rX" -- "$TARGET"
  setfacl -m "u:$WEB_SERVER_USER:x" -- "$TARGET"
  setfacl -R -m "u:$WEB_SERVER_USER:rX" -- "$TARGET/public"

  rm -rf -- "$TARGET/storage" "$TARGET/.env" "$TARGET/public/storage"
  ln -s -- "$SHARED/storage" "$TARGET/storage"
  ln -s -- "$SHARED/.env" "$TARGET/.env"
  ln -s -- ../storage/app/public "$TARGET/public/storage"

  log "Checking PHP platform requirements"
  if command -v composer >/dev/null; then
    composer check-platform-reqs --no-dev --no-interaction --working-dir="$TARGET" >"$TMPDIR_PRIVATE/platform" 2>&1 ||
      { cat "$TMPDIR_PRIVATE/platform" >&2; fail "PHP extensions/version do not satisfy composer.lock"; }
  else
    log "WARNING: composer is not installed, so PHP extensions are not checked here (the PHP version is still enforced by vendor/composer/platform_check.php)"
  fi

  log "Building Laravel caches"
  artisan_step package:discover
  artisan_step config:cache
  artisan_step route:cache
  artisan_step event:cache
  if [[ -d "$TARGET/resources/views" ]]; then artisan_step view:cache; fi
  [[ "$(artisan route:list --json | "$PHP_BIN" -r 'echo count(json_decode(stream_get_contents(STDIN), true));')" -gt 0 ]] ||
    fail "no routes registered"
  API_ROUTE="$(release_api_route "$TARGET")"
  if [[ "$HEALTH_API" == require && "$API_ROUTE" != 1 ]]; then
    fail "PORTFOLIO_HEALTH_API=require, but this release does not register GET api/v1/portfolio"
  fi

  log "Checking database connection and migrations"
  local status
  status="$(artisan migrate:status)" || fail "cannot read migration status (database unreachable?)"
  if grep -q 'Pending' <<<"$status"; then
    grep 'Pending' <<<"$status" >&2
    fail "pending migrations: back up the database and apply them in an approved maintenance step first"
  fi

  smoke_in_process || fail "in-process smoke test failed"
  setfacl -R -m "u:$HESTIA_USER:rX,m::rX" -- "$TARGET/bootstrap/cache"
}

do_deploy() {
  preflight
  build_release
  if [[ "$MODE" == dry-run ]]; then
    rm -f -- "$ARCHIVE" "$ARTIFACTS/release-$SHA.sha256"
    log "Dry run passed for $SHA; discarding the prepared release (site untouched)"
    return 0
  fi

  local old old_real new_real
  old="$(readlink "$CURRENT")"
  old_real="$(readlink -f "$CURRENT")"
  new_real="$(readlink -f "$TARGET")"
  log "Switching current: $old -> $SHA"
  switch_current "$SHA"
  ACTIVATED=1

  if wait_for_fpm_reload "$new_real" && http_checks "$TARGET"; then
    printf '%s\n' "$old" > "$RELEASES/.previous"
    record deployed "$SHA (previous: $old)"
    artisan queue:restart >/dev/null || log "WARNING: queue:restart failed (workers pick up the release on their next run)"
    prune_releases "$old"
    rm -f -- "$ARCHIVE" "$ARTIFACTS/release-$SHA.sha256"
    log "Deployment successful: $SHA"
    log "Rollback: bash $CURRENT/.github/scripts/deploy-release.sh rollback"
    return 0
  fi

  log "Health checks failed; switching back to $old"
  switch_current "$old"
  ACTIVATED=0
  wait_for_fpm_reload "$old_real" || true
  http_checks "$old_real" || log "WARNING: the previous release also fails health checks"
  record failed "$SHA (restored: $old)"
  rm -rf -- "$TARGET"
  fail "release $SHA was rolled back; database migrations are never reversed automatically"
}

do_rollback() {
  preflight
  local to="${SHA:-}"
  [[ -n "$to" ]] || to="$(cat "$RELEASES/.previous" 2>/dev/null || true)"
  [[ -n "$to" ]] || fail "no previous release recorded; pass one explicitly"
  [[ "$to" =~ ^([0-9a-f]{40}|\.\./private)$ ]] || fail "invalid rollback target: $to"
  [[ -d "$RELEASES/$to/public" ]] || fail "rollback target $to does not exist"
  local from
  from="$(readlink "$CURRENT")"
  [[ "$from" != "$to" ]] || fail "$to is already current"
  log "Rolling back: $from -> $to"
  switch_current "$to"
  wait_for_fpm_reload "$(readlink -f "$RELEASES/$to")" || log "WARNING: PHP-FPM reload not confirmed"
  printf '%s\n' "$from" > "$RELEASES/.previous"
  record rollback "$to (from: $from)"
  http_checks "$RELEASES/$to" || fail "rolled back to $to, but it fails health checks"
  log "Rollback complete: $to is live"
}

prune_releases() { # keeps the newest $KEEP_RELEASES plus current and previous
  local previous="$1" dir name kept=0
  while IFS= read -r dir; do
    name="$(basename "$dir")"
    [[ "$name" =~ ^[0-9a-f]{40}$ ]] || continue
    if [[ "$name" == "$(readlink "$CURRENT")" || "$name" == "$previous" ]]; then continue; fi
    kept=$((kept + 1))
    if (( kept > KEEP_RELEASES )); then
      log "Pruning old release $name"
      rm -rf -- "$dir"
    fi
  done < <(find "$RELEASES" -mindepth 1 -maxdepth 1 -type d -printf '%T@ %p\n' | sort -rn | cut -d' ' -f2-)
}

do_status() {
  echo "current:  $(readlink "$CURRENT") -> $(readlink -f "$CURRENT")"
  echo "previous: $(cat "$RELEASES/.previous" 2>/dev/null || echo none)"
  echo "fpm reload stamp: $(cat "$RELOAD_STAMP" 2>/dev/null || echo missing)"
  echo "releases:"; find "$RELEASES" -mindepth 1 -maxdepth 1 -type d -name '[0-9a-f]*' -printf '  %TY-%Tm-%Td %TH:%TM  %f\n' | sort
  echo "history (last 10):"; tail -n 10 "$HISTORY" 2>/dev/null | sed 's/^/  /' || true
}

# Runs this script again in its own session with output going to a log file,
# and streams that log to the caller. If the SSH connection drops, or GitHub
# cancels or times out the job, the caller's side dies with SIGPIPE, but the
# detached run still finishes its health checks and any automatic rollback.
run_detached() {
  command -v setsid >/dev/null || fail "required command missing: setsid"
  local logdir="$ARTIFACTS/logs" log pid status=0
  install -d -m 700 "$logdir"
  log="$logdir/$(date -u +%Y%m%dT%H%M%SZ)-$MODE-${SHA:-previous}-$$.log"
  PORTFOLIO_FOREGROUND=1 setsid bash "$SELF" "$@" > "$log" 2>&1 < /dev/null &
  pid=$!
  log "Running detached as pid $pid; full log on the server: $log"
  tail -n +1 --pid="$pid" -f "$log" 2>/dev/null || true
  wait "$pid" || status=$?
  exit "$status"
}

case "$MODE" in
  deploy|dry-run|rollback)
    if [[ "$MODE" != rollback ]]; then
      [[ "$SHA" =~ ^[0-9a-f]{40}$ ]] || fail "usage: $0 $MODE <40-char-sha>"
    fi
    [[ "${PORTFOLIO_FOREGROUND:-0}" == 1 ]] || run_detached "$@"
    if [[ "$MODE" == rollback ]]; then do_rollback; else do_deploy; fi ;;
  status) do_status ;;
  *) echo "usage: $0 {deploy|dry-run} <sha> | rollback [release] | status" >&2; exit 2 ;;
esac
