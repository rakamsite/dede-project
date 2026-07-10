#!/usr/bin/env bash

# DeDe cPanel Git Deploy
# Repo: /home/dedeir/repositories/dede
# Live: /home/dedeir/public_html

set -u
IFS=$'\n\t'

REPO_ROOT="/home/dedeir/repositories/dede"
LIVE_ROOT="/home/dedeir/public_html"
BACKUP_BASE="/home/dedeir/dede-deploy-backups"
STATE_DIR="/home/dedeir/dede-deploy-state"
STATE_FILE="${STATE_DIR}/dede.last_commit"
INITIAL_MANIFEST="${REPO_ROOT}/tools/cpanel-initial-deploy-files.txt"

DEPLOY_ID="$(date +%Y%m%d-%H%M%S)"
BACKUP_DIR="${BACKUP_BASE}/${DEPLOY_ID}"
LOG_FILE="${BACKUP_DIR}/deploy.log"

mkdir -p "${BACKUP_DIR}" "${STATE_DIR}"

log() {
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*" | tee -a "${LOG_FILE}"
}

fail() {
  log "ERROR: $*"
  exit 1
}

is_allowed_path() {
  case "$1" in
    wp-content/themes/DeDeTemPlate/*) return 0 ;;
    wp-content/plugins/DeDeV1/*) return 0 ;;
    wp-content/plugins/DeDeV2/*) return 0 ;;
    *) return 1 ;;
  esac
}

is_forbidden_path() {
  case "$1" in
    ""|/*|*..*) return 0 ;;
    README.md|README.*) return 0 ;;
    .cpanel.yml|.git/*|.gitignore) return 0 ;;
    docs/*|tools/*) return 0 ;;
    wp-config.php) return 0 ;;
    wp-admin/*|wp-includes/*) return 0 ;;
    wp-content/uploads/*) return 0 ;;
    node_modules/*|*/node_modules/*) return 0 ;;
    vendor/*|*/vendor/*) return 0 ;;
    cache/*|*/cache/*|logs/*|*/logs/*|tmp/*|*/tmp/*) return 0 ;;
    *.zip|*.rar|*.7z|*.tar|*.gz|*.log) return 0 ;;
    *) return 1 ;;
  esac
}

cd "${REPO_ROOT}" || fail "Cannot cd to ${REPO_ROOT}"
CURRENT_COMMIT="$(git rev-parse HEAD 2>/dev/null)" || fail "Cannot read current Git commit."

log "============================================"
log "DeDe deploy started"
log "Repository: ${REPO_ROOT}"
log "Live root : ${LIVE_ROOT}"
log "Commit    : ${CURRENT_COMMIT}"
log "Backup dir: ${BACKUP_DIR}"
log "============================================"

[ -d "${LIVE_ROOT}" ] || fail "LIVE_ROOT does not exist: ${LIVE_ROOT}"

RAW_LIST="${BACKUP_DIR}/changed-files.raw"
FILTERED_LIST="${BACKUP_DIR}/deploy-files.filtered"
: > "${RAW_LIST}"
: > "${FILTERED_LIST}"

# First deployment bootstrap:
# If no previous deploy state exists, use tools/cpanel-initial-deploy-files.txt.
# After first successful deploy, future deploys use Git diff automatically.
if [ ! -f "${STATE_FILE}" ] && [ -f "${INITIAL_MANIFEST}" ]; then
  log "First deploy detected. Using initial deploy manifest: ${INITIAL_MANIFEST}"
  grep -v '^[[:space:]]*$' "${INITIAL_MANIFEST}" | grep -v '^[[:space:]]*#' > "${RAW_LIST}" || true
else
  LAST_COMMIT=""
  if [ -f "${STATE_FILE}" ]; then
    LAST_COMMIT="$(cat "${STATE_FILE}" 2>/dev/null || true)"
  fi

  if [ -n "${LAST_COMMIT}" ] && git cat-file -e "${LAST_COMMIT}^{commit}" 2>/dev/null; then
    log "Using changed files from ${LAST_COMMIT}..${CURRENT_COMMIT}"
    git diff --name-only --diff-filter=ACMRD "${LAST_COMMIT}..${CURRENT_COMMIT}" > "${RAW_LIST}"
  elif git rev-parse HEAD^ >/dev/null 2>&1; then
    log "No valid previous deploy state. Using latest commit changes: HEAD^..HEAD"
    git diff --name-only --diff-filter=ACMRD HEAD^..HEAD > "${RAW_LIST}"
  else
    log "No previous commit found. Listing all repository files."
    git ls-files > "${RAW_LIST}"
  fi
fi

while IFS= read -r rel; do
  rel="$(echo "$rel" | sed 's#\\#/#g' | sed 's#^\./##')"
  [ -z "$rel" ] && continue

  if is_forbidden_path "$rel"; then
    log "SKIP forbidden: ${rel}"
    continue
  fi

  if ! is_allowed_path "$rel"; then
    log "SKIP not allowed: ${rel}"
    continue
  fi

  echo "$rel" >> "${FILTERED_LIST}"
done < "${RAW_LIST}"

if [ ! -s "${FILTERED_LIST}" ]; then
  log "No allowed files to deploy."
  echo "${CURRENT_COMMIT}" > "${STATE_FILE}"
  log "Deploy state updated: ${STATE_FILE}"
  exit 0
fi

log "Allowed files to deploy:"
while IFS= read -r rel; do
  log " - ${rel}"
done < "${FILTERED_LIST}"

while IFS= read -r rel; do
  SRC="${REPO_ROOT}/${rel}"
  DST="${LIVE_ROOT}/${rel}"
  BAK="${BACKUP_DIR}/${rel}"

  if [ ! -e "${SRC}" ]; then
    log "Git deletion or missing source detected. Not deleting live file: ${rel}"
    continue
  fi

  mkdir -p "$(dirname "${DST}")" "$(dirname "${BAK}")"

  if [ -e "${DST}" ]; then
    cp -p "${DST}" "${BAK}" || fail "Backup failed for ${rel}"
    log "Backed up: ${rel}"
  else
    log "No existing live file to back up: ${rel}"
  fi

  cp -p "${SRC}" "${DST}" || fail "Deploy copy failed for ${rel}"
  log "Deployed: ${rel}"
done < "${FILTERED_LIST}"

echo "${CURRENT_COMMIT}" > "${STATE_FILE}" || fail "Could not update deploy state."
log "Deploy state updated: ${STATE_FILE}"
log "Deploy finished successfully."
exit 0
