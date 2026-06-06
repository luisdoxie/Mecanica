<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Mail\VehiculoListoMail;
use App\Models\Empleado;
use App\Services\ActivityLogger;
use App\Models\HistorialEstado;
use App\Models\OrdenTrabajo;
use App\Models\Servicio;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrdenTrabajoController extends Controller
{
    public function index(Request $request)
    {
        $query = OrdenTrabajo::with(['vehiculo.cliente.persona', 'empleado.persona'])
            ->latest();

        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }
        if ($fecha = $request->get('fecha')) {
            $query->whereDate('fecha_ingreso', $fecha);
        }
        if ($placa = $request->get('placa')) {
            $query->whereHas('vehiculo', fn($q) => $q->where('placa', 'ilike', "%{$placa}%"));
        }
        if ($cliente = $request->get('cliente')) {
            $query->whereHas('vehiculo.cliente.persona', fn($q) => $q
                ->where('nombre', 'ilike', "%{$cliente}%")
                ->orWhere('apellido', 'ilike', "%{$cliente}%"));
        }

        $ordenes  = $query->paginate(15)->withQueryString();
        $estados  = OrdenTrabajo::ESTADOS;
        $filtros  = $request->only('estado', 'fecha', 'placa', 'cliente');

        return view('gerente.ordenes.index', compact('ordenes', 'estados', 'filtros'));
    }

    public function create()
    {
        $vehiculos  = Vehiculo::with('cliente.persona')->orderBy('placa')->get();
        $empleados  = Empleado::with('persona')->where('activo', true)->get();
        return view('gerente.ordenes.create', compact('vehiculos', 'empleados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehiculo_id'          => 'required|exists:vehiculos,id',
            'empleado_id'          => 'nullable|exists:empleados,id',
            'descripcion_problema' => 'required|string|max:1000',
        ], [
            'vehiculo_id.required'          => 'Seleccione el vehículo.',
            'descripcion_problema.required' => 'La descripción del problema es obligatoria.',
        ]);

        $orden = OrdenTrabajo::create([
            'vehiculo_id'          => $request->vehiculo_id,
            'empleado_id'          => $request->empleado_id,
            'estado'               => 'RECIBIDO',
            'descripcion_problema' => $request->descripcion_problema,
            'fecha_ingreso'        => now(),
        ]);

        ActivityLogger::log('Orden de trabajo creada', class_basename(__CLASS__));

        return redirect()->route('gerente.ordenes.show', $orden)
            ->with('success', "Orden #{$orden->id} creada correctamente.");
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

        $serviciosDisponibles = Servicio::where('activo', true)->orderBy('nombre')->get();
        $empleados            = Empleado::with('persona')->where('activo', true)->get();
        $tiposImagen          = ['RECEPCION', 'DAÑO', 'REPUESTO', 'ENTREGA'];
        $imagenesAgrupadas    = $orden->imagenes->groupBy('tipo');

        return view('gerente.ordenes.show', compact(
            'orden', 'serviciosDisponibles', 'empleados', 'tiposImagen', 'imagenesAgrupadas'
        ));
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

        // Solo GERENTE puede marcar ENTREGADO
        if ($siguiente === 'ENTREGADO' && auth()->user()->rol !== 'SUPER_ADMIN' && auth()->user()->rol !== 'GERENTE') {
            return back()->with('error', 'Solo el gerente puede marcar una orden como ENTREGADO.');
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

        // Email al cliente cuando llega a LISTO
        if ($siguiente === 'LISTO') {
            $orden->load('vehiculo.cliente.persona');
            $email = $orden->vehiculo->cliente->persona->email ?? null;
            if ($email) {
                try {
                    Mail::to($email)->send(new VehiculoListoMail($orden));
                } catch (\Exception $e) {
                    // No bloquear si el mail falla
                }
            }
        }

        ActivityLogger::log("Estado cambiado a {$siguiente}", 'OrdenTrabajoController', $orden->id);

        return back()->with('success', "Estado actualizado a {$siguiente}.");
    }

    public function agregarServicio(Request $request, OrdenTrabajo $orden)
    {
        $request->validate([
            'servicio_id'     => 'required|exists:servicios,id',
            'precio_aplicado' => 'required|numeric|min:0',
            'observaciones'   => 'nullable|string|max:300',
        ]);

        // Evitar duplicados
        if ($orden->servicios()->where('servicio_id', $request->servicio_id)->exists()) {
            return back()->with('error', 'Este servicio ya está agregado a la orden.');
        }

        DB::table('orden_servicio')->insert([
            'orden_id'        => $orden->id,
            'servicio_id'     => $request->servicio_id,
            'precio_aplicado' => $request->precio_aplicado,
            'observaciones'   => $request->observaciones,
        ]);

        $this->recalcularCosto($orden);

        return back()->with('success', 'Servicio agregado correctamente.');
    }

    public function quitarServicio(Request $request, OrdenTrabajo $orden)
    {
        DB::table('orden_servicio')
            ->where('orden_id', $orden->id)
            ->where('servicio_id', $request->servicio_id)
            ->delete();

        $this->recalcularCosto($orden);

        return back()->with('success', 'Servicio eliminado.');
    }

    public function destroy(OrdenTrabajo $orden)
    {
        $orden->delete();
        return redirect()->route('gerente.ordenes.index')
            ->with('success', "Orden #{$orden->id} eliminada.");
    }

    private function recalcularCosto(OrdenTrabajo $orden): void
    {
        $costoServicios  = DB::table('orden_servicio')->where('orden_id', $orden->id)->sum('precio_aplicado');
        $costoRepuestos  = $orden->repuestos()->selectRaw('SUM(costo * cantidad) as total')->value('total') ?? 0;
        $orden->update(['costo_total' => $costoServicios + $costoRepuestos]);
    }
}
