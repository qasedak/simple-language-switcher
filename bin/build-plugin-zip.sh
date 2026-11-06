#!/usr/bin/env bash
# Build an installable WordPress plugin zip.
#
# WordPress copies a single top-level directory from the archive into
# wp-content/plugins/. The zip must look like:
#   simple-language-switcher/simple-language-switcher.php
# and not like plugin files sitting at the archive root.

set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT"

SLUG="simple-language-switcher"
OUT_DIR="$ROOT/build"
OUT_ZIP="$OUT_DIR/$SLUG.zip"
STAGE="$(mktemp -d "${TMPDIR:-/tmp}/$SLUG.XXXXXX")"

cleanup() {
	rm -rf "$STAGE"
}
trap cleanup EXIT

if ! command -v zip >/dev/null 2>&1; then
	echo "zip is required to build the plugin archive." >&2
	exit 1
fi

if ! command -v rsync >/dev/null 2>&1; then
	echo "rsync is required to stage the plugin archive." >&2
	exit 1
fi

if [[ ! -x "$ROOT/node_modules/.bin/wp-scripts" ]]; then
	npm ci
fi

npm run build

mkdir -p "$STAGE/$SLUG" "$OUT_DIR"

# Paths starting with / are anchored to the repo root.
# "build/" here is only the zip output directory, not blocks/*/build.
rsync -a \
	--exclude '/.git/' \
	--exclude '/.github/' \
	--exclude '/.gitignore' \
	--exclude '/.gitattributes' \
	--exclude '/node_modules/' \
	--exclude '/package.json' \
	--exclude '/package-lock.json' \
	--exclude '/README.md' \
	--exclude '/build/' \
	--exclude '/bin/' \
	--exclude '.DS_Store' \
	--exclude '.idea/' \
	--exclude '.vscode/' \
	--exclude '*.log' \
	"$ROOT/" "$STAGE/$SLUG/"

rm -f "$OUT_ZIP"
(
	cd "$STAGE"
	zip -X -r "$OUT_ZIP" "$SLUG"
)

if ! unzip -Z1 "$OUT_ZIP" | grep -qx "${SLUG}/${SLUG}.php"; then
	echo "Zip is missing ${SLUG}/${SLUG}.php" >&2
	exit 1
fi

if unzip -Z1 "$OUT_ZIP" | awk -v prefix="${SLUG}/" 'index($0, prefix) != 1 { found = 1 } END { exit !found }'; then
	echo "Zip contains entries outside ${SLUG}/" >&2
	unzip -Z1 "$OUT_ZIP" | awk -v prefix="${SLUG}/" 'index($0, prefix) != 1 { print }' >&2
	exit 1
fi

if unzip -Z1 "$OUT_ZIP" | grep -E '(^|/)(package\.json|package-lock\.json|node_modules/|\.github/)' >/dev/null; then
	echo "Zip contains development files." >&2
	unzip -Z1 "$OUT_ZIP" | grep -E 'package\.json|package-lock\.json|node_modules/|\.github/' >&2
	exit 1
fi

echo "Created $OUT_ZIP"
