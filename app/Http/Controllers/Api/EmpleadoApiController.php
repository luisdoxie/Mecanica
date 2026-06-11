<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empleado;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmpleadoApiController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with('persona')->get();

        return response()->json($empleados->map(fn($e) => [
            'id'       => $e->id,
            'nombre'   => $e->persona->nombre . ' ' . $e->persona->apellido,
            'ci'       => $e->persona->ci,
            'cargo'    => $e->cargo,
            'telefono' => $e->persona->telefono,
            'activo'   => $e->activo,
        ]));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'        => 'required|string|max:100',
            'apellido'      => 'required|string|max:100',
            'ci'            => 'required|string|max:20|unique:personas,ci',
            'telefono'      => 'nullable|string|max:20',
            'email'         => 'required|email|max:100|unique:users,email',
            'direccion'     => 'nullable|string|max:255',
            'cargo'         => 'required|string|max:50',
            'fecha_ingreso' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request) {
            $persona = Persona::create([
                'nombre'    => $request->nombre,
                'apellido'  => $request->apellido,
                'ci'        => $request->ci,
                'telefono'  => $request->telefono,
                'email'     => $request->email,
                'direccion' => $request->direccion,
            ]);

            Empleado::create([
                'persona_id'    => $persona->id,
                'cargo'         => $request->cargo,
                'fecha_ingreso' => $request->fecha_ingreso,
                'activo'        => true,
            ]);

            User::create([
                'name'       => $request->nombre . ' ' . $request->apellido,
                'email'      => $request->email,
                'password'   => Hash::make($request->ci),
                'persona_id' => $persona->id,
                'rol'        => 'MECANICO',
                'activo'     => true,
            ]);
        });

        return response()->json(['message' => 'Empleado creado. Contraseña inicial: CI del empleado.'], 201);
    }

    public function show(Empleado $empleado)
    {
        $empleado->load('persona');

        return response()->json([
            'id'            => $empleado->id,
            'nombre'        => $empleado->persona->nombre,
            'apellido'      => $empleado->persona->apellido,
            'ci'            => $empleado->persona->ci,
            'telefono'      => $empleado->persona->telefono,
            'email'         => $empleado->persona->email,
            'direccion'     => $empleado->persona->direccion,
            'cargo'         => $empleado->cargo,
            'fecha_ingreso' => $empleado->fecha_ingreso?->format('Y-m-d'),
            'activo'        => $empleado->activo,
        ]);
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'nombre'        => 'required|string|max:100',
            'apellido'      => 'required|string|max:100',
            'ci'            => 'required|string|max:20|unique:personas,ci,' . $empleado->persona_id,
            'telefono'      => 'nullable|string|max:20',
            'direccion'     => 'nullable|string|max:255',
            'cargo'         => 'required|string|max:50',
            'fecha_ingreso' => 'nullable|date',
        ]);

        $empleado->persona->update([
            'nombre'    => $request->nombre,
            'apellido'  => $request->apellido,
            'ci'        => $request->ci,
            'telefono'  => $request->telefono,
            'direccion' => $request->direccion,
        ]);

        $empleado->update([
            'cargo'         => $request->cargo,
            'fecha_ingreso' => $request->fecha_ingreso,
        ]);

        return response()->json(['message' => 'Empleado actualizado.']);
    }

    public function desactivar(Empleado $empleado)
    {
        $empleado->update(['activo' => false]);

        $user = User::where('persona_id', $empleado->persona_id)->first();
        $user?->update(['activo' => false]);

        return response()->json(['message' => 'Empleado desactivado.']);
    }

    public function reactivar(Empleado $empleado)
    {
        $empleado->update(['activo' => true]);

        $user = User::where('persona_id', $empleado->persona_id)->first();
        $user?->update(['activo' => true]);

        return response()->json(['message' => 'Empleado reactivado.']);
    }

    public function destroy(Empleado $empleado)
    {
        $activas = $empleado->ordenesTrabajo()->whereNotIn('estado', ['ENTREGADO'])->count();

        if ($activas > 0) {
            return response()->json(['message' => 'No se puede eliminar: tiene órdenes activas.'], 422);
        }

        DB::transaction(function () use ($empleado) {
            $persona = $empleado->persona;
            User::where('persona_id', $persona->id)->delete();
            $empleado->delete();
            $persona->delete();
        });

        return response()->json(['message' => 'Empleado eliminado.']);
    }
}
