#!/usr/bin/env bash
set -euo pipefail

# Edit these values on the staging server.
SITE_DIR="/home/USER/domains/test.dede.ir/public_html"
BRANCH="main"
REMOTE="origin"

cd "$SITE_DIR"

echo "==> DeDe staging deploy started"
echo "==> Site: $SITE_DIR"
echo "==> Branch: $BRANCH"

git fetch "$REMOTE" "$BRANCH"
git reset --hard "$REMOTE/$BRANCH"

# Clear common cache folders if they exist. This is safe for staging.
rm -rf wp-content/cache/* 2>/dev/null || true
rm -rf wp-content/litespeed/* 2>/dev/null || true

# Optional WP-CLI cache flush if available.
if command -v wp >/dev/null 2>&1; then
  wp cache flush --path="$SITE_DIR" || true
fi

echo "==> Current commit:"
git --no-pager log -1 --oneline

echo "==> DeDe staging deploy finished"
