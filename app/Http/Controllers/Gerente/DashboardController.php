<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\OrdenTrabajo;
use App\Models\PagoEmpleado;
use App\Models\PagoOrden;
use App\Models\Gasto;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $mes   = now()->format('Y-m');
        $inicio = now()->startOfMonth();
        $fin    = now()->endOfMonth();

        $ingresos  = PagoOrden::where('estado', 'PAGADO')
            ->whereBetween('fecha_pago', [$inicio, $fin])
            ->sum('monto');

        $gastos    = Gasto::whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->sum('monto');

        $salarios  = PagoEmpleado::where('periodo', $mes)->sum('monto');

        $ganancia  = $ingresos - $gastos - $salarios;

        $topOrdenes = OrdenTrabajo::with('vehiculo.cliente.persona')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->orderByDesc('costo_total')
            ->limit(5)
            ->get();

        $totalOrdenes      = OrdenTrabajo::whereMonth('created_at', now()->month)->count();
        $ordenesPendientes = OrdenTrabajo::whereNotIn('estado', ['ENTREGADO'])->count();
        $porCobrar         = PagoOrden::whereIn('estado', ['FIADO', 'PARCIAL'])->sum('monto');
        $autosListosHoy    = OrdenTrabajo::where('estado', 'LISTO')->whereDate('updated_at', today())->count();

        // Órdenes activas con datos para tarjetas de progreso
        $ordenesActivasList = OrdenTrabajo::with(['vehiculo.cliente.persona', 'empleado.persona'])
            ->whereNotIn('estado', ['ENTREGADO'])
            ->latest()
            ->limit(6)
            ->get();

        // Ingresos últimos 7 días para gráfico
        $ingresosUltimos7 = collect(range(6, 0))->map(fn($i) => [
            'fecha' => now()->subDays($i)->format('d/m'),
            'total' => (float) PagoOrden::where('estado', 'PAGADO')
                ->whereDate('fecha_pago', now()->subDays($i))
                ->sum('monto'),
        ]);

        // Top 5 cuentas por cobrar más antiguas
        $cuentasPorCobrar = PagoOrden::with('orden.vehiculo.cliente.persona')
            ->whereIn('estado', ['FIADO', 'PARCIAL'])
            ->orderBy('created_at')
            ->limit(5)
            ->get();

        // Actividad reciente (últimas 5)
        $actividadReciente = DB::table('activity_log')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('gerente.dashboard', compact(
            'ingresos', 'gastos', 'salarios', 'ganancia',
            'topOrdenes', 'totalOrdenes', 'ordenesPendientes', 'porCobrar', 'mes',
            'autosListosHoy', 'ordenesActivasList', 'ingresosUltimos7',
            'cuentasPorCobrar', 'actividadReciente'
        ));
    }
}
