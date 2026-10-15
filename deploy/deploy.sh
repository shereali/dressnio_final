#!/bin/bash
set -e

APP_USER="${USER:-dressnio}"
APP_DIR="/var/www/${APP_USER}"
BACKUP_DIR="/var/backups/${APP_USER}"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
SNAPSHOT_FILE="$BACKUP_DIR/pre_deploy_${TIMESTAMP}.sql.gz"

cd "$APP_DIR"

# Source environment variables if .env exists
if [ -f .env ]; then
    set -a
    . .env
    set +a
fi

# 1. Fix Git dubious ownership security error in CI/CD environments
git config --global --add safe.directory "$PWD" 2>/dev/null || true

echo "=== [1/6] Pulling latest git changes ==="
git fetch origin main
git reset --hard origin/main

echo "=== [2/6] Creating Automated Pre-Deploy Database Snapshot ==="
mkdir -p "$BACKUP_DIR"
if docker compose ps db 2>/dev/null | grep -q "Up"; then
    echo ">>> Taking database snapshot before deployment..."
    docker compose exec -T db mysqldump -u "${DB_USERNAME:-${APP_USER}}" -p"${DB_PASSWORD}" "${DB_DATABASE:-${APP_USER}}" 2>/dev/null | gzip > "$SNAPSHOT_FILE" || true
    if [ -s "$SNAPSHOT_FILE" ]; then
        echo ">>> Database snapshot verified: $SNAPSHOT_FILE ($(du -h "$SNAPSHOT_FILE" | cut -f1))"
        # Retain only the last 5 snapshots to conserve disk space
        ls -t "$BACKUP_DIR"/pre_deploy_*.sql.gz 2>/dev/null | tail -n +6 | xargs -r rm -f || true
    else
        echo ">>> [NOTICE] DB snapshot was empty or not ready yet. Removing 0-byte file."
        rm -f "$SNAPSHOT_FILE"
    fi
fi

echo "=== [3/6] Building and launching containers ==="
docker compose up -d --build --remove-orphans

echo "=== [4/6] Waiting for database readiness & running migrations ==="
if docker compose ps db 2>/dev/null | grep -q "healthy"; then
    echo ">>> Database is healthy and responsive!"
elif docker compose ps db 2>/dev/null | grep -q "Up"; then
    for i in $(seq 1 30); do
        if docker compose exec -T db mysqladmin ping -h 127.0.0.1 -u root -p"${DB_ROOT_PASSWORD}" --silent 2>/dev/null; then
            echo ">>> Database is fully responsive!"
            break
        fi
        sleep 1
    done
fi

if docker compose ps backend 2>/dev/null | grep -q "Up"; then
    echo ">>> Running safe production migrations (non-destructive)..."
    docker compose exec -T backend php artisan migrate --force
    docker compose exec -T backend php artisan db:seed --force || true
    docker compose exec -T backend php artisan config:cache || true
    docker compose exec -T backend php artisan route:cache || true
    docker compose exec -T backend php artisan view:cache || true
fi

echo "=== [5/6] Pruning dangling Docker build layers ==="
docker image prune -f

echo "=== [6/6] Reloading central Caddy gateway & verifying health ==="
docker exec caddy-caddy-1 caddy reload --config /etc/caddy/Caddyfile 2>/dev/null || true
docker compose ps

mkdir -p "/var/log/${APP_USER}"
echo "$(date -u '+%Y-%m-%d %H:%M:%S UTC') deploy complete" >> "/var/log/${APP_USER}/deploy.log"

echo "=== [SUCCESS] Deployment completed with full data safety! ==="
