#!/bin/bash
set -e

APP_USER="${USER:-dressnio}"
BACKUP_DIR="/var/backups/${APP_USER}"

LATEST_BACKUP=$(ls -t "$BACKUP_DIR"/pre_deploy_*.sql.gz 2>/dev/null | head -n 1)

if [ -z "$LATEST_BACKUP" ]; then
    echo "[ERROR] No pre-deploy snapshot found in $BACKUP_DIR!"
    exit 1
fi

echo ">>> Rolling back database to latest pre-deploy snapshot: $LATEST_BACKUP"
if docker compose ps db 2>/dev/null | grep -q "Up"; then
    zcat "$LATEST_BACKUP" | docker compose exec -T db mysql -u "${DB_USERNAME:-${APP_USER}}" -p"${DB_PASSWORD}" "${DB_DATABASE:-${APP_USER}}"
else
    zcat "$LATEST_BACKUP" | mysql -u "${DB_USERNAME:-${APP_USER}}" -p"${DB_PASSWORD}" "${DB_DATABASE:-${APP_USER}}"
fi

echo "[SUCCESS] Database state successfully restored to pre-deploy snapshot!"
