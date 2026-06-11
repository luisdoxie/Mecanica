<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\OrdenApiController;
use App\Http\Controllers\Api\DashboardApiController;
use App\Http\Controllers\Api\ClienteApiController;
use App\Http\Controllers\Api\VehiculoApiController;
use App\Http\Controllers\Api\EmpleadoApiController;
use App\Http\Controllers\Api\CobroApiController;
use App\Http\Controllers\Api\GastoApiController;
use App\Http\Controllers\Api\ComisionApiController;
use App\Http\Controllers\Api\BitacoraApiController;
use App\Http\Controllers\Api\UsuarioApiController;
use App\Http\Controllers\Api\CatalogoApiController;
use Illuminate\Support\Facades\Route;

// Login público (máx 5 intentos por minuto por IP)
Route::post('/login', [AuthApiController::class, 'login'])->middleware('throttle:5,1');

// Rutas protegidas con Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me',      [AuthApiController::class, 'me']);

    // Órdenes
    Route::get('/ordenes',                                       [OrdenApiController::class, 'index']);
    Route::post('/ordenes',                                      [OrdenApiController::class, 'store']);
    Route::get('/ordenes/disponibles',                           [OrdenApiController::class, 'disponibles']);
    Route::get('/ordenes/en-taller',                             [OrdenApiController::class, 'enTaller']);
    Route::get('/ordenes/{orden}',                               [OrdenApiController::class, 'show']);
    Route::delete('/ordenes/{orden}',                            [OrdenApiController::class, 'destroy']);
    Route::post('/ordenes/{orden}/estado',                       [OrdenApiController::class, 'cambiarEstado']);
    Route::post('/ordenes/{orden}/asignar',                      [OrdenApiController::class, 'asignar']);
    Route::post('/ordenes/{orden}/servicios',                    [OrdenApiController::class, 'agregarServicio']);
    Route::delete('/ordenes/{orden}/servicios',                  [OrdenApiController::class, 'quitarServicio']);
    Route::post('/ordenes/{orden}/repuestos',                    [OrdenApiController::class, 'agregarRepuesto']);
    Route::delete('/ordenes/{orden}/repuestos/{repuesto}',       [OrdenApiController::class, 'eliminarRepuesto']);
    Route::post('/ordenes/{orden}/fotos',                        [OrdenApiController::class, 'subirFoto']);

    // Dashboards
    Route::get('/dashboard/gerente',         [DashboardApiController::class, 'gerente']);
    Route::get('/dashboard/mecanico',        [DashboardApiController::class, 'mecanico']);
    Route::get('/dashboard/reporte-semanal', [DashboardApiController::class, 'reporteSemanal']);

    // Clientes
    Route::get('/clientes',                          [ClienteApiController::class, 'index']);
    Route::post('/clientes',                         [ClienteApiController::class, 'store']);
    Route::get('/clientes/{cliente}',                [ClienteApiController::class, 'show']);
    Route::put('/clientes/{cliente}',                [ClienteApiController::class, 'update']);
    Route::patch('/clientes/{cliente}/toggle-login', [ClienteApiController::class, 'toggleLogin']);

    // Vehículos
    Route::get('/vehiculos',              [VehiculoApiController::class, 'index']);
    Route::post('/vehiculos',             [VehiculoApiController::class, 'store']);
    Route::get('/vehiculos/{vehiculo}',   [VehiculoApiController::class, 'show']);
    Route::put('/vehiculos/{vehiculo}',   [VehiculoApiController::class, 'update']);

    // Empleados
    Route::get('/empleados',                            [EmpleadoApiController::class, 'index']);
    Route::post('/empleados',                           [EmpleadoApiController::class, 'store']);
    Route::get('/empleados/{empleado}',                 [EmpleadoApiController::class, 'show']);
    Route::put('/empleados/{empleado}',                 [EmpleadoApiController::class, 'update']);
    Route::patch('/empleados/{empleado}/desactivar',    [EmpleadoApiController::class, 'desactivar']);
    Route::patch('/empleados/{empleado}/reactivar',     [EmpleadoApiController::class, 'reactivar']);
    Route::delete('/empleados/{empleado}',              [EmpleadoApiController::class, 'destroy']);

    // Cobros
    Route::get('/cobros',                  [CobroApiController::class, 'index']);
    Route::get('/cobros/ordenes-cobrable', [CobroApiController::class, 'ordenesCobrable']);
    Route::post('/cobros',                 [CobroApiController::class, 'store']);
    Route::put('/cobros/{cobro}',          [CobroApiController::class, 'update']);

    // Gastos
    Route::get('/gastos',  [GastoApiController::class, 'index']);
    Route::post('/gastos', [GastoApiController::class, 'store']);

    // Comisiones / Planilla
    Route::get('/comisiones',  [ComisionApiController::class, 'index']);
    Route::post('/comisiones', [ComisionApiController::class, 'store']);

    // Bitácora
    Route::get('/bitacora', [BitacoraApiController::class, 'index']);

    // Usuarios (solo SUPER_ADMIN)
    Route::get('/usuarios', [UsuarioApiController::class, 'index']);

    // Catálogos para formularios
    Route::get('/catalogo/servicios',        [CatalogoApiController::class, 'servicios']);
    Route::get('/catalogo/categorias-gasto', [CatalogoApiController::class, 'categoriasGasto']);
    Route::get('/catalogo/clientes',         [CatalogoApiController::class, 'clientesSelect']);
    Route::get('/catalogo/vehiculos',        [CatalogoApiController::class, 'vehiculosSelect']);
    Route::get('/catalogo/empleados',        [CatalogoApiController::class, 'empleadosSelect']);
});
