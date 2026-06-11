<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\HistorialEstado;
use App\Models\ImagenOrden;
use App\Models\OrdenTrabajo;
use App\Models\RepuestoUtilizado;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenApiController extends Controller
{
    private function _empleadoActual(Request $request): ?Empleado
    {
        return Empleado::where('persona_id', $request->user()->persona_id)->first();
    }

    private function _autorizarOrden(OrdenTrabajo $orden, Request $request): bool
    {
        $user = $request->user();
        if (in_array($user->rol, ['GERENTE', 'SUPER_ADMIN'])) return true;
        $empleado = $this->_empleadoActual($request);
        return $empleado && $orden->empleado_id === $empleado->id;
    }

    public function index(Request $request)
    {
        $user     = $request->user();
        $empleado = $this->_empleadoActual($request);

        $query = OrdenTrabajo::with(['vehiculo.cliente.persona', 'empleado.persona'])->latest();

        if ($user->rol === 'MECANICO' && $empleado) {
            $query->where('empleado_id', $empleado->id);
        }

        if ($estado = $request->get('estado')) {
            $query->where('estado', $estado);
        }

        return response()->json($query->limit(50)->get()->map(fn($o) => [
            'id'           => $o->id,
            'estado'       => $o->estado,
            'placa'        => $o->vehiculo?->placa ?? '—',
            'marca'        => $o->vehiculo?->marca ?? '—',
            'modelo'       => $o->vehiculo?->modelo ?? '—',
            'cliente'      => ($o->vehiculo?->cliente?->persona?->nombre ?? '') . ' ' . ($o->vehiculo?->cliente?->persona?->apellido ?? ''),
            'mecanico'     => $o->empleado ? $o->empleado->persona?->nombre . ' ' . $o->empleado->persona?->apellido : null,
            'costo_total'  => $o->costo_total,
            'fecha_ingreso'=> $o->fecha_ingreso?->format('d/m/Y H:i'),
            'descripcion'  => $o->descripcion_problema,
        ]));
    }

    public function enTaller()
    {
        $ordenes = OrdenTrabajo::with(['vehiculo.cliente.persona', 'empleado.persona'])
            ->whereNotIn('estado', ['ENTREGADO'])
            ->latest()
            ->get();

        return response()->json($ordenes->map(fn($o) => [
            'id'       => $o->id,
            'estado'   => $o->estado,
            'placa'    => $o->vehiculo?->placa ?? '—',
            'marca'    => $o->vehiculo?->marca ?? '—',
            'modelo'   => $o->vehiculo?->modelo ?? '—',
            'color'    => $o->vehiculo?->color,
            'anio'     => $o->vehiculo?->anio,
            'cliente'  => ($o->vehiculo?->cliente?->persona?->nombre ?? '') . ' ' . ($o->vehiculo?->cliente?->persona?->apellido ?? ''),
            'telefono' => $o->vehiculo?->cliente?->persona?->telefono,
            'mecanico' => $o->empleado
                ? $o->empleado->persona?->nombre . ' ' . $o->empleado->persona?->apellido
                : null,
            'fecha'    => $o->created_at?->format('d/m/Y'),
        ]));
    }

    public function disponibles()
    {
        $ordenes = OrdenTrabajo::with(['vehiculo.cliente.persona'])
            ->whereNull('empleado_id')
            ->whereNotIn('estado', ['ENTREGADO'])
            ->latest()
            ->get();

        return response()->json($ordenes->map(fn($o) => [
            'id'           => $o->id,
            'estado'       => $o->estado,
            'placa'        => $o->vehiculo?->placa ?? '—',
            'marca'        => $o->vehiculo?->marca ?? '—',
            'modelo'       => $o->vehiculo?->modelo ?? '—',
            'cliente'      => ($o->vehiculo?->cliente?->persona?->nombre ?? '') . ' ' . ($o->vehiculo?->cliente?->persona?->apellido ?? ''),
            'descripcion'  => $o->descripcion_problema,
            'fecha_ingreso'=> $o->fecha_ingreso?->format('d/m/Y H:i'),
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehiculo_id'          => 'required|exists:vehiculos,id',
            'descripcion_problema' => 'required|string|max:1000',
            'empleado_id'          => 'nullable|exists:empleados,id',
        ]);

        $user     = $request->user();
        $empleado = Empleado::where('persona_id', $user->persona_id)->first();

        $empleadoId = match ($user->rol) {
            'MECANICO'    => $empleado?->id,
            default       => $request->empleado_id,
        };

        $orden = OrdenTrabajo::create([
            'vehiculo_id'          => $request->vehiculo_id,
            'empleado_id'          => $empleadoId,
            'estado'               => 'RECIBIDO',
            'descripcion_problema' => $request->descripcion_problema,
            'fecha_ingreso'        => now(),
        ]);

        HistorialEstado::create([
            'orden_id'        => $orden->id,
            'empleado_id'     => $empleadoId,
            'estado_anterior' => null,
            'estado_nuevo'    => 'RECIBIDO',
            'nota'            => 'Orden creada.',
            'created_at'      => now(),
        ]);

        ActivityLogger::log("API: Orden #{$orden->id} creada", 'OrdenApiController', $orden->id);

        return response()->json(['message' => 'Orden creada.', 'id' => $orden->id], 201);
    }

    public function show(Request $request, OrdenTrabajo $orden)
    {
        if (!$this->_autorizarOrden($orden, $request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $orden->load(['vehiculo.cliente.persona', 'empleado.persona', 'servicios', 'repuestos', 'imagenes', 'historialEstados']);

        return response()->json([
            'id'           => $orden->id,
            'estado'       => $orden->estado,
            'siguiente'    => $orden->siguienteEstado(),
            'placa'        => $orden->vehiculo?->placa ?? '—',
            'marca'        => $orden->vehiculo?->marca ?? '—',
            'modelo'       => $orden->vehiculo?->modelo ?? '—',
            'anio'         => $orden->vehiculo?->anio,
            'color'        => $orden->vehiculo?->color,
            'vehiculo_id'  => $orden->vehiculo?->id,
            'cliente'      => [
                'nombre'   => $orden->vehiculo?->cliente?->persona?->nombre ?? '—',
                'apellido' => $orden->vehiculo?->cliente?->persona?->apellido ?? '—',
                'telefono' => $orden->vehiculo?->cliente?->persona?->telefono,
            ],
            'mecanico'     => $orden->empleado ? [
                'nombre'   => $orden->empleado->persona->nombre,
                'apellido' => $orden->empleado->persona->apellido,
                'cargo'    => $orden->empleado->cargo,
            ] : null,
            'empleado_id'   => $orden->empleado_id,
            'descripcion'   => $orden->descripcion_problema,
            'diagnostico'   => $orden->diagnostico,
            'costo_total'   => $orden->costo_total,
            'fecha_ingreso' => $orden->fecha_ingreso?->format('d/m/Y H:i'),
            'servicios'     => $orden->servicios->map(fn($s) => [
                'id'     => $s->pivot->id ?? null,
                'servicio_id' => $s->id,
                'nombre' => $s->nombre,
                'precio' => $s->pivot->precio_aplicado,
                'obs'    => $s->pivot->observaciones,
            ]),
            'repuestos'     => $orden->repuestos->map(fn($r) => [
                'id'                => $r->id,
                'nombre'            => $r->nombre,
                'cantidad'          => $r->cantidad,
                'costo'             => $r->costo,
                'origen'            => $r->origen,
                'calidad_observada' => $r->calidad_observada,
            ]),
            'imagenes'      => $orden->imagenes->map(fn($i) => ['tipo' => $i->tipo, 'url' => $i->cloudinary_url]),
            'historial'     => $orden->historialEstados->map(fn($h) => [
                'de'    => $h->estado_anterior,
                'a'     => $h->estado_nuevo,
                'nota'  => $h->nota,
                'fecha' => \Carbon\Carbon::parse($h->created_at)->format('d/m/Y H:i'),
            ]),
        ]);
    }

    public function cambiarEstado(Request $request, OrdenTrabajo $orden)
    {
        if (!$this->_autorizarOrden($orden, $request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $request->validate(['nota' => 'nullable|string|max:500']);

        $siguiente = $orden->siguienteEstado();
        if (!$siguiente) {
            return response()->json(['message' => 'La orden ya está en el estado final.'], 422);
        }

        $empleado = $this->_empleadoActual($request);

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

        ActivityLogger::log("API: Estado cambiado a {$siguiente}", 'OrdenApiController', $orden->id);

        return response()->json(['message' => "Estado actualizado a {$siguiente}.", 'estado' => $siguiente]);
    }

    public function asignar(Request $request, OrdenTrabajo $orden)
    {
        if ($orden->empleado_id !== null) {
            return response()->json(['message' => 'La orden ya tiene mecánico asignado.'], 422);
        }

        $empleado = $this->_empleadoActual($request);
        if (!$empleado) {
            return response()->json(['message' => 'No tienes perfil de empleado.'], 422);
        }

        $orden->update(['empleado_id' => $empleado->id]);

        return response()->json(['message' => 'Te has asignado a la orden.']);
    }

    private function _recalcularCosto(OrdenTrabajo $orden): void
    {
        $costoServicios = DB::table('orden_servicio')->where('orden_id', $orden->id)->sum('precio_aplicado');
        $costoRepuestos = $orden->repuestos()->selectRaw('SUM(costo * cantidad) as total')->value('total') ?? 0;
        $orden->update(['costo_total' => $costoServicios + $costoRepuestos]);
    }

    public function agregarServicio(Request $request, OrdenTrabajo $orden)
    {
        if (!$this->_autorizarOrden($orden, $request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $request->validate([
            'servicio_id'    => 'required|exists:servicios,id',
            'precio_aplicado'=> 'required|numeric|min:0',
            'observaciones'  => 'nullable|string|max:255',
        ]);

        $orden->servicios()->attach($request->servicio_id, [
            'precio_aplicado' => $request->precio_aplicado,
            'observaciones'   => $request->observaciones,
        ]);

        $this->_recalcularCosto($orden);

        return response()->json(['message' => 'Servicio agregado.']);
    }

    public function quitarServicio(Request $request, OrdenTrabajo $orden)
    {
        if (!$this->_autorizarOrden($orden, $request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $request->validate(['servicio_id' => 'required|exists:servicios,id']);

        $orden->servicios()->detach($request->servicio_id);

        $this->_recalcularCosto($orden);

        return response()->json(['message' => 'Servicio quitado.']);
    }

    public function agregarRepuesto(Request $request, OrdenTrabajo $orden)
    {
        if (!$this->_autorizarOrden($orden, $request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $request->validate([
            'nombre'             => 'required|string|max:150',
            'origen'             => 'required|in:TALLER,CLIENTE',
            'cantidad'           => 'required|integer|min:1',
            'costo'              => 'required|numeric|min:0',
            'calidad_observada'  => 'nullable|string|max:200',
        ]);

        RepuestoUtilizado::create([
            'orden_id'           => $orden->id,
            'nombre'             => $request->nombre,
            'origen'             => $request->origen,
            'cantidad'           => $request->cantidad,
            'costo'              => $request->costo,
            'calidad_observada'  => $request->calidad_observada,
        ]);

        $this->_recalcularCosto($orden);

        return response()->json(['message' => 'Repuesto agregado.']);
    }

    public function eliminarRepuesto(Request $request, OrdenTrabajo $orden, RepuestoUtilizado $repuesto)
    {
        if (!$this->_autorizarOrden($orden, $request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($repuesto->orden_id !== $orden->id) {
            return response()->json(['message' => 'Repuesto no pertenece a esta orden.'], 422);
        }

        $repuesto->delete();

        $this->_recalcularCosto($orden);

        return response()->json(['message' => 'Repuesto eliminado.']);
    }

    public function subirFoto(Request $request, OrdenTrabajo $orden)
    {
        if (!$this->_autorizarOrden($orden, $request)) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $request->validate([
            'foto' => 'required|image|max:5120',
            'tipo' => 'required|in:RECEPCION,DAÑO,REPUESTO,ENTREGA',
        ]);

        try {
            $resultado = cloudinary()->uploadApi()->upload($request->file('foto')->getRealPath(), [
                'folder'        => 'taller-mecanico/ordenes/' . $orden->id,
                'resource_type' => 'image',
            ]);

            $imagen = ImagenOrden::create([
                'orden_trabajo_id' => $orden->id,
                'tipo'             => $request->tipo,
                'cloudinary_url'   => $resultado['secure_url'],
                'cloudinary_id'    => $resultado['public_id'],
                'descripcion'      => $request->descripcion ?? '',
                'subida_por'       => $request->user()->id,
            ]);

            return response()->json(['url' => $imagen->cloudinary_url, 'tipo' => $imagen->tipo]);
        } catch (\Exception $e) {
            \Log::error('Cloudinary upload error: ' . $e->getMessage());
            return response()->json(['message' => 'No se pudo subir la foto. Intenta de nuevo.'], 500);
        }
    }

    public function destroy(Request $request, OrdenTrabajo $orden)
    {
        if (!in_array($request->user()->rol, ['GERENTE', 'SUPER_ADMIN'])) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $orden->delete();
        return response()->json(['message' => 'Orden eliminada.']);
    }
}
