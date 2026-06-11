<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioApiController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->rol !== 'SUPER_ADMIN') {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $usuarios = User::with('persona')
            ->orderBy('name')
            ->get()
            ->map(fn($u) => [
                'id'      => $u->id,
                'name'    => $u->name,
                'email'   => $u->email,
                'rol'     => $u->rol,
                'activo'  => $u->activo,
                'nombre'  => $u->persona?->nombre,
                'apellido'=> $u->persona?->apellido,
                'ci'      => $u->persona?->ci,
            ]);

        return response()->json($usuarios);
    }
}
