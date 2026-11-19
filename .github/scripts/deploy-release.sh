#!/usr/bin/env bash
# Runs as portfolio-deploy through the restricted, no-PTY GitHub Actions SSH key.
# This user owns only the release area, not Hestia or the existing private checkout.
set -Eeuo pipefail

SHA="$1"
[[ "$SHA" =~ ^[0-9a-f]{40}$ ]] || { echo "Invalid commit SHA" >&2; exit 2; }
[[ "$(id -un)" == "portfolio-deploy" ]] || { echo "Wrong SSH identity" >&2; exit 2; }

BASE="/home/moh/web/mohammedzomlot.dev"
RELEASES="$BASE/releases"
SHARED="$BASE/shared"
CURRENT="$RELEASES/current"
TARGET="$RELEASES/$SHA"
ARCHIVE="$HOME/.portfolio-artifacts/release-$SHA.tar.gz"
PUBLIC_LINK="$BASE/public_html"
EXPECTED_PUBLIC_LINK="$RELEASES/current/public"

# Fail before modifying the site if an administrator has not bootstrapped Hestia.
test -d "$RELEASES" && test -w "$RELEASES" || { echo "Release directory not provisioned" >&2; exit 2; }
test -L "$CURRENT" || { echo "No current release symlink" >&2; exit 2; }
test -L "$PUBLIC_LINK" && [[ "$(readlink "$PUBLIC_LINK")" == "$EXPECTED_PUBLIC_LINK" ]] || {
  echo "Hestia public_html is not configured for atomic releases" >&2; exit 2;
}
test -r "$BASE/private/.env" || { echo "Deploy account cannot read the shared .env" >&2; exit 2; }
test -d "$SHARED/storage" && test -w "$SHARED/storage" || {
  echo "Shared storage not provisioned or not writable" >&2; exit 2;
}
test -f "$ARCHIVE" || { echo "Missing GitHub release archive" >&2; exit 2; }
command -v php >/dev/null && command -v composer >/dev/null && command -v curl >/dev/null || {
  echo "php, composer and curl must be on the deploy user's PATH" >&2; exit 2;
}
if [[ -e "$TARGET" ]]; then
  echo "Release already exists: $TARGET" >&2
  exit 2
fi

# Archive created by GitHub Actions with git archive from the exact tested SHA.
tar -tzf "$ARCHIVE" >/dev/null
STAGE=""
OLD_TARGET=""
SWAPPED=0
on_error() {
  local status=$?
  trap - ERR
  if [[ "$SWAPPED" == "1" ]]; then
    echo "Release failed; reverting web symlink to previous code" >&2
    ln -s "$OLD_TARGET" "$CURRENT.rollback-$SHA" &&
      mv -Tf "$CURRENT.rollback-$SHA" "$CURRENT" || true
    echo "Database migrations are NOT rolled back automatically" >&2
  fi
  if [[ -n "$STAGE" && -d "$STAGE" ]]; then
    rm -rf -- "$STAGE"
  fi
  exit "$status"
}
trap on_error ERR

STAGE="$(mktemp -d "$RELEASES/.stage-$SHA-XXXXXX")"
tar -xzf "$ARCHIVE" -C "$STAGE" --no-same-owner --no-same-permissions

test -f "$STAGE/artisan"
test -f "$STAGE/composer.lock"
test -f "$STAGE/.github/scripts/deploy-release.sh"
rm -rf -- "$STAGE/storage"
ln -s "$SHARED/storage" "$STAGE/storage"
ln -s "$BASE/private/.env" "$STAGE/.env"
ln -s ../storage/app/public "$STAGE/public/storage"

if [[ -f "$STAGE/package.json" ]]; then
  test -f "$STAGE/public/build/manifest.json" || {
    echo "Frontend release has no Vite manifest" >&2; exit 1;
  }
fi

(
  cd "$STAGE"
  composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
  php artisan config:cache
  php artisan route:cache
  # Schema migrations require a separate reviewed operation with DB backup.
  STATUS="$(php artisan migrate:status --no-ansi --no-interaction)"
  if printf '%s\n' "$STATUS" | grep -qi 'Pending'; then
    echo "Pending migrations detected. Review and apply with a verified DB backup first." >&2
    exit 1
  fi
)

# Prepare the immutable release while the previous site remains live.
mv -- "$STAGE" "$TARGET"
STAGE=""
OLD_TARGET="$(readlink "$CURRENT")"
ln -s "$TARGET" "$CURRENT.next-$SHA"
mv -Tf "$CURRENT.next-$SHA" "$CURRENT"
SWAPPED=1

# Check THIS server (not a possibly still-old public DNS endpoint).
LOCAL_IP="$(ip -4 route get 1.1.1.1 | sed -nE 's/.* src ([0-9.]+).*/\1/p' | head -n 1)"
[[ -n "$LOCAL_IP" ]] || { echo "Could not determine local Hestia IPv4" >&2; exit 1; }
curl --fail --silent --show-error --max-time 20 \
  --resolve "mohammedzomlot.dev:443:$LOCAL_IP" \
  "https://mohammedzomlot.dev/up" >/dev/null

(
  cd "$TARGET"
  php artisan queue:restart
)
SWAPPED=0
trap - ERR
rm -f -- "$ARCHIVE"
echo "Deployment successful: $SHA"
echo "Previous symlink target retained for rollback: $OLD_TARGET"
