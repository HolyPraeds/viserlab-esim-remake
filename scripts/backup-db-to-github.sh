#!/bin/bash
# Dump travelsim MySQL and push backups/travelsim.sql to GitHub.
# Cron (every 2 days at 03:00):
#   0 3 */2 * * /var/www/travelsim/scripts/backup-db-to-github.sh >> /var/log/travelsim-db-backup.log 2>&1
set -euo pipefail

REPO="${REPO:-/var/www/travelsim}"
ENV_FILE="$REPO/core/.env"
OUT="$REPO/backups/travelsim.sql"
BRANCH="${BRANCH:-master}"
REMOTE="${REMOTE:-origin}"

if [[ ! -f "$ENV_FILE" ]]; then
  echo "Missing $ENV_FILE" >&2
  exit 1
fi

get_env() {
  local key="$1"
  grep -E "^${key}=" "$ENV_FILE" | tail -n1 | cut -d= -f2- | sed -e 's/\r$//' -e 's/^"//' -e 's/"$//' -e "s/^'//" -e "s/'$//"
}

DB_HOST="$(get_env DB_HOST)"
DB_PORT="$(get_env DB_PORT)"
DB_DATABASE="$(get_env DB_DATABASE)"
DB_USERNAME="$(get_env DB_USERNAME)"
DB_PASSWORD="$(get_env DB_PASSWORD)"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"

if [[ -z "$DB_DATABASE" || -z "$DB_USERNAME" ]]; then
  echo "DB_DATABASE / DB_USERNAME empty in .env" >&2
  exit 1
fi

mkdir -p "$REPO/backups"
cd "$REPO"

git fetch "$REMOTE"
git pull --rebase "$REMOTE" "$BRANCH"

export MYSQL_PWD="$DB_PASSWORD"
mysqldump \
  --host="$DB_HOST" \
  --port="$DB_PORT" \
  --user="$DB_USERNAME" \
  --single-transaction \
  --routines \
  --triggers \
  --default-character-set=utf8mb4 \
  "$DB_DATABASE" > "$OUT"
unset MYSQL_PWD

if git diff --quiet -- "$OUT" && git diff --cached --quiet -- "$OUT"; then
  echo "No DB changes, skip commit"
  exit 0
fi

git add backups/travelsim.sql
git -c user.name="travelsim-backup" -c user.email="backup@travelsim.live" \
  commit -m "DB backup $(date -u +%Y-%m-%dT%H:%MZ)"

git push "$REMOTE" "$BRANCH"
echo "Pushed DB backup"
