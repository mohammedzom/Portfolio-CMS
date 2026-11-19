#!/usr/bin/env bash
# One-time (idempotent) server setup for atomic releases on Hestia. Run as root.
#
#   hestia-bootstrap-releases.sh prepare  <hestia-user> <domain> [--seed-media DIR]
#       Creates releases/ + shared/ next to the existing private/ checkout,
#       installs the PHP-FPM backend template and the PHP-FPM reload watcher.
#       Does NOT touch public_html, the database or private/.
#   hestia-bootstrap-releases.sh switch-public-html <hestia-user> <domain>
#       Points public_html at releases/current/public, only if that resolves to
#       the directory public_html serves today (so the swap itself changes nothing).
#   hestia-bootstrap-releases.sh revert-public-html <hestia-user> <domain>
#       Points public_html back at private/public (emergency exit from the layout).
set -Eeuo pipefail
umask 022

ACTION="${1:-}"; HUSER="${2:-}"; DOMAIN="${3:-}"; shift 3 || true
SEED_MEDIA=""
while [[ $# -gt 0 ]]; do
  case "$1" in
    --seed-media) SEED_MEDIA="${2:?}"; shift 2 ;;
    *) echo "unknown option $1" >&2; exit 2 ;;
  esac
done

DEPLOY_USER="${PORTFOLIO_DEPLOY_USER:-portfolio-deploy}"
WEB_SERVER_USER="www-data"
PHP_VERSION="${PORTFOLIO_PHP_VERSION:-8.5}"
TEMPLATE="portfolio-releases-PHP-${PHP_VERSION/./_}"
HERE="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
HESTIA_BIN=/usr/local/hestia/bin

fail() { echo "ERROR: $*" >&2; exit 1; }
log() { echo "==> $*"; }

[[ "$(id -u)" == 0 ]] || fail "run as root"
[[ "$HUSER" =~ ^[a-z][a-z0-9_-]*$ && "$DOMAIN" =~ ^[a-z0-9.-]+$ ]] || fail "usage: $0 <action> <hestia-user> <domain>"
id "$DEPLOY_USER" >/dev/null 2>&1 || fail "user $DEPLOY_USER does not exist"
sudo -l -U "$DEPLOY_USER" 2>/dev/null | grep -q 'may run' && fail "$DEPLOY_USER must not have sudo rights"
BASE="/home/$HUSER/web/$DOMAIN"
[[ -d "$BASE/private" ]] || fail "$BASE/private not found (is $DOMAIN a Hestia domain of $HUSER?)"
UNIT="portfolio-release-${DOMAIN//./-}"

