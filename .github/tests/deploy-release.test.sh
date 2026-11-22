#!/usr/bin/env bash
# Hermetic tests for .github/scripts/deploy-release.sh.
#
# Each test builds a fake Hestia layout in a temporary directory and runs the
# real deploy script against it. Only the server-specific tools are faked:
#   php       artisan commands and the in-process smoke test (php -r runs real PHP)
#   curl      answers for whichever release public_html currently serves
#   systemctl reports the PHP-FPM watcher unit as active
#   setfacl   no-op
# A background loop plays the root PHP-FPM reload watcher: it writes the
# resolved releases/current target to the reload stamp, like
# .github/deploy/hestia/portfolio-fpm-reload does after reloading PHP-FPM.
# tar, sha256sum, flock, ln and mv are the real tools.
#
# Usage: bash .github/tests/deploy-release.test.sh [name-filter]
set -Euo pipefail

HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
REPO="$(cd "$HERE/../.." && pwd)"
SCRIPT="$REPO/.github/scripts/deploy-release.sh"
REAL_PHP="$(command -v php)"
FILTER="${1:-}"
ROOT="$(mktemp -d)"
trap 'stop_watcher; rm -rf -- "$ROOT"' EXIT

PASSED=0
FAILED=0
FAILURES=()
WATCHER_PID=""

A=aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa
B=bbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb

