#!/usr/bin/env bash

# DeDe cPanel Git Deploy
# Repo: /home/dedeir/repositories/dede
# Live: /home/dedeir/public_html

set -u
set -o pipefail
IFS=$'\n\t'

REPO_ROOT="/home/dedeir/repositories/dede"
LIVE_ROOT="/home/dedeir/public_html"
BACKUP_BASE="/home/dedeir/dede-deploy-backups"
STATE_DIR="/home/dedeir/dede-deploy-state"
STATE_FILE="${STATE_DIR}/dede.last_commit"
INITIAL_MANIFEST="${REPO_ROOT}/tools/cpanel-initial-deploy-files.txt"
ALLOWLIST_FILE="${REPO_ROOT}/tools/cpanel-deploy-allowlist.txt"

DEPLOY_ID="$(date +%Y%m%d-%H%M%S)-$$"
BACKUP_DIR="${BACKUP_BASE}/${DEPLOY_ID}"
LOG_FILE="${BACKUP_DIR}/deploy.log"
APPLIED_LOG="${BACKUP_DIR}/applied-files.tsv"
DEPLOY_ACTIVE=0
ROLLBACK_IN_PROGRESS=0

mkdir -p "${BACKUP_DIR}" "${STATE_DIR}" || {
  echo "Could not create deployment directories." >&2
  exit 1
}
: > "${APPLIED_LOG}" || {
  echo "Could not create deployment state log." >&2
  exit 1
}

log() {
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*" | tee -a "${LOG_FILE}"
}

rollback_applied_files() {
  [ "${DEPLOY_ACTIVE}" -eq 1 ] || return 0
  [ "${ROLLBACK_IN_PROGRESS}" -eq 0 ] || return 0
  [ -s "${APPLIED_LOG}" ] || return 0

  ROLLBACK_IN_PROGRESS=1
  log "Deployment failed. Restoring files changed during this deployment."

  tac "${APPLIED_LOG}" | while IFS=$'\t' read -r rel; do
    [ -n "${rel}" ] || continue
    DST="${LIVE_ROOT}/${rel}"
    BAK="${BACKUP_DIR}/${rel}"

    if [ -e "${BAK}" ]; then
      mkdir -p "$(dirname "${DST}")"
      if cp -p "${BAK}" "${DST}"; then
        log "Rollback restored: ${rel}"
      else
        log "ROLLBACK ERROR: Could not restore ${rel}"
      fi
    else
      if rm -f "${DST}"; then
        log "Rollback removed newly deployed file: ${rel}"
      else
        log "ROLLBACK ERROR: Could not remove ${rel}"
      fi
    fi
  done

  DEPLOY_ACTIVE=0
  ROLLBACK_IN_PROGRESS=0
  log "Automatic rollback attempt finished."
}

fail() {
  log "ERROR: $*"
  rollback_applied_files
  exit 1
}

write_deploy_state() {
  local state_tmp="${STATE_FILE}.tmp.$$"
  printf '%s\n' "${CURRENT_COMMIT}" > "${state_tmp}" || fail "Could not write deploy state."
  mv -f "${state_tmp}" "${STATE_FILE}" || fail "Could not update deploy state."
}

