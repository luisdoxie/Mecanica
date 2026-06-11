<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cliente\StoreClienteRequest;
use App\Http\Requests\Cliente\UpdateClienteRequest;
use App\Models\Cliente;
use App\Models\Persona;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::with('persona')
            ->join('personas', 'clientes.persona_id', '=', 'personas.id')
            ->select('clientes.*');

        if ($buscar = $request->get('buscar')) {
            $query->where(function ($q) use ($buscar) {
                $q->where('personas.nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('personas.apellido', 'ilike', "%{$buscar}%")
                  ->orWhere('personas.ci', 'ilike', "%{$buscar}%")
                  ->orWhere('personas.telefono', 'ilike', "%{$buscar}%");
            });
        }

        $clientes = $query->orderBy('personas.apellido')->paginate(15)->withQueryString();

        return view('gerente.clientes.index', compact('clientes', 'buscar'));
    }

    public function create()
    {
        return view('gerente.clientes.create');
    }

    public function store(StoreClienteRequest $request)
    {
        $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $puedeLogin = $request->boolean('puede_login');

        DB::transaction(function () use ($request, $pin, $puedeLogin) {
            $persona = Persona::create([
                'nombre'    => $request->nombre,
                'apellido'  => $request->apellido,
                'ci'        => $request->ci,
                'telefono'  => $request->telefono,
                'email'     => $request->email,
                'direccion' => $request->direccion,
            ]);

            Cliente::create([
                'persona_id'  => $persona->id,
                'pin_acceso'  => $pin,
                'puede_login' => $puedeLogin,
            ]);

            if ($puedeLogin && $persona->email) {
                User::create([
                    'persona_id' => $persona->id,
                    'name'       => $persona->nombre . ' ' . $persona->apellido,
                    'email'      => $persona->email,
                    'password'   => Hash::make($pin),
                    'rol'        => 'CLIENTE',
                    'activo'     => true,
                ]);
            }
        });

        ActivityLogger::log('Cliente creado', class_basename(__CLASS__));

        $mensaje = $puedeLogin && $request->email
            ? "Cliente registrado. Puede ingresar con su correo y PIN: {$pin}"
            : 'Cliente registrado correctamente.';

        return redirect()->route('gerente.clientes.index')
            ->with('success', $mensaje);
    }

    public function show(Cliente $cliente)
    {
        $cliente->load(['persona', 'vehiculos']);
        return view('gerente.clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        $cliente->load('persona');
        return view('gerente.clientes.edit', compact('cliente'));
    }

    public function update(UpdateClienteRequest $request, Cliente $cliente)
    {
        DB::transaction(function () use ($request, $cliente) {
            $cliente->persona->update([
                'nombre'    => $request->nombre,
                'apellido'  => $request->apellido,
                'ci'        => $request->ci,
                'telefono'  => $request->telefono,
                'email'     => $request->email,
                'direccion' => $request->direccion,
            ]);

            $cliente->update([
                'puede_login' => $request->boolean('puede_login'),
            ]);
        });

        ActivityLogger::log('Cliente actualizado', class_basename(__CLASS__));

        return redirect()->route('gerente.clientes.show', $cliente)
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function toggleLogin(Cliente $cliente)
    {
        $cliente->load('persona');
        $activando = !$cliente->puede_login;

        if ($activando && !$cliente->persona->email) {
            return back()->with('error', 'El cliente no tiene email registrado. Es necesario para crear su acceso web.');
        }

        $cliente->update(['puede_login' => $activando]);

        if ($activando) {
            $user = User::where('persona_id', $cliente->persona_id)->first();

            if ($user) {
                $user->update([
                    'activo'   => true,
                    'password' => Hash::make($cliente->pin_acceso),
                ]);
            } else {
                User::create([
                    'persona_id' => $cliente->persona_id,
                    'name'       => $cliente->persona->nombre . ' ' . $cliente->persona->apellido,
                    'email'      => $cliente->persona->email,
                    'password'   => Hash::make($cliente->pin_acceso),
                    'rol'        => 'CLIENTE',
                    'activo'     => true,
                ]);
            }

            ActivityLogger::log('Acceso web activado', 'ClienteController', $cliente->id);
            return back()->with('success', "Acceso web activado. El cliente puede ingresar con su email y PIN: {$cliente->pin_acceso}");
        }

        $user = User::where('persona_id', $cliente->persona_id)->first();
        if ($user) {
            $user->update(['activo' => false]);
        }

        ActivityLogger::log('Acceso web desactivado', 'ClienteController', $cliente->id);
        return back()->with('success', 'Acceso web desactivado correctamente.');
    }
}
