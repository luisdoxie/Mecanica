<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PagoEmpleado;
use Illuminate\Http\Request;

class ComisionApiController extends Controller
{
    public function index()
    {
        $pagos = PagoEmpleado::with('empleado.persona')->latest('fecha_pago')->limit(100)->get();

        return response()->json($pagos->map(fn($p) => [
            'id'          => $p->id,
            'empleado'    => $p->empleado->persona->nombre . ' ' . $p->empleado->persona->apellido,
            'empleado_id' => $p->empleado_id,
            'monto'       => $p->monto,
            'periodo'     => $p->periodo,
            'fecha_pago'  => $p->fecha_pago?->format('d/m/Y'),
            'observacion' => $p->observacion,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'periodo'     => 'required|string|max:20',
            'monto'       => 'required|numeric|min:0.01',
            'fecha_pago'  => 'required|date',
            'observacion' => 'nullable|string|max:255',
        ]);

        $pago = PagoEmpleado::create($request->only('empleado_id', 'periodo', 'monto', 'fecha_pago', 'observacion'));

        return response()->json(['message' => 'Pago registrado.', 'id' => $pago->id], 201);
    }
}
