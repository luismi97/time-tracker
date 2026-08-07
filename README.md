# Time Tracking & Payroll

Sistema de control de horas y nomina de empleados (Employee Time Tracking & Payroll).
Permite a los empleados registrar su entrada/salida y a los administradores gestionar
empleados, consultar registros de asistencia y generar reportes de pago en PDF.

Construido en **PHP puro** (sin frameworks), **Tailwind CSS**, **JavaScript vanilla**,
**MySQL/MariaDB** y **Docker**, siguiendo la estructura y convenciones del proyecto de
referencia `retailpos` (separacion `app/ / public/ / routes/ / resources/ / database/`,
Docker con PHP-Apache + MySQL + phpMyAdmin, `docker-compose up -d --build`).

---

## Tabla de contenido

- [Tecnologias](#tecnologias)
- [Instalacion](#instalacion)
- [Credenciales iniciales](#credenciales-iniciales)
- [Funcionamiento](#funcionamiento)
- [Estructura del proyecto](#estructura-del-proyecto)
- [Base de datos](#base-de-datos)
- [Reglas de negocio y supuestos](#reglas-de-negocio-y-supuestos)
- [Deploy en Cloudways](#deploy-en-cloudways)
- [Seguridad](#seguridad)

---

## Tecnologias

| Capa | Tecnologia |
|---|---|
| Backend | PHP 8.2 (sin framework, arquitectura MVC ligera propia) |
| Estilos | Tailwind CSS 3 (precompilado a un archivo CSS estatico) |
| Interactividad | JavaScript vanilla (sin librerias) |
| Base de datos | MySQL/MariaDB (PDO + consultas preparadas) |
| PDF | FPDF (libreria pura de generacion de PDF, via Composer) |
| Contenedores | Docker + Docker Compose (PHP-Apache, MariaDB, phpMyAdmin) |

Incluye **modo oscuro** (toggle en la esquina superior de cada pantalla, con icono de
sol/luna), implementado con la variante `dark` de Tailwind (`darkMode: 'class'`) y un
pequeno script vanilla JS que guarda la preferencia en `localStorage` y respeta
`prefers-color-scheme` la primera vez.

La interfaz es **100% responsiva**: el sidebar del administrador se convierte en un
menu deslizable en pantallas pequenas, y todas las tablas (empleados, registros de
asistencia) se muestran como tarjetas apiladas en movil y como tabla completa a partir
de `md` (768px), sin generar scroll horizontal de pagina en ningun tamano de pantalla.

No se usa ningun framework PHP (Laravel, Symfony, etc.). El enrutamiento, la
autenticacion, el acceso a datos y el renderizado de vistas son una capa propia y
minimalista (`app/Core`), pensada para ser simple de leer y mantener.

---

## Instalacion

### Requisitos

- [Docker](https://www.docker.com/) y Docker Compose (incluido en Docker Desktop).
- Ningun otro requisito: PHP, Composer, Node y Tailwind ya estan resueltos dentro de
  Docker o precompilados en el repositorio.

### Pasos

1. Clonar el repositorio.
2. Copiar el archivo de entorno:

   ```bash
   cp .env.example .env
   ```

3. Levantar todo con un unico comando:

   ```bash
   docker compose up -d --build
   ```

   Esto construye la imagen de PHP + Apache, instala las dependencias de Composer
   automaticamente en el primer arranque (ver `entrypoint.sh`), crea la base de datos
   MariaDB y ejecuta **automaticamente** las migraciones y datos semilla ubicadas en
   `database/migrations/` (MySQL/MariaDB ejecuta cualquier `.sql` en
   `/docker-entrypoint-initdb.d` la primera vez que el volumen de datos esta vacio).

4. Abrir la aplicacion:

   - App: <http://localhost:8081>
   - phpMyAdmin: <http://localhost:8083> (servidor `db`, usuario/clave del `.env`)

No se requiere ningun paso manual adicional (no hay que correr `composer install`,
migraciones ni seeders a mano).

> Los puertos por defecto son `8081` (app), `3308` (MySQL) y `8083` (phpMyAdmin) para
> evitar conflictos con otros proyectos que usen los puertos tipicos 8080/3307/8082.
> Pueden cambiarse libremente en `docker-compose.yml`.

### Variables de entorno (`.env`)

| Variable | Descripcion |
|---|---|
| `APP_NAME` | Nombre mostrado en la interfaz. |
| `APP_ENV` | `local` o `production`. |
| `APP_DEBUG` | Muestra errores detallados si es `true`. |
| `APP_URL` | URL publica de la app. |
| `APP_TIMEZONE` | Zona horaria usada para calcular horas trabajadas. |
| `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Conexion a MySQL/MariaDB. |
| `DB_ROOT_PASSWORD` | Contrasena root de MariaDB (solo usada por el contenedor de base de datos). |

### Detener / reiniciar

```bash
docker compose down        # detiene los contenedores (conserva los datos)
docker compose down -v     # detiene y borra tambien la base de datos
```

---

## Credenciales iniciales

El script de semilla (`database/migrations/005_seed_data.sql`) crea dos cuentas de
ejemplo:

| Rol | Correo | Contrasena |
|---|---|---|
| Administrador | `admin@timetracking.test` | `Admin123!` |
| Empleado (demo) | `empleado@timetracking.test` | `Employee123!` |

**Cambiar estas contrasenas antes de usar la aplicacion en un entorno real.**

### Datos de ejemplo (mock data)

Ademas de las dos cuentas anteriores, `database/migrations/006_demo_data.sql` crea 7
empleados adicionales (numeros `002` a `008`) con distintas fechas de ingreso,
salarios por hora y estados (2 de ellos `inactive`, para poder probar los filtros de
estado), junto con un historial de asistencia realista repartido en los ultimos dias,
semanas y meses (incluye jornadas con horas extra y un registro de hace ~200 dias
para poder probar el filtro por ano). Todas las cuentas de empleados de ejemplo usan
la contrasena `Employee123!` (mismo hash que el empleado demo). Correos:
`ana.rodriguez@`, `carlos.jimenez@`, `laura.vargas@`, `jose.mora@`, `sofia.castro@`
(inactivo), `diego.solano@`, `valeria.chinchilla@` (inactivo), todos
`@timetracking.test`.

Cada empleado (incluido el demo) tiene un **numero de empleado** correlativo (`001`,
`002`, ...) asignado automaticamente al crearlo (`Employee::nextEmployeeNumber()`);
se muestra en el listado de administracion, en el perfil del empleado y en los PDFs,
y no es editable una vez asignado.

---

## Funcionamiento

### Iniciar sesion

Ir a `/login`, ingresar correo y contrasena. Segun el rol del usuario se redirige a
`/admin/dashboard` o `/employee/dashboard`. Las cuentas inactivas no pueden iniciar
sesion aunque la contrasena sea correcta.

### Crear empleados (administrador)

`Empleados` &rarr; `+ Nuevo empleado`. Al crear un empleado se genera automaticamente
su cuenta de usuario (rol `employee`) con el correo y contrasena indicados; con esas
credenciales el empleado ya puede iniciar sesion. Desde la misma pantalla se puede
editar, activar/desactivar o eliminar un empleado (con confirmacion previa).

### Registrar entradas y salidas (empleado)

`Mis registros` muestra el estado actual (jornada abierta o cerrada) y dos botones:
**Registrar entrada** y **Registrar salida**. Las reglas de secuencia se validan en el
servidor:

- No se puede marcar entrada si ya hay una jornada abierta.
- No se puede marcar salida si no hay una entrada previa.
- Al marcar la salida se calculan automaticamente las horas trabajadas y las horas
  extra del dia.

Si el administrador activo el modo kiosco (ver Configuracion), esta pantalla no
muestra los botones y en su lugar enlaza a `/kiosk`.

### Kiosco de asistencia con codigo de empleado

Cuando `Configuracion > Registro de horas` esta en modo "kiosco", cualquier persona
puede abrir `/kiosk` (sin iniciar sesion), escribir su numero de empleado (`001`,
`002`, ...) y marcar entrada o salida. Al marcar la salida se muestra un mensaje con
la hora de entrada y el total de horas trabajadas en esa jornada.

### Generar reportes y PDFs (administrador)

`Reportes` permite elegir un empleado (o todos) y un periodo (dia, semana, mes o rango
personalizado). El boton **Vista previa** muestra en pantalla una tabla con horas
trabajadas, horas extra, horas pagadas y total a pagar por empleado (respetando las
reglas de horas extra/almuerzo de cada uno) antes de exportar. El boton **Generar
PDF** descarga un PDF con la informacion del empleado, el detalle de entradas y
salidas, y los mismos totales (`Content-Disposition: attachment`).

`Registros` (menu de administrador) permite consultar y filtrar todos los registros de
asistencia por empleado, dia, semana, mes, ano o rango personalizado, con paginacion.

### Configuracion (administrador)

`Configuracion` centraliza los ajustes globales:

- **General**: nombre del sitio (se muestra en el sidebar, login y titulos) y logo
  (PNG/JPG/WEBP/SVG, maximo 2MB).
- **Registro de horas**: alterna entre inicio de sesion o kiosco con codigo de
  empleado.
- **Horario de atencion**: casillas rapidas "Abierto 24 horas" y "Abierto los 7 dias"
  (mismo horario), o desmarcar ambas para configurar manualmente cada dia (abierto/
  cerrado y horas de apertura/cierre).

---

## Estructura del proyecto

```
app/
  Controllers/       Controladores HTTP (Auth, Admin, Employee)
  Core/               Nucleo: Router, Database (PDO), Auth, Session, Csrf, Validator, Paginator
  Middleware/         Autenticacion, control de roles (auth/admin/employee/guest)
  Models/             Acceso a datos con consultas preparadas (User, Employee, AttendanceRecord, Role)
  Services/           Logica de negocio (AttendanceService, PayrollService, PdfReportService, EmployeeService)
  Support/            Utilidades (DateRange: resuelve dia/semana/mes/anio/rango)
  Helpers/            Funciones globales de vista (e, view, old, csrf_field, formatos, etc.)
bootstrap/
  app.php             Arranque: autoload, entorno, sesion, CSRF, router
config/
  app.php, database.php
database/
  migrations/         Scripts SQL versionados (tablas + datos semilla)
public/
  index.php           Front controller unico
  assets/             CSS compilado y JS vanilla
resources/
  views/              Vistas PHP planas (layouts, admin, employee, auth, errores)
routes/
  web.php             Definicion de rutas y middleware
docker/
  php/, apache/        Configuracion de PHP y del VirtualHost de Apache
```

### Arquitectura

- **Front controller unico** (`public/index.php`): todas las peticiones pasan por un
  `Router` propio que resuelve rutas, aplica middleware (`auth`, `guest`, `admin`,
  `employee`) y despacha al controlador correspondiente.
- **Controladores delgados**: reciben la peticion, delegan la logica de negocio a
  `Services` y el acceso a datos a `Models`, y renderizan una vista.
- **Vistas PHP planas** con un sistema simple de layouts (captura de contenido con
  buffers + inclusion del layout), sin motor de plantillas externo.
- **DocumentRoot** de Apache apunta unicamente a `public/`; el resto de carpetas
  (`app`, `config`, `database`, `resources`, `routes`, `storage`) nunca son accesibles
  por HTTP.

---

## Base de datos

Modelo normalizado con llaves foraneas e indices (ver `database/migrations/`):

- **`roles`**: catalogo de roles (`admin`, `employee`).
- **`users`**: credenciales de acceso (correo, hash de contrasena, estado activo,
  ultimo acceso) enlazadas a `roles`.
- **`employees`**: datos personales y laborales del empleado (numero de empleado
  correlativo unico, nombre, telefono, direccion, documento opcional, fecha de
  ingreso, salario por hora, `overtime_paid`, `has_lunch_break`, estado
  activo/inactivo), enlazados 1 a 1 con `users`.
- **`attendance_records`**: cada jornada de un empleado (fecha, hora de entrada, hora
  de salida, horas trabajadas, horas extra, estado abierto/cerrado), enlazada a
  `employees`.
- **`settings`**: fila unica con la configuracion global (nombre del sitio, logo,
  modo de registro de horas, horario del negocio).
- **`business_hours`**: horario manual por dia de la semana (0=Domingo...6=Sabado),
  usado cuando el horario no es "24 horas" ni "mismo horario todos los dias".

Relaciones e integridad:

- `users.role_id -> roles.id` (`ON DELETE RESTRICT`)
- `employees.user_id -> users.id` (`ON DELETE CASCADE`, unico por usuario)
- `attendance_records.employee_id -> employees.id` (`ON DELETE CASCADE`)
- Indices en `employees.status`, `employees.full_name`,
  `attendance_records(employee_id, work_date)`, `attendance_records.status` y
  `attendance_records.work_date` para acelerar filtros y reportes.

Al eliminar un empleado desde el panel se elimina su usuario, lo que en cascada borra
el registro de empleado y todo su historial de asistencia.

---

## Reglas de negocio y supuestos

Decisiones tomadas para que el sistema quede completo y consistente:

- **Calculo de horas**: se realiza al momento de registrar la salida
  (`clock-out`), tomando la diferencia entre la hora de entrada y de salida. Una
  jornada abierta no suma horas hasta que se cierra.
- **Horas extra por empleado**: cualquier hora que exceda las 8 horas dentro de una
  misma jornada se marca como `overtime_hours` (informativo, visible para todos). El
  **pago** de esas horas depende de un ajuste por empleado (`overtime_paid`,
  editable en `Empleados > Editar`, **NO por defecto**):
  - `overtime_paid = No` (por defecto): las horas extra se pagan igual que las
    horas normales (`Total = Horas Trabajadas x Salario por Hora`).
  - `overtime_paid = Si`: las horas extra se pagan a 1.5x el salario por hora.
- **Hora de almuerzo por empleado**: si el empleado tiene activado `has_lunch_break`
  (editable en `Empleados > Editar`, **NO por defecto**), se descuenta 1 hora de las
  horas regulares de cada jornada trabajada **solo para efectos de pago** (las horas
  "trabajadas" que se muestran en pantalla y en el PDF no cambian).
- **Estado del empleado**: activar/desactivar un empleado tambien activa/desactiva su
  cuenta de acceso (`users.is_active`); un empleado inactivo no puede iniciar sesion.
- **Visibilidad de salario**: el empleado nunca ve su salario por hora, sus reglas de
  pago ni las de terceros; esa informacion solo aparece en el panel de administracion
  y en los PDFs.
- **Vista previa de reportes**: antes de exportar el PDF, el boton "Vista previa"
  (`/admin/reports/preview`) muestra una tabla con horas trabajadas, horas extra,
  horas pagadas y total a pagar por empleado, con los mismos filtros que el PDF.
- **Reportes por rango**: al generar un PDF para "todos los empleados" se crea una
  seccion (pagina) por empleado con su propio detalle y total.
- **Registro de horas: login vs. kiosco** (`Configuracion > Registro de horas`): el
  administrador elige si los empleados marcan su horario iniciando sesion (por
  defecto) o escribiendo su numero de empleado en `/kiosk` (pantalla publica, sin
  iniciar sesion, pensada para un dispositivo compartido). Al activar el modo kiosco:
  - `/employee/attendance` deja de mostrar los botones de marcar y en su lugar enlaza
    a `/kiosk`; el backend tambien rechaza `clock-in`/`clock-out` autenticados.
  - `/kiosk` deja de estar disponible (redirige a `/login`) si el modo es "login".
  - Al marcar salida en el kiosco se muestra un mensaje con la hora de entrada y las
    horas trabajadas de esa jornada.
- **Nombre y logo personalizables** (`Configuracion > General`): el nombre mostrado
  en el sidebar, login y titulos de pagina, y el logo (PNG/JPG/WEBP/SVG, maximo 2MB)
  se guardan en la tabla `settings` y se sirven desde `public/assets/uploads/`.
- **Horario del negocio** (`Configuracion > Horario de atencion`): permite marcar
  "Abierto 24 horas" o "Abierto los 7 dias" (mismo horario para todos los dias) para
  evitar configurar dia por dia; si ninguna de las dos esta marcada, se habilita el
  horario **manual** por dia (`business_hours`, con casilla de abierto/cerrado y hora
  de apertura/cierre por dia). Es informativo por ahora: no bloquea el registro de
  horas, solo se usa para mostrar el horario configurado.
- **Sesiones**: se guardan en el almacenamiento temporal propio del contenedor (no en
  el volumen de codigo montado) para evitar problemas de permisos de archivos entre
  distintos sistemas operativos host; se regeneran automaticamente si el contenedor se
  reinicia.

---

  ## Deploy en Cloudways

  El proyecto ya incluye archivos para acelerar el deploy en Cloudways (entorno sin
  Docker):

  - Plantilla de entorno para produccion: `.env.cloudways.example`
  - Script de deploy base (composer + permisos): `scripts/cloudways-deploy.sh`
  - Script de importacion SQL (migraciones): `scripts/cloudways-import-migrations.sh`
  - Guia paso a paso completa: `docs/cloudways-deploy.md`

  ### Flujo rapido

  1. Subir repo por **Git Deploy** en Cloudways.
  2. Configurar webroot para apuntar a `public/`.
  3. Crear `.env` en servidor usando `.env.cloudways.example`.
  4. Ejecutar en SSH:

    ```bash
    bash scripts/cloudways-deploy.sh
    ```

  5. Importar migraciones SQL (automatico en un solo comando):

    ```bash
    RUN_MIGRATIONS=1 \
    DB_HOST="127.0.0.1" \
    DB_PORT="3306" \
    DB_DATABASE="tu_db" \
    DB_USERNAME="tu_user" \
    DB_PASSWORD="tu_pass" \
    bash scripts/cloudways-deploy.sh
    ```

  > Importante: en local Docker las migraciones corren solas via
  > `/docker-entrypoint-initdb.d`. En Cloudways deben importarse manualmente (o con el
  > script).

  ---

## Seguridad

- **Contrasenas**: hash con `password_hash` (bcrypt).
- **CSRF**: todo formulario incluye un token de sesion verificado en el servidor antes
  de procesar cualquier `POST` (`app/Core/Csrf.php`).
- **XSS**: toda salida dinamica en las vistas pasa por `e()` (`htmlspecialchars`).
- **SQL Injection**: todo acceso a datos usa PDO con consultas preparadas
  (`PDO::ATTR_EMULATE_PREPARES => false`).
- **Sesiones**: cookies `HttpOnly`, `SameSite=Lax`, regeneracion de ID en cada login.
- **Control de acceso por rol**: middleware `auth`/`admin`/`employee` valida en el
  servidor en cada peticion (no solo se oculta en la interfaz); un empleado que
  intente acceder a una URL de administrador recibe `403` aunque conozca la ruta.
- **Acceso directo a archivos**: Apache sirve unicamente `public/`; el resto del
  codigo (incluido `.env`, `database/`, `app/`) no es alcanzable por HTTP.
