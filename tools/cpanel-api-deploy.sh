#!/usr/bin/env bash

set -Eeuo pipefail
IFS=$'\n\t'

COMMAND="${1:-}"
TARGET_SHA="${COMMAND}"
CPANEL_REPOSITORY_ROOT="${CPANEL_REPOSITORY_ROOT:-/home/dedeir/repositories/dede}"
CPANEL_BRANCH="${CPANEL_BRANCH:-main}"
PRODUCTION_HEALTHCHECK_URL="${PRODUCTION_HEALTHCHECK_URL:-https://dede.ir/}"
POLL_INTERVAL_SECONDS="${POLL_INTERVAL_SECONDS:-5}"
UPDATE_TIMEOUT_SECONDS="${UPDATE_TIMEOUT_SECONDS:-180}"
DEPLOY_TIMEOUT_SECONDS="${DEPLOY_TIMEOUT_SECONDS:-300}"

log() {
  printf '[%s] %s\n' "$(date -u '+%Y-%m-%d %H:%M:%S UTC')" "$*"
}

fail() {
  log "ERROR: $*"
  exit 1
}

for required_var in CPANEL_HOST CPANEL_USER CPANEL_API_TOKEN; do
  [ -n "${!required_var:-}" ] || fail "Required environment variable is missing: ${required_var}"
done

CPANEL_BASE="${CPANEL_HOST%/}"
case "${CPANEL_BASE}" in
  http://*|https://*) ;;
  *) CPANEL_BASE="https://${CPANEL_BASE}" ;;
esac

# Add cPanel's secure port unless the caller already supplied one.
if [[ ! "${CPANEL_BASE}" =~ ^https?://[^/]+:[0-9]+$ ]]; then
  CPANEL_BASE="${CPANEL_BASE}:2083"
fi
API_BASE="${CPANEL_BASE}/execute"
AUTH_HEADER="Authorization: cpanel ${CPANEL_USER}:${CPANEL_API_TOKEN}"
TMP_DIR="$(mktemp -d)"
trap 'rm -rf "${TMP_DIR}"' EXIT

validate_cpanel_response() {
  local response_file="$1"
  python3 - "${response_file}" <<'PY'
import json
import sys

path = sys.argv[1]
try:
    with open(path, "r", encoding="utf-8") as handle:
        payload = json.load(handle)
except Exception as exc:
    print(f"Invalid JSON response from cPanel: {exc}", file=sys.stderr)
    sys.exit(1)

result = payload.get("result") or {}
if result.get("status") != 1:
    errors = result.get("errors") or payload.get("errors") or ["Unknown cPanel API error"]
    messages = result.get("messages") or []
    print("cPanel API request failed:", file=sys.stderr)
    for item in [*errors, *messages]:
        print(f"- {item}", file=sys.stderr)
    sys.exit(1)
PY
}

api_call() {
  local output_file="$1"
  local module="$2"
  local function="$3"
  shift 3

  local http_code
  http_code="$(curl \
    --silent \
    --show-error \
    --location \
    --output "${output_file}" \
    --write-out '%{http_code}' \
    --header "${AUTH_HEADER}" \
    --get \
    "${API_BASE}/${module}/${function}" \
    "$@")" || fail "Could not connect to cPanel API."

  [[ "${http_code}" =~ ^2[0-9][0-9]$ ]] || {
    cat "${output_file}" >&2 || true
    fail "cPanel API returned HTTP ${http_code} for ${module}/${function}."
  }

  validate_cpanel_response "${output_file}" || {
    cat "${output_file}" >&2 || true
    fail "cPanel rejected ${module}/${function}."
  }
}

extract_repository_identifier() {
  local response_file="$1"
  local field_name="$2"
  python3 - "${response_file}" "${CPANEL_REPOSITORY_ROOT}" "${field_name}" <<'PY'
import json
import sys

path, expected_root, field_name = sys.argv[1:4]
with open(path, "r", encoding="utf-8") as handle:
    payload = json.load(handle)

def repositories(value):
    if isinstance(value, dict):
        if value.get("repository_root") == expected_root:
            yield value
        for nested in value.values():
            yield from repositories(nested)
    elif isinstance(value, list):
        for nested in value:
            yield from repositories(nested)

for repo in repositories(payload.get("result", {}).get("data")):
    if field_name == "last_update":
        identifier = (repo.get("last_update") or {}).get("identifier")
    else:
        identifier = ((repo.get("last_deployment") or {}).get("repository_state") or {}).get("identifier")
    if identifier:
        print(identifier)
        break
PY
}

wait_for_identifier() {
  local field_name="$1"
  local expected_sha="$2"
  local timeout_seconds="$3"
  local label="$4"
  local started_at now elapsed response_file observed_sha

  started_at="$(date +%s)"
  response_file="${TMP_DIR}/retrieve-${field_name}.json"

  while true; do
    api_call "${response_file}" VersionControl retrieve \
      --data-urlencode "repository_root=${CPANEL_REPOSITORY_ROOT}"

    observed_sha="$(extract_repository_identifier "${response_file}" "${field_name}")"
    if [ "${observed_sha}" = "${expected_sha}" ]; then
      log "${label} confirmed at ${expected_sha}."
      return 0
    fi

    now="$(date +%s)"
    elapsed=$((now - started_at))
    if [ "${elapsed}" -ge "${timeout_seconds}" ]; then
      fail "Timed out waiting for ${label}. Expected ${expected_sha}, observed ${observed_sha:-none}."
    fi

    log "Waiting for ${label}; current value: ${observed_sha:-none}."
    sleep "${POLL_INTERVAL_SECONDS}"
  done
}

if [ "${COMMAND}" = "--current-deployed-sha" ]; then
  api_call "${TMP_DIR}/retrieve-current.json" VersionControl retrieve \
    --data-urlencode "repository_root=${CPANEL_REPOSITORY_ROOT}"
  extract_repository_identifier "${TMP_DIR}/retrieve-current.json" "last_deployment"
  exit 0
fi

[[ "${TARGET_SHA}" =~ ^[0-9a-fA-F]{40}$ ]] || fail "Target SHA must be a full 40-character Git commit SHA."

log "Requesting cPanel repository update to ${CPANEL_BRANCH} (${TARGET_SHA})."
api_call "${TMP_DIR}/update.json" VersionControl update \
  --data-urlencode "repository_root=${CPANEL_REPOSITORY_ROOT}" \
  --data-urlencode "branch=${CPANEL_BRANCH}"

wait_for_identifier "last_update" "${TARGET_SHA}" "${UPDATE_TIMEOUT_SECONDS}" "repository update"

log "Requesting cPanel deployment for ${TARGET_SHA}."
api_call "${TMP_DIR}/deploy.json" VersionControlDeployment create \
  --data-urlencode "repository_root=${CPANEL_REPOSITORY_ROOT}"

wait_for_identifier "last_deployment" "${TARGET_SHA}" "${DEPLOY_TIMEOUT_SECONDS}" "deployment"

if [ -n "${PRODUCTION_HEALTHCHECK_URL}" ]; then
  log "Running production health check: ${PRODUCTION_HEALTHCHECK_URL}"
  curl \
    --silent \
    --show-error \
    --location \
    --fail \
    --retry 5 \
    --retry-delay 5 \
    --retry-all-errors \
    --connect-timeout 15 \
    --max-time 30 \
    --output /dev/null \
    "${PRODUCTION_HEALTHCHECK_URL}" \
    || fail "Production health check failed."
  log "Production health check passed."
fi

log "Production deployment completed successfully: ${TARGET_SHA}"
