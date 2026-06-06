<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\PagoEmpleado;
use App\Services\ActivityLogger;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlanillaController extends Controller
{
    public function index(Request $request)
    {
        $semanaLabel  = 'Semana ' . now()->weekOfYear . ' (' . now()->startOfWeek()->format('d/m') . ' - ' . now()->endOfWeek()->format('d/m/Y') . ')';

        $empleados = Empleado::with(['persona', 'pagos' => fn($q) => $q->orderByDesc('fecha_pago')])
            ->where('activo', true)
            ->join('personas', 'empleados.persona_id', '=', 'personas.id')
            ->orderBy('personas.apellido')
            ->select('empleados.*')
            ->get();

        // Total de la semana actual basado en fecha_pago
        $totalSemana = PagoEmpleado::whereBetween('fecha_pago', [
            now()->startOfWeek()->format('Y-m-d'),
            now()->endOfWeek()->format('Y-m-d'),
        ])->sum('monto');

        $semanaActual = now()->format('Y-m');

        $empleadoId = $request->get('empleado_id');
        $historial  = null;

        if ($empleadoId) {
            $historial = PagoEmpleado::with('empleado.persona')
                ->where('empleado_id', $empleadoId)
                ->orderByDesc('fecha_pago')
                ->get();
        }

        return view('gerente.planilla.index', compact(
            'empleados', 'totalSemana', 'semanaActual', 'semanaLabel', 'historial', 'empleadoId'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'fecha_pago'  => 'required|date',
            'monto'       => 'required|numeric|min:0.01',
            'observacion' => 'nullable|string|max:500',
        ], [
            'empleado_id.required' => 'Seleccione el empleado.',
            'monto.required'       => 'El monto es obligatorio.',
            'fecha_pago.required'  => 'La fecha de pago es obligatoria.',
        ]);

        // periodo = YYYY-MM (7 chars max en la BD)
        $fecha   = Carbon::parse($request->fecha_pago);
        $periodo = $fecha->format('Y-m');
        $semana  = $fecha->weekOfYear;

        $pago = PagoEmpleado::create([
            'empleado_id' => $request->empleado_id,
            'periodo'     => $periodo,
            'monto'       => $request->monto,
            'fecha_pago'  => $request->fecha_pago,
            'observacion' => $request->observacion,
        ]);

        ActivityLogger::log("Pago semana {$semana} registrado", 'PlanillaController', $pago->id);

        return redirect()->route('gerente.planilla.index')
            ->with('success', 'Pago de planilla registrado correctamente.');
    }
}
