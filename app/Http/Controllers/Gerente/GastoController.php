<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use App\Models\CategoriaGasto;
use App\Models\Gasto;
use App\Models\OrdenTrabajo;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $mesActual = now()->format('Y-m');

        $query = Gasto::with(['categoria', 'registradoPor'])->latest('fecha');

        if ($cat = $request->get('categoria_id')) {
            $query->where('categoria_id', $cat);
        }
        if ($desde = $request->get('desde')) {
            $query->where('fecha', '>=', $desde);
        }
        if ($hasta = $request->get('hasta')) {
            $query->where('fecha', '<=', $hasta);
        }

        $gastos      = $query->paginate(15)->withQueryString();
        $categorias  = CategoriaGasto::orderBy('nombre')->get();
        $totalMes    = Gasto::whereRaw("TO_CHAR(fecha, 'YYYY-MM') = ?", [$mesActual])->sum('monto');
        $filtros     = $request->only('categoria_id', 'desde', 'hasta');

        return view('gerente.gastos.index', compact('gastos', 'categorias', 'totalMes', 'filtros', 'mesActual'));
    }

    public function create()
    {
        $categorias = CategoriaGasto::orderBy('nombre')->get();
        $ordenes    = OrdenTrabajo::whereIn('estado', ['LISTO', 'ENTREGADO'])
                        ->with('vehiculo')->orderByDesc('created_at')->limit(50)->get();
        return view('gerente.gastos.create', compact('categorias', 'ordenes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id'    => 'required|exists:categorias_gasto,id',
            'descripcion'     => 'required|string|max:300',
            'monto'           => 'required|numeric|min:0.01',
            'fecha'           => 'required|date',
            'comprobante'     => 'nullable|image|max:5120',
            'orden_trabajo_id'=> 'nullable|exists:ordenes_trabajo,id',
        ], [
            'categoria_id.required' => 'Seleccione la categoría.',
            'descripcion.required'  => 'La descripción es obligatoria.',
            'monto.required'        => 'El monto es obligatorio.',
            'fecha.required'        => 'La fecha es obligatoria.',
        ]);

        $comprobanteUrl = null;

        if ($request->hasFile('comprobante')) {
            try {
                $res = cloudinary()->upload($request->file('comprobante')->getRealPath(), [
                    'folder' => 'taller-mecanico/gastos',
                ]);
                $comprobanteUrl = $res->getSecurePath();
            } catch (\Exception $e) {
                return back()->withInput()->with('error', 'Error al subir comprobante: ' . $e->getMessage());
            }
        }

        $gasto = Gasto::create([
            'categoria_id'     => $request->categoria_id,
            'descripcion'      => $request->descripcion,
            'monto'            => $request->monto,
            'fecha'            => $request->fecha,
            'comprobante_url'  => $comprobanteUrl,
            'registrado_por'   => auth()->id(),
            'orden_trabajo_id' => $request->orden_trabajo_id,
        ]);

        ActivityLogger::log('Gasto registrado', class_basename(__CLASS__));

        return redirect()->route('gerente.gastos.index')
            ->with('success', 'Gasto registrado correctamente.');
    }
}
