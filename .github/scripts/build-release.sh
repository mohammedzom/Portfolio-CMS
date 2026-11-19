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
# commit's activation script (it is also what `rollback` runs later).
mkdir -p "$SRC/.github/scripts"
git -C "$REPO_ROOT" show "$SHA:.github/scripts/deploy-release.sh" > "$SRC/.github/scripts/deploy-release.sh"

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
tar --sort=name --owner=0 --group=0 --numeric-owner \
  -czf "$OUT_DIR/$ARCHIVE" -C "$SRC" .
(cd "$OUT_DIR" && sha256sum "$ARCHIVE" > "release-$SHA.sha256")

echo "Built $OUT_DIR/$ARCHIVE ($(du -h "$OUT_DIR/$ARCHIVE" | cut -f1))"
