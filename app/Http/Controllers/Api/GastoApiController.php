<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gasto;
use Illuminate\Http\Request;

class GastoApiController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        if (!in_array($request->user()->rol, ['GERENTE', 'SUPER_ADMIN'])) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $gastos = Gasto::with('categoria')->latest('fecha')->limit(100)->get();

        return response()->json($gastos->map(fn($g) => [
            'id'          => $g->id,
            'descripcion' => $g->descripcion,
            'monto'       => $g->monto,
            'fecha'       => $g->fecha?->format('d/m/Y'),
            'categoria'   => $g->categoria?->nombre ?? '—',
            'categoria_id'=> $g->categoria_id,
        ]));
    }

    public function store(Request $request)
    {
        if (!in_array($request->user()->rol, ['GERENTE', 'SUPER_ADMIN'])) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $request->validate([
            'categoria_id'  => 'required|exists:categorias_gasto,id',
            'descripcion'   => 'required|string|max:255',
            'monto'         => 'required|numeric|min:0.01',
            'fecha'         => 'required|date',
            'orden_trabajo_id' => 'nullable|exists:ordenes_trabajo,id',
        ]);

        $gasto = Gasto::create([
            'categoria_id'     => $request->categoria_id,
            'descripcion'      => $request->descripcion,
            'monto'            => $request->monto,
            'fecha'            => $request->fecha,
            'orden_trabajo_id' => $request->orden_trabajo_id,
            'registrado_por'   => $request->user()->id,
        ]);

        return response()->json(['message' => 'Gasto registrado.', 'id' => $gasto->id], 201);
    }
}
