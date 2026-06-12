<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Servicio;
use App\Models\CategoriaGasto;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Empleado;

class CatalogoApiController extends Controller
{
    public function servicios()
    {
        return response()->json(
            Servicio::where('activo', true)->orderBy('nombre')->get()
                ->map(fn($s) => ['id' => $s->id, 'nombre' => $s->nombre, 'precio_base' => $s->precio_base])
        );
    }

    public function categoriasGasto()
    {
        return response()->json(
            CategoriaGasto::orderBy('nombre')->get()
                ->map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre])
        );
    }

    public function clientesSelect()
    {
        return response()->json(
            Cliente::with('persona')->get()
                ->map(fn($c) => [
                    'id'     => $c->id,
                    'nombre' => $c->persona->nombre . ' ' . $c->persona->apellido,
                    'ci'     => $c->persona->ci,
                ])
        );
    }

    public function vehiculosSelect()
    {
        return response()->json(
            Vehiculo::with('cliente.persona')->get()
                ->map(fn($v) => [
                    'id'         => $v->id,
                    'label'      => $v->placa . ' — ' . $v->marca . ' ' . $v->modelo,
                    'placa'      => $v->placa,
                    'cliente'    => $v->cliente->persona->nombre . ' ' . $v->cliente->persona->apellido,
                    'cliente_id' => $v->cliente_id,
                ])
        );
    }

    public function empleadosSelect()
    {
        return response()->json(
            Empleado::with('persona')->where('activo', true)->get()
                ->map(fn($e) => ['id' => $e->id, 'nombre' => $e->persona->nombre . ' ' . $e->persona->apellido, 'cargo' => $e->cargo])
        );
    }
}
