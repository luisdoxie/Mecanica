<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\OrdenTrabajo;
use App\Models\PagoOrden;
use App\Models\PagoEmpleado;
use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function gerente(Request $request)
    {
        $inicio = now()->startOfMonth();
        $fin    = now()->endOfMonth();
        $mes    = now()->format('Y-m');

        $ingresos  = PagoOrden::where('estado', 'PAGADO')->whereBetween('fecha_pago', [$inicio, $fin])->sum('monto');
        $gastos    = Gasto::whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])->sum('monto');
        $salarios  = PagoEmpleado::where('periodo', $mes)->sum('monto');
        $ganancia  = $ingresos - $gastos - $salarios;

        $ordenesActivas  = OrdenTrabajo::whereNotIn('estado', ['ENTREGADO'])->count();
        $autosListosHoy  = OrdenTrabajo::where('estado', 'LISTO')->whereDate('updated_at', today())->count();
        $porCobrar       = PagoOrden::whereIn('estado', ['FIADO', 'PARCIAL'])->sum('monto');

        $ordenesRecientes = OrdenTrabajo::with(['vehiculo.cliente.persona', 'empleado.persona'])
            ->whereNotIn('estado', ['ENTREGADO'])
            ->latest()->limit(5)->get()
            ->map(fn($o) => [
                'id'      => $o->id,
                'estado'  => $o->estado,
                'placa'   => $o->vehiculo->placa,
                'cliente' => $o->vehiculo->cliente->persona->apellido . ', ' . $o->vehiculo->cliente->persona->nombre,
            ]);

        return response()->json([
            'ingresos'         => $ingresos,
            'gastos'           => $gastos,
            'salarios'         => $salarios,
            'ganancia'         => $ganancia,
            'ordenes_activas'  => $ordenesActivas,
            'autos_listos_hoy' => $autosListosHoy,
            'por_cobrar'       => $porCobrar,
            'ordenes_recientes'=> $ordenesRecientes,
        ]);
    }

    public function mecanico(Request $request)
    {
        $user     = $request->user();
        $empleado = Empleado::where('persona_id', $user->persona_id)->first();

        if (!$empleado) {
            return response()->json(['message' => 'Sin perfil de empleado vinculado.'], 404);
        }

        $ordenesActivas  = OrdenTrabajo::where('empleado_id', $empleado->id)->whereNotIn('estado', ['ENTREGADO'])->count();
        $completadasHoy  = OrdenTrabajo::where('empleado_id', $empleado->id)->where('estado', 'LISTO')->whereDate('updated_at', today())->count();
        $semana          = OrdenTrabajo::where('empleado_id', $empleado->id)->whereBetween('fecha_ingreso', [now()->startOfWeek(), now()->endOfWeek()])->count();

        $misOrdenes = OrdenTrabajo::with(['vehiculo.cliente.persona'])
            ->where('empleado_id', $empleado->id)
            ->whereNotIn('estado', ['ENTREGADO'])
            ->latest()->limit(10)->get()
            ->map(fn($o) => [
                'id'        => $o->id,
                'estado'    => $o->estado,
                'siguiente' => $o->siguienteEstado(),
                'placa'     => $o->vehiculo->placa,
                'marca'     => $o->vehiculo->marca,
                'modelo'    => $o->vehiculo->modelo,
                'cliente'   => $o->vehiculo->cliente->persona->nombre . ' ' . $o->vehiculo->cliente->persona->apellido,
                'fecha'     => $o->fecha_ingreso?->format('d/m/Y H:i'),
            ]);

        return response()->json([
            'ordenes_activas'  => $ordenesActivas,
            'completadas_hoy'  => $completadasHoy,
            'vehiculos_semana' => $semana,
            'mis_ordenes'      => $misOrdenes,
        ]);
    }

    public function reporteSemanal(Request $request)
    {
        $user     = $request->user();
        $empleado = Empleado::where('persona_id', $user->persona_id)->first();

        if (!$empleado) {
            return response()->json(['message' => 'Sin perfil de empleado vinculado.'], 404);
        }

        $inicio = now()->startOfWeek(1); // Lunes
        $fin    = now()->endOfWeek(6);   // Sábado (6 días laborales)

        $ordenes = OrdenTrabajo::with(['vehiculo.cliente.persona', 'repuestos', 'servicios'])
            ->where('empleado_id', $empleado->id)
            ->where(function ($q) use ($inicio, $fin) {
                $q->whereBetween('fecha_ingreso', [$inicio, $fin])
                  ->orWhereBetween('updated_at', [$inicio, $fin]);
            })
            ->latest()
            ->get();

        $sueldoAsignado = PagoEmpleado::where('empleado_id', $empleado->id)
            ->where('periodo', now()->format('Y-m'))
            ->sum('monto');

        return response()->json([
            'semana_inicio'    => $inicio->format('d/m/Y'),
            'semana_fin'       => $fin->format('d/m/Y'),
            'total_vehiculos'  => $ordenes->count(),
            'total_servicios'  => $ordenes->sum(fn($o) => $o->servicios->count()),
            'total_repuestos'  => $ordenes->sum(fn($o) => $o->repuestos->count()),
            'ingreso_generado' => $ordenes->sum(fn($o) => (float) $o->costo_total),
            'sueldo_asignado'  => (float) $sueldoAsignado,
            'ordenes'          => $ordenes->map(fn($o) => [
                'id'            => $o->id,
                'estado'        => $o->estado,
                'placa'         => $o->vehiculo->placa,
                'marca'         => $o->vehiculo->marca,
                'modelo'        => $o->vehiculo->modelo,
                'color'         => $o->vehiculo->color,
                'cliente'       => $o->vehiculo->cliente->persona->nombre . ' ' . $o->vehiculo->cliente->persona->apellido,
                'fecha_ingreso' => $o->fecha_ingreso?->format('d/m/Y'),
                'costo_total'   => $o->costo_total,
                'num_servicios' => $o->servicios->count(),
                'num_repuestos' => $o->repuestos->count(),
            ]),
        ]);
    }
}
