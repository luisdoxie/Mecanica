<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\OrdenTrabajo;
use App\Models\PagoOrden;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CobrosController extends Controller
{
    public function create(Request $request)
    {
        $empleado = Empleado::where('persona_id', auth()->user()->persona_id)->first();

        // Todas las órdenes del mecánico sin pago PAGADO completo
        $query = OrdenTrabajo::with(['vehiculo.cliente.persona'])
            ->whereIn('estado', ['LISTO', 'ENTREGADO'])
            ->orderByDesc('created_at');

        if ($empleado) {
            $query->where('empleado_id', $empleado->id);
        }

        $ordenes = $query->get();
        $ordenId = $request->get('orden_id');
        $qrUrl   = DB::table('config_taller')->where('clave', 'qr_imagen_url')->value('valor');

        return view('mecanico.cobros.create', compact('ordenes', 'ordenId', 'qrUrl'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'orden_trabajo_id' => 'required|exists:ordenes_trabajo,id',
            'metodo_pago'      => 'required|in:EFECTIVO,QR',
            'tipo_cobro'       => 'required_if:metodo_pago,EFECTIVO|nullable|in:TOTAL,PARCIAL',
            'monto'            => 'required|numeric|min:0.01',
            'observacion'      => 'nullable|string|max:500',
        ], [
            'orden_trabajo_id.required' => 'Seleccione la orden.',
            'metodo_pago.required'      => 'Seleccione el método de pago.',
            'monto.required'            => 'El monto es obligatorio.',
            'monto.min'                 => 'El monto debe ser mayor a 0.',
        ]);

        $estado = 'PAGADO';
        if ($request->metodo_pago === 'EFECTIVO' && $request->tipo_cobro === 'PARCIAL') {
            $estado = 'PARCIAL';
        }

        $pago = PagoOrden::create([
            'orden_trabajo_id' => $request->orden_trabajo_id,
            'monto'            => $request->monto,
            'metodo_pago'      => $request->metodo_pago,
            'estado'           => $estado,
            'fecha_pago'       => now(),
            'registrado_por'   => auth()->id(),
            'observacion'      => $request->observacion,
        ]);

        ActivityLogger::log('Cobro registrado por mecánico', 'Cobros', $pago->id);

        return redirect()->route('mecanico.dashboard')
            ->with('success', 'Cobro registrado correctamente.');
    }
}
