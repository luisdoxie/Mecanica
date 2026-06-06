<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadoVehiculoController extends Controller
{
    public function consultar(Request $request, string $placa = null)
    {
        $placa = $placa ?? $request->input('placa');
        $resultado = null;

        if ($placa) {
            $vehiculo = DB::table('vehiculos')
                ->where('placa', strtoupper(trim($placa)))
                ->first();

            if ($vehiculo) {
                $orden = DB::table('ordenes_trabajo')
                    ->where('vehiculo_id', $vehiculo->id)
                    ->whereNotIn('estado', ['ENTREGADO'])
                    ->orderByDesc('created_at')
                    ->first();

                // Si no hay activa, buscar la última
                if (!$orden) {
                    $orden = DB::table('ordenes_trabajo')
                        ->where('vehiculo_id', $vehiculo->id)
                        ->orderByDesc('created_at')
                        ->first();
                }

                if ($orden) {
                    $empleado = $orden->empleado_id
                        ? DB::table('empleados')
                            ->join('personas', 'empleados.persona_id', '=', 'personas.id')
                            ->where('empleados.id', $orden->empleado_id)
                            ->select('personas.nombre', 'personas.apellido')
                            ->first()
                        : null;

                    $servicios = DB::table('orden_servicio')
                        ->join('servicios', 'orden_servicio.servicio_id', '=', 'servicios.id')
                        ->where('orden_servicio.orden_id', $orden->id)
                        ->select('servicios.nombre', 'orden_servicio.observaciones')
                        ->get();

                    $fotos = DB::table('imagenes_orden')
                        ->where('orden_trabajo_id', $orden->id)
                        ->whereIn('tipo', ['DAÑO', 'ENTREGA'])
                        ->get();

                    $resultado = [
                        'encontrado' => true,
                        'vehiculo'   => $vehiculo,
                        'orden'      => $orden,
                        'mecanico'   => $empleado,
                        'servicios'  => $servicios,
                        'fotos'      => $fotos,
                    ];
                } else {
                    $resultado = ['encontrado' => false, 'placa' => $placa];
                }
            } else {
                $resultado = ['encontrado' => false, 'placa' => $placa];
            }
        }

        return view('welcome', compact('resultado', 'placa'));
    }
}
