#!/usr/bin/env bash
set -euo pipefail

# Script de deploy rapido para Cloudways.
# Ejecutar desde la raiz del proyecto dentro del servidor.
#
# Variables opcionales:
#   RUN_MIGRATIONS=1   -> importa database/migrations/*.sql al terminar composer
#
# Variables requeridas si RUN_MIGRATIONS=1:
#   DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "${ROOT_DIR}"

echo "[1/4] Verificando composer"
if ! command -v composer >/dev/null 2>&1; then
  echo "Error: composer no esta disponible en PATH."
  exit 1
fi

echo "[2/4] Instalando dependencias PHP"
composer install --no-dev --optimize-autoloader --no-interaction

echo "[3/4] Verificando carpetas de escritura"
mkdir -p storage/logs storage/sessions public/assets/uploads
chmod -R 775 storage public/assets/uploads || true

echo "[4/4] Deploy base completado"

if [ "${RUN_MIGRATIONS:-0}" = "1" ]; then
  echo "RUN_MIGRATIONS=1 detectado. Importando migraciones..."
  bash scripts/cloudways-import-migrations.sh
fi

echo "Listo. Prueba login, reportes PDF y kiosko para validar el deploy."
