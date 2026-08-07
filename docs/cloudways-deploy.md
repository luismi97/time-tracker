# Deploy en Cloudways (guia completa)

Esta guia aplica a este proyecto PHP + MariaDB sin framework.
En Cloudways NO se usa docker-compose: se despliega codigo PHP y se conecta a la BD administrada.

## 1) Crear servidor y app

1. En Cloudways, crear un servidor nuevo.
2. Crear una aplicacion tipo **PHP** (Custom PHP App).
3. Guardar estos datos del panel:
   - Application URL
   - Credenciales SSH/SFTP
   - DB name, DB user, DB password, DB host

## 2) Desplegar codigo

Opcion recomendada (Git Deploy):

1. Subir este repo a GitHub/GitLab/Bitbucket.
2. En Cloudways > Deployment Via Git, conectar repo y rama.
3. Hacer deploy inicial.

Opcion alternativa:

- Subir archivos por SFTP al directorio de la aplicacion.

## 3) Configurar webroot a /public

El front controller esta en `public/index.php`.

1. En Application Settings, definir el webroot/public folder para que apunte a `public`.
2. Verificar que `public/.htaccess` existe y este activo.

Si el panel no permite apuntar a `public`, mover el contenido de `public/` a `public_html/` y ajustar rutas internas de `index.php`.

## 4) Variables de entorno

1. Entrar por SSH a la raiz del proyecto.
2. Crear `.env` desde `.env.cloudways.example`.
3. Completar valores reales de app y BD.

Variables minimas:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://tu-dominio.com`
- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

## 5) Dependencias y permisos

Desde la raiz del proyecto en el servidor:

```bash
bash scripts/cloudways-deploy.sh
```

El script:

- Ejecuta `composer install --no-dev --optimize-autoloader`
- Crea carpetas necesarias
- Ajusta permisos de escritura para `storage` y uploads

## 6) Migraciones SQL (schema + seed)

Como Cloudways no ejecuta `docker-entrypoint-initdb.d`, debes importar SQL manualmente.

### Opcion A (script automatizado)

1. Exportar variables DB en la sesion SSH.
2. Ejecutar:

```bash
RUN_MIGRATIONS=1 \
DB_HOST="127.0.0.1" \
DB_PORT="3306" \
DB_DATABASE="tu_db" \
DB_USERNAME="tu_user" \
DB_PASSWORD="tu_pass" \
bash scripts/cloudways-deploy.sh
```

### Opcion B (manual)

Importar en orden los archivos `database/migrations/*.sql` usando DB Manager o CLI mysql.

## 7) Dominio y SSL

1. En Domain Management, registrar dominio principal.
2. Apuntar DNS (A record) al servidor Cloudways.
3. Activar SSL Let\'s Encrypt.
4. Forzar HTTPS desde Cloudways.

## 8) Verificacion post-deploy

Probar en este orden:

1. Login admin y empleado.
2. CRUD de empleados.
3. Clock-in/clock-out.
4. Reportes: vista previa + descarga PDF.
5. Configuracion: logo, modo kiosko/login, horario.
6. Kiosko publico en `/kiosk`.
7. Responsive y modo oscuro en movil.

## 9) Actualizaciones futuras

En cada nuevo release:

1. Deploy de codigo (Git).
2. `bash scripts/cloudways-deploy.sh`
3. Si hay nuevos `.sql`, importarlos en orden.
4. Smoke test rapido (login + PDF + kiosko).

## 10) Errores comunes

- 404 en rutas: webroot incorrecto o rewrite desactivado.
- Error DB: credenciales/host incorrectos en `.env`.
- PDF no descarga: revisar logs PHP si hay output previo a headers.
- Assets no cargan: recompilar CSS en local y redeploy de `public/assets/css/app.css`.
