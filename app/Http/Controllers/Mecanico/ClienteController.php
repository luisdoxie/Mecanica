<?php

namespace App\Http\Controllers\Mecanico;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Models\Cliente;
use App\Models\Persona;
use App\Services\ActivityLogger;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function create()
    {
        return view('mecanico.clientes.create');
    }

    public function store(StoreClienteRequest $request)
    {
        $nombre   = $request->nombre;
        $apellido = $request->apellido;
        $clienteId = null;

        DB::transaction(function () use ($request, &$clienteId) {
            $persona = Persona::create([
                'nombre'    => $request->nombre,
                'apellido'  => $request->apellido,
                'ci'        => $request->ci,
                'telefono'  => $request->telefono,
                'email'     => $request->email,
                'direccion' => $request->direccion,
            ]);

            $cliente = Cliente::create([
                'persona_id'  => $persona->id,
                'pin_acceso'  => str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT),
                'puede_login' => false,
            ]);

            $clienteId = $cliente->id;
        });

        ActivityLogger::log('Cliente registrado por mecánico', 'ClienteController', $clienteId);

        return redirect()
            ->route('mecanico.vehiculos.create', ['cliente_id' => $clienteId])
            ->with('success', "Cliente {$nombre} {$apellido} registrado. Ahora registra su vehículo.");
    }
}
