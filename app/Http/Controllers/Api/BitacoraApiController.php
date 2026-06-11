<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;

class BitacoraApiController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        if (!in_array($request->user()->rol, ['GERENTE', 'SUPER_ADMIN'])) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $logs = ActivityLog::with('usuario')->latest()->limit(100)->get();

        return response()->json($logs->map(fn($a) => [
            'id'          => $a->id,
            'accion'      => $a->accion,
            'modulo'      => $a->modulo,
            'rol'         => $a->rol,
            'usuario'     => $a->usuario?->name,
            'ip'          => $a->ip,
            'dispositivo' => $a->dispositivo,
            'fecha'       => $a->created_at?->format('d/m/Y H:i'),
        ]));
    }
}
