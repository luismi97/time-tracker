#!/bin/bash
set -e

# El codigo fuente llega por bind-mount, asi que vendor/ no existe hasta el primer
# arranque. Se instala aqui para que "docker compose up -d --build" sea autosuficiente.
if [ ! -d vendor ]; then
    composer install --no-dev --optimize-autoloader --no-interaction
fi

mkdir -p storage/logs
chmod -R 775 storage

exec "$@"
