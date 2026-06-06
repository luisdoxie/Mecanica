<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\BitacoraExport;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('usuario')->orderByDesc('created_at');

        if ($usuario = $request->get('usuario')) {
            $query->whereHas('usuario', fn($q) => $q->where('name', 'ilike', "%{$usuario}%"));
        }
        if ($accion = $request->get('accion')) {
            $query->where('accion', 'ilike', "%{$accion}%");
        }
        if ($modulo = $request->get('modulo')) {
            $query->where('modulo', 'ilike', "%{$modulo}%");
        }
        if ($desde = $request->get('desde')) {
            $query->where('created_at', '>=', $desde . ' 00:00:00');
        }
        if ($hasta = $request->get('hasta')) {
            $query->where('created_at', '<=', $hasta . ' 23:59:59');
        }

        $registros = $query->paginate(25)->withQueryString();
        $filtros   = $request->only('usuario', 'accion', 'modulo', 'desde', 'hasta');

        return view('admin.bitacora.index', compact('registros', 'filtros'));
    }

    public function exportar(Request $request)
    {
        $filtros = $request->only('usuario', 'accion', 'modulo', 'desde', 'hasta');
        $filename = 'bitacora_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new BitacoraExport($filtros), $filename);
    }
}
