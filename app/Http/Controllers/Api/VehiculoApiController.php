<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use Illuminate\Http\Request;

class VehiculoApiController extends Controller
{
    public function index()
    {
        $vehiculos = Vehiculo::with('cliente.persona')->get();

        return response()->json($vehiculos->map(fn($v) => [
            'id'      => $v->id,
            'placa'   => $v->placa,
            'marca'   => $v->marca,
            'modelo'  => $v->modelo,
            'anio'    => $v->anio,
            'color'   => $v->color,
            'km_actual' => $v->km_actual,
            'cliente' => $v->cliente->persona->nombre . ' ' . $v->cliente->persona->apellido,
            'cliente_id' => $v->cliente_id,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'placa'      => 'required|string|max:20|unique:vehiculos,placa',
            'marca'      => 'required|string|max:50',
            'modelo'     => 'required|string|max:50',
            'anio'       => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'color'      => 'nullable|string|max:30',
            'km_actual'  => 'nullable|integer|min:0',
        ]);

        $vehiculo = Vehiculo::create($request->only('cliente_id', 'placa', 'marca', 'modelo', 'anio', 'color', 'km_actual'));

        return response()->json(['message' => 'Vehículo creado.', 'id' => $vehiculo->id], 201);
    }

    public function show(Vehiculo $vehiculo)
    {
        $vehiculo->load('cliente.persona', 'ordenesTrabajo');

        return response()->json([
            'id'         => $vehiculo->id,
            'placa'      => $vehiculo->placa,
            'marca'      => $vehiculo->marca,
            'modelo'     => $vehiculo->modelo,
            'anio'       => $vehiculo->anio,
            'color'      => $vehiculo->color,
            'km_actual'  => $vehiculo->km_actual,
            'cliente_id' => $vehiculo->cliente_id,
            'cliente'    => $vehiculo->cliente->persona->nombre . ' ' . $vehiculo->cliente->persona->apellido,
        ]);
    }

    public function update(Request $request, Vehiculo $vehiculo)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'placa'      => 'required|string|max:20|unique:vehiculos,placa,' . $vehiculo->id,
            'marca'      => 'required|string|max:50',
            'modelo'     => 'required|string|max:50',
            'anio'       => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'color'      => 'nullable|string|max:30',
            'km_actual'  => 'nullable|integer|min:0',
        ]);

        $vehiculo->update($request->only('cliente_id', 'placa', 'marca', 'modelo', 'anio', 'color', 'km_actual'));

        return response()->json(['message' => 'Vehículo actualizado.']);
    }
}
