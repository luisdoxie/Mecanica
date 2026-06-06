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

This is a **Laravel 11 + Vue.js** repair shop management system (Taller García). The frontend uses Blade templates with Vue components compiled via Vite.

### Role-Based Access Control

Four roles with completely separate controller namespaces and route groups in `routes/web.php`:

| Role | Namespace | Scope |
|------|-----------|-------|
| `SUPER_ADMIN` | `App\Http\Controllers\Admin\` | System config, user management |
| `GERENTE` | `App\Http\Controllers\Gerente\` | Clients, work orders, finances |
| `MECANICO` | `App\Http\Controllers\Mecanico\` | Repair work, vehicle management |
| `CLIENTE` | `App\Http\Controllers\Cliente\` | View own vehicle status |

Middleware enforces these in `routes/web.php`. API routes live in `routes/api.php` and use Laravel Sanctum token auth.

### Core Domain Models

Work order lifecycle: `RECIBIDO → DIAGNOSTICO → REPARACION → LISTO → ENTREGADO`

Key relationships:
- `OrdenTrabajo` (work order) → has many `OrdenServicio` (services performed), `RepuestoUtilizado` (parts used), `HistorialEstado` (status history), `ImagenOrden` (photos), `PagoOrden` (payments)
- `Vehiculo` → belongs to `Cliente` → belongs to `User`/`Persona`
- `Empleado` → has many `PagoEmpleado` (payroll)

### Services & Cross-Cutting Concerns

- **`app/Services/ActivityLogger.php`** — wraps Spatie Activity Log; call it to audit any user action
- **Cloudinary** — used for vehicle/repair images (`cloudinary-labs/cloudinary-laravel`)
- **PDF generation** — `barryvdh/laravel-dompdf` for receipts/invoices
- **Excel exports** — `maatwebsite/excel` in `app/Exports/`
- **Email** — Resend service via `resend/resend-laravel`, mail classes in `app/Mail/`

### Database

Defaults to SQLite (set in `.env`). Can switch to MySQL by changing `DB_CONNECTION`. Migrations are in `database/migrations/` (23 migrations). Session, cache, and queue all use the database driver by default.

### Environment

Timezone is `America/La_Paz`. Check `.env.example` for all required variables — notable ones: `CLOUDINARY_*`, `RESEND_KEY`, `APP_URL`.
