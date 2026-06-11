<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EstadoVehiculoController;
use App\Http\Controllers\ImagenOrdenController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\BitacoraController;
use App\Http\Controllers\Admin\ReporteController as AdminReporteController;
use App\Http\Controllers\Admin\ConfiguracionController;
use App\Http\Controllers\Gerente\DashboardController as GerenteDashboard;
use App\Http\Controllers\Gerente\ClienteController;
use App\Http\Controllers\Gerente\VehiculoController;
use App\Http\Controllers\Gerente\EmpleadoController;
use App\Http\Controllers\Gerente\OrdenTrabajoController as GerenteOrdenController;
use App\Http\Controllers\Gerente\CobrosController;
use App\Http\Controllers\Gerente\GastoController;
use App\Http\Controllers\Gerente\PlanillaController;
use App\Http\Controllers\Gerente\ReciboController;
use App\Http\Controllers\Gerente\ReporteVehiculoController;
use App\Http\Controllers\Mecanico\DashboardController as MecanicoDashboard;
use App\Http\Controllers\Mecanico\OrdenTrabajoController as MecanicoOrdenController;
use App\Http\Controllers\Mecanico\CobrosController as MecanicoCobrosController;
use App\Http\Controllers\Mecanico\VehiculoController as MecanicoVehiculoController;
use App\Http\Controllers\Mecanico\ClienteController as MecanicoClienteController;
use App\Http\Controllers\Cliente\VehiculoController as ClienteVehiculoController;

// Rutas públicas
Route::get('/', [EstadoVehiculoController::class, 'consultar'])->name('home');
Route::get('/estado/{placa}', [EstadoVehiculoController::class, 'consultar'])->name('estado.vehiculo');
Route::post('/estado', [EstadoVehiculoController::class, 'consultar'])->name('estado.consultar');

