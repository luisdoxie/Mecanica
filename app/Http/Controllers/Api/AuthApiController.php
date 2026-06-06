<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::with('persona')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Credenciales incorrectas.'], 401);
        }

        if (!$user->activo) {
            return response()->json(['message' => 'Cuenta desactivada.'], 403);
        }

        if (!in_array($user->rol, ['GERENTE', 'MECANICO', 'SUPER_ADMIN'])) {
            return response()->json(['message' => 'Acceso no permitido para este rol.'], 403);
        }

        $token = $user->createToken('mecanica-app')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'      => $user->id,
                'name'    => $user->name,
                'email'   => $user->email,
                'rol'     => $user->rol,
                'nombre'  => $user->persona?->nombre,
                'apellido'=> $user->persona?->apellido,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sesión cerrada.']);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('persona');
        return response()->json([
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'rol'     => $user->rol,
            'nombre'  => $user->persona?->nombre,
            'apellido'=> $user->persona?->apellido,
        ]);
    }
}
