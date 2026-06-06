<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\OrdenTrabajo;
use App\Models\PagoOrden;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class CobrosController extends Controller
{
    public function index(Request $request)
    {
        $porCobrar = PagoOrden::with(['orden.vehiculo.cliente.persona'])
            ->whereIn('estado', ['FIADO', 'PARCIAL'])
            ->orderBy('created_at')
            ->get();

        $query = PagoOrden::with(['orden.vehiculo.cliente.persona', 'registradoPor'])
            ->latest();

        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }
        if ($buscar = $request->get('buscar')) {
            $query->whereHas('orden.vehiculo', function ($q) use ($buscar) {
                $q->where('placa', 'ilike', "%{$buscar}%")
                  ->orWhereHas('cliente.persona', fn($p) => $p->where('nombre', 'ilike', "%{$buscar}%")
                      ->orWhere('apellido', 'ilike', "%{$buscar}%"));
            });
        }

        $pagos   = $query->paginate(15)->withQueryString();
        $filtros = $request->only('estado', 'buscar');

        return view('gerente.cobros.index', compact('pagos', 'porCobrar', 'estado', 'filtros'));
    }

    public function create(Request $request)
    {
        $ordenes = OrdenTrabajo::with(['vehiculo.cliente.persona'])
            ->whereIn('estado', ['LISTO', 'ENTREGADO'])
            ->orderByDesc('created_at')
            ->get();

        $ordenId = $request->get('orden_id');

        return view('gerente.cobros.create', compact('ordenes', 'ordenId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'orden_trabajo_id' => 'required|exists:ordenes_trabajo,id',
            'monto'            => 'required|numeric|min:0.01',
            'metodo_pago'      => 'required|in:EFECTIVO,QR',
            'estado'           => 'required|in:PENDIENTE,PARCIAL,PAGADO,FIADO',
            'observacion'      => 'nullable|string|max:500',
        ], [
            'orden_trabajo_id.required' => 'Seleccione la orden.',
            'monto.required'            => 'El monto es obligatorio.',
            'metodo_pago.required'      => 'Seleccione el método de pago.',
            'estado.required'           => 'Seleccione el estado del pago.',
        ]);

        PagoOrden::create([
            'orden_trabajo_id' => $request->orden_trabajo_id,
            'monto'            => $request->monto,
            'metodo_pago'      => $request->metodo_pago,
            'estado'           => $request->estado,
            'fecha_pago'       => now(),
            'registrado_por'   => auth()->id(),
            'observacion'      => $request->observacion,
        ]);

        ActivityLogger::log("Cobro registrado: {$request->estado}", 'CobrosController');

        return redirect()->route('gerente.cobros.index')
            ->with('success', 'Pago registrado correctamente.');
    }

    public function edit(PagoOrden $cobro)
    {
        $cobro->load('orden.vehiculo.cliente.persona');
        return view('gerente.cobros.edit', compact('cobro'));
    }

    public function update(Request $request, PagoOrden $cobro)
    {
        $request->validate([
            'monto'       => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|in:EFECTIVO,QR',
            'estado'      => 'required|in:PENDIENTE,PARCIAL,PAGADO,FIADO',
            'observacion' => 'nullable|string|max:500',
        ]);

        $cobro->update([
            'monto'       => $request->monto,
            'metodo_pago' => $request->metodo_pago,
            'estado'      => $request->estado,
            'fecha_pago'  => now(),
            'observacion' => $request->observacion,
        ]);

        ActivityLogger::log("Cobro actualizado: {$request->estado}", 'CobrosController');

        return redirect()->route('gerente.cobros.index')
            ->with('success', 'Pago actualizado correctamente.');
    }
}
