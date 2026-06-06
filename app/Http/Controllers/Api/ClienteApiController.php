<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClienteApiController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('persona', 'vehiculos')->get();

        return response()->json($clientes->map(fn($c) => [
            'id'          => $c->id,
            'nombre'      => $c->persona->nombre . ' ' . $c->persona->apellido,
            'ci'          => $c->persona->ci,
            'telefono'    => $c->persona->telefono,
            'vehiculos'   => $c->vehiculos->count(),
            'puede_login' => $c->puede_login,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'ci'        => 'required|string|max:20|unique:personas,ci',
            'telefono'  => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
        ]);

        $cliente = DB::transaction(function () use ($request) {
            $persona = Persona::create([
                'nombre'    => $request->nombre,
                'apellido'  => $request->apellido,
                'ci'        => $request->ci,
                'telefono'  => $request->telefono,
                'email'     => $request->email,
                'direccion' => $request->direccion,
            ]);

            $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            return Cliente::create([
                'persona_id'  => $persona->id,
                'pin_acceso'  => $pin,
                'puede_login' => false,
            ]);
        });

        return response()->json(['message' => 'Cliente creado.', 'id' => $cliente->id], 201);
    }

    public function show(Cliente $cliente)
    {
        $cliente->load('persona', 'vehiculos');

        return response()->json([
            'id'          => $cliente->id,
            'nombre'      => $cliente->persona->nombre,
            'apellido'    => $cliente->persona->apellido,
            'ci'          => $cliente->persona->ci,
            'telefono'    => $cliente->persona->telefono,
            'email'       => $cliente->persona->email,
            'direccion'   => $cliente->persona->direccion,
            'puede_login' => $cliente->puede_login,
            'vehiculos'   => $cliente->vehiculos->map(fn($v) => [
                'id'     => $v->id,
                'placa'  => $v->placa,
                'marca'  => $v->marca,
                'modelo' => $v->modelo,
                'anio'   => $v->anio,
            ]),
        ]);
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'apellido'  => 'required|string|max:100',
            'ci'        => 'required|string|max:20|unique:personas,ci,' . $cliente->persona_id,
            'telefono'  => 'nullable|string|max:20',
            'email'     => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
        ]);

        $cliente->persona->update([
            'nombre'    => $request->nombre,
            'apellido'  => $request->apellido,
            'ci'        => $request->ci,
            'telefono'  => $request->telefono,
            'email'     => $request->email,
            'direccion' => $request->direccion,
        ]);

        return response()->json(['message' => 'Cliente actualizado.']);
    }

    public function toggleLogin(Cliente $cliente)
    {
        $cliente->update(['puede_login' => !$cliente->puede_login]);
        return response()->json(['puede_login' => $cliente->puede_login]);
    }
}
