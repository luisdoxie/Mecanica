<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use App\Mail\VehiculoListoMail;
use App\Models\Cliente;
use App\Models\Empleado;
use App\Models\HistorialEstado;
use App\Models\OrdenTrabajo;
use App\Models\RepuestoUtilizado;
use App\Models\Servicio;
use App\Models\Vehiculo;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrdenTrabajoController extends Controller
{
    public function index(Request $request)
    {
        $empleado = auth()->user()->persona_id
            ? Empleado::where('persona_id', auth()->user()->persona_id)->first()
            : null;

        $query = OrdenTrabajo::with(['vehiculo.cliente.persona', 'empleado.persona'])
            ->latest();

        // Mecánico ve solo sus órdenes asignadas (si tiene empleado vinculado)
        if (auth()->user()->rol === 'MECANICO' && $empleado) {
            $query->where('empleado_id', $empleado->id);
        }

        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }
        if ($placa = $request->get('placa')) {
            $query->whereHas('vehiculo', fn($q) => $q->where('placa', 'ilike', "%{$placa}%"));
        }

        $ordenes = $query->paginate(15)->withQueryString();
        $estados = OrdenTrabajo::ESTADOS;
        $filtros = $request->only('estado', 'placa');

        return view('mecanico.ordenes.index', compact('ordenes', 'estados', 'filtros'));
    }

    public function show(OrdenTrabajo $orden)
    {
        $orden->load([
            'vehiculo.cliente.persona',
            'empleado.persona',
            'servicios',
            'repuestos',
            'historialEstados.empleado.persona',
            'imagenes',
        ]);

        $tiposImagen       = ['RECEPCION', 'DAÑO', 'REPUESTO', 'ENTREGA'];
        $imagenesAgrupadas = $orden->imagenes->groupBy('tipo');

        return view('mecanico.ordenes.show', compact('orden', 'tiposImagen', 'imagenesAgrupadas'));
    }

    public function cambiarEstado(Request $request, OrdenTrabajo $orden)
    {
        $request->validate([
            'nota' => 'nullable|string|max:500',
        ]);

        $siguiente = $orden->siguienteEstado();

        if (!$siguiente) {
            return back()->with('error', 'Esta orden ya está en el estado final.');
        }

        $empleado = Empleado::where('persona_id', auth()->user()->persona_id)->first();

        try {
            DB::transaction(function () use ($orden, $siguiente, $request, $empleado) {
                HistorialEstado::create([
                    'orden_id'        => $orden->id,
                    'empleado_id'     => $empleado?->id,
                    'estado_anterior' => $orden->estado,
                    'estado_nuevo'    => $siguiente,
                    'nota'            => $request->nota,
                    'created_at'      => now(),
                ]);

                $orden->update(['estado' => $siguiente]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'No se pudo actualizar el estado: ' . $e->getMessage());
        }

        if ($siguiente === 'LISTO') {
            $orden->load('vehiculo.cliente.persona');
            $email = $orden->vehiculo->cliente->persona->email ?? null;
            if ($email) {
                try {
                    Mail::to($email)->send(new VehiculoListoMail($orden));
                } catch (\Exception $e) {}
            }
        }

        return back()->with('success', "Estado actualizado a {$siguiente}.");
    }

    public function agregarRepuesto(Request $request, OrdenTrabajo $orden)
    {
        $request->validate([
            'nombre'            => 'required|string|max:200',
            'origen'            => 'required|in:TALLER,CLIENTE',
            'calidad_observada' => 'nullable|string|max:300',
            'costo'             => 'required|numeric|min:0',
            'cantidad'          => 'required|integer|min:1',
        ], [
            'nombre.required'   => 'El nombre del repuesto es obligatorio.',
            'origen.required'   => 'Seleccione el origen del repuesto.',
            'costo.required'    => 'El costo es obligatorio.',
            'cantidad.required' => 'La cantidad es obligatoria.',
        ]);

        RepuestoUtilizado::create([
            'orden_id'          => $orden->id,
            'nombre'            => $request->nombre,
            'origen'            => $request->origen,
            'calidad_observada' => $request->calidad_observada,
            'costo'             => $request->costo,
            'cantidad'          => $request->cantidad,
        ]);

        $this->recalcularCosto($orden);

        return back()->with('success', 'Repuesto agregado correctamente.');
    }

    public function eliminarRepuesto(OrdenTrabajo $orden, RepuestoUtilizado $repuesto)
    {
        $repuesto->delete();
        $this->recalcularCosto($orden);
        return back()->with('success', 'Repuesto eliminado.');
    }

    public function create(Request $request)
    {
        $vehiculoId = $request->get('vehiculo_id') ?? session()->pull('mecanico_nuevo_vehiculo_id');

        // Vehículo nuevo va primero, luego el resto por placa
        $vehiculos = Vehiculo::with('cliente.persona')
            ->join('clientes', 'vehiculos.cliente_id', '=', 'clientes.id')
            ->join('personas', 'clientes.persona_id', '=', 'personas.id')
            ->orderByRaw($vehiculoId ? "CASE WHEN vehiculos.id = ? THEN 0 ELSE 1 END, vehiculos.placa" : "vehiculos.placa", $vehiculoId ? [$vehiculoId] : [])
            ->select('vehiculos.*')
            ->get();

        $empleados = Empleado::with('persona')->where('activo', true)->get();
        return view('mecanico.ordenes.create', compact('vehiculos', 'empleados', 'vehiculoId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehiculo_id'          => 'required|exists:vehiculos,id',
            'descripcion_problema' => 'required|string|max:1000',
        ], [
            'vehiculo_id.required'          => 'Seleccione el vehículo.',
            'descripcion_problema.required' => 'La descripción del problema es obligatoria.',
        ]);

        $empleado = Empleado::where('persona_id', auth()->user()->persona_id)->first();

        $orden = OrdenTrabajo::create([
            'vehiculo_id'          => $request->vehiculo_id,
            'empleado_id'          => $empleado?->id,
            'estado'               => 'RECIBIDO',
            'descripcion_problema' => $request->descripcion_problema,
            'fecha_ingreso'        => now(),
        ]);

        ActivityLogger::log('Orden registrada por mecánico', 'OrdenTrabajo', $orden->id);

        return redirect()->route('mecanico.ordenes.show', $orden)
            ->with('success', "Orden #{$orden->id} registrada correctamente.");
    }

    public function asignar(OrdenTrabajo $orden)
    {
        $empleado = Empleado::where('persona_id', auth()->user()->persona_id)->first();

        if (!$empleado) {
            return back()->with('error', 'No tienes un perfil de empleado asociado.');
        }

        if ($orden->empleado_id) {
            return back()->with('error', 'Esta orden ya tiene un mecánico asignado.');
        }

        $orden->update(['empleado_id' => $empleado->id]);

        ActivityLogger::log('Orden auto-asignada por mecánico', 'OrdenTrabajo', $orden->id);

        return back()->with('success', "Te has asignado a la orden #{$orden->id}.");
    }

    private function recalcularCosto(OrdenTrabajo $orden): void
    {
        $costoServicios = DB::table('orden_servicio')->where('orden_id', $orden->id)->sum('precio_aplicado');
        $costoRepuestos = $orden->repuestos()->selectRaw('SUM(costo * cantidad) as total')->value('total') ?? 0;
        $orden->update(['costo_total' => $costoServicios + $costoRepuestos]);
    }
}
