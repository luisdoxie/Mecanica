<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('persona')->latest();

        if ($rol = $request->get('rol')) {
            $query->where('rol', $rol);
        }
        if ($request->has('activo')) {
            $query->where('activo', (bool) $request->get('activo'));
        }

        $usuarios = $query->paginate(15)->withQueryString();
        $roles    = ['SUPER_ADMIN', 'GERENTE', 'MECANICO', 'CLIENTE'];
        $filtros  = $request->only('rol', 'activo');

        return view('admin.usuarios.index', compact('usuarios', 'roles', 'filtros'));
    }

    public function cambiarRol(Request $request, User $usuario)
    {
        $request->validate(['rol' => 'required|in:SUPER_ADMIN,GERENTE,MECANICO,CLIENTE']);

        $rolAnterior = $usuario->rol;
        $usuario->update(['rol' => $request->rol]);

        ActivityLogger::log("Rol cambiado de {$rolAnterior} a {$request->rol}", 'Usuarios', $usuario->id);

        return back()->with('success', "Rol actualizado a {$request->rol}.");
    }

    public function toggleActivo(User $usuario)
    {
        $usuario->update(['activo' => !$usuario->activo]);
        $estado = $usuario->activo ? 'activado' : 'desactivado';

        ActivityLogger::log("Usuario {$estado}", 'Usuarios', $usuario->id);

        return back()->with('success', "Usuario {$estado} correctamente.");
    }

    public function resetPassword(Request $request, User $usuario)
    {
        $request->validate([
            'password' => 'required|string|min:6',
        ], ['password.required' => 'La nueva contraseña es obligatoria.']);

        $usuario->update(['password' => Hash::make($request->password)]);

        ActivityLogger::log('Contraseña reseteada', 'Usuarios', $usuario->id);

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propia cuenta.');
        }

        DB::transaction(function () use ($usuario) {
            $personaId = $usuario->persona_id;
            ActivityLogger::log('Usuario eliminado', 'Usuarios', $usuario->id);
            $usuario->delete();
            if ($personaId) {
                DB::table('personas')->where('id', $personaId)->delete();
            }
        });

        return redirect()->route('admin.usuarios.index')
            ->with('success', 'Usuario y persona eliminados correctamente.');
    }
}
