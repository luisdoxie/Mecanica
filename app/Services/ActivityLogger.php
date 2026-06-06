<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ActivityLogger
{
    public static function log(
        string $accion,
        string $modulo,
        int    $registroId = null,
        array  $datosAnteriores = [],
        array  $datosNuevos = []
    ): void {
        $user = auth()->user();

        try {
            DB::table('activity_log')->insert([
                'usuario_id'       => $user?->id,
                'rol'              => $user?->rol ?? 'SISTEMA',
                'accion'           => $accion,
                'modulo'           => $modulo,
                'registro_id'      => $registroId,
                'datos_anteriores' => empty($datosAnteriores) ? null : json_encode($datosAnteriores),
                'datos_nuevos'     => empty($datosNuevos)     ? null : json_encode($datosNuevos),
                'ip'               => request()->ip(),
                'dispositivo'      => substr(request()->userAgent() ?? '', 0, 255),
                'created_at'       => now(),
            ]);
        } catch (\Exception $e) {
            // No bloquear la operación si falla el log
        }
    }
}