# ------------------------------------------------------------------ fakes
write_fakes() {
  mkdir -p "$BIN"
  cat > "$BIN/php" <<'FAKE'
#!/usr/bin/env bash
# Fake php: artisan commands and the smoke test read the release's .sim file.
for a in "$@"; do [[ "$a" == -r ]] && exec "$SIM_REAL_PHP" "$@"; done
args=("$@"); release=""; cmd=""; smoke=0
for ((i = 0; i < ${#args[@]}; i++)); do
  a="${args[$i]}"
  if [[ "$a" == */artisan ]]; then
    release="${a%/artisan}"
    for ((j = i + 1; j < ${#args[@]}; j++)); do
      [[ "${args[$j]}" == --* ]] || { cmd="${args[$j]}"; break; }
    done
    break
  fi
  if [[ "$a" == */smoke-release.php || "$a" == -- ]]; then smoke=1; release="${args[$((i + 1))]}"; break; fi
done
# sim <key> <default>: value from the release's .sim file; a key set to "" stays empty.
sim() { if grep -q "^$1=" "$release/.sim" 2>/dev/null; then sed -n "s/^$1=//p" "$release/.sim" | tail -n 1; else echo "$2"; fi; }
echo "${cmd:-smoke} $release" >> "$SIM_CALLS"
if (( smoke )); then
  [[ -t 0 ]] || cat > /dev/null
  [[ "$(sim smoke ok)" == ok ]] || { echo "  / -> 500" >&2; exit 1; }
  echo "  /up -> 200"; echo "  / -> 200"; exit 0
fi
if [[ "$(sim fail none)" == "$cmd" ]]; then echo "simulated failure of $cmd" >&2; exit 1; fi
case "$cmd" in
  config:cache)
    mkdir -p "$release/bootstrap/cache"
    printf '<?php return ["app" => ["api_key" => "%s"]];\n' "$(sim api_key test-key)" > "$release/bootstrap/cache/config.php" ;;
  route:list)
    if [[ "$(sim api_route 1)" == 1 ]]; then
      echo '[{"method":"GET|HEAD","uri":"up"},{"method":"GET|HEAD","uri":"\/"},{"method":"GET|HEAD","uri":"api\/v1\/portfolio"}]'
    else
      echo '[{"method":"GET|HEAD","uri":"up"},{"method":"GET|HEAD","uri":"\/"}]'
    fi ;;
  migrate:status)
    echo "  2026_01_01_000000_create_users_table ......... [1] Ran"
    [[ "$(sim pending 0)" == 1 ]] && echo "  2026_12_01_000000_add_column ................ Pending"
    ;;
esac
exit 0
FAKE

  cat > "$BIN/curl" <<'FAKE'
#!/usr/bin/env bash
# Fake curl: answers for the release public_html currently resolves to.
out=""; fmt=""; url=""
while [[ $# -gt 0 ]]; do
  case "$1" in
    -o) out="$2"; shift 2 ;;
    -w) fmt="$2"; shift 2 ;;
    -H|-A|--resolve|--max-time|--max-redirs|--connect-timeout) shift 2 ;;
    http://*|https://*) url="$1"; shift ;;
    *) shift ;;
  esac
done
served="$(readlink -f "$SIM_BASE/public_html")"
release="$(dirname "$served")"
sim() { local v; v="$(sed -n "s/^$1=//p" "$release/.sim" 2>/dev/null | tail -n 1)"; echo "${v:-$2}"; }
path="/${url#*://*/}"; [[ "$url" == *://*/* ]] || path="/"
echo "GET $path $(basename "$release")" >> "$SIM_CALLS"
code="$(sim http 200)"; body="<html>ok</html>"; effective="$url"; redirects=0
if [[ "$code" == down ]]; then echo "curl: (28) Operation timed out" >&2; exit 28; fi
if [[ "$path" == /api/v1/portfolio ]]; then
  if [[ "$(sim api ok)" == ok ]]; then body='{"success":true,"data":[]}'; else code=500; body='{"success":false}'; fi
fi
if [[ "$path" == / && -n "$(sim redirect '')" ]]; then effective="$(sim redirect '')"; redirects=1; fi
[[ -n "$out" ]] && printf '%s' "$body" > "$out"
fmt="${fmt//'%{http_code}'/$code}"; fmt="${fmt//'%{num_redirects}'/$redirects}"; fmt="${fmt//'%{url_effective}'/$effective}"
printf '%s' "$fmt"
FAKE

  printf '#!/usr/bin/env bash\nexit "${SIM_SYSTEMCTL_STATUS:-0}"\n' > "$BIN/systemctl"
  printf '#!/usr/bin/env bash\nexit 0\n' > "$BIN/setfacl"
  chmod +x "$BIN"/*
}

# ----------------------------------------------------------------- layout
setup() {
  WORK="$(mktemp -d "$ROOT/t.XXXXXX")"
  BASE="$WORK/web"; BIN="$WORK/bin"; HOMEDIR="$WORK/home"; ART="$HOMEDIR/.portfolio-artifacts"
  STAMP="$WORK/state/active"; CALLS="$WORK/calls"; OUT="$WORK/out.log"
  mkdir -p "$BASE/private/public" "$BASE/private/bootstrap/cache" "$BASE/releases" "$HOMEDIR" "$ART" "$WORK/state"
  mkdir -p "$BASE/shared/storage/app/public" "$BASE/shared/storage/logs" "$BASE/shared/storage/framework/views"
  : > "$BASE/private/artisan"; printf 'http=200\n' > "$BASE/private/.sim"
  printf 'img' > "$BASE/shared/storage/app/public/avatar.png"
  printf 'APP_KEY=base64:test\n' > "$BASE/shared/.env"; chmod 0444 "$BASE/shared/.env"
  ln -s ../private "$BASE/releases/current"
  ln -s "$BASE/releases/current/public" "$BASE/public_html"
  readlink -f "$BASE/releases/current" > "$STAMP"
  : > "$CALLS"
  write_fakes
  start_watcher
}

start_watcher() {
  stop_watcher
  ( while :; do
      t="$(readlink -f "$BASE/releases/current" 2>/dev/null)" || t=""
      if [[ -n "$t" && -d "$t/public" && "$(cat "$STAMP" 2>/dev/null)" != "$t" ]]; then
        printf '%s\n' "$t" > "$STAMP.tmp" && mv -f "$STAMP.tmp" "$STAMP"
      fi
      sleep 0.1
    done ) &
  WATCHER_PID=$!
}

stop_watcher() {
  if [[ -n "$WATCHER_PID" ]]; then kill "$WATCHER_PID" 2>/dev/null; wait "$WATCHER_PID" 2>/dev/null; WATCHER_PID=""; fi
}

# make_release <sha> [sim lines...]: a minimal Laravel-shaped release archive.
make_release() {
  local sha="$1"; shift
  local dir="$WORK/build/$sha"
  rm -rf -- "$dir"; mkdir -p "$dir/public/build" "$dir/vendor" "$dir/bootstrap/cache" "$dir/.github/scripts" "$dir/resources/views"
  : > "$dir/artisan"; : > "$dir/composer.lock"; : > "$dir/vendor/autoload.php"
  : > "$dir/public/index.php"; : > "$dir/bootstrap/app.php"
  printf 'User-agent: *\n' > "$dir/public/robots.txt"
  cp "$SCRIPT" "$dir/.github/scripts/deploy-release.sh"
  [[ -f "$REPO/.github/scripts/smoke-release.php" ]] && cp "$REPO/.github/scripts/smoke-release.php" "$dir/.github/scripts/"
  printf 'sha=%s\nbuilt_at=2026-01-01T00:00:00Z\nfrontend=no\ngithub_run=test\n' "$sha" > "$dir/.release-manifest"
  printf '%s\n' "$@" > "$dir/.sim"
  pack_release "$sha"
}

pack_release() {
  local sha="$1"
  tar --sort=name --owner=0 --group=0 --numeric-owner -czf "$ART/release-$sha.tar.gz" -C "$WORK/build/$sha" .
  (cd "$ART" && sha256sum "release-$sha.tar.gz" > "release-$sha.sha256")
}

# add_entry <sha> <kind> <name> [target]: appends a crafted tar member (re-packs + new checksum).
add_entry() {
  python3 -I - "$ART/release-$1.tar.gz" "$2" "$3" "${4:-}" <<'PY'
import io, sys, tarfile
path, kind, name, target = sys.argv[1:5]
with tarfile.open(path, 'r:gz') as src:
    members = [(m, src.extractfile(m).read() if m.isfile() else None) for m in src.getmembers()]
with tarfile.open(path, 'w:gz') as dst:
    for m, data in members:
        dst.addfile(m, io.BytesIO(data) if data is not None else None)
    info = tarfile.TarInfo(name)
    if kind == 'file':
        info.size = 1; dst.addfile(info, io.BytesIO(b'x'))
    else:
        info.type = {'symlink': tarfile.SYMTYPE, 'hardlink': tarfile.LNKTYPE, 'fifo': tarfile.FIFOTYPE}[kind]
        info.linkname = target
        dst.addfile(info)
PY
  (cd "$ART" && sha256sum "release-$1.tar.gz" > "release-$1.sha256")
}

# run <args...>: runs the deploy script as the fake deploy user, returns its exit code.
run() {
  # shellcheck disable=SC2086 # EXTRA_ENV holds KEY=value words on purpose
  ( cd "$HOMEDIR" && env PATH="$BIN:$PATH" HOME="$HOMEDIR" \
      SIM_REAL_PHP="$REAL_PHP" SIM_BASE="$BASE" SIM_CALLS="$CALLS" \
      PORTFOLIO_BASE="$BASE" PORTFOLIO_RELOAD_STAMP="$STAMP" PORTFOLIO_ARTIFACTS="$ART" \
      PORTFOLIO_DEPLOY_USER="$(id -un)" PORTFOLIO_HESTIA_USER="$(id -un)" PORTFOLIO_WEB_SERVER_USER="$(id -un)" \
      PORTFOLIO_PHP="$BIN/php" PORTFOLIO_HEALTH_IP=127.0.0.1 PORTFOLIO_KEEP_RELEASES="${KEEP:-5}" \
      PORTFOLIO_FPM_RELOAD_TIMEOUT=3 PORTFOLIO_HEALTH_ATTEMPTS=2 PORTFOLIO_HEALTH_RETRY_DELAY=0 \
      PORTFOLIO_FOREGROUND="${FOREGROUND:-1}" ${EXTRA_ENV:-} \
      bash "$SCRIPT" "$@" ) > "$OUT" 2>&1 < /dev/null
}

# ------------------------------------------------------------- assertions
current() { readlink "$BASE/releases/current"; }
CASE_FAILED=0
fail_check() { CASE_FAILED=1; printf '      ✗ %s\n' "$*"; }
expect_exit()      { [[ "$1" == "$2" ]] || fail_check "exit code $1, expected $2"; }
expect_nonzero()   { [[ "$1" != 0 ]] || fail_check "exit code 0, expected a failure"; }
expect_current()   { [[ "$(current)" == "$1" ]] || fail_check "current -> $(current), expected $1"; }
expect_dir()       { [[ -d "$1" ]] || fail_check "missing directory ${1#"$WORK"/}"; }
expect_no_path()   { [[ ! -e "$1" && ! -L "$1" ]] || fail_check "unexpected path ${1#"$WORK"/}"; }
expect_log()       { grep -qF -- "$1" "$OUT" || fail_check "log lacks: $1"; }
expect_no_log()    { ! grep -qF -- "$1" "$OUT" || fail_check "log unexpectedly has: $1"; }
expect_previous()  { [[ "$(cat "$BASE/releases/.previous" 2>/dev/null)" == "$1" ]] || fail_check ".previous is '$(cat "$BASE/releases/.previous" 2>/dev/null)', expected $1"; }
expect_history()   { grep -qE -- "$1" "$BASE/releases/.history" 2>/dev/null || fail_check "history lacks /$1/"; }
expect_called()    { grep -qE -- "$1" "$CALLS" || fail_check "expected call /$1/"; }
expect_not_called(){ ! grep -qE -- "$1" "$CALLS" || fail_check "unexpected call /$1/"; }
expect_no_leftovers() {
  local left
  left="$(find "$BASE/releases" -mindepth 1 -maxdepth 1 -name '.stage-*' -o -mindepth 1 -maxdepth 1 -name '.replaced-*' -o -mindepth 1 -maxdepth 1 -name '.current.*')"
  [[ -z "$left" ]] || fail_check "leftover temporary entries: $left"
}

it() { # <name> <function>
  [[ -z "$FILTER" || "$1" == *"$FILTER"* ]] || return 0
  CASE_FAILED=0
  setup
  "$2"
  stop_watcher
  if (( CASE_FAILED )); then
    FAILED=$((FAILED + 1)); FAILURES+=("$1"); printf '  ✗ %s\n' "$1"; sed 's/^/        | /' "$OUT" | tail -n 25
  else
    PASSED=$((PASSED + 1)); printf '  ✓ %s\n' "$1"
  fi
}

# ------------------------------------------------------------------ tests
t_first_deploy() {
  make_release "$A"; run deploy "$A"; local s=$?
  expect_exit "$s" 0; expect_current "$A"; expect_previous ../private
  expect_history "deployed $A \(previous: \.\./private\)"
  expect_no_path "$ART/release-$A.tar.gz"; expect_no_leftovers
  expect_called "GET /up $A"; expect_called "GET /api/v1/portfolio $A"
}

t_second_deploy() {
  make_release "$A"; run deploy "$A"
  make_release "$B"; run deploy "$B"; local s=$?
  expect_exit "$s" 0; expect_current "$B"; expect_previous "$A"; expect_dir "$BASE/releases/$A"
}

t_dry_run_untouched() {
  make_release "$A"; run deploy "$A"
  make_release "$B"; run dry-run "$B"; local s=$?
  expect_exit "$s" 0; expect_current "$A"; expect_no_path "$BASE/releases/$B"; expect_no_leftovers
  expect_log "Dry run passed for $B"
}

t_dry_run_removes_artifact() {
  make_release "$A"; run dry-run "$A"; local s=$?
  expect_exit "$s" 0; expect_no_path "$ART/release-$A.tar.gz"; expect_no_path "$ART/release-$A.sha256"
}

t_dry_run_keeps_inactive_release() {
  make_release "$A"; run deploy "$A"
  make_release "$B"; run deploy "$B"          # A is now the rollback target
  make_release "$A"; run dry-run "$A"; local s=$?
  expect_exit "$s" 0; expect_current "$B"
  expect_dir "$BASE/releases/$A"; expect_previous "$A"; expect_no_leftovers
}

t_failed_rebuild_keeps_inactive_release() {
  make_release "$A"; run deploy "$A"
  make_release "$B"; run deploy "$B"
  make_release "$A" "fail=config:cache"; run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current "$B"
  expect_dir "$BASE/releases/$A"; expect_previous "$A"; expect_no_leftovers
}

t_health_failure_restores_set_aside_release() {
  make_release "$A"; run deploy "$A"; make_release "$B"; run deploy "$B"
  run rollback                                   # current A, previous B (healthy copy)
  make_release "$B" "http=500"; run deploy "$B"; local s=$?
  expect_nonzero "$s"; expect_current "$A"; expect_previous "$B"; expect_no_leftovers
  expect_dir "$BASE/releases/$B"
  grep -qx 'http=500' "$BASE/releases/$B/.sim" 2>/dev/null && fail_check "the failed build replaced the original copy of $B"
}

t_redeploy_active_sha() {
  make_release "$A"; run deploy "$A"
  make_release "$A"; : > "$CALLS"; run deploy "$A"; local s=$?
  expect_exit "$s" 0; expect_current "$A"; expect_log "already active"
  expect_called "GET /up $A"; expect_no_path "$ART/release-$A.tar.gz"
}

t_fail_before_activation() {
  make_release "$A" "fail=config:cache"; run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_no_path "$BASE/releases/$A"; expect_no_leftovers
  expect_log "php artisan config:cache failed"
}

t_smoke_failure_blocks_activation() {
  make_release "$A" "smoke=fail"; run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_no_path "$BASE/releases/$A"
  expect_not_called "GET / $A"
}

t_pending_migrations_block() {
  make_release "$A" "pending=1"; run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_log "pending migrations"; expect_no_path "$BASE/releases/$A"
}

t_health_failure_rolls_back() {
  make_release "$A"; run deploy "$A"
  make_release "$B" "http=500"; run deploy "$B"; local s=$?
  expect_nonzero "$s"; expect_current "$A"; expect_no_path "$BASE/releases/$B"
  expect_history "failed $B \(restored: $A\)"; expect_called "GET /up $A"
}

t_first_deploy_failure_restores_private() {
  make_release "$A" "http=500"; run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_no_path "$BASE/releases/$A"
}

t_unreachable_web_stack_rolls_back() {
  make_release "$A"; run deploy "$A"
  make_release "$B" "http=down"; run deploy "$B"; local s=$?
  expect_nonzero "$s"; expect_current "$A"
}

t_fpm_reload_unconfirmed_rolls_back() {
  make_release "$A"; run deploy "$A"
  stop_watcher
  make_release "$B"; run deploy "$B"; local s=$?
  expect_nonzero "$s"; expect_current "$A"; expect_log "PHP-FPM reload was not confirmed"
}

t_api_failure_rolls_back() {
  make_release "$A"; run deploy "$A"
  make_release "$B" "api=fail"; run deploy "$B"; local s=$?
  expect_nonzero "$s"; expect_current "$A"
}

t_checksum_mismatch() {
  make_release "$A"; printf 'x' >> "$ART/release-$A.tar.gz"; run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_log "checksum mismatch"; expect_no_path "$BASE/releases/$A"
}

t_missing_artifact() {
  run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_log "artifact or checksum missing"
}

unsafe_case() { # <kind> <name> [target]
  make_release "$A"; add_entry "$A" "$@"; run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_no_path "$BASE/releases/$A"
  expect_no_path "$WORK/escaped"; expect_no_path "/tmp/portfolio-deploy-test-escape"
}
t_unsafe_absolute()  { unsafe_case file /tmp/portfolio-deploy-test-escape; }
t_unsafe_traversal() { unsafe_case file ./../escaped; }
t_unsafe_symlink()   { unsafe_case symlink ./public/passwd /etc/passwd; }
t_unsafe_hardlink()  { unsafe_case hardlink ./public/env ./artisan; }
t_unsafe_fifo()      { unsafe_case fifo ./public/pipe; }
t_unsafe_outside()   { unsafe_case file outside-dot-slash; }

t_manifest_mismatch() {
  make_release "$A"
  printf 'sha=%s\n' "$B" > "$WORK/build/$A/.release-manifest"; pack_release "$A"
  run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_log "manifest does not match"
}

t_concurrent_deploy_refused() {
  make_release "$A"; mkdir -p "$BASE/releases"
  ( exec 9>"$BASE/releases/.deploy.lock"; flock 9; sleep 5 ) & local holder=$!
  sleep 0.5
  run deploy "$A"; local s=$?
  kill "$holder" 2>/dev/null; wait "$holder" 2>/dev/null
  expect_nonzero "$s"; expect_current ../private; expect_log "another deployment is running"
}

t_interrupted_leftovers_recovered() {
  make_release "$A"; run deploy "$A"
  # A killed deploy of B left a staging dir and a half-built release behind.
  mkdir -p "$BASE/releases/.stage-$B-XXXX/public" "$BASE/releases/$B"
  make_release "$B"; run deploy "$B"; local s=$?
  expect_exit "$s" 0; expect_current "$B"; expect_no_leftovers
}

t_rollback_to_previous() {
  make_release "$A"; run deploy "$A"; make_release "$B"; run deploy "$B"
  run rollback; local s=$?
  expect_exit "$s" 0; expect_current "$A"; expect_previous "$B"; expect_history "rollback $A \(from: $B\)"
}

t_rollback_to_private() {
  make_release "$A"; run deploy "$A"
  run rollback ../private; local s=$?
  expect_exit "$s" 0; expect_current ../private
}

t_rollback_missing_target() {
  make_release "$A"; run deploy "$A"; make_release "$B"; run deploy "$B"
  rm -rf -- "${BASE:?}/releases/$A"
  run rollback; local s=$?
  expect_nonzero "$s"; expect_current "$B"; expect_log "does not exist"
}

t_rollback_without_previous() {
  run rollback; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_log "no previous release recorded"
}

t_rollback_rejects_bad_target() {
  run rollback "../../etc"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_log "invalid rollback target"
}

t_prune_keeps_documented_count() {
  KEEP=2
  local s
  for s in 1111111111111111111111111111111111111111 2222222222222222222222222222222222222222 \
           3333333333333333333333333333333333333333 4444444444444444444444444444444444444444 \
           5555555555555555555555555555555555555555; do
    make_release "$s"; run deploy "$s"; sleep 1.1   # distinct mtimes for the newest-first order
  done
  KEEP=5
  # current (5…) + previous (4…) + the KEEP=2 newest others (3…, 2…); 1… is pruned.
  expect_current 5555555555555555555555555555555555555555
  expect_dir "$BASE/releases/4444444444444444444444444444444444444444"
  expect_dir "$BASE/releases/3333333333333333333333333333333333333333"
  expect_dir "$BASE/releases/2222222222222222222222222222222222222222"
  expect_no_path "$BASE/releases/1111111111111111111111111111111111111111"
}

t_requires_deploy_user() {
  make_release "$A"
  EXTRA_ENV="PORTFOLIO_DEPLOY_USER=someone-else" run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_log "must run as someone-else"; expect_current ../private
}

t_writable_env_refused() {
  chmod 0644 "$BASE/shared/.env"; make_release "$A"; run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_log "must be read-only"; expect_current ../private
}

t_inactive_watcher_refused() {
  make_release "$A"
  EXTRA_ENV="SIM_SYSTEMCTL_STATUS=3" run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_log "watcher"; expect_current ../private
}

t_api_auto_checked_when_route_and_key() {
  make_release "$A"; run deploy "$A"; local s=$?
  expect_exit "$s" 0; expect_called "GET /api/v1/portfolio $A"
}

t_api_auto_skipped_without_route() {
  make_release "$A" "api_route=0" "api=fail"; run deploy "$A"; local s=$?
  expect_exit "$s" 0; expect_current "$A"; expect_not_called "GET /api/v1/portfolio"
  expect_log "route not registered by this release"
}

t_api_auto_skipped_without_key() {
  make_release "$A" "api_key=" "api=fail"; run deploy "$A"; local s=$?
  expect_exit "$s" 0; expect_not_called "GET /api/v1/portfolio"; expect_log "API_KEY is not configured"
}

t_api_require_without_route_blocks() {
  make_release "$A" "api_route=0"
  EXTRA_ENV="PORTFOLIO_HEALTH_API=require" run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_log "does not register GET api/v1/portfolio"
}

t_api_skip_ignores_api() {
  make_release "$A" "api=fail"
  EXTRA_ENV="PORTFOLIO_HEALTH_API=skip" run deploy "$A"; local s=$?
  expect_exit "$s" 0; expect_current "$A"; expect_not_called "GET /api/v1/portfolio"; expect_log "PORTFOLIO_HEALTH_API=skip"
}

t_api_invalid_mode_refused() {
  make_release "$A"
  EXTRA_ENV="PORTFOLIO_HEALTH_API=sometimes" run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_log "must be auto, require or skip"
}

t_redirect_away_rolls_back() {
  make_release "$A"; run deploy "$A"
  make_release "$B" "redirect=https://elsewhere.example/"; run deploy "$B"; local s=$?
  expect_nonzero "$s"; expect_current "$A"; expect_log "redirected away from"
}

t_detached_run_keeps_log() {
  make_release "$A"; FOREGROUND=0 run deploy "$A"; local s=$?
  expect_exit "$s" 0; expect_current "$A"; expect_log "Running detached"
  grep -lqF "Deployment successful: $A" "$ART"/logs/*-deploy-"$A"-*.log 2>/dev/null || fail_check "no server-side log with the result"
}

t_detached_failure_propagates_exit() {
  make_release "$A" "fail=route:cache"; FOREGROUND=0 run deploy "$A"; local s=$?
  expect_nonzero "$s"; expect_current ../private; expect_log "php artisan route:cache failed"
}

# The SSH client disappears right after the switch (GitHub job cancelled or timed
# out): the deploy must still finish its health checks and roll back on its own.
t_client_disconnect_mid_deploy() {
  make_release "$A"; run deploy "$A"
  make_release "$B" "http=500"
  ( cd "$HOMEDIR" && env PATH="$BIN:$PATH" HOME="$HOMEDIR" \
      SIM_REAL_PHP="$REAL_PHP" SIM_BASE="$BASE" SIM_CALLS="$CALLS" \
      PORTFOLIO_BASE="$BASE" PORTFOLIO_RELOAD_STAMP="$STAMP" PORTFOLIO_ARTIFACTS="$ART" \
      PORTFOLIO_DEPLOY_USER="$(id -un)" PORTFOLIO_HESTIA_USER="$(id -un)" PORTFOLIO_WEB_SERVER_USER="$(id -un)" \
      PORTFOLIO_PHP="$BIN/php" PORTFOLIO_HEALTH_IP=127.0.0.1 \
      PORTFOLIO_FPM_RELOAD_TIMEOUT=3 PORTFOLIO_HEALTH_ATTEMPTS=2 PORTFOLIO_HEALTH_RETRY_DELAY=1 \
      PORTFOLIO_FOREGROUND="${FOREGROUND:-0}" \
      bash "$SCRIPT" deploy "$B" 2>&1 < /dev/null ) |
    while IFS= read -r line; do printf '%s\n' "$line" >> "$OUT"; [[ "$line" == *"Switching current"* ]] && break; done
  # Give a surviving deploy time to finish (it holds the lock while it runs).
  local _
  for _ in $(seq 1 40); do
    if ( exec 9>"$BASE/releases/.deploy.lock"; flock -n 9 ); then break; fi
    sleep 0.25
  done
  sleep 0.5
  expect_current "$A"
  expect_history "failed $B \(restored: $A\)"
}

t_usage_errors() {
  run deploy not-a-sha; local s=$?
  expect_nonzero "$s"; run bogus; s=$?; expect_exit "$s" 2
}

TESTS=(
  "first deploy switches from the legacy checkout:t_first_deploy"
  "second deploy records the previous release:t_second_deploy"
  "dry-run leaves the site untouched:t_dry_run_untouched"
  "dry-run removes the uploaded artifact:t_dry_run_removes_artifact"
  "dry-run keeps an existing inactive release (rollback target):t_dry_run_keeps_inactive_release"
  "failed rebuild keeps an existing inactive release:t_failed_rebuild_keeps_inactive_release"
  "failed redeploy restores the original inactive copy:t_health_failure_restores_set_aside_release"
  "redeploying the active SHA only re-checks health:t_redeploy_active_sha"
  "failure before activation leaves no release:t_fail_before_activation"
  "in-process smoke failure blocks activation:t_smoke_failure_blocks_activation"
  "pending migrations block activation:t_pending_migrations_block"
  "health failure after the switch rolls back:t_health_failure_rolls_back"
  "failed first deploy restores the legacy checkout:t_first_deploy_failure_restores_private"
  "unreachable web stack rolls back:t_unreachable_web_stack_rolls_back"
  "unconfirmed PHP-FPM reload rolls back:t_fpm_reload_unconfirmed_rolls_back"
  "failing API check rolls back:t_api_failure_rolls_back"
  "corrupted artifact is refused:t_checksum_mismatch"
  "missing artifact is refused:t_missing_artifact"
  "archive with an absolute path is refused:t_unsafe_absolute"
  "archive with path traversal is refused:t_unsafe_traversal"
  "archive with a symlink is refused:t_unsafe_symlink"
  "archive with a hardlink is refused:t_unsafe_hardlink"
  "archive with a FIFO is refused:t_unsafe_fifo"
  "archive entry outside ./ is refused:t_unsafe_outside"
  "release manifest must match the SHA:t_manifest_mismatch"
  "concurrent deployment is refused:t_concurrent_deploy_refused"
  "leftovers of an interrupted deploy are recovered:t_interrupted_leftovers_recovered"
  "rollback switches to the previous release:t_rollback_to_previous"
  "rollback to the legacy checkout:t_rollback_to_private"
  "rollback to a missing release is refused:t_rollback_missing_target"
  "rollback without a recorded previous release is refused:t_rollback_without_previous"
  "rollback rejects an invalid target:t_rollback_rejects_bad_target"
  "pruning keeps KEEP_RELEASES newest plus current and previous:t_prune_keeps_documented_count"
  "only the deploy user may run it:t_requires_deploy_user"
  "a writable shared .env is refused:t_writable_env_refused"
  "an inactive PHP-FPM watcher is refused:t_inactive_watcher_refused"
  "legacy API is checked when the route and API_KEY exist (auto):t_api_auto_checked_when_route_and_key"
  "legacy API check is skipped when the route is gone (auto):t_api_auto_skipped_without_route"
  "legacy API check is skipped without API_KEY (auto):t_api_auto_skipped_without_key"
  "PORTFOLIO_HEALTH_API=require blocks a release without the route:t_api_require_without_route_blocks"
  "PORTFOLIO_HEALTH_API=skip ignores the legacy API:t_api_skip_ignores_api"
  "an invalid PORTFOLIO_HEALTH_API value is refused:t_api_invalid_mode_refused"
  "a redirect away from the domain rolls back:t_redirect_away_rolls_back"
  "a detached run streams and keeps its log:t_detached_run_keeps_log"
  "a detached run returns the failure exit code:t_detached_failure_propagates_exit"
  "SSH disconnect mid-deploy still completes the rollback:t_client_disconnect_mid_deploy"
  "usage errors:t_usage_errors"
)

echo "deploy-release.sh hermetic tests"
for entry in "${TESTS[@]}"; do it "${entry%%:*}" "${entry##*:}"; done
echo
echo "$PASSED passed, $FAILED failed"
if (( FAILED )); then printf '  failed: %s\n' "${FAILURES[@]}"; exit 1; fi
