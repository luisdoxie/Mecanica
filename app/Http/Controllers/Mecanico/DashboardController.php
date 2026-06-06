<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\ImagenOrden;
use App\Models\OrdenTrabajo;

class DashboardController extends Controller
{
    public function index()
    {
        $empleado = auth()->user()->persona_id
            ? Empleado::where('persona_id', auth()->user()->persona_id)->first()
            : null;

        $ordenesActivas   = 0;
        $completadasHoy   = 0;
        $vehiculosSemana  = 0;
        $ordenesRecientes = collect();

        if ($empleado) {
            $ordenesActivas = OrdenTrabajo::where('empleado_id', $empleado->id)
                ->whereNotIn('estado', ['ENTREGADO'])
                ->count();

            $completadasHoy = OrdenTrabajo::where('empleado_id', $empleado->id)
                ->where('estado', 'LISTO')
                ->whereDate('updated_at', today())
                ->count();

            $vehiculosSemana = OrdenTrabajo::where('empleado_id', $empleado->id)
                ->whereBetween('fecha_ingreso', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();

            $ordenesRecientes = OrdenTrabajo::with(['vehiculo.cliente.persona'])
                ->where('empleado_id', $empleado->id)
                ->whereNotIn('estado', ['ENTREGADO'])
                ->latest()
                ->limit(8)
                ->get();
        }

        // Órdenes sin asignar — cualquier mecánico puede verlas y tomarselas
        $ordenesSinAsignar = OrdenTrabajo::with(['vehiculo.cliente.persona'])
            ->whereNull('empleado_id')
            ->whereNotIn('estado', ['ENTREGADO'])
            ->latest()
            ->limit(8)
            ->get();

        $ultimasFotos          = collect();
        $ordenesSinFotoRecepcion = collect();

        if ($empleado) {
            $ultimasFotos = ImagenOrden::whereHas('orden', fn($q) => $q->where('empleado_id', $empleado->id))
                ->orderByDesc('created_at')
                ->limit(6)
                ->get();

            $ordenesSinFotoRecepcion = OrdenTrabajo::where('empleado_id', $empleado->id)
                ->whereNotIn('estado', ['ENTREGADO'])
                ->whereDoesntHave('imagenes', fn($q) => $q->where('tipo', 'RECEPCION'))
                ->get();
        }

        return view('mecanico.dashboard', compact(
            'empleado', 'ordenesActivas', 'completadasHoy',
            'vehiculosSemana', 'ordenesRecientes', 'ordenesSinAsignar',
            'ultimasFotos', 'ordenesSinFotoRecepcion'
        ));
    }
}