// Autenticación
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit')->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin — solo SUPER_ADMIN
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:SUPER_ADMIN'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Empleados
    Route::resource('empleados', EmpleadoController::class);
    Route::patch('empleados/{empleado}/desactivar', [EmpleadoController::class, 'desactivar'])->name('empleados.desactivar');
    Route::patch('empleados/{empleado}/reactivar', [EmpleadoController::class, 'reactivar'])->name('empleados.reactivar');

    // Usuarios
    Route::get('usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::patch('usuarios/{usuario}/rol', [UsuarioController::class, 'cambiarRol'])->name('usuarios.rol');
    Route::patch('usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleActivo'])->name('usuarios.toggle');
    Route::patch('usuarios/{usuario}/password', [UsuarioController::class, 'resetPassword'])->name('usuarios.password');
    Route::delete('usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

    // Bitacora
    Route::get('bitacora', [BitacoraController::class, 'index'])->name('bitacora.index');
    Route::get('bitacora/exportar', [BitacoraController::class, 'exportar'])->name('bitacora.exportar');

    // Reportes
    Route::get('reportes', [AdminReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/ordenes', [AdminReporteController::class, 'ordenes'])->name('reportes.ordenes');
    Route::get('reportes/financiero', [AdminReporteController::class, 'financiero'])->name('reportes.financiero');
    Route::get('reportes/cobros', [AdminReporteController::class, 'cobros'])->name('reportes.cobros');
    Route::get('reportes/servicios', [AdminReporteController::class, 'servicios'])->name('reportes.servicios');
    Route::get('reportes/clientes', [AdminReporteController::class, 'clientes'])->name('reportes.clientes');

    // Configuracion
    Route::get('configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    Route::post('configuracion', [ConfiguracionController::class, 'update'])->name('configuracion.update');
});

// Gerente — GERENTE y SUPER_ADMIN
Route::prefix('gerente')->name('gerente.')->middleware(['auth', 'role:GERENTE,SUPER_ADMIN'])->group(function () {
    Route::get('/dashboard', [GerenteDashboard::class, 'index'])->name('dashboard');

    // Clientes
    Route::resource('clientes', ClienteController::class)->except(['destroy']);
    Route::patch('clientes/{cliente}/toggle-login', [ClienteController::class, 'toggleLogin'])->name('clientes.toggle-login');

    // Empleados
    Route::resource('empleados', EmpleadoController::class);
    Route::patch('empleados/{empleado}/desactivar', [EmpleadoController::class, 'desactivar'])->name('empleados.desactivar');
    Route::patch('empleados/{empleado}/reactivar', [EmpleadoController::class, 'reactivar'])->name('empleados.reactivar');

    // Órdenes de trabajo
    Route::resource('ordenes', GerenteOrdenController::class)->except(['edit', 'update'])->parameters(['ordenes' => 'orden']);
    Route::post('ordenes/{orden}/estado', [GerenteOrdenController::class, 'cambiarEstado'])->name('ordenes.cambiarEstado');
    Route::post('ordenes/{orden}/servicios', [GerenteOrdenController::class, 'agregarServicio'])->name('ordenes.agregarServicio');
    Route::delete('ordenes/{orden}/servicios', [GerenteOrdenController::class, 'quitarServicio'])->name('ordenes.quitarServicio');

    // Fotos (desde vista gerente)
    Route::post('ordenes/{orden}/fotos', [ImagenOrdenController::class, 'store'])->name('ordenes.fotos.store');
    Route::delete('ordenes/{orden}/fotos/{imagen}', [ImagenOrdenController::class, 'destroy'])->name('ordenes.fotos.destroy');

    // Cobros / Pagos de órdenes
    Route::get('cobros', [CobrosController::class, 'index'])->name('cobros.index');
    Route::get('cobros/crear', [CobrosController::class, 'create'])->name('cobros.create');
    Route::post('cobros', [CobrosController::class, 'store'])->name('cobros.store');
    Route::get('cobros/{cobro}/editar', [CobrosController::class, 'edit'])->name('cobros.edit');
    Route::put('cobros/{cobro}', [CobrosController::class, 'update'])->name('cobros.update');

    // Gastos
    Route::get('gastos', [GastoController::class, 'index'])->name('gastos.index');
    Route::get('gastos/crear', [GastoController::class, 'create'])->name('gastos.create');
    Route::post('gastos', [GastoController::class, 'store'])->name('gastos.store');

    // Planilla
    Route::get('planilla', [PlanillaController::class, 'index'])->name('planilla.index');
    Route::post('planilla', [PlanillaController::class, 'store'])->name('planilla.store');

    // Reportes gerente
    Route::get('reportes', [AdminReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/ordenes', [AdminReporteController::class, 'ordenes'])->name('reportes.ordenes');
    Route::get('reportes/financiero', [AdminReporteController::class, 'financiero'])->name('reportes.financiero');
    Route::get('reportes/cobros', [AdminReporteController::class, 'cobros'])->name('reportes.cobros');
    Route::get('reportes/servicios', [AdminReporteController::class, 'servicios'])->name('reportes.servicios');
    Route::get('reportes/clientes', [AdminReporteController::class, 'clientes'])->name('reportes.clientes');

    // Recibos PDF
    Route::get('recibos', [ReciboController::class, 'index'])->name('recibos.index');
    Route::post('recibos/generar/{orden}', [ReciboController::class, 'generar'])->name('recibos.generar');
    Route::get('recibos/{recibo}/descargar', [ReciboController::class, 'descargar'])->name('recibos.descargar');

    // Reporte de vehículos
    Route::get('reportes/vehiculos', [ReporteVehiculoController::class, 'index'])->name('reportes.vehiculos');
    Route::post('reportes/vehiculos/generar', [ReporteVehiculoController::class, 'generar'])->name('reportes.vehiculos.generar');
});

// Vehículos — GERENTE, MECANICO y SUPER_ADMIN
Route::prefix('gerente')->name('gerente.')->middleware(['auth', 'role:GERENTE,MECANICO,SUPER_ADMIN'])->group(function () {
    Route::resource('vehiculos', VehiculoController::class)->except(['destroy']);
});

// Mecánico — MECANICO y GERENTE
Route::prefix('mecanico')->name('mecanico.')->middleware(['auth', 'role:MECANICO,GERENTE,SUPER_ADMIN'])->group(function () {
    Route::get('/dashboard', [MecanicoDashboard::class, 'index'])->name('dashboard');

    // Órdenes
    Route::get('ordenes', [MecanicoOrdenController::class, 'index'])->name('ordenes.index');
    Route::get('ordenes/nueva', [MecanicoOrdenController::class, 'create'])->name('ordenes.create');
    Route::post('ordenes', [MecanicoOrdenController::class, 'store'])->name('ordenes.store');
    Route::get('ordenes/{orden}', [MecanicoOrdenController::class, 'show'])->name('ordenes.show');
    Route::post('ordenes/{orden}/estado', [MecanicoOrdenController::class, 'cambiarEstado'])->name('ordenes.cambiarEstado');
    Route::post('ordenes/{orden}/asignar', [MecanicoOrdenController::class, 'asignar'])->name('ordenes.asignar');
    Route::post('ordenes/{orden}/repuestos', [MecanicoOrdenController::class, 'agregarRepuesto'])->name('ordenes.agregarRepuesto');
    Route::delete('ordenes/{orden}/repuestos/{repuesto}', [MecanicoOrdenController::class, 'eliminarRepuesto'])->name('ordenes.eliminarRepuesto');

    // Fotos (desde vista mecánico)
    Route::post('ordenes/{orden}/fotos', [ImagenOrdenController::class, 'store'])->name('ordenes.fotos.store');
    Route::delete('ordenes/{orden}/fotos/{imagen}', [ImagenOrdenController::class, 'destroy'])->name('ordenes.fotos.destroy');

    // Cobros
    Route::get('cobros/nuevo', [MecanicoCobrosController::class, 'create'])->name('cobros.create');
    Route::post('cobros', [MecanicoCobrosController::class, 'store'])->name('cobros.store');

    // Vehículos (con layout de mecánico)
    Route::get('vehiculos', [MecanicoVehiculoController::class, 'index'])->name('vehiculos.index');
    Route::get('vehiculos/nueva', [MecanicoVehiculoController::class, 'create'])->name('vehiculos.create');
    Route::post('vehiculos', [MecanicoVehiculoController::class, 'store'])->name('vehiculos.store');

    // Clientes
    Route::get('clientes/nuevo', [MecanicoClienteController::class, 'create'])->name('clientes.create');
    Route::post('clientes', [MecanicoClienteController::class, 'store'])->name('clientes.store');
});

// Cliente — solo CLIENTE
Route::prefix('cliente')->name('cliente.')->middleware(['auth', 'role:CLIENTE'])->group(function () {
    Route::get('/mi-vehiculo', [ClienteVehiculoController::class, 'miVehiculo'])->name('vehiculo');
});
