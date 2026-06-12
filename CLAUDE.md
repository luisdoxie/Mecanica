# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Setup
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate

# Development (run both concurrently)
php artisan serve          # Laravel dev server at localhost:8000
npm run dev                # Vite HMR for frontend assets

# Production
npm run build

# Testing
./vendor/bin/phpunit                          # All tests
./vendor/bin/phpunit --testsuite=Feature      # Feature tests only
./vendor/bin/phpunit --testsuite=Unit         # Unit tests only
./vendor/bin/phpunit --filter=TestName        # Single test

# Code formatting
./vendor/bin/pint
```

## Architecture

**Laravel 11 + Blade/Vue** repair shop management system (Taller García). Frontend is primarily Blade templates with Vue components compiled via Vite. API routes (`routes/api.php`) use Laravel Sanctum token auth and mirror most web functionality for mobile/external clients.

### Role-Based Access Control

Four roles with completely separate controller namespaces and route groups in `routes/web.php`. The custom `RoleMiddleware` (`middleware('role:ROLE1,ROLE2,...')`) enforces access — unauthenticated users are redirected to login.

| Role | Route Prefix | Controller Namespace | Scope |
|------|-------------|---------------------|-------|
| `SUPER_ADMIN` | `/admin` | `App\Http\Controllers\Admin\` | Users, audit log, reports, system config |
| `GERENTE` | `/gerente` | `App\Http\Controllers\Gerente\` | Clients, work orders, payments, expenses, payroll, receipts |
| `MECANICO` | `/mecanico` | `App\Http\Controllers\Mecanico\` | Work orders, vehicles, parts, payments |
| `CLIENTE` | `/cliente` | `App\Http\Controllers\Cliente\` | View own vehicle status only |

Vehicles (`/gerente/vehiculos`) are shared between GERENTE, MECANICO, and SUPER_ADMIN. Public routes exist for the home page and vehicle status inquiry by plate (`/estado/{placa}`).

### Core Domain Models

Work order lifecycle: `RECIBIDO → DIAGNOSTICO → REPARACION → LISTO → ENTREGADO`

The `Persona` model is a shared identity base — both `Empleado` and `Cliente` belong to a `Persona`, and `User` also belongs to `Persona`. This means a single person record can be linked to multiple roles.

Key relationships:
- `OrdenTrabajo` → `Vehiculo` (belongs), `Empleado` (belongs), `OrdenServicio` (has-many pivot with `precio_aplicado`/`observaciones`), `RepuestoUtilizado` (has-many), `HistorialEstado` (has-many), `ImagenOrden` (has-many), `PagoOrden` (has-many)
- `Vehiculo` → `Cliente` (belongs) → `Persona` (belongs); `placa` is always uppercased by the model
- `Cliente` → `Persona` (belongs), `vehiculos` (has-many); has `pin_acceso` and `puede_login` flag
- `Empleado` → `Persona` (belongs), `especialidades` (many-to-many), `ordenesTrabajo` (has-many), `PagoEmpleado` (has-many)
- `User` → `Persona` (belongs); `rol` is one of `SUPER_ADMIN|GERENTE|MECANICO|CLIENTE`; has `activo` flag
- `Gasto` → optional `orden_trabajo_id`; links to `CategoriaGasto`
- `ConfigTaller` — key-value store (`clave`, `valor`, `tipo`) for shop-wide settings

### Activity Logging

`app/Services/ActivityLogger.php` is a **custom** logger that writes directly to the `activity_log` table (not Spatie). Call it in controllers after any state-changing operation:

```php
ActivityLogger::log(
    accion: 'crear',
    modulo: 'ordenes',
    registroId: $orden->id,
    datosAnteriores: [],
    datosNuevos: $orden->toArray()
);
```

It automatically captures the authenticated user's ID, role, IP, and user agent. Failures are silently swallowed so logging never blocks the main operation.

### Services & Cross-Cutting Concerns

- **Cloudinary** — vehicle/repair image uploads (`cloudinary-labs/cloudinary-laravel`); `ImagenOrden` stores the Cloudinary URL
- **PDF generation** — `barryvdh/laravel-dompdf` for receipts; templates in `resources/views/pdf/`; note: DomPDF does not support CSS flexbox — use HTML tables in PDF templates
- **Excel exports** — `maatwebsite/excel`; six export classes in `app/Exports/` (bitácora, órdenes, financiero, cobros, servicios, clientes)
- **Email** — Resend service via `resend/resend-laravel`; `VehiculoListoMail` notifies the customer when their vehicle is ready
- **Security headers** — `SecurityHeaders` middleware applies OWASP headers (CSP, X-Frame-Options, HSTS in production) globally

### Database

SQLite by default (configured in `.env`). Session, cache, and queue all use the `database` driver. Switch to MySQL by changing `DB_CONNECTION` in `.env`. Migrations are in `database/migrations/` (24 migrations).

### Environment

Timezone is `America/La_Paz` (set in `.env`/`config/app.php`). Required `.env` variables: `CLOUDINARY_CLOUD_NAME`, `CLOUDINARY_API_KEY`, `CLOUDINARY_API_SECRET`, `RESEND_KEY`, `APP_URL`.