is_forbidden_path() {
  case "$1" in
    ""|/*|*..*) return 0 ;;
    README.md|README.*) return 0 ;;
    .cpanel.yml|.github/*|.git/*|.gitignore) return 0 ;;
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

load_allowed_roots() {
  [ -f "${ALLOWLIST_FILE}" ] || fail "Allowlist not found: ${ALLOWLIST_FILE}"

  ALLOWED_ROOTS=()
  while IFS= read -r line || [ -n "$line" ]; do
    line="${line%$'\r'}"
    line="${line#"${line%%[![:space:]]*}"}"
    line="${line%"${line##*[![:space:]]}"}"

    [ -z "${line}" ] && continue
    case "${line}" in
      \#*) continue ;;
    esac

    case "${line}" in
      wp-content/themes/*|wp-content/plugins/*) ;;
      *) fail "Invalid allowlist entry: ${line}" ;;
    esac

    case "${line}" in
      *".."*|"*"*|"?"*|"["*) fail "Invalid allowlist entry: ${line}" ;;
    esac

    case "${line}" in
      */) ;;
      *) fail "Invalid allowlist entry: ${line}" ;;
    esac

    case "${line}" in
      wp-content/themes/|wp-content/plugins/)
        fail "Invalid allowlist entry: ${line}"
        ;;
    esac

    component="${line%/}"
    component="${component#wp-content/themes/}"
    component="${component#wp-content/plugins/}"
    case "${component}" in
      ""|*/*) fail "Invalid allowlist entry: ${line}" ;;
    esac

    ALLOWED_ROOTS+=("${line}")
  done < "${ALLOWLIST_FILE}"

  [ "${#ALLOWED_ROOTS[@]}" -gt 0 ] || fail "Allowlist is empty: ${ALLOWLIST_FILE}"
}

is_allowed_path() {
  local rel="$1"
  local root
  for root in "${ALLOWED_ROOTS[@]}"; do
    case "${rel}" in
      "${root}"*) return 0 ;;
    esac
  done
  return 1
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
load_allowed_roots

RAW_LIST="${BACKUP_DIR}/changed-files.raw.tsv"
FILTERED_LIST="${BACKUP_DIR}/deploy-actions.filtered.tsv"
: > "${RAW_LIST}"
: > "${FILTERED_LIST}"

# First deployment bootstrap:
# If no previous deploy state exists, use tools/cpanel-initial-deploy-files.txt.
# After first successful deploy, future deploys use Git diff automatically.
if [ ! -f "${STATE_FILE}" ] && [ -f "${INITIAL_MANIFEST}" ]; then
  log "First deploy detected. Using initial deploy manifest: ${INITIAL_MANIFEST}"
  while IFS= read -r rel || [ -n "${rel}" ]; do
    rel="${rel%$'\r'}"
    [ -z "${rel}" ] && continue
    case "${rel}" in
      \#*) continue ;;
    esac
    printf 'A\t%s\n' "${rel}" >> "${RAW_LIST}"
  done < "${INITIAL_MANIFEST}"
else
  LAST_COMMIT=""
  if [ -f "${STATE_FILE}" ]; then
    LAST_COMMIT="$(cat "${STATE_FILE}" 2>/dev/null || true)"
  fi

  if [ -n "${LAST_COMMIT}" ] && git cat-file -e "${LAST_COMMIT}^{commit}" 2>/dev/null; then
    log "Using changed files from ${LAST_COMMIT}..${CURRENT_COMMIT}"
    git diff --name-status --no-renames --diff-filter=ACMD "${LAST_COMMIT}..${CURRENT_COMMIT}" > "${RAW_LIST}" \
      || fail "Could not calculate Git changes."
  elif git rev-parse HEAD^ >/dev/null 2>&1; then
    log "No valid previous deploy state. Using latest commit changes: HEAD^..HEAD"
    git diff --name-status --no-renames --diff-filter=ACMD HEAD^..HEAD > "${RAW_LIST}" \
      || fail "Could not calculate latest Git changes."
  else
    log "No previous commit found. Listing all repository files."
    git ls-files | while IFS= read -r rel; do
      printf 'A\t%s\n' "${rel}"
    done > "${RAW_LIST}"
  fi
fi

while IFS=$'\t' read -r status rel; do
  status="${status%%[0-9]*}"
  rel="$(echo "${rel:-}" | sed 's#\\#/#g' | sed 's#^\./##')"
  [ -z "${rel}" ] && continue

  case "${status}" in
    A|C|M|D) ;;
    *)
      log "SKIP unsupported Git status ${status}: ${rel}"
      continue
      ;;
  esac

  if is_forbidden_path "${rel}"; then
    log "SKIP forbidden: ${rel}"
    continue
  fi

  if ! is_allowed_path "${rel}"; then
    log "SKIP not allowed: ${rel}"
    continue
  fi

  printf '%s\t%s\n' "${status}" "${rel}" >> "${FILTERED_LIST}"
done < "${RAW_LIST}"

if [ ! -s "${FILTERED_LIST}" ]; then
  log "No allowed files to deploy."
  write_deploy_state
  log "Deploy state updated: ${STATE_FILE}"
  exit 0
fi

log "Allowed deployment actions:"
while IFS=$'\t' read -r status rel; do
  log " - ${status} ${rel}"
done < "${FILTERED_LIST}"

DEPLOY_ACTIVE=1

while IFS=$'\t' read -r status rel; do
  SRC="${REPO_ROOT}/${rel}"
  DST="${LIVE_ROOT}/${rel}"
  BAK="${BACKUP_DIR}/${rel}"

  if [ -e "${DST}" ]; then
    mkdir -p "$(dirname "${BAK}")" || fail "Could not create backup directory for ${rel}"
    cp -p "${DST}" "${BAK}" || fail "Backup failed for ${rel}"
    log "Backed up: ${rel}"
  else
    log "No existing live file to back up: ${rel}"
  fi

  # Record before applying the operation so a partial copy can still be reversed.
  printf '%s\n' "${rel}" >> "${APPLIED_LOG}"

  if [ "${status}" = "D" ]; then
    if [ -e "${DST}" ]; then
      rm -f "${DST}" || fail "Live deletion failed for ${rel}"
      log "Deleted: ${rel}"
    else
      log "Already absent on live: ${rel}"
    fi
    continue
  fi

  [ -e "${SRC}" ] || fail "Source file is missing for ${status} action: ${rel}"
  mkdir -p "$(dirname "${DST}")" || fail "Could not create live directory for ${rel}"
  cp -p "${SRC}" "${DST}" || fail "Deploy copy failed for ${rel}"
  log "Deployed: ${rel}"
done < "${FILTERED_LIST}"

write_deploy_state
DEPLOY_ACTIVE=0
log "Deploy state updated: ${STATE_FILE}"
log "Deploy finished successfully."
exit 0