prepare() {
  log "Traverse-only access for $DEPLOY_USER to $BASE"
  setfacl -m "u:$DEPLOY_USER:x" "/home/$HUSER" "/home/$HUSER/web" "$BASE"

  log "releases/ (owned by $DEPLOY_USER, read-only for $HUSER)"
  install -d -o "$DEPLOY_USER" -g "$DEPLOY_USER" -m 0750 "$BASE/releases"
  setfacl -m "u:$HUSER:rx,u:$WEB_SERVER_USER:x" "$BASE/releases"
  if [[ ! -L "$BASE/releases/current" ]]; then
    ln -s ../private "$BASE/releases/current"
    chown -h "$DEPLOY_USER:$DEPLOY_USER" "$BASE/releases/current"
  fi

  log "shared/ (root-owned; only storage is writable)"
  install -d -o root -g root -m 0750 "$BASE/shared"
  setfacl -m "u:$HUSER:rx,u:$DEPLOY_USER:rx,u:$WEB_SERVER_USER:x" "$BASE/shared"

  if [[ ! -e "$BASE/shared/.env" ]]; then
    [[ -f "$BASE/private/.env" ]] || fail "no private/.env to copy"
    cp -p "$BASE/private/.env" "$BASE/shared/.env"
  fi
  chown "$HUSER:$HUSER" "$BASE/shared/.env"
  chmod 0600 "$BASE/shared/.env"
  setfacl -m "u:$DEPLOY_USER:r" "$BASE/shared/.env"
  # The legacy checkout's .env was world-readable; the owner (PHP-FPM) is all that needs it.
  [[ -f "$BASE/private/.env" && ! -L "$BASE/private/.env" ]] && chmod 0600 "$BASE/private/.env"

  if [[ ! -e "$BASE/shared/storage" ]]; then
    log "Copying private/storage to shared/storage (private/ is left untouched)"
    cp -a "$BASE/private/storage" "$BASE/shared/storage"
  fi
  if [[ -n "$SEED_MEDIA" ]]; then
    [[ -d "$SEED_MEDIA" ]] || fail "$SEED_MEDIA is not a directory"
    if find "$SEED_MEDIA" ! -type f ! -type d -print -quit | grep -q .; then
      fail "$SEED_MEDIA contains links or special files"
    fi
    log "Adding missing public media from $SEED_MEDIA (existing files are never overwritten)"
    cp -R --update=none --no-preserve=all "$SEED_MEDIA/." "$BASE/shared/storage/app/public/"
  fi
  local s="$BASE/shared/storage"
  for d in app/public app/private framework/cache/data framework/sessions framework/views framework/testing logs; do
    install -d "$s/$d"
  done
  chown -R --no-dereference "$HUSER:$HUSER" "$s"
  chmod -R u=rwX,g=rX,o= "$s"
  setfacl -R -m "u:$HUSER:rwX,u:$DEPLOY_USER:rwX" "$s"
  find "$s" -type d -exec setfacl -m "d:u::rwX,d:g::rX,d:o::-,d:u:$HUSER:rwX,d:u:$DEPLOY_USER:rwX" {} +
  setfacl -m "u:$WEB_SERVER_USER:x" "$s" "$s/app"
  setfacl -R -m "u:$WEB_SERVER_USER:rX" "$s/app/public"
  find "$s/app/public" -type d -exec setfacl -m "d:u:$WEB_SERVER_USER:rX" {} +

  log "PHP-FPM backend template $TEMPLATE (adds releases/ and shared/ to open_basedir)"
  install -o root -g root -m 0644 "$HERE/portfolio-releases-PHP-8_5.tpl" \
    "/usr/local/hestia/data/templates/web/php-fpm/$TEMPLATE.tpl"
  local current_tpl
  current_tpl="$("$HESTIA_BIN/v-list-web-domain" "$HUSER" "$DOMAIN" json | sed -nE 's/.*"BACKEND": "([^"]*)".*/\1/p')"
  if [[ "$current_tpl" != "$TEMPLATE" ]]; then
    "$HESTIA_BIN/v-change-web-domain-backend-tpl" "$HUSER" "$DOMAIN" "$TEMPLATE" yes
  fi
  grep -q "$BASE/releases:$BASE/shared" "/etc/php/$PHP_VERSION/fpm/pool.d/$DOMAIN.conf" ||
    fail "pool config does not contain the release paths"

  log "PHP-FPM reload watcher ($UNIT.path)"
  install -o root -g root -m 0755 "$HERE/portfolio-fpm-reload" /usr/local/sbin/portfolio-fpm-reload
  cat > "/etc/systemd/system/$UNIT.service" <<EOF
[Unit]
Description=Reload PHP-FPM after a new $DOMAIN release is activated
# Triggered by every change in releases/; a run without a new target is a no-op.
StartLimitIntervalSec=0

[Service]
Type=oneshot
StateDirectory=portfolio-release
StateDirectoryMode=0755
ExecStart=/usr/local/sbin/portfolio-fpm-reload $HUSER $DOMAIN $PHP_VERSION
EOF
  cat > "/etc/systemd/system/$UNIT.path" <<EOF
[Unit]
Description=Watch the active $DOMAIN release
StartLimitIntervalSec=0

[Path]
TriggerLimitIntervalSec=0
# Watch the directory: a rename of the current symlink (or a touch of
# .reload-request by the deploy script) is an event on releases/ itself.
PathChanged=$BASE/releases
Unit=$UNIT.service

[Install]
WantedBy=multi-user.target
EOF
  systemctl daemon-reload
  systemctl reset-failed "$UNIT.path" "$UNIT.service" 2>/dev/null || true
  systemctl enable "$UNIT.path"
  systemctl restart "$UNIT.path"
  systemctl start "$UNIT.service"
  log "Stamp: $(cat "/var/lib/portfolio-release/$DOMAIN.active")"
}

switch_public_html() {
  local want="$BASE/releases/current/public"
  [[ -d "$want" ]] || fail "$want does not exist"
  [[ "$(readlink -f "$want")" == "$(readlink -f "$BASE/public_html")" ]] ||
    fail "releases/current/public ($(readlink -f "$want")) differs from what public_html serves now"
  ln -sfn "$want" "$BASE/.public_html.next"
  chown -h "$HUSER:$HUSER" "$BASE/.public_html.next"
  mv -Tf "$BASE/.public_html.next" "$BASE/public_html"
  log "public_html -> $(readlink "$BASE/public_html")"
}

revert_public_html() {
  ln -sfn "$BASE/private/public" "$BASE/.public_html.next"
  chown -h "$HUSER:$HUSER" "$BASE/.public_html.next"
  mv -Tf "$BASE/.public_html.next" "$BASE/public_html"
  log "public_html -> $(readlink "$BASE/public_html")"
}

case "$ACTION" in
  prepare) prepare ;;
  switch-public-html) switch_public_html ;;
  revert-public-html) revert_public_html ;;
  *) fail "unknown action $ACTION" ;;
esac
