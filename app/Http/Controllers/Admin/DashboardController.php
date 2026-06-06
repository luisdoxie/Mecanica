<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\OrdenTrabajo;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs
        $usuariosActivos  = User::where('activo', true)->count();
        $ordenesMes       = OrdenTrabajo::whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year)->count();
        $ingresosMes      = DB::table('pagos_orden')
                              ->where('estado', 'PAGADO')
                              ->whereMonth('fecha_pago', now()->month)
                              ->sum('monto');
        $bitacoraHoy      = ActivityLog::whereDate('created_at', today())->count();

        // Gráfico: órdenes por estado
        $ordenesPorEstado = OrdenTrabajo::select('estado', DB::raw('COUNT(*) as total'))
                              ->groupBy('estado')->get()
                              ->pluck('total', 'estado');

        // Gráfico: ingresos vs gastos últimos 6 meses
        $meses = collect(range(5, 0))->map(fn($i) => now()->subMonths($i)->format('Y-m'));
        $ingresosPorMes = DB::table('pagos_orden')
            ->select(DB::raw("TO_CHAR(fecha_pago, 'YYYY-MM') as mes"), DB::raw('SUM(monto) as total'))
            ->where('estado', 'PAGADO')
            ->where('fecha_pago', '>=', now()->subMonths(5)->startOfMonth())
            ->groupBy('mes')->pluck('total', 'mes');
        $gastosPorMes = DB::table('gastos')
            ->select(DB::raw("TO_CHAR(fecha, 'YYYY-MM') as mes"), DB::raw('SUM(monto) as total'))
            ->where('fecha', '>=', now()->subMonths(5)->startOfMonth()->toDateString())
            ->groupBy('mes')->pluck('total', 'mes');

        // Gráfico: servicios más solicitados
        $serviciosTop = DB::table('orden_servicio')
            ->join('servicios', 'orden_servicio.servicio_id', '=', 'servicios.id')
            ->select('servicios.nombre', DB::raw('COUNT(*) as total'))
            ->groupBy('servicios.nombre')
            ->orderByDesc('total')
            ->limit(6)->get();

        // Últimas 10 acciones de bitácora
        $ultimasAcciones = ActivityLog::with('usuario')
            ->orderByDesc('created_at')->limit(10)->get();

        return view('admin.dashboard', compact(
            'usuariosActivos', 'ordenesMes', 'ingresosMes', 'bitacoraHoy',
            'ordenesPorEstado', 'meses', 'ingresosPorMes', 'gastosPorMes',
            'serviciosTop', 'ultimasAcciones'
        ));
    }
}
