#!/usr/bin/env bash
# Builds the immutable release archive for one exact Git commit.
#
# Usage: build-release.sh <40-char-sha> <output-dir>
#
# Runs in GitHub Actions (and can be run locally for rehearsals). It needs git,
# php + composer and, when package.json exists, node + npm.
#
# The archive contains:
#   - the tracked source of <sha> (git archive, so no untracked/ignored files)
#   - vendor/ installed with --no-dev from composer.lock
#   - public/build from `npm run build` when the commit has a Vite frontend
#   - .release-manifest identifying the commit
# The server never runs composer/npm install, so what runs in production is
# exactly what this script produced from the tested commit.
set -Eeuo pipefail

SHA="${1:-}"
OUT_DIR="${2:-}"
[[ "$SHA" =~ ^[0-9a-f]{40}$ ]] || { echo "Usage: $0 <40-char-sha> <output-dir>" >&2; exit 2; }
[[ -n "$OUT_DIR" ]] || { echo "Usage: $0 <40-char-sha> <output-dir>" >&2; exit 2; }

REPO_ROOT="$(git rev-parse --show-toplevel)"
[[ "$(git -C "$REPO_ROOT" rev-parse "$SHA^{commit}")" == "$SHA" ]] || {
  echo "Commit $SHA is not available in this checkout" >&2; exit 2;
}

mkdir -p "$OUT_DIR"
OUT_DIR="$(cd "$OUT_DIR" && pwd)"
WORK="$(mktemp -d)"
trap 'rm -rf -- "$WORK"' EXIT
SRC="$WORK/release"
mkdir -p "$SRC"

# 1. Exact tracked source of the commit.
git -C "$REPO_ROOT" archive --format=tar "$SHA" | tar -xf - -C "$SRC"
# .gitattributes marks /.github as export-ignore, but the server needs this
# commit's activation script (it is also what `rollback` runs later) and the
# in-process smoke test it runs before the switch.
mkdir -p "$SRC/.github/scripts"
for f in deploy-release.sh smoke-release.php; do
  git -C "$REPO_ROOT" show "$SHA:.github/scripts/$f" > "$SRC/.github/scripts/$f"
done

# Secrets must never be packaged, even if one was committed by mistake. The
# server links .env and storage/ from shared/ into every release.
SECRETS="$(cd "$SRC" && find . \( -name '.env' -o -name '.env.*' ! -name '.env.example' -o -name 'auth.json' \
  -o -path './storage/*.key' -o -name '*.pem' -o -name 'id_rsa*' -o -name 'id_ed25519*' \) -print)"
if [[ -n "$SECRETS" ]]; then
  echo "Refusing to package files that look like secrets:" >&2
  printf '%s\n' "$SECRETS" >&2
  exit 1
fi

# Releases may contain only regular files and directories. A tracked symlink
# would be rejected by the server, so fail here with a clear message instead.
if find "$SRC" ! -type f ! -type d -print -quit | grep -q .; then
  echo "The commit tracks symlinks or special files; releases cannot contain them:" >&2
  find "$SRC" ! -type f ! -type d >&2
  exit 1
fi

# 2. Production PHP dependencies. Scripts are skipped because package:discover
#    needs the production environment; the server runs it per release.
(
  cd "$SRC"
  composer install \
    --no-dev --no-interaction --no-progress --prefer-dist \
    --optimize-autoloader --no-scripts
)

# 2b. Filament's compiled CSS/JS/fonts. Locally `composer install` publishes
#     them through the filament:upgrade script, which --no-scripts skips; they
#     are git-ignored, so the release gets them from the locked vendor/ here.
#     The package caches written to boot artisan are removed again: the server
#     rebuilds them per release (package:discover).
if [[ -d "$SRC/vendor/filament/filament" ]]; then
  (
    cd "$SRC"
    php artisan filament:assets --no-interaction --no-ansi
    rm -f bootstrap/cache/packages.php bootstrap/cache/services.php
  )
  for asset in public/js/filament/filament/app.js public/css/filament/filament/app.css; do
    [[ -s "$SRC/$asset" ]] || { echo "filament:assets produced no $asset" >&2; exit 1; }
  done
fi

# 3. Frontend assets, only when the commit has a Vite frontend.
rm -rf -- "$SRC/public/build"
if [[ -f "$SRC/package.json" ]]; then
  [[ -f "$SRC/package-lock.json" ]] || { echo "package.json without package-lock.json" >&2; exit 1; }
  (
    cd "$SRC"
    npm ci --no-audit --no-fund
    npm run build
    rm -rf node_modules
  )
  [[ -f "$SRC/public/build/manifest.json" ]] || { echo "Vite build produced no manifest" >&2; exit 1; }
  # Self-hosted fonts (laravel-vite-plugin/fonts): without this manifest @fonts
  # renders nothing and the site silently falls back to system fonts, which no
  # HTTP health check would notice.
  if grep -qs 'laravel-vite-plugin/fonts' "$SRC"/vite.config.*; then
    [[ -f "$SRC/public/build/fonts-manifest.json" ]] || { echo "Vite build produced no fonts-manifest.json" >&2; exit 1; }
  fi
fi

# vendor/ or node tooling must not have introduced links either.
if find "$SRC" ! -type f ! -type d -print -quit | grep -q .; then
  echo "Build output contains symlinks or special files:" >&2
  find "$SRC" ! -type f ! -type d >&2
  exit 1
fi

# 4. Release identity, checked by the server before activation.
{
  echo "sha=$SHA"
  echo "built_at=$(date -u +%Y-%m-%dT%H:%M:%SZ)"
  echo "frontend=$([[ -f "$SRC/public/build/manifest.json" ]] && echo yes || echo no)"
  echo "github_run=${GITHUB_RUN_ID:-local}"
} > "$SRC/.release-manifest"

ARCHIVE="release-$SHA.tar.gz"
# Written under a temporary name and renamed only when complete, so a failed
# or interrupted build never leaves an archive/checksum pair that looks valid.
rm -f -- "$OUT_DIR/$ARCHIVE" "$OUT_DIR/release-$SHA.sha256"
tar --sort=name --owner=0 --group=0 --numeric-owner \
  -czf "$OUT_DIR/.$ARCHIVE.partial" -C "$SRC" .
mv -f -- "$OUT_DIR/.$ARCHIVE.partial" "$OUT_DIR/$ARCHIVE"
(cd "$OUT_DIR" && sha256sum "$ARCHIVE" > "release-$SHA.sha256")

echo "Built $OUT_DIR/$ARCHIVE ($(du -h "$OUT_DIR/$ARCHIVE" | cut -f1))"
