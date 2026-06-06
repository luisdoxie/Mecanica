<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Http\Requests\Empleado\StoreEmpleadoRequest;
use App\Http\Requests\Empleado\UpdateEmpleadoRequest;
use App\Models\Empleado;
use App\Models\Especialidad;
use App\Models\Persona;
use App\Models\User;
use App\Models\OrdenTrabajo;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmpleadoController extends Controller
{
    private function prefixRoute(string $suffix): string
    {
        $prefix = request()->is('admin/*') ? 'admin' : 'gerente';
        return "{$prefix}.{$suffix}";
    }

    public function index(Request $request)
    {
        $query = Empleado::with(['persona', 'especialidades'])
            ->join('personas', 'empleados.persona_id', '=', 'personas.id')
            ->select('empleados.*')
            ->where('empleados.activo', true)
            ->whereNotIn('empleados.persona_id', function ($q) {
                $q->select('persona_id')->from('users')->whereIn('rol', ['GERENTE', 'SUPER_ADMIN'])->whereNotNull('persona_id');
            });

        if ($buscar = $request->get('buscar')) {
            $query->where(function ($q) use ($buscar) {
                $q->where('personas.nombre', 'ilike', "%{$buscar}%")
                  ->orWhere('personas.apellido', 'ilike', "%{$buscar}%")
                  ->orWhere('personas.ci', 'ilike', "%{$buscar}%")
                  ->orWhere('empleados.cargo', 'ilike', "%{$buscar}%");
            });
        }

        $empleados = $query->orderBy('personas.apellido')->paginate(15)->withQueryString();

        return view('gerente.empleados.index', compact('empleados', 'buscar'));
    }

    public function create()
    {
        $especialidades = Especialidad::orderBy('nombre')->get();
        return view('gerente.empleados.create', compact('especialidades'));
    }

    public function store(StoreEmpleadoRequest $request)
    {
        DB::transaction(function () use ($request) {
            $persona = Persona::create([
                'nombre'    => $request->nombre,
                'apellido'  => $request->apellido,
                'ci'        => $request->ci,
                'telefono'  => $request->telefono,
                'email'     => $request->email,
                'direccion' => $request->direccion,
            ]);

            $empleado = Empleado::create([
                'persona_id'   => $persona->id,
                'cargo'        => $request->cargo,
                'fecha_ingreso'=> $request->fecha_ingreso,
                'activo'       => true,
            ]);

            if ($request->especialidades) {
                $empleado->especialidades()->sync($request->especialidades);
            }

            User::create([
                'persona_id' => $persona->id,
                'name'       => $persona->nombre . ' ' . $persona->apellido,
                'email'      => $persona->email,
                'password'   => Hash::make($persona->ci),
                'rol'        => 'MECANICO',
            ]);

        });

        ActivityLogger::log('Empleado creado con acceso web', class_basename(__CLASS__));

        return redirect()->route($this->prefixRoute('empleados.index'))
            ->with('success', 'Empleado registrado. Contraseña inicial: su cédula.');
    }

    public function show(Empleado $empleado)
    {
        $empleado->load(['persona', 'especialidades', 'ordenesTrabajo' => fn($q) => $q->orderByDesc('created_at')->limit(10)]);
        return view('gerente.empleados.show', compact('empleado'));
    }

    public function edit(Empleado $empleado)
    {
        $empleado->load(['persona', 'especialidades']);
        $especialidades = Especialidad::orderBy('nombre')->get();
        return view('gerente.empleados.edit', compact('empleado', 'especialidades'));
    }

    public function update(UpdateEmpleadoRequest $request, Empleado $empleado)
    {
        DB::transaction(function () use ($request, $empleado) {
            $empleado->persona->update([
                'nombre'    => $request->nombre,
                'apellido'  => $request->apellido,
                'ci'        => $request->ci,
                'telefono'  => $request->telefono,
                'email'     => $request->email,
                'direccion' => $request->direccion,
            ]);

            $empleado->update([
                'cargo'         => $request->cargo,
                'fecha_ingreso' => $request->fecha_ingreso,
            ]);

            $empleado->especialidades()->sync($request->especialidades ?? []);

        });

        ActivityLogger::log('Empleado actualizado', class_basename(__CLASS__));

        return redirect()->route($this->prefixRoute('empleados.show'), $empleado)
            ->with('success', 'Empleado actualizado correctamente.');
    }

    public function desactivar(Empleado $empleado)
    {
        $empleado->update(['activo' => false]);

        // También desactivar el usuario vinculado
        if ($empleado->persona_id) {
            \App\Models\User::where('persona_id', $empleado->persona_id)->update(['activo' => false]);
        }

        ActivityLogger::log('Empleado desactivado', class_basename(__CLASS__));

        return redirect()->route($this->prefixRoute('empleados.index'))
            ->with('success', 'Empleado desactivado correctamente.');
    }

    public function destroy(Empleado $empleado)
    {
        // No permitir si tiene órdenes de trabajo asignadas
        if ($empleado->ordenesTrabajo()->whereNotIn('estado', ['ENTREGADO'])->exists()) {
            return back()->with('error', 'No se puede eliminar al empleado porque tiene órdenes de trabajo activas. Primero finalice o reasigne esas órdenes.');
        }

        $personaId = $empleado->persona_id;
        $nombre    = $empleado->persona->nombre . ' ' . $empleado->persona->apellido;

        DB::transaction(function () use ($empleado, $personaId) {
            User::where('persona_id', $personaId)->delete();
            $empleado->especialidades()->detach();
            $empleado->delete();
            \App\Models\Persona::find($personaId)?->delete();
        });

        ActivityLogger::log("Empleado eliminado permanentemente: {$nombre}", class_basename(__CLASS__));

        return redirect()->route($this->prefixRoute('empleados.index'))
            ->with('success', "Empleado \"{$nombre}\" eliminado permanentemente.");
    }
}
