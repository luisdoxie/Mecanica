<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PagoOrden;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;

class CobroApiController extends Controller
{
    public function index(Request $request)
    {
        $user  = $request->user();
        $query = PagoOrden::with('orden.vehiculo')->latest()->limit(100);

        if ($user->rol === 'MECANICO') {
            $empleado = \App\Models\Empleado::where('persona_id', $user->persona_id)->first();
            if ($empleado) {
                $query->whereHas('orden', fn($q) => $q->where('empleado_id', $empleado->id));
            }
        }

        $cobros = $query->get();

        return response()->json($cobros->map(fn($p) => [
            'id'          => $p->id,
            'orden_id'    => $p->orden_trabajo_id,
            'placa'       => $p->orden->vehiculo->placa,
            'monto'       => $p->monto,
            'estado'      => $p->estado,
            'metodo'      => $p->metodo_pago,
            'fecha'       => $p->fecha_pago?->format('d/m/Y'),
            'observacion' => $p->observacion,
        ]));
    }

    public function ordenesCobrable(Request $request)
    {
        $user     = $request->user();
        $query = OrdenTrabajo::with(['vehiculo.cliente.persona'])
            ->whereIn('estado', ['LISTO', 'ENTREGADO']);

        if ($user->rol === 'MECANICO') {
            $empleado = \App\Models\Empleado::where('persona_id', $user->persona_id)->first();
            if ($empleado) $query->where('empleado_id', $empleado->id);
        }

        return response()->json($query->latest()->get()->map(fn($o) => [
            'id'          => $o->id,
            'placa'       => $o->vehiculo->placa,
            'marca'       => $o->vehiculo->marca,
            'modelo'      => $o->vehiculo->modelo,
            'cliente'     => $o->vehiculo->cliente->persona->nombre . ' ' . $o->vehiculo->cliente->persona->apellido,
            'costo_total' => $o->costo_total,
            'estado'      => $o->estado,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'orden_trabajo_id' => 'required|exists:ordenes_trabajo,id',
            'monto'            => 'required|numeric|min:0.01',
            'metodo_pago'      => 'required|in:EFECTIVO,QR,PAGO_MOVIL',
            'estado'           => 'required|in:PAGADO,PARCIAL,FIADO',
            'observacion'      => 'nullable|string|max:255',
        ]);

        $cobro = PagoOrden::create([
            'orden_trabajo_id' => $request->orden_trabajo_id,
            'monto'            => $request->monto,
            'metodo_pago'      => $request->metodo_pago,
            'estado'           => $request->estado,
            'fecha_pago'       => now(),
            'registrado_por'   => $request->user()->id,
            'observacion'      => $request->observacion,
        ]);

        return response()->json(['message' => 'Cobro registrado.', 'id' => $cobro->id], 201);
    }

    public function update(Request $request, PagoOrden $cobro)
    {
        $request->validate([
            'monto'       => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:EFECTIVO,QR,PAGO_MOVIL',
            'estado'      => 'required|in:PAGADO,PARCIAL,FIADO',
            'observacion' => 'nullable|string|max:255',
        ]);

        $cobro->update([
            'monto'       => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'estado'      => $request->estado,
            'observacion' => $request->observacion,
        ]);

        return response()->json(['message' => 'Cobro actualizado.']);
    }
}
