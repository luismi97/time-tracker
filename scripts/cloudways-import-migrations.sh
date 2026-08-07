#!/usr/bin/env bash
set -euo pipefail

# Importa todas las migraciones SQL en orden a la BD configurada via variables de entorno.
# Uso:
#   DB_HOST=127.0.0.1 DB_PORT=3306 DB_DATABASE=mydb DB_USERNAME=user DB_PASSWORD=pass \
#   bash scripts/cloudways-import-migrations.sh

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
MIGRATIONS_DIR="${ROOT_DIR}/database/migrations"

: "${DB_HOST:?DB_HOST es requerido}"
: "${DB_PORT:?DB_PORT es requerido}"
: "${DB_DATABASE:?DB_DATABASE es requerido}"
: "${DB_USERNAME:?DB_USERNAME es requerido}"
: "${DB_PASSWORD:?DB_PASSWORD es requerido}"

if ! command -v mysql >/dev/null 2>&1; then
  echo "Error: mysql client no esta instalado o no esta en PATH."
  exit 1
fi

if [ ! -d "${MIGRATIONS_DIR}" ]; then
  echo "Error: no existe el directorio de migraciones: ${MIGRATIONS_DIR}"
  exit 1
fi

echo "Importando migraciones desde: ${MIGRATIONS_DIR}"

shopt -s nullglob
mapfile -t migration_files < <(find "${MIGRATIONS_DIR}" -maxdepth 1 -type f -name '*.sql' | sort)

if [ "${#migration_files[@]}" -eq 0 ]; then
  echo "No se encontraron archivos .sql para importar."
  exit 1
fi

for file in "${migration_files[@]}"; do
  echo " -> Importando $(basename "${file}")"
  mysql \
    -h "${DB_HOST}" \
    -P "${DB_PORT}" \
    -u "${DB_USERNAME}" \
    -p"${DB_PASSWORD}" \
    "${DB_DATABASE}" < "${file}"
done

echo "Migraciones importadas correctamente."
